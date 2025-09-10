<?php
/**
 * Rutas principales del sistema
 * routes/web.php
 */

// Ruta principal - redirigir a dashboard
$router->get('/', function() {
    $router = new Router();
    $router->redirect('/dashboard');
});

// Dashboard principal
$router->get('/dashboard', function() {
    renderView('dashboard/index', ['title' => 'Dashboard Principal']);
});

$router->get('/dashboard2', function() {
    renderView('dashboard/dashboard2', ['title' => 'Dashboard v2']);
});

$router->get('/dashboard3', function() {
    renderView('dashboard/dashboard3', ['title' => 'Dashboard v3']);
});

// Módulo de usuarios
require_once BASE_PATH . '/routes/usuarios.route.php';
// Módulo de ISO
require_once BASE_PATH . '/routes/iso.route.php';

// ruta temporal para pruebas
$router->get('/test', function() {
    renderView('test', ['title' => 'Página de Pruebas']);
});

// ruta temporal para visor de documentos pdf
$router->get('/visor', function() {
    renderView('components/PDFViewer/UnifiedDocumentViewer', ['title' => 'Visor de Documentos PDF']);
});



// API routes para usuarios
$router->post('/usuarios/create', function() {
    jsonResponse([
        'success' => true,
        'message' => 'Usuario creado exitosamente'
    ]);
});

$router->put('/usuarios/{id}/update', function($params) {
    jsonResponse([
        'success' => true,
        'message' => 'Usuario actualizado exitosamente',
        'id' => $params['id']
    ]);
});

$router->delete('/usuarios/{id}/delete', function($params) {
    jsonResponse([
        'success' => true,
        'message' => 'Usuario eliminado exitosamente',
        'id' => $params['id']
    ]);
});

// Configuración
$router->get('/configuracion', function() {
    renderView('configuracion/index', ['title' => 'Configuración General']);
});

$router->get('/configuracion/general', function() {
    renderView('configuracion/general', ['title' => 'Configuración General']);
});

$router->get('/configuracion/seguridad', function() {
    renderView('configuracion/seguridad', ['title' => 'Configuración de Seguridad']);
});

$router->get('/configuracion/sistema/logs', function() {
    renderView('configuracion/sistema/logs', ['title' => 'Logs del Sistema']);
});

$router->get('/configuracion/sistema/backups', function() {
    renderView('configuracion/sistema/backups', ['title' => 'Backups del Sistema']);
});

// Reportes
$router->get('/reportes', function() {
    renderView('reportes/index', ['title' => 'Reportes del Sistema']);
});

$router->get('/reportes/ventas', function() {
    renderView('reportes/ventas', ['title' => 'Reporte de Ventas']);
});

$router->get('/reportes/usuarios', function() {
    renderView('reportes/usuarios', ['title' => 'Reporte de Usuarios']);
});
