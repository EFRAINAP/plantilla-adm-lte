<?php

/**
 * Rutas para el módulo de Consumibles
 * routes/consumibles.route.php
 */

// Ruta principal - administrar consumibles
$router->get('/sistema/produccion/datos', function() {
    renderView('produccion/produccion', ['title' => '📝 Datos de Producción']);
});