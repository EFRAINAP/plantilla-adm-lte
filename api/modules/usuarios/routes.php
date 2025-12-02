<?php
/**
 * Rutas del módulo Usuarios
 */

return [
    // CRUD básico
    'GET /v1/usuarios' => 'UsuarioController@index',
    'GET /v1/usuarios/{id}' => 'UsuarioController@show',
    'POST /v1/usuarios' => 'UsuarioController@store',
    'PUT /v1/usuarios/{id}' => 'UsuarioController@update',
    'DELETE /v1/usuarios/{id}' => 'UsuarioController@delete',
    
    // Funciones adicionales
    'GET /v1/usuarios/search' => 'UsuarioController@search',
    'GET /v1/usuarios/activos' => 'UsuarioController@activos',
    'POST /v1/usuarios/{id}/cambiar-password' => 'UsuarioController@cambiarPassword',
    
    // Gestión de perfiles
    'GET /v1/usuarios/{id}/perfiles' => 'UsuarioController@perfiles',
    'POST /v1/usuarios/{id}/perfiles' => 'UsuarioController@asignarPerfil',
    'DELETE /v1/usuarios/{id}/perfiles' => 'UsuarioController@eliminarPerfil',
    'GET /v1/usuarios/perfiles/disponibles' => 'UsuarioController@perfilesDisponibles',
    
    // Gestión de accesos
    'GET /v1/usuarios/{id}/accesos' => 'UsuarioController@accesos',
    'POST /v1/usuarios/{id}/accesos' => 'UsuarioController@asignarAccesos',
];