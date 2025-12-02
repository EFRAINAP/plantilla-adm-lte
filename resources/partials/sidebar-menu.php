<?php
// resources/partials/sidebar-menu.php

// Configuración del menú dinámico jerárquico con control de permisos
$menu = [
    [
        'title' => 'Dashboard', 
        'icon' => 'fa-solid fa-gauge',
        'route' => 'dashboard',
        'permission' => null // Siempre visible para usuarios logueados
    ],
    ['separator' => 'Módulos'],
    [
        'title' => 'Usuarios',
        'icon' => 'fas fa-users',
        'permission' => ['01_AdministrarUsuarios.php', '01_AdministrarPerfiles.php'], // Cualquiera de estos permisos
        'children' => [
            [
                'title' => 'Gestión', 
                'route' => 'sistema/usuarios', 
                'icon' => 'fas fa-user-cog',
                'permission' => '01_AdministrarUsuarios.php'
            ],
            [
                'title' => 'Perfiles', 
                'route' => 'sistema/usuarios/perfiles', 
                'icon' => 'fa-solid fa-users',
                'permission' => '01_AdministrarPerfiles.php'
            ],
        ]
    ],

    ['separator' => 'Reportes'],
    [
        'title' => 'Reportes',
        'icon' => 'fas fa-chart-bar',
        'route' => 'sistema/reportes',
        'permission' => null // Ajustar según tus necesidades
    ],
];

/**
 * Verificar si el usuario tiene acceso a un item del menú
 */
function hasMenuAccess($permissions) {
    // Si no hay permisos definidos, permitir acceso
    if (empty($permissions)) {
        return true;
    }
    
    // Si es un array de permisos, verificar que tenga al menos uno
    if (is_array($permissions)) {
        foreach ($permissions as $permission) {
            if (has_access($permission)) {
                return true;
            }
        }
        return false;
    }
    
    // Si es un permiso único
    return has_access($permissions);
}

/**
 * Verificar si un item del menú tiene hijos visibles
 */
function hasVisibleChildren($children) {
    if (empty($children)) {
        return false;
    }
    
    foreach ($children as $child) {
        if (hasMenuAccess($child['permission'] ?? null)) {
            return true;
        }
        
        // Verificar recursivamente si tiene hijos visibles
        if (isset($child['children']) && hasVisibleChildren($child['children'])) {
            return true;
        }
    }
    
    return false;
}

/**
 * Función recursiva para renderizar menús con múltiples niveles y permisos
 */
function renderMenuItem($item, $level = 0) {
    $currentPath = Config::getCurrentPath();
    
    // Si es un separador
    if (isset($item['separator'])) {
        return '<li class="nav-header">' . htmlspecialchars($item['separator']) . '</li>';
    }
    
    // Verificar permisos del item actual
    if (!hasMenuAccess($item['permission'] ?? null)) {
        return '';
    }
    
    $hasChildren = isset($item['children']) && !empty($item['children']);
    
    // Si tiene hijos, verificar que al menos uno sea visible
    if ($hasChildren && !hasVisibleChildren($item['children'])) {
        return '';
    }
    
    $isActive = false;
    $isOpen = false;
    
    // Verificar si este item o alguno de sus hijos está activo
    if (isset($item['route'])) {
        $routeCheck = '/' . ltrim($item['route'], '/');
        $isActive = $currentPath === $routeCheck;
    }
    
    if ($hasChildren) {
        foreach ($item['children'] as $child) {
            if (hasMenuAccess($child['permission'] ?? null) && isMenuItemActive($child, $currentPath)) {
                $isOpen = true;
                break;
            }
        }
    }
    
    $liClass = 'nav-item';
    if ($hasChildren) {
        if ($isOpen) {
            $liClass .= ' menu-open';
        }
    }
    
    $aClass = 'nav-link';
    if ($isActive || $isOpen) {
        $aClass .= ' active';
    }
    
    $html = '<li class="' . $liClass . '">';
    
    if (isset($item['route']) && !$hasChildren) {
        // Item simple con enlace
        $target = isset($item['external']) && $item['external'] ? ' target="_blank"' : '';
        $url = isset($item['external']) && $item['external'] ? $item['route'] : url($item['route']);
        $html .= '<a href="' . htmlspecialchars($url) . '"' . $target . ' class="' . $aClass . '">';
    } else {
        // Item con hijos o sin enlace
        $html .= '<a href="#" class="' . $aClass . '">';
    }
    
    // Icono
    if (isset($item['icon'])) {
        $iconClass = $level > 0 ? 'nav-icon ' . htmlspecialchars($item['icon']) : 'nav-icon ' . htmlspecialchars($item['icon']);
        $html .= '<i class="' . $iconClass . '"></i>';
    } else {
        // Iconos por defecto según el nivel
        if ($level === 0) {
            $html .= '<i class="nav-icon fas fa-circle"></i>';
        } elseif ($level === 1) {
            $html .= '<i class="far fa-circle nav-icon"></i>';
        } else {
            $html .= '<i class="far fa-dot-circle nav-icon"></i>';
        }
    }
    
    // Título
    $html .= '<p>' . htmlspecialchars($item['title']);
    
    // Flecha para items con hijos
    if ($hasChildren) {
        $html .= '<i class="nav-arrow bi bi-chevron-right"></i>';
    }
    
    $html .= '</p></a>';
    
    // Renderizar hijos si existen
    if ($hasChildren) {
        $html .= '<ul class="nav nav-treeview">';
        foreach ($item['children'] as $child) {
            // Solo renderizar hijos que tengan permisos
            if (hasMenuAccess($child['permission'] ?? null)) {
                $html .= renderMenuItem($child, $level + 1);
            }
        }
        $html .= '</ul>';
    }
    
    $html .= '</li>';
    
    return $html;
}

/**
 * Verificar recursivamente si un item del menú está activo (actualizada con permisos)
 */
function isMenuItemActive($item, $currentPath) {
    // Verificar permisos primero
    if (!hasMenuAccess($item['permission'] ?? null)) {
        return false;
    }
    
    if (isset($item['route'])) {
        $routeCheck = '/' . ltrim($item['route'], '/');
        if ($currentPath === $routeCheck) {
            return true;
        }
    }
    
    if (isset($item['children'])) {
        foreach ($item['children'] as $child) {
            if (hasMenuAccess($child['permission'] ?? null) && isMenuItemActive($child, $currentPath)) {
                return true;
            }
        }
    }
    
    return false;
}
?>

<ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" aria-label="Main navigation" data-accordion="false">
    <?php foreach ($menu as $item): ?>
        <?php 
        // Solo renderizar items con permisos válidos
        $renderedItem = renderMenuItem($item);
        if (!empty($renderedItem)) {
            echo $renderedItem;
        }
        ?>
    <?php endforeach; ?>
</ul>
