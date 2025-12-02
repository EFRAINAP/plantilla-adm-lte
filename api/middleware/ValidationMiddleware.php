<?php
/**
 * Middleware de Validación de Datos
 * Valida y sanitiza los datos de entrada según reglas definidas
 */

namespace Api\Middleware;

use Api\Middleware\MiddlewareInterface;
use Api\Core\Request;
use Api\Core\Response;
use Api\Core\Database;
use Exception;

class ValidationMiddleware implements MiddlewareInterface {
    private $config;
    
    public function __construct() {
        $this->config = [];
        
        // Descubrir módulos dinámicamente
        $moduleDirs = glob(__DIR__ . '/../modules/*', GLOB_ONLYDIR);
        $modules = array_map('basename', $moduleDirs);
        
        foreach ($modules as $module) {
            $middlewareFile = __DIR__ . "/../modules/{$module}/middleware.php";
            if (file_exists($middlewareFile)) {
                $moduleConfig = require $middlewareFile;
                if (isset($moduleConfig['validation'])) {
                    $this->config = array_merge($this->config, $moduleConfig['validation']);
                }
            }
        }
    }
    
    public function handle(Request $request, callable $next): Response {
        $method = $request->getMethod();
        $path = $request->getPath();
        $fullPath = $method . ' ' . $path;
        
        // Obtener reglas para este endpoint
        $rules = $this->getRules($fullPath);
        
        if (empty($rules)) {
            return $next($request);
        }
        
        // Obtener datos a validar
        $data = $this->getValidationData($request);
        
        // Ejecutar validación
        $validator = new DataValidator($data, $rules);
        
        if (!$validator->passes()) {
            return new Response([
                'error' => true,
                'message' => 'Errores de validación',
                'errors' => $validator->getErrors()
            ], 422);
        }
        
        // Inyectar datos sanitizados al request
        $request->setValidatedData($validator->getSanitizedData());
        
        return $next($request);
    }
    
    private function getRules($fullPath) {
        // Buscar reglas exactas
        if (isset($this->config[$fullPath])) {
            return $this->config[$fullPath];
        }
        
        // Buscar reglas con patrones (para rutas con parámetros)
        foreach ($this->config as $pattern => $rules) {
            if ($this->matchesPattern($fullPath, $pattern)) {
                return $rules;
            }
        }
        
        return [];
    }
    
    private function matchesPattern($fullPath, $pattern) {
        $regex = '#^' . preg_replace('/\{[^}]+\}/', '[^/\s]+', $pattern) . '$#';
        return preg_match($regex, $fullPath);
    }
    
    private function getValidationData(Request $request) {
        $method = $request->getMethod();
        
        if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
            return $request->getBody();
        }
        
        if ($method === 'GET') {
            return $request->getQueryParams();
        }
        
        return [];
    }
}

/**
 * Clase de validación de datos
 */
class DataValidator {
    private $data;
    private $rules;
    private $errors = [];
    private $sanitizedData = [];
    
    public function __construct($data, $rules) {
        $this->data = $data;
        $this->rules = $rules;
    }
    
    public function passes() {
        foreach ($this->rules as $field => $ruleString) {
            $value = $this->data[$field] ?? null;
            $rules = explode('|', $ruleString);
            
            foreach ($rules as $rule) {
                if (!$this->validateRule($field, $value, $rule)) {
                    break; // Si una regla falla, no continuar
                }
            }
            
            // Sanitizar el valor después de validación
            $this->sanitizedData[$field] = $this->sanitizeValue($value, $rules);
        }
        
        return empty($this->errors);
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getSanitizedData() {
        return $this->sanitizedData;
    }
    
    private function validateRule($field, $value, $rule) {
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $ruleValue = $ruleParts[1] ?? null;
        
        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, "El campo {$field} es requerido");
                    return false;
                }
                break;
                
            case 'string':
                if (!empty($value) && !is_string($value)) {
                    $this->addError($field, "El campo {$field} debe ser una cadena de texto");
                    return false;
                }
                break;
                
            case 'integer':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($field, "El campo {$field} debe ser un número entero");
                    return false;
                }
                break;
                
            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "El campo {$field} debe ser un email válido");
                    return false;
                }
                break;
                
            case 'min':
                if (!empty($value) && strlen($value) < intval($ruleValue)) {
                    $this->addError($field, "El campo {$field} debe tener al menos {$ruleValue} caracteres");
                    return false;
                }
                break;
                
            case 'max':
                if (!empty($value) && strlen($value) > intval($ruleValue)) {
                    $this->addError($field, "El campo {$field} no puede tener más de {$ruleValue} caracteres");
                    return false;
                }
                break;
                
            case 'in':
                if (!empty($value)) {
                    $allowedValues = explode(',', $ruleValue);
                    if (!in_array($value, $allowedValues)) {
                        $this->addError($field, "El campo {$field} debe ser uno de: " . implode(', ', $allowedValues));
                        return false;
                    }
                }
                break;
                
            case 'unique':
                if (!empty($value) && !$this->validateUnique($field, $value, $ruleValue)) {
                    $this->addError($field, "El campo {$field} ya está en uso");
                    return false;
                }
                break;
        }
        
        return true;
    }
    
    private function validateUnique($field, $value, $ruleValue) {
        try {
            $parts = explode(',', $ruleValue);
            $table = $parts[0];
            $column = $parts[1] ?? $field;
            $ignoreId = $parts[2] ?? null;
            
            $db = Database::getInstance();
            
            $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = ?";
            $params = [$value];
            
            if ($ignoreId) {
                $sql .= " AND id != ?";
                $params[] = $ignoreId;
            }
            
            $stmt = $db->query($sql, $params);
            $count = $stmt->fetchColumn();
            
            return $count == 0;
            
        } catch (Exception $e) {
            // En caso de error en la consulta, permitir el valor (log del error)
            error_log("Error validando unique: " . $e->getMessage());
            return true;
        }
    }
    
    private function sanitizeValue($value, $rules) {
        if (empty($value)) {
            return $value;
        }
        
        // Limpiar espacios en blanco
        $value = trim($value);
        
        // Aplicar sanitización específica según el tipo
        if (in_array('email', $rules)) {
            $value = filter_var($value, FILTER_SANITIZE_EMAIL);
        } elseif (in_array('integer', $rules)) {
            $value = intval($value);
        } elseif (in_array('string', $rules)) {
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        
        return $value;
    }
    
    private function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
}