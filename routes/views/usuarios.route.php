<?php
/**
 * Rutas para el módulo de usuarios
 * routes/usuarios.route.php
 */

// Ruta principal - listar usuarios
$router->get('/sistema/usuarios', function() {
    renderView('usuarios/index', ['title' => 'Gestión de Usuarios']);
});

// Rutas para la gestión de perfiles de usuario
$router->get('/sistema/usuarios/perfiles', function() {
    renderView('usuarios/perfil', ['title' => 'Perfiles de Usuario']);
});

$router->get('/sistema/usuarios/perfiles/agregar', function() {
    renderView('usuarios/agregar_perfil', ['title' => 'Agregar Perfil']);
});

$router->get('/sistema/usuarios/perfiles/agregar/editar_perfil', function() {
    renderView('usuarios/editar_perfil', ['title' => 'Editar Perfil']);
});

$router->get('/sistema/mi-perfil', function() {
    renderView('usuarios/mi_perfil', ['title' => 'Mi Perfil']);
});

// ...más rutas de usuarios...