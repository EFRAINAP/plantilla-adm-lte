<?php
/**
 * Rutas del sitio completo (público + sistema)
 * routes/views/views.route.php
 */

// Ruta principal - redirigir a dashboard
$router->get('/sistema', function() {
    global $session;
    if ($session && $session->isUserLoggedIn(true)) {
        redirectTo('sistema/dashboard');
    } else {
        renderView('auth/index', ['title' => 'Iniciar Sesión']);
    }
});

// Ruta legacy /dashboard - redirigir al login o dashboard según autenticación
$router->get('/dashboard', function() {
    global $session;
    if ($session && $session->isUserLoggedIn(true)) {
        redirectTo('sistema/dashboard');
    } else {
        redirectTo('sistema/auth/login');
    }
});

// Rutas públicas (sin autenticación requerida)
$router->get('/sistema/auth/login', function() {
    global $session;
    if ($session && $session->isUserLoggedIn(true)) {
        redirectTo('sistema/dashboard');  // Si ya está logueado, ir a dashboard
    } else {
        renderView('auth/index', ['title' => 'Iniciar Sesión']);
    }
});

// Rutas públicas (sin autenticación requerida)
$router->get('/sistema/auth/login2', function() {
    global $session;
    if ($session && $session->isUserLoggedIn(true)) {
        redirectTo('sistema/dashboard');  // Si ya está logueado, ir a dashboard
    } else {
        renderView('auth/index-old', ['title' => 'Iniciar Sesión']);
    }
});

// Dashboard principal del sistema
$router->get('/sistema/dashboard', function() {
    renderView('dashboard/index', ['title' => 'Dashboard Principal']);
});

$router->get('/sistema/prueba-template', function() {
    renderView('dashboard/test-template', ['title' => 'Prueba de Template']);
});

// Módulo de usuarios
require_once BASE_PATH . '/routes/views/usuarios.route.php';
// Módulo de ISO
require_once BASE_PATH . '/routes/views/iso.route.php';
// Módulo de Biblioteca
require_once BASE_PATH . '/routes/views/biblioteca.route.php';
// Módulo de Tareas
require_once BASE_PATH . '/routes/views/tarea.route.php';
// Módulo de Capacitaciones
require_once BASE_PATH . '/routes/views/capacitacion.route.php';
// Módulo de dosier
require_once BASE_PATH . '/routes/views/dosier.route.php';
// Módulo de Consumibles
require_once BASE_PATH . '/routes/views/consumibles.route.php';
// Módulo de Producción
require_once BASE_PATH . '/routes/views/produccion.route.php';
// Módulo de Mejora Continua (SAC-SNC)
require_once BASE_PATH . '/routes/views/mejoraContinua.route.php';

// Módulo de Manual de Funciones
$router->get('/sistema/manual/funciones', function() {
    renderView('manual/manual', ['title' => 'Manual de Funciones']);
});

// ruta temporal para pruebas
$router->get('/sistema/test', function() {
    renderView('test', ['title' => 'Página de Pruebas']);
});

// ruta para visor de documentos pdf de documentos en general
$router->get('/sistema/visor', function() {
    renderView('components/PDFViewer/UnifiedDocumentViewer', ['title' => 'Documento - Visor']);
});

$router->get('/sistema/visor/dosier/descarga', function() {
    renderView('components/PDFViewer/visor_pdf_descargar', ['title' => 'Dossier - Descargar']);
});

$router->get('/sistema/visor/dosier/lectura', function() {
    renderView('components/PDFViewer/visor_pdf_dosier', ['title' => 'Dossier - Lectura']);
});



// Ruta del calendario
$router->get('/sistema/calendario', function() {
    renderView('calendar/calendariov1', ['title' => 'Calendario']);
});

// Configuración
$router->get('/sistema/configuracion', function() {
    renderView('configuracion/index', ['title' => 'Configuración General']);
});


$router->get('/sistema/configuracion/general', function() {
    renderView('configuracion/general', ['title' => 'Configuración General']);
});

$router->get('/sistema/configuracion/seguridad', function() {
    renderView('configuracion/seguridad', ['title' => 'Configuración de Seguridad']);
});

$router->get('/sistema/configuracion/sistema/logs', function() {
    renderView('configuracion/sistema/logs', ['title' => 'Logs del Sistema']);
});

$router->get('/sistema/configuracion/sistema/backups', function() {
    renderView('configuracion/sistema/backups', ['title' => 'Backups del Sistema']);
});

// Reportes
$router->get('/sistema/reportes', function() {
    renderView('reportes/index', ['title' => 'Reportes del Sistema']);
});

$router->get('/sistema/reportes/ventas', function() {
    renderView('reportes/ventas', ['title' => 'Reporte de Ventas']);
});

$router->get('/sistema/reportes/usuarios', function() {
    renderView('reportes/usuarios', ['title' => 'Reporte de Usuarios']);
});