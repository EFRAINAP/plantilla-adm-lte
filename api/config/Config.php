<?php
/**
 * Configuración del Backend API - Carga desde .env
 */

namespace Api\Config;
use Exception;

class Config {
    private static $config = [];
    
    public static function init() {
        self::loadEnv();
        date_default_timezone_set(self::get('APP_TIMEZONE', 'America/Lima'));
        
        // Crear directorios si no existen
        $uploadPath = self::getUploadPath();
        $logPath = self::getLogPath();
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        if (!is_dir($logPath)) {
            mkdir($logPath, 0755, true);
        }
    }
    
    private static function loadEnv() {
        $envPath = dirname(__DIR__, 2) . '/.env';
        
        if (!file_exists($envPath)) {
            throw new Exception('.env file not found');
        }
        
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue; // Skip comments
            }
            
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, '"\'');
            
            self::$config[$key] = $value;
        }
    }
    
    public static function get($key, $default = null) {
        return isset(self::$config[$key]) ? self::$config[$key] : $default;
    }
    
    // Database getters
    public static function getDbHost() {
        return self::get('DB_HOST', 'localhost');
    }
    
    public static function getDbName() {
        return self::get('DB_DATABASE', 'iso');
    }
    
    public static function getDbUser() {
        return self::get('DB_USERNAME', 'root');
    }
    
    public static function getDbPass() {
        return self::get('DB_PASSWORD', '');
    }
    
    public static function getDbCharset() {
        return 'utf8mb4';
    }
    
    // App getters
    public static function getAppName() {
        return self::get('APP_NAME', 'Sistema T&M');
    }
    
    public static function getAppEnv() {
        return self::get('APP_ENV', 'development');
    }
    
    public static function isDebug() {
        return self::get('APP_DEBUG', 'true') === 'true';
    }
    
    public static function getAppUrl() {
        return self::get('APP_URL', 'http://localhost');
    }
    
    // Paths
    public static function getUploadPath() {
        return dirname(__DIR__, 2) . '/uploads/';
    }
    
    public static function getLogPath() {
        return dirname(__DIR__, 2) . '/logs/';
    }
    
    // JWT
    public static function getJwtSecret() {
        return self::get('JWT_SECRET', self::get('APP_KEY', 'default-secret-key'));
    }
    
    public static function getJwtExpire() {
        return (int)self::get('JWT_EXPIRE', 7200); // 2 horas por defecto
    }
    
    public static function getJwtRefreshThreshold() {
        return (int)self::get('JWT_REFRESH_THRESHOLD', 3600); // 1 hora por defecto
    }
    
    // CORS
    public static function getCorsAllowedOrigins() {
        return self::get('CORS_ALLOWED_ORIGINS', '*');
    }
    
    public static function getCorsMaxAge() {
        return (int)self::get('CORS_MAX_AGE', 3600);
    }
    
    // Rate Limiting
    public static function getRateLimitRequests() {
        return (int)self::get('RATE_LIMIT_REQUESTS', 100);
    }
    
    public static function getRateLimitWindow() {
        return (int)self::get('RATE_LIMIT_WINDOW', 3600);
    }
}