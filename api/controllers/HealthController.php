<?php
/**
 * HealthController - Control de salud del API
 */

namespace Api\Controllers;

use Api\Core\BaseController;
use Api\Config\Config;
use Exception;

class HealthController extends BaseController {
    
    public function check() {
        try {
            // Verificar conexión a base de datos
            $this->db->query("SELECT 1");
            $dbStatus = 'OK';
        } catch (Exception $e) {
            $dbStatus = 'ERROR: ' . $e->getMessage();
        }
        
        $this->response->success([
            'api_version' => '1.0',
            'timestamp' => date('Y-m-d H:i:s'),
            'timezone' => Config::get('APP_TIMEZONE', 'America/Lima'),
            'database' => $dbStatus,
            'php_version' => PHP_VERSION,
            'memory_usage' => $this->formatBytes(memory_get_usage()),
            'uptime' => $this->getUptime(),
            'environment' => Config::getAppEnv(),
            'debug_mode' => Config::isDebug()
        ], 'API funcionando correctamente');
    }
    
    private function formatBytes($size, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
    
    private function getUptime() {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return 'Load average: ' . implode(', ', $load);
        }
        return 'Uptime info not available';
    }
}