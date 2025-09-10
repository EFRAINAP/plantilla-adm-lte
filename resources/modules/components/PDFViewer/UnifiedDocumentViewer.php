<?php
$page_title = 'Visualizar Documento';
require_once BASE_PATH . '/app/core/00_load.php';

$user = current_user();

// Verificar parámetros requeridos
if (!isset($_GET['categoria']) || !isset($_GET['id']) || !isset($_GET['tipo'])) {
    $session->msg("d", "Parámetros insuficientes para mostrar el documento.");
    redirect('', false);
}

$categoria = $_GET['categoria']; // 'iso', 'biblioteca', 'wps', 'pqr', 'of', etc.
$id = $_GET['id'];
$tipo = $_GET['tipo']; // 'documento' o 'esquema'

// Configuración de categorías
$categorias_config = [
    'iso' => [
        'titulo' => 'Documento ISO',
        'permiso_page' => '01_ListadoDocumentos.php',
        'redirect_page' => '../../06_DocumentosISO/01_ListadoDocumentos.php',
        'tabla' => 'documentos',
        'campo_archivo' => 'nombre_archivo',
        'campo_esquema' => '', // No aplicable para ISO
        'carpeta_constante' => 'carpeta_iso',
        'carpeta_esquema_constante' => '', // No aplicable para ISO
        'color_header' => 'bg-info',
        'icono' => 'bi-file-earmark-text'
    ],
    'biblioteca' => [
        'titulo' => 'Documento Biblioteca',
        'permiso_page' => '01_ListadoBiblioteca.php',
        'redirect_page' => '../../10_BibliotecaVirtual/01_ListadoBiblioteca.php',
        'tabla' => 'biblioteca',
        'campo_archivo' => 'nombre_archivo',
        'campo_esquema' => '', // No aplicable para biblioteca
        'carpeta_constante' => 'carpeta_biblioteca',
        'carpeta_esquema_constante' => '',
        'color_header' => 'bg-warning',
        'icono' => 'bi-book'
    ],
    'wps' => [
        'titulo' => 'Documento WPS',
        'permiso_page' => '02_AdmWPS.php',
        'redirect_page' => '../../14_GestionDosier/02_AdmWPS.php',
        'tabla' => 'wps',
        'campo_archivo' => 'nombre_archivo',
        'campo_esquema' => 'esquema',
        'carpeta_constante' => 'carpeta_wps',
        'carpeta_esquema_constante' => 'carpeta_esquema',
        'color_header' => 'bg-success',
        'icono' => 'bi-file-earmark-pdf'
    ],
    'pqr' => [
        'titulo' => 'Documento PQR',
        'permiso_page' => '02_AdmPQR.php',
        'redirect_page' => '../../14_GestionDosier/02_AdmPQR.php',
        'tabla' => 'pqr',
        'campo_archivo' => 'nombre_archivo',
        'campo_esquema' => 'esquema',
        'carpeta_constante' => 'carpeta_pqr',
        'carpeta_esquema_constante' => 'carpeta_esquema',
        'color_header' => 'bg-primary',
        'icono' => 'bi-file-earmark-pdf'
    ],
    'of' => [
        'titulo' => 'Orden de Fabricación',
        'permiso_page' => '02_AdmOF.php',
        'redirect_page' => '../../14_GestionDosier/02_AdmOF.php',
        'tabla' => 'ordenes_fabricacion',
        'campo_archivo' => 'nombre_archivo',
        'campo_esquema' => 'esquema',
        'carpeta_constante' => 'carpeta_of',
        'carpeta_esquema_constante' => 'carpeta_esquema',
        'color_header' => 'bg-danger',
        'icono' => 'bi-file-earmark-pdf'
    ],
    'odv' => [
        'titulo' => 'Orden de Venta',
        'permiso_page' => '02_AdmODV.php',
        'redirect_page' => '../../14_GestionDosier/02_AdmODV.php',
        'tabla' => 'odv',
        'campo_archivo' => 'nombre_archivo',
        'campo_esquema' => '',
        'carpeta_constante' => 'carpeta_odv',
        'carpeta_esquema_constante' => '',
        'color_header' => 'bg-secondary',
        'icono' => 'bi-file-earmark-pdf'
    ],
    'rgc' => [
        'titulo' => 'Registro de Calidad',
        'permiso_page' => '02_AdmRGC.php',
        'redirect_page' => '../../14_GestionDosier/02_AdmRGC.php',
        'tabla' => 'rgc',
        'campo_archivo' => 'nombre_archivo',
        'campo_esquema' => '',
        'carpeta_constante' => 'carpeta_rgc',
        'carpeta_esquema_constante' => '',
        'color_header' => 'bg-dark',
        'icono' => 'bi-file-earmark-pdf'
    ],
    'wpq' => [
        'titulo' => 'WPQ',
        'permiso_page' => '02_AdmWPQ.php',
        'redirect_page' => '../../14_GestionDosier/02_AdmWPQ.php',
        'tabla' => 'wpq',
        'campo_archivo' => 'nombre_archivo',
        'campo_esquema' => '',
        'carpeta_constante' => 'carpeta_wpq',
        'carpeta_esquema_constante' => '',
        'color_header' => 'bg-info',
        'icono' => 'bi-file-earmark-text'
    ],
    'mof' => [
        'titulo' => 'Manual de Funciones',
        'permiso_page' => '01_ManualFunciones.php',
        'redirect_page' => '../../11_ManualFunciones/01_ManualFunciones.php',
        'tabla' => 'puestos',
        'campo_archivo' => 'nombre_archivo',
        'campo_esquema' => '',
        'carpeta_constante' => 'carpeta_puestos',
        'carpeta_esquema_constante' => '',
        'color_header' => 'bg-secondary',
        'icono' => 'bi-journal-text'
    ],
    'cap2' => [
        'titulo' => 'Registro de Calidad',
        'permiso_page' => '01_AdministrarDosier.php',
        'redirect_page' => '../../14_GestionDosier/01_AdministrarDosier.php',
        'tabla' => 'cap2_registros',
        'campo_archivo' => 'nombre_archivo',
        'campo_esquema' => '',
        'carpeta_constante' => 'carpeta_cap2',
        'carpeta_esquema_constante' => '',
        'color_header' => 'bg-warning',
        'icono' => 'bi-file-earmark-text'
    ]
];

