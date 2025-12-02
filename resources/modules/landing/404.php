<?php
/**
 * Página 404 para sitio público
 * resources/modules/landing/404.php
 */

$page_title = $title ?? 'Página no encontrada - EMPRESA Ingenieros';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="<?= bootstrap('css/bootstrap.min.css') ?>" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="<?= fontawesome('css/all.min.css') ?>" rel="stylesheet">
    
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .error-content {
            text-align: center;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

    <div class="error-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="error-content">
                        <div class="error-code">404</div>
                        <h2 class="mb-4">¡Oops! Página no encontrada</h2>
                        <p class="lead mb-4">
                            La página que estás buscando no existe o ha sido movida.
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="<?= url('/') ?>" class="btn btn-light btn-lg">
                                <i class="fas fa-home"></i> Ir al Inicio
                            </a>
                            <a href="<?= url('/servicios') ?>" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-cogs"></i> Ver Servicios
                            </a>
                        </div>
                        
                        <div class="mt-4">
                            <p class="mb-2">¿Necesitas ayuda? Contáctanos:</p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="mailto:info@tamaingenieros.pe" class="text-white">
                                    <i class="fas fa-envelope"></i> Email
                                </a>
                                <a href="tel:+51999999999" class="text-white">
                                    <i class="fas fa-phone"></i> Teléfono
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="<?= bootstrap('js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>