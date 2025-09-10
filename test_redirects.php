<?php
/**
 * Test de redirecciones
 * test_redirects.php
 */

// Cargar el sistema
require_once __DIR__ . '/app/core/00_load.php';

echo "<h2>Test de URLs y Redirecciones</h2>";

// Mostrar configuración actual
echo "<h3>Configuración actual:</h3>";
echo "BASE_URL: " . (defined('BASE_URL') ? BASE_URL : 'No definido') . "<br>";

// Test de las funciones de URL si existen
if (class_exists('Config')) {
    echo "Config::getBaseUrl(): " . Config::getBaseUrl() . "<br>";
    echo "Config::url('dashboard'): " . Config::url('dashboard') . "<br>";
    echo "Config::url('/dashboard'): " . Config::url('/dashboard') . "<br>";
    echo "Config::url(''): " . Config::url('') . "<br>";
}

echo "<h3>URLs que se generarían con redirect():</h3>";

// Test manual de las URLs que generaría redirect()
function testRedirectUrl($path) {
    echo "redirect('$path') generaría: ";
    
    if (!preg_match('/^https?:\/\//', $path)) {
        switch ($path) {
            case 'dashboard':
                $url = defined('BASE_URL') ? BASE_URL . '/public/dashboard' : '/public/dashboard';
                break;
            case 'login':
            case 'index.php':
            case '':
                $url = defined('BASE_URL') ? BASE_URL . '/index.php' : '/index.php';
                break;
            case 'usuarios':
                $url = defined('BASE_URL') ? BASE_URL . '/public/usuarios' : '/public/usuarios';
                break;
            default:
                if (strpos($path, '/') === 0) {
                    $url = defined('BASE_URL') ? BASE_URL . $path : $path;
                } else {
                    $url = defined('BASE_URL') ? BASE_URL . '/' . $path : '/' . $path;
                }
                break;
        }
    } else {
        $url = $path;
    }
    
    echo $url . "<br>";
}

testRedirectUrl('dashboard');
testRedirectUrl('index.php');
testRedirectUrl('usuarios');
testRedirectUrl('/dashboard');
testRedirectUrl('/usuarios');

echo "<h3>Test de acceso:</h3>";
echo "<a href='" . (defined('BASE_URL') ? BASE_URL : '') . "/public/dashboard'>Ir al Dashboard</a><br>";
echo "<a href='" . (defined('BASE_URL') ? BASE_URL : '') . "/index.php'>Ir al Login</a><br>";

?>
