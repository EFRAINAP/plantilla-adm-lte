<?php
/**
 * Rutas del módulo de autenticación
 */

return [
    'POST /v1/auth/login' => 'AuthController@login',
    'POST /v1/auth/logout' => 'AuthController@logout', 
    'POST /v1/auth/refresh' => 'AuthController@refresh',
    'GET /v1/auth/profile' => 'AuthController@profile'
];