// Verificar que la categoría existe
if (!isset($categorias_config[$categoria])) {
    $session->msg("d", "Categoría de documento no válida.");
    redirect('iso/asignados', false);
}

$config = $categorias_config[$categoria];

// Verificar permisos
if (!has_access($config['permiso_page'])) {
    $session->msg("d", "No tienes permisos para acceder a esta página. Contacta al administrador.");
    redirect('dashboard', false);
}

// Obtener información del archivo
try {
    
    if ($categoria === 'iso' || $categoria === 'biblioteca') {
        $registro = find_table_field_only($config['tabla'], 'cod_documento', (string)$id);
        
    } elseif ($categoria === 'wps' || $categoria === 'pqr' || $categoria === 'of' || $categoria === 'rgc' || $categoria === 'wpq' || $categoria === 'odv' || $categoria === 'cap2') {
        $registro = find_table_field_only($config['tabla'], 'id', (int)$id);
    } elseif ($categoria === 'mof') {
        $registro = find_table_field_only($config['tabla'], 'cod_puesto', (string)$id);
    }

    if (!$registro) {
        $session->msg("d", "No se encontró el documento solicitado.");
        redirect($config['redirect_page'], false);
    }
    
    // Determinar el nombre del archivo según el tipo
    if ($tipo === 'esquema' && !empty($config['campo_esquema'])) {
        $nombreArchivo = $registro[$config['campo_esquema']];
        $page_title = $config['titulo'] . ' - Esquema';
    } elseif ($tipo === 'documento') {
        $nombreArchivo = $registro[$config['campo_archivo']];
        $page_title = $config['titulo'] . ' - Documento';
    } else {
        $session->msg("d", "Tipo de documento no válido.");
        redirect($config['redirect_page'], false);
    }
    
    if (empty($nombreArchivo)) {
        $session->msg("d", "El archivo solicitado no está disponible.");
        redirect($config['redirect_page'], false);
    }
    
} catch (Exception $e) {
    $session->msg("d", "Error al acceder al documento.");
    redirect($config['redirect_page'], false);
}

// Headers CORS
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
}
ob_start()
?>

<link href="<?= BASE_URL ?>/resources/modules/components/PDFViewer/pdf-viewer.css" rel="stylesheet">


<div class="row">
    <div class="col-12">
        <!-- Header con información del documento -->
        <div class="card mb-3">
            <div class="card-header <?php echo $config['color_header']; ?> text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="<?php echo $config['icono']; ?>"></i>
                            <?php echo htmlspecialchars($config['titulo'] . ': ' . $nombreArchivo); ?>
                        </h5>
                        <small>Tipo: <?php echo ucfirst($tipo); ?> | Categoría: <?php echo ucfirst($categoria); ?></small>
                    </div>
                    <div>
                        <a href="<?php echo $config['redirect_page']; ?>" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contenedor del PDF Viewer -->
        <div class="card">
            <div class="card-body p-0">
                <!-- Incluir el HTML del PDF Viewer -->
                <div id="pdfViewer">
                    <?php include("pdf-viewer.html"); ?>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
$content = ob_get_clean();

// Agregar scripts específicos para esta página
$pageScripts = '
<script>
    // Definir variables globales para JavaScript
    const BASE_URL = "' . BASE_URL . '";
    document.addEventListener("DOMContentLoaded", function() {
    // Usar el servicio seguro de archivos
    const pdfUrl = BASE_URL + "/resources/modules/components/servir_archivo.php?id=' . $id . '&tipo=' . $tipo . '&categoria=' . $categoria . '";
    const viewer = new PDFViewer(pdfUrl, "pdfViewer");
});
</script>
<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    // Configurar PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
</script>
<script src="' . BASE_URL . '/resources/modules/components/PDFViewer/pdf-viewer.js"></script>
<script src="' . BASE_URL . '/resources/modules/components/PDFViewer/visor.js"></script>
';

include __DIR__ . '/../../../layouts/main-modelo2.php';
?>



