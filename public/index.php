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

// Autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Cargar configuración del sistema
require_once __DIR__ . '/../app/Config/Config.php';

// carga la conexión a la base de datos
require_once __DIR__ . '/../app/core/00_load.php';

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Iniciar sesión
//session_start();

// Configuración básica
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('RESOURCES_PATH', BASE_PATH . '/resources');
define('APP_PATH', BASE_PATH . '/app');
define('BASE_URL', Config::getBaseUrl());
define('ASSETS_URL', Config::getAssetsUrl());
define('ADMINLTE_URL', Config::getAdminLTEUrl());

// Verificación global de sesión para todas las rutas protegidas
global $session;
if (!$session || !$session->isUserLoggedIn(true)) { 
    // Solo redirigir si no estamos ya en el login
    $currentPath = Config::getCurrentPath();
    if ($currentPath !== '/' && $currentPath !== '') {
        redirect('', false);
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
        if (empty($path) || $path === '/') {
            $this->redirect('/dashboard');
            return;
        }
        
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
    
    public function redirect($path, $statusCode = 302) {
        $url = Config::url($path);
        http_response_code($statusCode);
        header("Location: $url");
        exit;
    }
}

/**
 * Función para renderizar vistas
 */
function renderView($viewPath, $data = []) {
    // Hacer disponibles las variables globales del sistema
    global $session, $db, $msg;
    
    extract($data);
    
    $fullPath = RESOURCES_PATH . '/modules/' . $viewPath . '.php';
    
    if (file_exists($fullPath)) {
        include $fullPath;
    } else {
        http_response_code(404);
        $title = "Página no encontrada";
        ob_start();
        ?>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="error-page text-center">
                        <h2 class="headline text-warning">404</h2>
                        <div class="error-content">
                            <h3><i class="fas fa-exclamation-triangle text-warning"></i> ¡Oops! Página no encontrada.</h3>
                            <p>
                                No pudimos encontrar la página que estás buscando.
                                Mientras tanto, puedes <a href="<?= Config::url('dashboard') ?>">regresar al dashboard</a> o probar usando el menú de navegación.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $content = ob_get_clean();
        include RESOURCES_PATH . '/layouts/main.php';
    }
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
    return BASE_URL . '/vendor/components/jquery/' . ltrim($path, '/');
}

/**
 * Función helper para Bootstrap
 */
function bootstrap($path) {
    return BASE_URL . '/vendor/twbs/bootstrap/dist/' . ltrim($path, '/');
}

/**
 * Función helper para FontAwesome
 */
function fontawesome($path) {
    return BASE_URL . '/vendor/fortawesome/font-awesome/' . ltrim($path, '/');
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
require_once __DIR__ . '/../routes/web.php';

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
    // Ruta no encontrada
    renderView('404');
}
