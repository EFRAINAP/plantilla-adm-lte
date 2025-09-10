<?php
// Usar __DIR__ para obtener rutas absolutas del sistema de archivos
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 3)); // Ajusta el nivel según la ubicación de tu archivo principal
}
require_once BASE_PATH . '/app/core/00_load.php';

// Verificar que el usuario esté autenticado
$user = current_user();
if (!$session->isUserLoggedIn(true)) {
    http_response_code(401);
    exit('Acceso no autorizado');
}

// Verificar parámetros requeridos
if (!isset($_GET['id']) || !isset($_GET['tipo']) || !isset($_GET['categoria'])) {
    http_response_code(400);
    exit('Parámetros insuficientes');
}

$id = $_GET['id'];
$tipo = $_GET['tipo']; // 'documento' o 'esquema'
$categoria = $_GET['categoria']; // 'wps', 'pqr', 'of', etc.

// Validar valores permitidos
$tipos_permitidos = ['documento', 'esquema'];
$categorias_permitidas = ['wps', 'pqr', 'of', 'iso', 'biblioteca', 'rgc', 'wpq', 'odv', 'mof', 'cap2'];

if (!in_array($tipo, $tipos_permitidos) || !in_array($categoria, $categorias_permitidas)) {
    http_response_code(400);
    exit('Parámetros inválidos');
}

// Verificar permisos específicos según la categoría
$pages_permisos = [
    'wps' => '02_AdmWPS.php',
    'pqr' => '02_AdmPQR.php',
    'of' => '02_AdmOF.php',
    'iso' => '01_ListadoDocumentos.php',
    'biblioteca' => '01_ListadoBiblioteca.php',
    'rgc' => '02_AdmRGC.php',
    'wpq' => '02_AdmWPQ.php',
    'odv' => '02_AdmODV.php',
    'mof' => '01_ManualFunciones.php',
    'cap2' => '01_AdministrarDosier.php'
];

if (!has_access($pages_permisos[$categoria])) {
    http_response_code(403);
    exit('Sin permisos para acceder a este archivo');
}

