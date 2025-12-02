<?php

/**
 * Rutas para el módulo de Consumibles
 * routes/consumibles.route.php
 */

// Ruta Dashboard Mejora Continua
$router->get('/sistema/mejora/dashboard', function() {
    renderView('mejora_continua/dashboard', ['title' => '📝 Mejora Continua']);
});

// Ruta principal - administrar acciones correctivas
$router->get('/sistema/mejora/sac', function() {
    renderView('mejora_continua/sac', ['title' => '📝 Mejora Continua - Acciones Correctivas (SAC)']);
});

// Ruta principal - administrar consumibles
$router->get('/sistema/mejora/snc', function() {
    renderView('mejora_continua/snc', ['title' => '📝 Mejora Continua - Salidas No Conformes (SNC)']);
});


