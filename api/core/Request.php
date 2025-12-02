<?php
/**
 * Request - Manejo de peticiones HTTP
 */

namespace Api\Core;

class Request {
    private $method;
    private $uri;
    private $params;
    private $query;
    private $body;
    private $headers;
    private $user;
    private $validatedData;
    private $requestId;
    
    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->query = $_GET;
        $this->headers = $this->getAllHeaders();
        $this->body = $this->getBody();
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function getUri() {
        // Remover /api del path si existe
        return str_replace('/api', '', $this->uri);
    }
    
    public function getPath() {
        // Obtener URI original
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remover /sistema-new/api del path si existe
        $basePath = '/sistema-new/api';
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        
        // Si está vacío, establecer como raíz
        if (empty($path) || $path === '/') {
            $path = '/';
        }
        
        return $path;
    }
    
    public function getQuery($key = null, $default = null) {
        if ($key === null) {
            return $this->query;
        }
        return isset($this->query[$key]) ? $this->query[$key] : $default;
    }
    
    public function getBody() {
        if ($this->body === null) {
            $input = file_get_contents('php://input');
            
            // Intentar decodificar JSON
            if ($this->isJson()) {
                $this->body = json_decode($input, true) ?: [];
            } else {
                // Para form-data
                $this->body = $_POST;
            }
        }
        
        return $this->body;
    }
    
    public function getInput($key = null, $default = null) {
        $body = $this->getBody();
        
        if ($key === null) {
            return $body;
        }
        
        return isset($body[$key]) ? $body[$key] : $default;
    }
    
    public function getHeader($key, $default = null) {
        $key = strtolower($key);
        return isset($this->headers[$key]) ? $this->headers[$key] : $default;
    }
    
    public function isJson() {
        return strpos($this->getHeader('content-type', ''), 'application/json') !== false;
    }
    
    public function isPost() {
        return $this->method === 'POST';
    }
    
    public function isGet() {
        return $this->method === 'GET';
    }
    
    public function isPut() {
        return $this->method === 'PUT';
    }
    
    public function isDelete() {
        return $this->method === 'DELETE';
    }
    
    public function setParams($params) {
        $this->params = $params;
    }
    
    public function getParam($key, $default = null) {
        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }
    
    public function getParams() {
        return $this->params ?: [];
    }
    
    public function getQueryParams() {
        return $this->query;
    }
    
    public function setParam($key, $value) {
        if (!is_array($this->params)) {
            $this->params = [];
        }
        $this->params[$key] = $value;
    }
    
    // Métodos para middleware
    public function getUser() {
        return $this->user;
    }
    
    public function setUser($user) {
        $this->user = $user;
    }
    
    public function getValidatedData() {
        return $this->validatedData ?: [];
    }
    
    public function setValidatedData($data) {
        $this->validatedData = $data;
    }
    
    public function getRequestId() {
        return $this->requestId;
    }
    
    public function setRequestId($id) {
        $this->requestId = $id;
    }
    
    private function getAllHeaders() {
        $headers = [];
        
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            // Fallback para nginx
            foreach ($_SERVER as $key => $value) {
                if (strpos($key, 'HTTP_') === 0) {
                    $header = str_replace('_', '-', substr($key, 5));
                    $headers[$header] = $value;
                }
            }
        }
        
        // Convertir a lowercase para consistencia
        return array_change_key_case($headers, CASE_LOWER);
    }
}