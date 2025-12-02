<?php
/**
 * API - Sistema T&M
 * Punto de entrada principal - PSR-4 Moderno
 */

// 🚀 AUTOLOADER PSR-4 CON COMPOSER
require_once __DIR__ . '/../vendor/autoload.php';

// Usar las clases con namespaces PSR-4
use Api\Config\Config;
use Api\Core\Router;
use Api\Core\Response;

// Inicializar configuración
Config::init();

// Cargar helpers
require_once __DIR__ . '/utils/helpers.php';

// CORS ahora es manejado por CorsMiddleware
// No es necesario manejarlo manualmente aquí

// Inicializar componentes core
$router = new Router();

// Rutas core (sin autenticación) - Usando PSR-4
$router->addRoute('GET', '/api/health', 'Api\\Controllers\\HealthController@check');

// Descubrir módulos dinámicamente con PSR-4
$moduleDirs = glob(__DIR__ . '/modules/*', GLOB_ONLYDIR);
$modules = array_map('basename', $moduleDirs);

foreach ($modules as $module) {
    $routeFile = __DIR__ . "/modules/{$module}/routes.php";
    $middlewareFile = __DIR__ . "/modules/{$module}/middleware.php";
    
    if (file_exists($routeFile)) {
        $moduleRoutes = require $routeFile;
        $moduleMiddleware = [];
        
        // Cargar configuración de middleware del módulo si existe
        if (file_exists($middlewareFile)) {
            $middlewareConfig = require $middlewareFile;
            $moduleMiddleware = $middlewareConfig['middleware'] ?? [];
        }
        
        if (is_array($moduleRoutes)) {
            foreach ($moduleRoutes as $route => $handler) {
                list($method, $path) = explode(' ', $route, 2);
                
                // 🔥 CONVERTIR HANDLER A PSR-4 NAMESPACE
                $handlerPSR4 = convertHandlerToPSR4($handler, $module);
                
                // Construir middleware para esta ruta
                $middleware = [];
                
                // Middleware universal del módulo (aplica a todas las rutas)
                if (isset($moduleMiddleware['*'])) {
                    $middleware = array_merge($middleware, $moduleMiddleware['*']);
                }
                
                // Middleware específico para esta ruta
                $routeKey = $method . ' ' . $path;
                if (isset($moduleMiddleware[$routeKey])) {
                    $middleware = array_merge($middleware, $moduleMiddleware[$routeKey]);
                }
                
                $router->addRoute($method, $path, $handlerPSR4, $middleware);
            }
        }
    }
}

/**
 * 🚀 CONVERSIÓN AUTOMÁTICA DE HANDLERS A PSR-4
 * Genera namespaces automáticamente según el nombre del módulo
 * ¡Agregar cualquier módulo nuevo sin configuración adicional!
 */
function convertHandlerToPSR4($handler, $module) {
    // Si ya tiene namespace, retornarlo tal cual
    if (strpos($handler, 'Api\\') === 0) {
        return $handler;
    }
    
    // 🔥 GENERACIÓN AUTOMÁTICA DE NAMESPACE
    // Convierte "tareas" -> "Api\Modules\Tareas\"
    // Convierte "mi_nuevo_modulo" -> "Api\Modules\MiNuevoModulo\"
    $moduleName = str_replace(['-', '_'], ' ', $module);
    $moduleName = ucwords($moduleName);
    $moduleName = str_replace(' ', '', $moduleName);
    
    $namespace = "Api\\Modules\\{$moduleName}\\";
    
    return $namespace . $handler;
}

// Procesar la petición
try {
    $router->dispatch();
} catch (Exception $e) {
    $response = new Response();
    
    if (Config::isDebug()) {
        $response->error('Error interno del servidor', 500, [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    } else {
        $response->error('Error interno del servidor', 500);
    }
}