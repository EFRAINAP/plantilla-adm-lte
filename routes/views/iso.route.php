<?php
/**
 * Rutas para el módulo de ISO
 * routes/iso.route.php
 */

// Ruta principal - listar documentos ISO
$router->get('/sistema/iso/asignados', function() {
    renderView('iso/asignados', ['title' => 'Listados de Documentos ISO']);
});

// Rutas para la gestión de documentos transversales
$router->get('/sistema/iso/transversales', function() {
    renderView('iso/transversales', ['title' => 'Documentos Transversales']);
});

$router->get('/sistema/iso/gestionar', function() {
    renderView('iso/gestionar', ['title' => 'Gestionar Documentos ISO']);
});

// ...más rutas de ISO...