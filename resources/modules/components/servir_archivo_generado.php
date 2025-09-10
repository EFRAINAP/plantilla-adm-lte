<?php
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
if (!isset($_GET['id']) || !isset($_GET['tipo']) || !isset($_GET['generador'])) {
    http_response_code(400);
    exit('Parámetros insuficientes');
}

$id = $_GET['id'];
$tipo = $_GET['tipo'];
$generador = $_GET['generador']; // 'wps', 'pqr', 'of', etc.
// Validar generador permitido
$generadores_permitidos = [
    2 => '14_GestionDosier/GenerarPDF/IndiceCaratulaPDFv3.php',
    3 => '14_GestionDosier/GenerarPDF/Dosier-FLSmidth.php'
];

if (!isset($generadores_permitidos[$tipo])) {
    http_response_code(400);
    exit('Generador no válido');
}

// Verificar permisos específicos según el generador
$pages_permisos = [
    'dosier' => '01_AdministrarDosier.php',
    'matriz_wps' => '01_AdministrarDosier.php',
    'matriz_pqr' => '01_AdministrarDosier.php',
    'matriz_wpq' => '01_AdministrarDosier.php',
    'matriz_rgc' => '01_AdministrarDosier.php',
];

// Verificar permisos específicos según el generador
if (!has_access($pages_permisos[$generador])) {
    http_response_code(403);
    exit('Sin permisos para acceder a este archivo');
}

// Obtener la ruta del archivo según la categoría
try {
    // Limpiar cualquier output previo
    if (ob_get_level()) {
        ob_end_clean();
    }

    // Capturar la salida del generador de PDF
    ob_start();

    // Guardar variables GET originales
    $original_get = $_GET;

    // Configurar parámetros para el generador
    $_GET = [
        'id' => $id,
        'tipo' => $tipo
    ];
    
    // Cambiar directorio temporalmente
    $old_cwd = getcwd();
    chdir(__DIR__ . '/../');
    
    // Incluir el generador
    $generator_path = $generadores_permitidos[$tipo];
    if (!file_exists($generator_path)) {
        throw new Exception('Archivo generador no encontrado');
    }

    include($generator_path);
    
    // Restaurar directorio
    chdir($old_cwd);

    // Restaurar variables GET
    $_GET = $original_get;
    
    // Obtener el contenido del PDF
    $pdf_content = ob_get_clean();
    
    // Verificar que se generó contenido válido
    if (empty($pdf_content) || strlen($pdf_content) < 100) {
        throw new Exception('PDF generado está vacío o corrupto');
    }
    
    // Verificar que es un PDF válido (comienza con %PDF)
    if (substr($pdf_content, 0, 4) !== '%PDF') {
        throw new Exception('El contenido generado no es un PDF válido');
    }

    // Headers optimizados para Edge
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($pdf_content));
    header('Accept-Ranges: bytes');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('X-Content-Type-Options: nosniff');
    
    // Enviar el PDF
    echo $pdf_content;
    exit;
    
} catch (Exception $e) {
    // Limpiar buffer si existe
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Log del error para debugging
    error_log("Error generando PDF: " . $e->getMessage());
    
    http_response_code(500);
    exit('Error al generar el PDF: ' . $e->getMessage());
}
?>
