<?php 
$title = "404 - Página no encontrada";
ob_start();
?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="error-page">
                <h2 class="headline text-warning">404</h2>
                <div class="error-content">
                    <h3><i class="fas fa-exclamation-triangle text-warning"></i> ¡Oops! Página no encontrada.</h3>
                    <p>
                        No pudimos encontrar la página que estás buscando.
                        Mientras tanto, puedes <a href="<?= url('dashboard') ?>">regresar al dashboard</a> o usar el menú de navegación.
                    </p>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-home fa-3x text-primary mb-3"></i>
                                    <h5>Ir al Dashboard</h5>
                                    <p class="text-muted">Volver a la página principal</p>
                                    <a href="<?= url('dashboard') ?>" class="btn btn-primary">Dashboard</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <i class="fas fa-search fa-3x text-info mb-3"></i>
                                    <h5>Buscar</h5>
                                    <p class="text-muted">Buscar lo que necesitas</p>
                                    <button class="btn btn-info" onclick="focusSearch()">Buscar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function focusSearch() {
    // Si tienes un campo de búsqueda en el layout, enfócalo
    const searchInput = document.querySelector('input[type="search"]');
    if (searchInput) {
        searchInput.focus();
    } else {
        toastr.info('Función de búsqueda en desarrollo');
    }
}
</script>

<?php
$content = ob_get_clean();
include RESOURCES_PATH . '/layouts/main.php';
