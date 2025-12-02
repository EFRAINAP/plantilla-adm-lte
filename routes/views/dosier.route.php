<?php
/**
 * Rutas para el módulo de usuarios
 * routes/usuarios.route.php
 */

// Ruta principal - listar dosieres
$router->get('/sistema/dosier/gestion', function() {
    renderView('dosier/gestion', ['title' => 'Gestión de Dosieres']);
});

// Rutas para la gestión de perfiles de dosieres
$router->get('/sistema/dosier/wpq', function() {
    renderView('dosier/wpq', ['title' => 'WPQ']);
});

$router->get('/sistema/dosier/pqr', function() {
    renderView('dosier/pqr', ['title' => 'PQR']);
});

$router->get('/sistema/dosier/wps', function() {
    renderView('dosier/wps', ['title' => 'WPS']);
});

$router->get('/sistema/dosier/rgc', function() {
    renderView('dosier/rgc', ['title' => 'RGC']);
});

$router->get('/sistema/dosier/rcm', function() {
    renderView('dosier/rcm', ['title' => 'RCM']);
});

$router->get('/sistema/dosier/rci', function() {
    renderView('dosier/rci', ['title' => 'RCI']);
});

// Ruta para la gestión del índice del dosier
$router->get('/sistema/dosier/indice', function() {
    renderView('dosier/indice', ['title' => '📝 Índice del Dosier']);
});


// Ruta para la gestión de trazabilidad
$router->get('/sistema/dosier/trazabilidad', function() {
    renderView('dosier/lector_cad_trazabilidad', ['title' => '🔍 Trazabilidad de Materiales']);
});

// Ruta para la generación de informes de trazabilidad
$router->get('/sistema/dosier/trazabilidad/descarga', function() {
    renderView('dosier/trazabilidad/descarga_excel', ['title' => '📊 Generar Informe de Trazabilidad']);
});

// Ruta para el procesador de planos DXF/DWG
$router->get('/sistema/dosier/dimensional', function() {
    renderView('dosier/lector_cad_dimensional', ['title' => 'Procesador de Planos DXF/DWG']);
});