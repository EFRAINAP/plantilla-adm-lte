<?php
/**
 * Middleware de Rate Limiting
 * Previene ataques DoS limitando peticiones por IP/usuario
 */
namespace Api\Middleware;
use Api\Core\Request;
use Api\Core\Response;
use Api\Config\Config;

class RateLimitMiddleware implements MiddlewareInterface {
    private $config;
    private $storage = [];
    
    public function __construct() {
        // Configuración global desde .env
        $this->config = [
            'global' => [
                'requests' => (int)Config::get('RATE_LIMIT_REQUESTS', 100),
                'per' => (int)Config::get('RATE_LIMIT_WINDOW', 3600),
                'storage' => 'memory'
            ],
            'endpoints' => []
        ];
        
        // Descubrir módulos dinámicamente
        $moduleDirs = glob(__DIR__ . '/../modules/*', GLOB_ONLYDIR);
        $modules = array_map('basename', $moduleDirs);
        
        foreach ($modules as $module) {
            $middlewareFile = __DIR__ . "/../modules/{$module}/middleware.php";
            if (file_exists($middlewareFile)) {
                $moduleConfig = require $middlewareFile;
                if (isset($moduleConfig['ratelimit'])) {
                    $this->config['endpoints'] = array_merge(
                        $this->config['endpoints'], 
                        $moduleConfig['ratelimit']
                    );
                }
            }
        }
    }
    
    public function handle(Request $request, callable $next): Response {
        $path = $request->getPath();
        $method = $request->getMethod();
        $fullPath = $method . ' ' . $path;
        
        // Obtener configuración específica del endpoint
        $endpointConfig = $this->getEndpointConfig($fullPath);
        
        // Generar key única para el limite
        $key = $this->generateKey($request, $endpointConfig['key']);
        
        // Verificar limite actual
        $current = $this->getCurrentCount($key);
        
        if ($current >= $endpointConfig['requests']) {
            return new Response([
                'error' => true,
                'message' => 'Demasiadas peticiones. Intente más tarde.',
                'retry_after' => $endpointConfig['per']
            ], 429, [
                'Retry-After' => $endpointConfig['per'],
                'X-RateLimit-Limit' => $endpointConfig['requests'],
                'X-RateLimit-Remaining' => 0
            ]);
        }
        
        // Incrementar contador
        $this->incrementCount($key, $endpointConfig['per']);
        
        // Continuar con la petición
        $response = $next($request);
        
        // Añadir headers informativos
        $response->addHeaders([
            'X-RateLimit-Limit' => $endpointConfig['requests'],
            'X-RateLimit-Remaining' => $endpointConfig['requests'] - $current - 1,
            'X-RateLimit-Reset' => time() + $endpointConfig['per']
        ]);
        
        return $response;
    }
    
    private function getEndpointConfig($fullPath) {
        // Buscar configuración específica del endpoint
        foreach ($this->config['endpoints'] as $pattern => $config) {
            if ($this->matchesPattern($fullPath, $pattern)) {
                return array_merge($this->config['global'], $config);
            }
        }
        
        // Usar configuración global por defecto
        return array_merge($this->config['global'], ['key' => 'ip']);
    }
    
    private function matchesPattern($fullPath, $pattern) {
        // Convertir pattern a regex
        $regex = '#^' . preg_replace('/\{[^}]+\}/', '[^/]+', $pattern) . '$#';
        return preg_match($regex, $fullPath);
    }
    
    private function generateKey(Request $request, $keyType) {
        $baseKey = 'ratelimit:';
        
        switch ($keyType) {
            case 'ip':
                return $baseKey . 'ip:' . $this->getClientIp($request);
                
            case 'user':
                $user = $request->getUser();
                if ($user) {
                    return $baseKey . 'user:' . $user['id'];
                }
                // Fallback a IP si no hay usuario
                return $baseKey . 'ip:' . $this->getClientIp($request);
                
            case 'api_key':
                $apiKey = $request->getHeader('X-API-Key');
                if ($apiKey) {
                    return $baseKey . 'api:' . hash('sha256', $apiKey);
                }
                // Fallback a IP
                return $baseKey . 'ip:' . $this->getClientIp($request);
                
            default:
                return $baseKey . 'ip:' . $this->getClientIp($request);
        }
    }
    
    private function getClientIp(Request $request) {
        // Verificar headers de proxy
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                // Si es una lista de IPs, tomar la primera
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                // Validar que sea una IP válida
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    private function getCurrentCount($key) {
        return $this->storage[$key]['count'] ?? 0;
    }
    
    private function incrementCount($key, $ttl) {
        if (!isset($this->storage[$key])) {
            $this->storage[$key] = [
                'count' => 0,
                'reset_time' => time() + $ttl
            ];
        }
        
        // Verificar si el periodo ha expirado
        if (time() >= $this->storage[$key]['reset_time']) {
            $this->storage[$key] = [
                'count' => 0,
                'reset_time' => time() + $ttl
            ];
        }
        
        $this->storage[$key]['count']++;
        
        // Limpiar entradas expiradas (para evitar memory leaks)
        $this->cleanupExpired();
    }
    
    private function cleanupExpired() {
        $currentTime = time();
        foreach ($this->storage as $key => $data) {
            if ($currentTime >= $data['reset_time']) {
                unset($this->storage[$key]);
            }
        }
    }
}