
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