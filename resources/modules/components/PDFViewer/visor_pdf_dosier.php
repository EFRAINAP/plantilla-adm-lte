<?php
require_once('../../01_General/00_load.php');
$page_title = 'Visor de Documentos PDF';

// Verificar autenticación
if (!$session->isUserLoggedIn(true)) {
    redirect('', false);
}

$id = $_GET['id'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$generador = $_GET['generador'] ?? 'dosier';

if (empty($id) || empty($tipo)) {
    $session->msg("d", "Parámetros insuficientes para mostrar el PDF");
    redirect('../../14_GestionDosier/01_AdministrarDosier.php', false);
}
?>
<?php include_once("../../04_header.php"); ?>
<link href="pdf-viewer.css" rel="stylesheet">
<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-12">
            <!-- Barra de herramientas personalizada -->
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-dark text-white rounded">
                <div>
                    <strong>📄 Documento PDF</strong>
                    <span class="text-white">- ID: <?= htmlspecialchars($id) ?></span>
                    <span class="text-muted ms-2">| Tipo: <?= htmlspecialchars($generador) ?></span>
                </div>
                <div class="btn-group">
                    <button class="btn btn-secondary" onclick="refreshPDF()">🔄 Actualizar</button>
                    <button class="btn btn-secondary" onclick="toggleFullscreen()">📺 Pantalla Completa</button>
                    <button class="btn btn-secondary" onclick="window.close()">✖ Cerrar</button>
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
</div>
<?php include_once("../../05_footer.php"); ?>

<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    // Configurar PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
</script>

<!-- PDF Viewer Component -->
<script src="pdf-viewer.js"></script>

<!-- Bootstrap JS -->
<script src="../../00_Librerias/Bootstrap-5.3.3/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Usar el servicio seguro de archivos
        const timestamp = Date.now();
        const baseUrl = `../servir_archivo_generado.php?id=<?= urlencode($id) ?>&tipo=<?= urlencode($tipo) ?>&generador=<?= urlencode($generador) ?>&t=${timestamp}`;
        
        // ¡AQUÍ ESTÁ LA MAGIA! 🎯
        const pdfUrl = baseUrl + '';
        const viewer = new PDFViewer(pdfUrl, 'pdfViewer');
    });
</script>

<script>
// Función para mostrar el modal de seguridad
function mostrarModalInspeccion() {
    const modalHtml = `
        <div class="modal fade" id="modalInspeccion" tabindex="-1" aria-labelledby="modalInspeccionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalInspeccionLabel"><i class="bi bi-lock"></i> Seguridad </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body justify-content-center text-center border border-4 border-danger rounded-bottom-3">
                        <p class="text-lg">La propiedad intelectual está protegida.<br>Todos los derechos reservados TAMA.</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('modalInspeccion'));
    modal.show();
}

// Bloquear click derecho
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    mostrarModalInspeccion();
});

// Bloquear teclas de inspección y atajos comunes
document.addEventListener('keydown', function(e) {
    // F12, Ctrl+Shift+I/J/C/U/S/P, Ctrl+U, Ctrl+S, Ctrl+P, Ctrl+C, Ctrl+V, Ctrl+X, Ctrl+A
    if (
        e.key === 'F12' ||
        (e.ctrlKey && e.shiftKey && ['I','J','C','U','S','P'].includes(e.key.toUpperCase())) ||
        (e.ctrlKey && ['U','S','P','C','V','X','A'].includes(e.key.toUpperCase()))
    ) {
        e.preventDefault();
        e.stopPropagation();
        mostrarModalInspeccion();
        return false;
    }
});

// capturar todos los errores
window.addEventListener('error', function(e) {
    console.error('Error capturado:', e);
});
</script>
