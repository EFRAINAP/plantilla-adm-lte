<?php
/**
 * Middleware de Logging
 * Registra todas las peticiones y respuestas del API
 */

namespace Api\Middleware;

use Api\Middleware\MiddlewareInterface;
use Api\Core\Request;
use Api\Core\Response;
use Api\Config\Config;
use Exception;

class LoggingMiddleware implements MiddlewareInterface {
    private $logPath;
    
    public function __construct() {
        $this->logPath = Config::getLogPath() . 'api.log';
    }
    
    public function handle(Request $request, callable $next): Response {
        $startTime = microtime(true);
        $startMemory = memory_get_peak_usage();
        
        // Generar ID único para la petición
        $requestId = uniqid('req_', true);
        $request->setRequestId($requestId);
        
        // Log de entrada
        $this->logRequest($request, $requestId);
        
        // Procesar petición
        $response = $next($request);
        
        // Calcular métricas
        $duration = (microtime(true) - $startTime) * 1000; // en ms
        $memoryUsed = memory_get_peak_usage() - $startMemory;
        
        // Log de salida con métricas
        $this->logResponse($request, $response, $requestId, [
            'duration_ms' => round($duration, 2),
            'memory_used' => $memoryUsed,
            'memory_peak' => memory_get_peak_usage()
        ]);
        
        // Añadir ID de petición a la respuesta
        $response->addHeader('X-Request-ID', $requestId);
        
        return $response;
    }
    
    private function logRequest(Request $request, $requestId) {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'request_id' => $requestId,
            'type' => 'request',
            'method' => $request->getMethod(),
            'path' => $request->getPath(),
            'query' => $request->getQueryParams(),
            'ip' => $this->getClientIp($request),
            'user_agent' => $request->getHeader('User-Agent'),
            'content_type' => $request->getHeader('Content-Type'),
            'content_length' => $request->getHeader('Content-Length') ?: 0
        ];
        
        // Añadir usuario si está autenticado
        if ($user = $request->getUser()) {
            $logData['user_id'] = $user['id'];
            $logData['username'] = $user['username'];
        }
        
        $this->writeLog($logData);
    }
    
    private function logResponse(Request $request, Response $response, $requestId, $metrics) {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'request_id' => $requestId,
            'type' => 'response',
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $metrics['duration_ms'],
            'memory_used' => $metrics['memory_used'],
            'memory_peak' => $metrics['memory_peak'],
            'response_size' => strlen($response->getBody() ?: '')
        ];
        
        // Log de errores con más detalle
        if ($response->getStatusCode() >= 400) {
            $logData['error'] = true;
            $responseBody = json_decode($response->getBody(), true);
            if ($responseBody && isset($responseBody['message'])) {
                $logData['error_message'] = $responseBody['message'];
            }
        }
        
        $this->writeLog($logData);
    }
    
    private function writeLog($data) {
        $logLine = json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL;
        file_put_contents($this->logPath, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    private function getClientIp(Request $request) {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
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
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}