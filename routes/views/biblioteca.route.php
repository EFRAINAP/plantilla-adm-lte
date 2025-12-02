<?php
/**
 * Rutas para el módulo de Biblioteca
 * routes/biblioteca.route.php
 */

// Ruta principal - listar documentos de biblioteca
$router->get('/sistema/biblioteca/listado', function() {
    renderView('biblioteca/listado', ['title' => 'Listados de Documentos de Biblioteca']);
});

$router->get('/sistema/biblioteca/administrar', function() {
    renderView('biblioteca/administrar', ['title' => 'Administrar Documentos de Biblioteca']);
});

$router->get('/sistema/biblioteca/administrar/agregar', function() {
    renderView('biblioteca/agregar', ['title' => 'Agregar Documento a Biblioteca']);
});

$router->get('/sistema/biblioteca/administrar/editar', function() {
    renderView('biblioteca/editar', ['title' => 'Editar Documento de Biblioteca']);
});

// ...más rutas de Biblioteca...