<?php
/**
 * Rutas para el módulo de capacitaciones
 * routes/capacitacion.route.php
 */

// Ruta para administrar capacitaciones
$router->get('/sistema/capacitaciones/administrar', function() {
    renderView('capacitacion/administrar', ['title' => '📚 Administrar Capacitaciones']);
});

// ...más rutas de Capacitación...