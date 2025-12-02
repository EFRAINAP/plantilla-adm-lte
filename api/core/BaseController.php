<?php
/**
 * BaseController - Controlador base con funciones comunes
 */

namespace Api\Core;

use Api\Core\Database;
use Api\Core\Request;
use Api\Core\Response;
use Api\Config\Config;

abstract class BaseController {
    protected $db;
    protected $request;
    protected $response;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->response = new Response();
    }
    
    // Método para inyectar request desde el router
    public function setRequest(Request $request) {
        $this->request = $request;
    }
    
    // Mantener compatibilidad con el método anterior
    public function setRequestParams($params) {
        if (!$this->request) {
            $this->request = new Request();
        }
        
        if (is_array($params)) {
            foreach ($params as $index => $value) {
                // El primer parámetro generalmente es 'id'
                if ($index === 0) {
                    $this->request->setParam('id', $value);
                } else {
                    $this->request->setParam("param{$index}", $value);
                }
            }
        }
    }
    
    protected function jsonResponse($data, $status = 200) {
        return new Response($data, $status);
    }
    
    protected function successResponse($data = [], $message = 'Operación exitosa') {
        return $this->response->success($data, $message);
    }
    
    protected function errorResponse($message = 'Error', $status = 400, $details = null) {
        return $this->response->error($message, $status, $details);
    }
    
    protected function getJsonInput() {
        // Usar datos validados por middleware si están disponibles
        if ($this->request && !empty($this->request->getValidatedData())) {
            return $this->request->getValidatedData();
        }
        
        // Fallback al método anterior
        if ($this->request) {
            return $this->request->getBody();
        }
        
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }
    

    
    // Método para acceder al usuario autenticado
    protected function getAuthUser() {
        return $this->request ? $this->request->getUser() : null;
    }
    
    // Método para verificar si el usuario está autenticado
    protected function isAuthenticated() {
        return $this->getAuthUser() !== null;
    }
    
    protected function pagination($page = 1, $limit = 10) {
        $page = max(1, (int)$page);
        $limit = max(1, min(100, (int)$limit)); // Máximo 100 registros
        $offset = ($page - 1) * $limit;
        
        return [
            'limit' => $limit,
            'offset' => $offset,
            'page' => $page
        ];
    }
    
    protected function logError($message, $context = []) {
        $log = [
            'timestamp' => date('Y-m-d H:i:s'),
            'message' => $message,
            'context' => $context,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
        ];
        
        file_put_contents(
            Config::getLogPath() . 'api_errors_' . date('Y-m-d') . '.log', 
            json_encode($log) . "\n", 
            FILE_APPEND | LOCK_EX
        );
    }
}