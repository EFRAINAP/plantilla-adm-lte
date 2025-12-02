<?php

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