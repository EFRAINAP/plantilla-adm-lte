<?php
/**
 * Rutas principales del sistema
 * routes/web.php
 */

// Ruta principal - redirigir a dashboard
// Se encuentra en el modulos landing

// ==================== SITIO PÚBLICO ====================


// Landing page principal
$router->get('/', function() {
    renderView('landing/inicio', ['title' => 'EMPRESA Ingenieros - Inicio']);
});

// Páginas institucionales
$router->get('/inicio', function() {
    renderView('landing/inicio', ['title' => 'Inicio - EMPRESA Ingenieros']);
});

$router->get('/nosotros', function() {
    renderView('landing/nosotros', ['title' => 'Nosotros - EMPRESA Ingenieros']);
});

$router->get('/servicios', function() {
    renderView('landing/servicios', ['title' => 'Servicios - EMPRESA Ingenieros']);
});

$router->get('/cotiza', function() {
    renderView('landing/cotiza', ['title' => 'Cotización - EMPRESA Ingenieros']);
});

$router->get('/contacto', function() {
    renderView('landing/contacto', ['title' => 'Contacto - EMPRESA Ingenieros']);
});

$router->get('/test-tailwind', function() {
    renderView('landing/test-simple', ['title' => 'Test Tailwind CSS']);
});