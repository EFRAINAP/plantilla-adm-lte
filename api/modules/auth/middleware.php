<?php
/**
 * Configuración de middleware para el módulo de autenticación
 */

return [
    'middleware' => [
        // Middleware específico para login
        'POST /api/auth/login' => [
            'ValidationMiddleware',
            'RateLimitMiddleware'
        ],
        
        // Middleware para endpoints autenticados
        'POST /api/auth/refresh' => [
            'AuthenticationMiddleware'
        ],
        
        'GET /api/auth/profile' => [
            'AuthenticationMiddleware'
        ]
        
        // logout sin middleware especial
    ],
    
    'validation' => [
        'POST /api/auth/login' => [
            'username' => 'required|string|max:50',
            'password' => 'required|string|min:1'
        ]
    ],
    
    'ratelimit' => [
        'POST /api/auth/login' => [
            'requests' => 5,
            'per' => 300, // 5 minutos
            'key' => 'ip'
        ]
    ]
];