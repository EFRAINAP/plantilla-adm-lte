<?php
/**
 * Sanitizer - Funciones de sanitización y validación
 */

namespace Api\Utils;

use Exception;
use DateTime;

class Sanitizer {
    
    public static function cleanString($input, $maxLength = null) {
        $cleaned = trim(strip_tags($input));
        $cleaned = htmlspecialchars($cleaned, ENT_QUOTES, 'UTF-8');
        
        if ($maxLength && strlen($cleaned) > $maxLength) {
            $cleaned = substr($cleaned, 0, $maxLength);
        }
        
        return $cleaned;
    }
    
    public static function cleanArray($array) {
        $cleaned = [];
        
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $cleaned[$key] = self::cleanArray($value);
            } elseif (is_string($value)) {
                $cleaned[$key] = self::cleanString($value);
            } else {
                $cleaned[$key] = $value;
            }
        }
        
        return $cleaned;
    }
    
    public static function validateEmail($email) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false ? $email : false;
    }
    
    public static function validateUrl($url) {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return filter_var($url, FILTER_VALIDATE_URL) !== false ? $url : false;
    }
    
    public static function validateInt($int, $min = null, $max = null) {
        $options = [];
        
        if ($min !== null || $max !== null) {
            $options['options'] = [];
            if ($min !== null) $options['options']['min_range'] = $min;
            if ($max !== null) $options['options']['max_range'] = $max;
        }
        
        return filter_var($int, FILTER_VALIDATE_INT, $options);
    }
    
    public static function validateFloat($float, $min = null, $max = null) {
        $options = [];
        
        if ($min !== null || $max !== null) {
            $options['options'] = [];
            if ($min !== null) $options['options']['min_range'] = $min;
            if ($max !== null) $options['options']['max_range'] = $max;
        }
        
        return filter_var($float, FILTER_VALIDATE_FLOAT, $options);
    }
    
    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    public static function sanitizeFilename($filename) {
        // Remover caracteres peligrosos
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        // Prevenir archivos ocultos
        $filename = ltrim($filename, '.');
        
        // Limitar longitud
        if (strlen($filename) > 255) {
            $filename = substr($filename, 0, 255);
        }
        
        return $filename;
    }
    
    public static function validateRequired($data, $requiredFields) {
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[$field] = "El campo {$field} es requerido";
            }
        }
        
        return $errors;
    }
    
    public static function validateLength($value, $min = 0, $max = null) {
        $length = strlen($value);
        
        if ($length < $min) {
            return "Debe tener al menos {$min} caracteres";
        }
        
        if ($max && $length > $max) {
            return "No puede tener más de {$max} caracteres";
        }
        
        return true;
    }
    
    public static function preventSQLInjection($input) {
        // Remover caracteres peligrosos para SQL
        $dangerous = ['--', ';', '/*', '*/', 'xp_', 'sp_', 'EXEC', 'EXECUTE', 'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'CREATE', 'ALTER'];
        
        foreach ($dangerous as $pattern) {
            $input = str_ireplace($pattern, '', $input);
        }
        
        return $input;
    }
    
    public static function preventXSS($input) {
        // Convertir caracteres especiales a entidades HTML
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}