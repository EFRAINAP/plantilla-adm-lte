<?php
/**
 * Configuración de URLs y rutas del sistema
 * app/Config/Config.php
 */

class Config {
    /**
     * Obtener la URL base del sistema dinámicamente
     */
    public static function getBaseUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' 
            || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        
        $domainName = $_SERVER['HTTP_HOST'];
        
        // Detectar el directorio del proyecto
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = dirname(dirname($scriptName)); // Remover /public/index.php
        
        // Limpiar la ruta base
        if ($basePath === '/' || $basePath === '\\') {
            $basePath = '';
        }
        
        return $protocol . $domainName . $basePath;
    }
    
    /**
     * Obtener la URL de assets (css, js, etc.)
     */
    public static function getAssetsUrl() {
        return self::getBaseUrl() . '/public/assets';
    }
    
    /**
     * Obtener la ruta de assets AdminLTE
     */
    public static function getAdminLTEUrl() {
        return self::getBaseUrl() . '/vendor/almasaeed2010/adminlte';
    }
    
    /**
     * Generar URL completa para una ruta
     */
    public static function url($path = '') {
        $baseUrl = self::getBaseUrl();
        $path = ltrim($path, '/');
        return $baseUrl . ($path ? '/' . $path : '');
    }
    
    /**
     * Obtener la ruta actual limpia
     */
    public static function getCurrentPath() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remover el directorio base si existe
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $basePath = dirname(dirname($scriptName)); // Remover /public/index.php
        
        if ($basePath !== '/' && $basePath !== '\\' && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }
        
        // Si la ruta está vacía, es la raíz
        if (empty($path) || $path === '/') {
            $path = '/';
        }
        
        return $path;
    }
    
    /**
     * Verificar si una ruta está activa
     */
    public static function isActiveRoute($route) {
        $currentPath = self::getCurrentPath();
        $route = '/' . ltrim($route, '/');
        
        return $currentPath === $route;
    }
}
