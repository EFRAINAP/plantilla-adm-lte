<?php
/**
 * Configuración de middleware para el módulo de usuarios
 */

return [
    'middleware' => [
        // Middleware base para todos los endpoints
        '*' => [
            'AuthenticationMiddleware',
            'AuthorizationMiddleware'
        ],
        
        // Validation middleware para endpoints específicos
        'POST /api/usuarios' => [
            'ValidationMiddleware'
        ],
        
        'PUT /api/usuarios/{id}' => [
            'ValidationMiddleware'
        ],
        
        'POST /api/usuarios/{id}/cambiar-password' => [
            'ValidationMiddleware'
        ]
    ],
    
    'authorization' => [
        'page' => 'usuarios',
        'enabled' => true,
        'exceptions' => [
            'GET /api/usuarios/perfiles/disponibles' => [
                'permission' => 'seguimiento',
                'page' => 'perfiles'
            ],
            'POST /api/usuarios/{id}/cambiar-password' => [
                'permission' => 'editar',
                'page' => 'usuarios'
            ]
        ]
    ],
    
    'validation' => [
        'POST /api/usuarios' => [
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:8',
            'user_level' => 'integer|in:0,1,2,3',
            'cargo' => 'string|max:100',
            'area' => 'string|max:100'
        ],
        
        'PUT /api/usuarios/{id}' => [
            'name' => 'string|max:100',
            'username' => 'string|max:50',
            'password' => 'string|min:8',
            'user_level' => 'integer|in:0,1,2,3',
            'cargo' => 'string|max:100',
            'area' => 'string|max:100'
        ],
        
        'POST /api/usuarios/{id}/cambiar-password' => [
            'password' => 'required|string|min:8'
        ]
    ]
];