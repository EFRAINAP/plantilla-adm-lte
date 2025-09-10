<?php
$page_title = "Visor de PDF Generado";
require_once('../../01_General/00_load.php');

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

<div class="container-fluid mt-3 mb-5">
    <div class="pdf-container">
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
        
        <!-- Contenido del PDF -->
        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-dark text-white rounded">
            <div id="loading" class="text-center">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                <p class="mt-3 h5">Generando documento PDF...</p>
                <p class="text-muted">Por favor espere mientras se procesa la información</p>
            </div>
            
            <div id="error" class="error-container" style="display: none;">
                <div class="text-danger mb-3">
                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                </div>
                <h5 class="text-danger">Error al cargar el documento</h5>
                <p class="text-muted mb-4">No se pudo generar o cargar el PDF solicitado.</p>
                <button class="btn btn-primary" onclick="refreshPDF()">🔄 Reintentar</button>
                <button class="btn btn-secondary ms-2" onclick="window.close()">Cerrar</button>
            </div>
            
            <iframe 
                id="pdf-iframe" 
                style="display: none; width: 100%; height: 100vh; border: none;"
                title="Documento PDF">
            </iframe>
        </div>
    </div>
</div>

<?php include_once("../../05_footer.php"); ?>

<script>
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
    let loadAttempts = 0;
    const maxAttempts = 3;
    
    function refreshPDF() {
        loadPDF();
    }
    
    function toggleFullscreen() {
        const pdfContent = document.querySelector('.pdf-content');
        
        if (!document.fullscreenElement) {
            pdfContent.requestFullscreen().catch(err => {
                console.log(`Error al entrar en pantalla completa: ${err.message}`);
            });
        } else {
            document.exitFullscreen();
        }
    }
    
    function loadPDF() {
        const iframe = document.getElementById('pdf-iframe');
        const loading = document.getElementById('loading');
        const error = document.getElementById('error');
        
        // Mostrar loading
        loading.style.display = 'block';
        error.style.display = 'none';
        iframe.style.display = 'none';
        
        // Incrementar intentos
        loadAttempts++;
        
        // URL del PDF dinámico con parámetros para ocultar toolbar
        const timestamp = Date.now();
        const baseUrl = `../servir_archivo_generado.php?id=<?= urlencode($id) ?>&tipo=<?= urlencode($tipo) ?>&generador=<?= urlencode($generador) ?>&t=${timestamp}&attempt=${loadAttempts}`;
        
        // ¡AQUÍ ESTÁ LA MAGIA! 🎯
        const pdfUrl = baseUrl + '';
        
        console.log('Cargando PDF SIN TOOLBAR desde:', pdfUrl);
        
        // Configurar eventos del iframe
        iframe.onload = function() {
            try {
                setTimeout(() => {
                    loading.style.display = 'none';
                    iframe.style.display = 'block';
                    console.log('✅ PDF cargado correctamente SIN TOOLBAR');
                    
                    // Intentar ocultar toolbar adicional via JavaScript (para navegadores que lo soporten)
                    try {
                        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                        if (iframeDoc) {
                            // Ocultar elementos específicos del visor PDF
                            const style = iframeDoc.createElement('style');
                            style.textContent = `
                                #toolbar, .toolbar, #toolbarContainer { display: none !important; }
                                #secondaryToolbar { display: none !important; }
                                #sidebarContainer { display: none !important; }
                                #viewerContainer { top: 0 !important; }
                                .textLayer { -webkit-user-select: none !important; -moz-user-select: none !important; }
                            `;
                            iframeDoc.head.appendChild(style);
                        }
                    } catch (e) {
                        // Ignore cross-origin errors
                        console.log('No se puede acceder al contenido del iframe (normal por seguridad)' + e);
                    }
                }, 1000);
                
            } catch (e) {
                console.error('Error accediendo al contenido del iframe:', e);
                showError();
            }
        };
        
        iframe.onerror = function() {
            console.error('Error al cargar el iframe');
            showError();
        };
        
        // Cargar el PDF con los parámetros de ocultación
        try {
            iframe.src = pdfUrl;
        } catch (e) {
            console.error('Error al configurar src del iframe:', e);
            showError();
        }
        
        // Timeout de seguridad
        setTimeout(function() {
            if (loading.style.display !== 'none') {
                console.warn('Timeout al cargar PDF');
                showError();
            }
        }, 30000);
    }
    
    function showError() {
        const loading = document.getElementById('loading');
        const error = document.getElementById('error');
        const iframe = document.getElementById('pdf-iframe');
        
        loading.style.display = 'none';
        iframe.style.display = 'none';
        error.style.display = 'block';
    }
    
    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Iniciando carga de PDF sin toolbar...');
        loadPDF();
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
    
    // Manejar errores globales
    window.addEventListener('error', function(e) {
        console.error('Error global:', e.error);
    });
    
    // Detectar cambios de pantalla completa
    document.addEventListener('fullscreenchange', function() {
        if (document.fullscreenElement) {
            console.log('📺 Modo pantalla completa activado');
        } else {
            console.log('📺 Saliendo de pantalla completa');
        }
    });
</script>