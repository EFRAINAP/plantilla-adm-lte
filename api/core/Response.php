<?php
/**
 * Response - Manejo de respuestas HTTP
 */

namespace Api\Core;
use Api\Config\Config;

class Response {
    private $statusCode = 200;
    private $headers = [];
    private $body = '';
    
    public function __construct($data = null, $statusCode = 200, $headers = []) {
        $this->statusCode = $statusCode;
        
        // Headers por defecto
        $this->setHeader('Content-Type', 'application/json');
        
        // No aplicar CORS automáticamente (lo hace el middleware)
        // $this->setCorsHeaders();
        
        // Aplicar headers adicionales
        if (!empty($headers)) {
            $this->setHeaders($headers);
        }
        
        // Si hay datos, configurar body
        if ($data !== null) {
            if (is_array($data) || is_object($data)) {
                $this->body = json_encode($data, JSON_UNESCAPED_UNICODE);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->body = '{"error": true, "message": "JSON encoding error"}';
                }
            } else {
                $this->body = $data;
            }
        }
    }
    
    public function setStatusCode($code) {
        $this->statusCode = $code;
        return $this;
    }
    
    public function setHeader($key, $value) {
        $this->headers[$key] = $value;
        return $this;
    }
    
    public function setHeaders($headers) {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }
    
    private function setCorsHeaders() {
        $this->headers['Access-Control-Allow-Origin'] = '*';
        $this->headers['Access-Control-Allow-Methods'] = 'GET, POST, PUT, DELETE, OPTIONS';
        $this->headers['Access-Control-Allow-Headers'] = 'Content-Type, Authorization, X-Requested-With';
        return $this;
    }
    
    public function json($data, $statusCode = 200) {
        $this->setStatusCode($statusCode);
        $this->body = json_encode($data, JSON_UNESCAPED_UNICODE);
        return $this->send();
    }
    
    public function success($data = [], $message = 'Operación exitosa', $statusCode = 200) {
        return $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ], $statusCode);
    }
    
    public function error($message = 'Error', $statusCode = 400, $details = null) {
        $response = [
            'error' => true,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if ($details && Config::isDebug()) {
            $response['details'] = $details;
        }
        
        return $this->json($response, $statusCode);
    }
    
    public function notFound($message = 'Recurso no encontrado') {
        return $this->error($message, 404);
    }
    
    public function unauthorized($message = 'No autorizado') {
        return $this->error($message, 401);
    }
    
    public function forbidden($message = 'Acceso prohibido') {
        return $this->error($message, 403);
    }
    
    public function validationError($errors, $message = 'Error de validación') {
        return $this->json([
            'error' => true,
            'message' => $message,
            'validation_errors' => $errors,
            'timestamp' => date('Y-m-d H:i:s')
        ], 422);
    }
    
    public function send() {
        // Enviar código de estado
        http_response_code($this->statusCode);
        
        // Enviar headers
        foreach ($this->headers as $key => $value) {
            header($key . ': ' . $value);
        }
        
        // Enviar body
        echo $this->body;
        exit;
    }
    
    public function redirect($url, $statusCode = 302) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Location', $url);
        return $this->send();
    }
    
    // Métodos para middleware
    public function getStatusCode() {
        return $this->statusCode;
    }
    
    public function getBody() {
        return $this->body;
    }
    
    public function getHeaders() {
        return $this->headers;
    }
    
    public function addHeader($key, $value) {
        $this->headers[$key] = $value;
        return $this;
    }
    
    public function addHeaders($headers) {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }
    
    public function withoutSending() {
        // Para middleware que necesita procesar la respuesta sin enviarla
        return $this;
    }
}