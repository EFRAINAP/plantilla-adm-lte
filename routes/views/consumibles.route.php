<?php

/**
 * Rutas para el módulo de Consumibles
 * routes/consumibles.route.php
 */

// Ruta principal - administrar consumibles
$router->get('/sistema/consumibles/administrar', function() {
    renderView('consumibles/administrar', ['title' => '📊 Administrar Consumibles']);
});
    
$router->get('/sistema/consumibles/asignados', function() {
    renderView('consumibles/asignados', ['title' => '📂 Reporte Asignados de Consumibles por proceso']);
});

$router->get('/sistema/consumibles/base', function() {
    renderView('consumibles/base', ['title' => '📋 Historial de Consumibles']);
});