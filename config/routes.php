<?php
/**
 * Configuración de rutas escalable
 * config/routes.php
 */

return [
    // ==================== PREFIJOS PROTEGIDOS ====================
    'protected_prefixes' => [
        '/sistema',      // Sistema principal
        '/admin',        // Panel de administración
        '/dashboard',    // Dashboard legacy  
        '/panel',        // Panel alternativo
        '/manage',       // Gestión avanzada
        '/control',      // Panel de control
        '/private'       // Área privada
    ],
    
    // NOTA: Los bloqueos de seguridad se manejan en .htaccess
    // Esta configuración se mantiene solo para documentación
    
    // ==================== EXCEPCIONES DE AUTH ====================
    'auth_exceptions' => [
        // Sistema
        '/sistema/auth/login',
    ],
    
    // ==================== MAPEO DE LOGIN URLs ====================
    'login_mapping' => [
        '/dashboard' => '/sistema/auth/login',  // Legacy redirect
        'default'    => '/sistema/auth/login'   // Fallback
    ],
    
    // ==================== RUTAS PÚBLICAS ====================
    'public_routes' => [
        // Landing
        '/',
        '/inicio',
        '/nosotros', 
        '/servicios',
        '/cotiza',
        '/contacto',
        '/about',
        '/portfolio',
    ]
];