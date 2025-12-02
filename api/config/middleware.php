<?php
/**
 * Configuración global de middleware para el sistema
 * Las configuraciones específicas están en cada módulo
 */

return [
    // Middleware global aplicado a todas las rutas
    'global' => [
        'SecurityHeadersMiddleware',
        'CorsMiddleware', 
        'LoggingMiddleware'
    ],
    
    // Configuraciones globales (valores sensibles en .env)
    'authentication' => [
        'header_name' => 'Authorization',
        'cookie_name' => 'auth_token',
        'exclude_paths' => [
            '/api/auth/login',
            '/api/auth/register', 
            '/api/health'
        ]
    ],
    
    'cors' => [
        'allowed_origins' => [
            '*' // Configurar en .env: CORS_ALLOWED_ORIGINS
        ],
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => [
            'Content-Type',
            'Authorization',
            'X-Requested-With', 
            'Accept',
            'Origin'
        ],
        'exposed_headers' => [
            'X-RateLimit-Limit',
            'X-RateLimit-Remaining'
        ],
        'max_age' => 3600,
        'supports_credentials' => true
    ],
    
    'security' => [
        'headers' => [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains'
        ],
        'remove_headers' => [
            'X-Powered-By',
            'Server'
        ]
    ]
];