// Obtener la ruta del archivo según la categoría
try {
    // Usar __DIR__ para obtener rutas absolutas del sistema de archivos
    $proyecto_raiz = BASE_PATH;

    switch ($categoria) {
        case 'wps':
            $carpeta_principal = find_table_field_only('constantes', 'nombre_constante', 'carpeta_wps');
            $registro = find_table_field_only('wps', 'id', (string)$id);
            
            if ($tipo === 'esquema') {
                $carpeta_esquema = find_table_field_only('constantes', 'nombre_constante', 'carpeta_esquema');
                $archivo_ruta = $proyecto_raiz . DIRECTORY_SEPARATOR . $carpeta_principal['valor_constante'] . $carpeta_esquema['valor_constante'] . $registro['esquema'];
                $nombre_archivo = $registro['esquema'];
            } else {
                $archivo_ruta = $proyecto_raiz . DIRECTORY_SEPARATOR . $carpeta_principal['valor_constante'] . $registro['nombre_archivo'];
                $nombre_archivo = $registro['nombre_archivo'];
            }
            break;
            
        case 'pqr':
            $carpeta_principal = find_table_field_only('constantes', 'nombre_constante', 'carpeta_pqr');
            $registro = find_table_field_only('pqr', 'id', (string)$id);
            
            if ($tipo === 'esquema') {
                $carpeta_esquema = find_table_field_only('constantes', 'nombre_constante', 'carpeta_esquema');
                $archivo_ruta = $proyecto_raiz . DIRECTORY_SEPARATOR . $carpeta_principal['valor_constante'] . $carpeta_esquema['valor_constante'] . $registro['esquema'];
                $nombre_archivo = $registro['esquema'];
            } else {
                $archivo_ruta = $proyecto_raiz . DIRECTORY_SEPARATOR . $carpeta_principal['valor_constante'] . $registro['nombre_archivo'];
                $nombre_archivo = $registro['nombre_archivo'];
            }
            break;
            
        case 'of':
            $carpeta_principal = find_table_field_only('constantes', 'nombre_constante', 'carpeta_of');
            $registro = find_table_field_only('ordenes_fabricacion', 'id', (string)$id);
            
            if ($tipo === 'esquema') {
                $carpeta_esquema = find_table_field_only('constantes', 'nombre_constante', 'carpeta_esquema');
                $archivo_ruta = $proyecto_raiz . DIRECTORY_SEPARATOR . $carpeta_principal['valor_constante'] . $carpeta_esquema['valor_constante'] . $registro['esquema'];
                $nombre_archivo = $registro['esquema'];
            } else {
                $archivo_ruta = $proyecto_raiz . DIRECTORY_SEPARATOR . $carpeta_principal['valor_constante'] . $registro['nombre_archivo'];
                $nombre_archivo = $registro['nombre_archivo'];
            }
            break;
            
        case 'iso':
            $carpeta_principal = find_table_field_only('constantes', 'nombre_constante', 'carpeta_iso');
            $registro = find_table_field_only('documentos', 'cod_documento', (string)$id);
            
            // ISO solo maneja documentos, no esquemas
            $archivo_ruta = $proyecto_raiz . DIRECTORY_SEPARATOR . $carpeta_principal['valor_constante'] . $registro['nombre_archivo'];
            $nombre_archivo = $registro['nombre_archivo'];
            break;
            
        case 'biblioteca':
            $carpeta_principal = find_table_field_only('constantes', 'nombre_constante', 'carpeta_biblioteca');
            $registro = find_table_field_only('biblioteca', 'cod_documento', (string)$id);
            
            // Biblioteca solo maneja documentos, no esquemas
            $archivo_ruta = $proyecto_raiz . DIRECTORY_SEPARATOR . $carpeta_principal['valor_constante'] . $registro['nombre_archivo'];
            $nombre_archivo = $registro['nombre_archivo'];
            break;
        case 'rgc':
            $carpeta_principal = find_table_field_only('constantes', 'nombre_constante', 'carpeta_rgc');
            $registro = find_table_field_only('rgc', 'id', (string)$id);

            // RGC solo maneja documentos, no esquemas
            $archivo_ruta = $proyecto_raiz . DIRECTORY_SEPARATOR . $carpeta_principal['valor_constante'] . $registro['nombre_archivo'];
            $nombre_archivo = $registro['nombre_archivo'];
            break;

        case 'wpq':
            $carpeta_principal = find_table_field_only('constantes', 'nombre_constante', 'carpeta_wpq');
            $registro = find_table_field_only('wpq', 'id', (string)$id);

            // WPQ solo maneja documentos, no esquemas
            $archivo_ruta = $proyecto_raiz . DIRECTORY_SEPARATOR . $carpeta_principal['valor_constante'] . $registro['nombre_archivo'];
            $nombre_archivo = $registro['nombre_archivo'];
            break;

        case 'odv':
            $carpeta_principal = find_table_field_only('constantes', 'nombre_constante', 'carpeta_odv');
            $registro = find_table_field_only('odv', 'id', (string)$id);
            // ODV solo maneja documentos, no esquemas
            $archivo_ruta = $proyecto_raiz . DIRECTORY_SEPARATOR . $carpeta_principal['valor_constante'] . $registro['nombre_archivo'];
            $nombre_archivo = $registro['nombre_archivo'];
            break;

        case 'mof':
            $carpeta_principal = find_table_field_only('constantes', 'nombre_constante', 'carpeta_puestos');
            $registro = find_table_field_only('puestos', 'cod_puesto', (string)$id);
            // MOF solo maneja documentos, no esquemas
            $archivo_ruta = $proyecto_raiz . DIRECTORY_SEPARATOR . $carpeta_principal['valor_constante'] . $registro['nombre_archivo'];
            $nombre_archivo = $registro['nombre_archivo'];
            break;

        case 'cap2':
            $carpeta_principal = find_table_field_only('constantes', 'nombre_constante', 'carpeta_cap2');
            $registro = find_table_field_only('cap2_registros', 'id', (string)$id);
            // CAP2 solo maneja documentos, no esquemas
            $archivo_ruta = $proyecto_raiz . DIRECTORY_SEPARATOR . $carpeta_principal['valor_constante'] . $registro['nombre_archivo'];
            $nombre_archivo = $registro['nombre_archivo'];
            break;

        default:
            http_response_code(400);
            exit('Categoría no válida');
    }
    
    // Normalizar las barras de directorio
    $archivo_ruta = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $archivo_ruta);
    
    // Verificar que el archivo existe
    if (!$registro || !file_exists($archivo_ruta)) {
        http_response_code(404);
        exit('Archivo no encontrado');
    }
    
    // Verificar que es un archivo PDF (seguridad adicional)
    $info_archivo = pathinfo($archivo_ruta);
    if (strtolower($info_archivo['extension']) !== 'pdf') {
        http_response_code(400);
        exit('Tipo de archivo no permitido');
    }
    
    // Servir el archivo con headers seguros
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($nombre_archivo) . '"');
    header('Content-Length: ' . filesize($archivo_ruta));
    header('Cache-Control: private, max-age=0, no-cache');
    header('Pragma: no-cache');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    
    // Leer y enviar el archivo
    readfile($archivo_ruta);
    exit;
    
} catch (Exception $e) {
    http_response_code(500);
    exit('Error interno del servidor');
}
?>
