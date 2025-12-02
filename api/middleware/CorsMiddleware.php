<?php
/**
 * Middleware CORS 
 * Maneja Cross-Origin Resource Sharing de forma segura
 */

namespace Api\Middleware;

use Api\Middleware\MiddlewareInterface;
use Api\Core\Request;
use Api\Core\Response;
use Api\Config\Config;

class CorsMiddleware implements MiddlewareInterface {
    private $config;
    
    public function __construct() {
        $middlewareConfig = require __DIR__ . '/../config/middleware.php';
        
        // Configuración de CORS desde .env
        $allowedOrigins = Config::get('CORS_ALLOWED_ORIGINS', '*');
        $originsArray = $allowedOrigins === '*' ? ['*'] : explode(',', $allowedOrigins);
        
        $this->config = array_merge($middlewareConfig['cors'], [
            'allowed_origins' => $originsArray,
            'max_age' => (int)Config::get('CORS_MAX_AGE', 3600)
        ]);
    }
    
    public function handle(Request $request, callable $next): Response {
        $origin = $request->getHeader('Origin');
        
        // Headers CORS por defecto
        $corsHeaders = [];
        
        // Validar origen si está especificado
        if ($origin && $this->isAllowedOrigin($origin)) {
            $corsHeaders['Access-Control-Allow-Origin'] = $origin;
            
            if ($this->config['supports_credentials']) {
                $corsHeaders['Access-Control-Allow-Credentials'] = 'true';
            }
        } elseif (empty($this->config['allowed_origins']) || in_array('*', $this->config['allowed_origins'])) {
            $corsHeaders['Access-Control-Allow-Origin'] = '*';
        }
        
        // Manejar preflight requests (OPTIONS)
        if ($request->getMethod() === 'OPTIONS') {
            $corsHeaders = array_merge($corsHeaders, [
                'Access-Control-Allow-Methods' => implode(', ', $this->config['allowed_methods']),
                'Access-Control-Allow-Headers' => implode(', ', $this->config['allowed_headers']),
                'Access-Control-Max-Age' => $this->config['max_age']
            ]);
            
            // Crear respuesta JSON vacía con headers CORS
            return new Response([
                'success' => true,
                'message' => 'CORS preflight successful',
                'timestamp' => date('Y-m-d H:i:s')
            ], 200, $corsHeaders);
        }
        
        // Para peticiones normales, continuar y aplicar headers en la respuesta
        $response = $next($request);
        
        // Si la respuesta es un objeto Response, agregar headers CORS
        if ($response instanceof Response) {
            foreach ($corsHeaders as $header => $value) {
                $response->addHeader($header, $value);
            }
            
            // Headers expuestos
            if (!empty($this->config['exposed_headers'])) {
                $response->addHeader('Access-Control-Expose-Headers', implode(', ', $this->config['exposed_headers']));
            }
        }
        
        return $response;
    }
    
    private function isAllowedOrigin($origin) {
        return in_array($origin, $this->config['allowed_origins']);
    }
}