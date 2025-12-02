<?php
/**
 * AuthorizationMiddleware - Middleware de autorización basado en métodos HTTP
 * Implementa el mapeo automático de métodos HTTP a permisos de usuario
 */
namespace Api\Middleware;
use Api\Core\Request;
use Api\Core\Response;
use Api\Core\Database;;
use Api\Middleware\MiddlewareInterface;
use Exception;

class AuthorizationMiddleware implements MiddlewareInterface {
    
    private $httpMethodMap = [
        'GET' => 'seguimiento',
        'POST' => 'adicionar', 
        'PUT' => 'editar',
        'PATCH' => 'editar',
        'DELETE' => 'eliminar'
    ];
    
    private $moduleConfig = [];
    
    public function __construct() {
        // Cargar configuraciones de autorización de todos los módulos
        $this->loadModuleConfigurations();
    }
    
    public function handle(Request $request, callable $next): Response {
        $method = $request->getMethod();
        $path = $request->getPath();
        $user = $request->getUser();
        
        // Si no hay usuario autenticado, el AuthenticationMiddleware debería haberlo manejado
        if (!$user) {
            return new Response([
                'error' => true,
                'message' => 'Usuario no autenticado',
                'timestamp' => date('Y-m-d H:i:s')
            ], 401);
        }
        
        // Los administradores (user_level = 1) tienen acceso total
        if (isset($user['user_level']) && $user['user_level'] == 1) {
            return $next($request);
        }
        
        // Determinar el módulo basándose en la ruta
        $module = $this->extractModuleFromPath($path);
        
        if (!$module) {
            // Si no se puede determinar el módulo, permitir acceso
            // (endpoints como /api/health, etc.)
            return $next($request);
        }
        
        // Obtener configuración de autorización del módulo
        $authConfig = $this->getModuleAuthConfig($module);
        
        if (!$authConfig || !$authConfig['enabled']) {
            // Si no hay configuración o está deshabilitada, permitir acceso
            return $next($request);
        }
        
        // Determinar página y permiso requerido
        $pagePermission = $this->determinePageAndPermission($method, $path, $authConfig);
        
        if (!$pagePermission) {
            return new Response([
                'error' => true,
                'message' => 'No se pudo determinar los permisos requeridos',
                'timestamp' => date('Y-m-d H:i:s')
            ], 403);
        }
        
        // Verificar si el usuario tiene el permiso requerido
        if ($this->hasPermission($user['username'], $pagePermission['page'], $pagePermission['permission'])) {
            return $next($request);
        }
        
        // Acceso denegado
        return new Response([
            'error' => true,
            'message' => 'Sin permisos para realizar esta acción',
            'details' => [
                'page' => $pagePermission['page'],
                'permission' => $pagePermission['permission'],
                'method' => $method
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ], 403);
    }
    
    private function loadModuleConfigurations() {
        $moduleDirs = glob(__DIR__ . '/../modules/*', GLOB_ONLYDIR);
        
        foreach ($moduleDirs as $moduleDir) {
            $module = basename($moduleDir);
            $middlewareFile = $moduleDir . '/middleware.php';
            
            if (file_exists($middlewareFile)) {
                $config = require $middlewareFile;
                
                if (isset($config['authorization'])) {
                    $this->moduleConfig[$module] = $config['authorization'];
                }
            }
        }
    }
    
    private function extractModuleFromPath($path) {
        // Extraer módulo de rutas como /api/usuarios, /api/iso, etc.
        if (preg_match('#^/api/([^/]+)#', $path, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    private function getModuleAuthConfig($module) {
        return isset($this->moduleConfig[$module]) ? $this->moduleConfig[$module] : null;
    }
    
    private function determinePageAndPermission($method, $path, $authConfig) {
        // Primero, verificar si hay excepciones para esta ruta específica
        if (isset($authConfig['exceptions'])) {
            foreach ($authConfig['exceptions'] as $exceptionPath => $exceptionConfig) {
                if ($this->pathMatches($method . ' ' . $path, $exceptionPath)) {
                    return [
                        'page' => $exceptionConfig['page'],
                        'permission' => $exceptionConfig['permission']
                    ];
                }
            }
        }
        
        // Si no hay excepción, usar el mapeo estándar HTTP -> Permiso
        $permission = $this->httpMethodMap[$method] ?? null;
        
        if (!$permission) {
            return null;
        }
        
        return [
            'page' => $authConfig['page'],
            'permission' => $permission
        ];
    }
    
    private function pathMatches($fullPath, $pattern) {
        // Convertir patrones como "GET /api/usuarios/{id}" a regex
        $regex = '#^' . preg_replace('/\{[^}]+\}/', '[^/\s]+', $pattern) . '$#';
        return preg_match($regex, $fullPath);
    }
    
    private function hasPermission($username, $page, $permission) {
        try {
            $db = Database::getInstance();
            
            // Verificar permiso específico en la tabla acceso_paginas
            $sql = "SELECT {$permission} FROM acceso_paginas 
                    WHERE username = :username AND pagina = :page 
                    LIMIT 1";
                    
            $result = $db->fetch($sql, [
                'username' => $username,
                'page' => $page
            ]);
            
            if (!$result) {
                return false; // No tiene acceso a la página
            }
            
            // Verificar si el permiso específico está habilitado
            return isset($result[$permission]) && $result[$permission] === 'Si';
            
        } catch (Exception $e) {
            // En caso de error, denegar acceso por seguridad
            error_log("Error verificando permisos: " . $e->getMessage());
            return false;
        }
    }
}