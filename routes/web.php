<?php
/**
 * Rutas principales del sistema
 * routes/web.php
 */
$router = new Router();

// Rutas públicas (sin autenticación requerida)
require_once BASE_PATH . '/routes/public/public.route.php';

// Rutas de módulos y paginas del sistema
require_once BASE_PATH . '/routes/views/views.route.php';

// Rutas de API
require_once BASE_PATH . '/routes/api/api.routes.php';