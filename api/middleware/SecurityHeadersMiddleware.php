<?php
/**
 * Middleware de Headers de Seguridad
 * Aplica headers de seguridad estándar para proteger contra ataques comunes
 */
namespace Api\Middleware;
use Api\Core\Request;
use Api\Core\Response;

class SecurityHeadersMiddleware implements MiddlewareInterface {
    private $config;
    
    public function __construct() {
        $middlewareConfig = require __DIR__ . '/../config/middleware.php';
        $this->config = $middlewareConfig['security'];
    }
    
    public function handle(Request $request, callable $next): Response {
        // Continuar con la petición
        $response = $next($request);
        
        // Aplicar headers de seguridad a la respuesta
        if ($response instanceof Response) {
            foreach ($this->config['headers'] as $header => $value) {
                $response->addHeader($header, $value);
            }
            
            // Para remover headers, los configuramos como vacíos
            foreach ($this->config['remove_headers'] as $header) {
                $response->addHeader($header, '');
            }
        }
        
        return $response;
    }
    
    // Métodos removidos - ahora todo se maneja en el handle()
}

// CorsMiddleware y LoggingMiddleware ahora están en archivos separados
// Este archivo contiene solo SecurityHeadersMiddleware