<?php
/**
 * Router - Manejo de rutas de la API con soporte de middleware
 */

namespace Api\Core;

use Api\Core\Request;
use Api\Core\Response;
use Api\Middleware\MiddlewareInterface;
use Api\Config\Config;
use Exception;

class Router {
    private $routes = [];
    private $globalMiddleware = [];
    private $middlewareConfig = [];
    
    public function __construct() {
        // Cargar configuración de middleware
        $this->middlewareConfig = require __DIR__ . '/../config/middleware.php';
        
        // Cargar middleware global
        foreach ($this->middlewareConfig['global'] as $middleware) {
            $this->addGlobalMiddleware($middleware);
        }
    }
    
    public function addRoute($method, $path, $handler, $middleware = []) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }
    
    public function addGlobalMiddleware($middleware) {
        $this->globalMiddleware[] = $middleware;
    }
    
    public function group($prefix, $options, $callback) {
        // Para implementación futura de grupos de rutas
        $callback($this);
    }
    
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remover /sistema-new/api del path si existe
        $basePath = '/sistema-new/api';
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Si la URI está vacía, establecer como raíz
        if (empty($uri) || $uri === '/') {
            $uri = '/';
        }
        
        // Debug logging
        if (Config::isDebug()) {
            error_log("Router Debug - Method: {$method}, URI: {$uri}");
            error_log("Available routes: " . print_r($this->routes, true));
        }
        
        // Crear objetos Request y Response
        $request = new Request();
        
        // Manejar OPTIONS requests globalmente
        if ($method === 'OPTIONS') {
            // Para OPTIONS, solo ejecutar middleware global (incluyendo CORS)
            $response = $this->runMiddleware($this->globalMiddleware, $request, function($request) {
                // OPTIONS manejado por CorsMiddleware
                return new Response([
                    'success' => true,
                    'message' => 'OPTIONS request handled by CORS middleware',
                    'timestamp' => date('Y-m-d H:i:s')
                ], 200);
            });
            
            if ($response instanceof Response) {
                return $response->send();
            }
            return;
        }
        
        // Buscar ruta coincidente para otros métodos
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            $pattern = $this->convertToRegex($route['path']);
            
            if (preg_match($pattern, $uri, $matches)) {
                // Extraer parámetros de la URL
                array_shift($matches); // Remover match completo
                $request->setParams($this->extractNamedParams($matches));
                
                // Combinar middleware global con middleware específico de la ruta
                $routeMiddleware = array_merge(
                    $this->globalMiddleware,
                    $route['middleware'] ?? []
                );
                
                // Crear la cadena de middleware
                $response = $this->runMiddleware($routeMiddleware, $request, function($request) use ($route) {
                    return $this->executeHandler($route['handler'], $request);
                });
                
                // Si la respuesta no se ha enviado, enviarla ahora
                if ($response instanceof Response) {
                    return $response->send();
                }
                
                return;
            }
        }
        
        // Ruta no encontrada
        $response = new Response([
            'error' => true,
            'message' => 'Endpoint no encontrado',
            'path' => $uri,
            'method' => $method,
            'timestamp' => date('Y-m-d H:i:s')
        ], 404);
        
        return $response->send();
    }
    
    private function convertToRegex($path) {
        // Convertir {param} a regex
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    private function runMiddleware($middlewares, $request, $handler) {
        $pipeline = array_reduce(
            array_reverse($middlewares),
            function ($next, $middleware) {
                return function ($request) use ($next, $middleware) {
                    // 🔥 CONVERSIÓN AUTOMÁTICA A PSR-4
                    $middlewareClass = $this->resolveMiddlewareClass($middleware);
                    
                    if (class_exists($middlewareClass)) {
                        $middlewareInstance = new $middlewareClass();
                        if ($middlewareInstance instanceof MiddlewareInterface) {
                            return $middlewareInstance->handle($request, $next);
                        }
                    }
                    return $next($request);
                };
            },
            $handler
        );
        
        return $pipeline($request);
    }
    
    /**
     * 🚀 Convierte nombres de middleware a namespaces PSR-4
     */
    private function resolveMiddlewareClass($middleware) {
        // Si ya tiene namespace completo, retornarlo
        if (strpos($middleware, 'Api\\') === 0) {
            return $middleware;
        }
        
        // Si termina en 'Middleware', agregar namespace Api\Middleware
        if (strpos($middleware, 'Middleware') !== false) {
            return 'Api\\Middleware\\' . $middleware;
        }
        
        // Por defecto, asumir que es del namespace Api\Middleware
        return 'Api\\Middleware\\' . $middleware;
    }
    
    private function extractNamedParams($matches) {
        // Por simplicidad, usar índices numéricos
        // Esto se puede mejorar para manejar nombres de parámetros
        $params = [];
        foreach ($matches as $index => $match) {
            $params["param{$index}"] = $match;
            if ($index === 0) {
                $params['id'] = $match; // Convención común
            }
        }
        return $params;
    }
    
    private function executeHandler($handler, $request) {
        list($controller, $method) = explode('@', $handler);
        
        // 🚀 PSR-4: No necesitamos require_once, Composer autocarga
        // Solo verificar que la clase existe (autoloading automático)
        
        if (!class_exists($controller)) {
            return new Response([
                'error' => true,
                'message' => "Controller $controller no encontrado",
                'debug' => Config::isDebug() ? "Verificar namespace PSR-4: $controller" : null,
                'timestamp' => date('Y-m-d H:i:s')
            ], 500);
        }
        
        try {
            $controllerInstance = new $controller();
        } catch (Exception $e) {
            return new Response([
                'error' => true,
                'message' => "Error al instanciar controller $controller",
                'debug' => Config::isDebug() ? $e->getMessage() : null,
                'timestamp' => date('Y-m-d H:i:s')
            ], 500);
        }
        
        if (!method_exists($controllerInstance, $method)) {
            return new Response([
                'error' => true,
                'message' => "Método $method no encontrado en $controller",
                'timestamp' => date('Y-m-d H:i:s')
            ], 500);
        }
        
        // Inyectar request en el controlador si es posible
        if (method_exists($controllerInstance, 'setRequest')) {
            $controllerInstance->setRequest($request);
        }
        
        try {
            $response = $controllerInstance->$method();
            
            // Si el controlador retorna algo que no es Response, convertirlo
            if (!($response instanceof Response)) {
                return new Response($response);
            }
            
            return $response;
            
        } catch (Exception $e) {
            return new Response([
                'error' => true,
                'message' => 'Error interno del servidor',
                'details' => Config::isDebug() ? [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null,
                'timestamp' => date('Y-m-d H:i:s')
            ], 500);
        }
    }
    
    // ✅ findController eliminado - PSR-4 autocarga las clases
    
    private function extractParamNames($handler) {
        // Por ahora, asumimos que los parámetros siguen el orden {id}, etc.
        // Esto se puede mejorar más adelante si es necesario
        return ['id']; // Por defecto, el primer parámetro es 'id'
    }
}