<?php
/**
 * Punto de entrada principal del sistema
 * public/index.php
 */

// Configuración de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de timezone para lima
date_default_timezone_set('America/Lima');

// carga la conexión a la base de datos (que ya incluye .env)
require_once __DIR__ . '/../app/core/load.php';

// Configuración básica
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('RESOURCES_PATH', BASE_PATH . '/resources');
define('APP_PATH', BASE_PATH . '/app');
define('BASE_URL', Config::getBaseUrl());
define('ASSETS_URL', Config::getAssetsUrl());
define('ADMINLTE_URL', Config::getAdminLTEUrl());

// ==================== CARGAR CONFIGURACIÓN DE RUTAS ====================
$routeConfig = require_once BASE_PATH . '/config/routes.php';

// Extraer configuración
$publicRoutes = array_merge(
    $routeConfig['public_routes'],
    $routeConfig['auth_exceptions']  // Las excepciones de auth también son públicas
);

// ==================== SISTEMA DE AUTENTICACIÓN ESCALABLE ====================

/**
 * Función para verificar si una ruta requiere autenticación
 */
function requiresAuth($path, $routeConfig) {
    // Si está en las excepciones, no requiere auth
    if (in_array($path, $routeConfig['auth_exceptions'])) {
        return false;
    }
    
    // Si empieza con algún prefijo protegido, requiere auth
    foreach ($routeConfig['protected_prefixes'] as $prefix) {
        if (strpos($path, $prefix) === 0) {
            return true;
        }
    }
    
    return false;
}

/**
 * Función para obtener la URL de login según el prefijo
 */
function getLoginUrl($path, $routeConfig) {
    $loginMapping = $routeConfig['login_mapping'];
    
    // Buscar mapeo específico
    foreach ($loginMapping as $prefix => $loginUrl) {
        if ($prefix !== 'default' && strpos($path, $prefix) === 0) {
            return $loginUrl;
        }
    }
    
    // Usar default si no encuentra mapeo específico
    return $loginMapping['default'];
}

// Verificación inteligente y escalable
global $session;
$currentPath = Config::getCurrentPath();
$isPublicRoute = in_array($currentPath, $publicRoutes);
$needsAuth = requiresAuth($currentPath, $routeConfig);

// Solo verificar autenticación si la ruta la requiere y no es pública
if ($needsAuth && !$isPublicRoute) {
    if (!$session || !$session->isUserLoggedIn(true)) {
        $loginUrl = getLoginUrl($currentPath, $routeConfig);
        redirectTo($loginUrl, false);
    }
}

/**
 * Router mejorado para manejar las rutas
 */
class Router {
    private $routes = [];
    private $basePath = '';
    
    public function __construct() {
        // Detectar el directorio base del proyecto
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $this->basePath = dirname(dirname($scriptName)); // Remover /public/index.php
        
        if ($this->basePath === '/' || $this->basePath === '\\') {
            $this->basePath = '';
        }
    }
    
    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }
    
    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }
    
    public function delete($path, $handler) {
        $this->routes['DELETE'][$path] = $handler;
    }
    
    public function put($path, $handler) {
        $this->routes['PUT'][$path] = $handler;
    }
    
    public function resolve() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = Config::getCurrentPath();
        
        // Si la ruta está vacía, redirigir a dashboard
        // if (empty($path) || $path === '/') {
        //     $this->redirectTo('/dashboard');
        //     return;
        // }
        
        // Buscar ruta exacta
        if (isset($this->routes[$method][$path])) {
            return $this->routes[$method][$path];
        }
        
        // Buscar rutas con parámetros
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^/]+)', $route);
            $pattern = str_replace('/', '\/', $pattern);
            
            if (preg_match('/^' . $pattern . '$/', $path, $matches)) {
                // Extraer solo los parámetros nombrados
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return ['handler' => $handler, 'params' => $params];
            }
        }
        
        return null;
    }
}

/**
 * Función para renderizar vistas con layout inteligente
 */
function renderView($viewPath, $data = []) {
    // Hacer disponibles las variables globales del sistema
    global $session, $db, $msg;
    
    extract($data);
    
    $fullPath = RESOURCES_PATH . '/modules/' . $viewPath . '.php';
    
    if (file_exists($fullPath)) {
        include $fullPath;
    } else {
        render404();
    }
}

/**
 * Función para renderizar página 404 inteligente
 */
function render404() {
    global $session;
    http_response_code(404);
    $currentPath = Config::getCurrentPath();
    
    // Determinar si estamos en el sistema o en el sitio público
    $isSystemArea = (strpos($currentPath, '/sistema') === 0);
    
    if ($isSystemArea) {
        // 404 del sistema interno (con layout main.php)
        renderView('404', ['title' => 'Página no encontrada - Sistema']);
    } else {
        // 404 del sitio público (con layout público)
        renderView('landing/404', ['title' => 'Página no encontrada']);
    }
}

/*
 * Función para ruta no declarada
*/
function routeNotFound() {
    $title = "Página no encontrada - Sistema";
        ob_start();
        ?>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="error-page text-center">
                        <h2 class="headline text-warning">404</h2>
                        <div class="error-content">
                            <h3><i class="fas fa-exclamation-triangle text-warning"></i> Página no encontrada</h3>
                            <p class="lead">
                                La página del sistema que buscas no existe o ha sido movida.vcv
                            </p>
                            <div class="mt-4">
                                <a href="<?= Config::url('/sistema/dashboard') ?>" class="btn btn-primary">
                                    <i class="fas fa-home"></i> Ir al Dashboard
                                </a>
                                <a href="javascript:history.back()" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $content = ob_get_clean();
        include RESOURCES_PATH . '/layouts/main.php';
}

/**
 * Función para respuestas JSON
*/
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Función helper para generar URLs
 */
function url($path = '') {
    return Config::url($path);
}

/**
 * Función helper para assets de public
 */
function assetPublic($path) {
    return BASE_URL . '/' . ltrim($path, '/');
}

/**
 * Función helper para assets de public para directorio de imágenes
 */
function assetPublicImages($path) {
    return BASE_URL . '/public/img/' . ltrim($path, '/');
}

/**
 * Función helper para assets d
 */
function asset($path) {
    return ASSETS_URL . '/' . ltrim($path, '/');
}

/**
 * Función helper para assets de AdminLTE
 */
function adminlte($path) {
    return ADMINLTE_URL . '/' . ltrim($path, '/');
}

/**
 * Función helper para jQuery
 */
function jquery($path) {
    return BASE_URL . '/public/assets/jquery/' . ltrim($path, '/');
}

/**
 * Función helper para Bootstrap
 */
function bootstrap($path) {
    return BASE_URL . '/public/assets/bootstrap/' . ltrim($path, '/');
}

/**
 * Función helper para FontAwesome
 */
function fontawesome($path) {
    return BASE_URL . '/public/assets/font-awesome/' . ltrim($path, '/');
}

/**
 * Función para vendor
 */
function vendor($path) {
    return BASE_URL . '/vendor/' . ltrim($path, '/');
}

// Inicializar el router
$router = new Router();

// Cargar rutas desde archivo separado
require_once BASE_PATH . '/routes/web.php';

// Resolución de rutas
$result = $router->resolve();

if ($result) {
    if (is_callable($result)) {
        $result();
    } elseif (is_array($result) && isset($result['handler'])) {
        $handler = $result['handler'];
        $params = $result['params'] ?? [];
        
        if (is_callable($handler)) {
            call_user_func_array($handler, [$params]);
        }
    }
} else {
    render404();
}
