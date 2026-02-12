<?php
// resources/partials/sidebar-menu-tail.php - Menú del sidebar con Tailwind CSS

// Configuración del menú dinámico jerárquico con control de permisos
$menu = [
    [
        'title' => 'Dashboard', 
        'icon' => 'fas fa-tachometer-alt',
        'route' => 'dashboard',
        'permission' => null // Siempre visible para usuarios logueados
    ],
    [  
        'title' => 'Prueba Template', 
        'icon' => 'fas fa-flask',
        'route' => 'sistema/prueba-template',
        'permission' => null
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
                'icon' => 'fas fa-user-tag',
                'permission' => '01_AdministrarPerfiles.php'
            ],
        ]
    ],
    ['separator' => 'Reportes'],
    [
        'title' => 'Reportes',
        'icon' => 'fas fa-chart-bar',
        'route' => 'sistema/reportes',
        'permission' => null
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
function renderMenuItemTailwind($item, $level = 0) {
    $currentPath = Config::getCurrentPath();
    
    // Si es un separador
    if (isset($item['separator'])) {
        return '<div class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider sidebar-text">' 
               . htmlspecialchars($item['separator']) . '</div>';
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
            if (hasMenuAccess($child['permission'] ?? null) && isMenuItemActiveTailwind($child, $currentPath)) {
                $isOpen = true;
                break;
            }
        }
    }
    
    // Clases CSS
    $itemClasses = 'nav-item group flex items-center w-full text-left rounded-lg transition-colors duration-150';
    
    if ($level === 0) {
        $itemClasses .= ' px-3 py-2 text-sm font-medium';
    } else {
        $itemClasses .= ' px-3 py-2 ml-6 text-sm';
    }
    
    // Estados visuales
    if ($isActive) {
        $itemClasses .= ' bg-blue-600 text-white';
        $iconClasses = 'text-blue-200';
        $textClasses = 'text-white';
    } elseif ($isOpen) {
        $itemClasses .= ' bg-slate-700 text-slate-200';
        $iconClasses = 'text-slate-300';
        $textClasses = 'text-slate-200';
    } else {
        $itemClasses .= ' text-slate-300 hover:bg-slate-700 hover:text-white';
        $iconClasses = 'text-slate-400 group-hover:text-slate-300';
        $textClasses = 'text-slate-300 group-hover:text-white';
    }
    
    $html = '<div class="nav-item">';
    
    // Enlace o botón
    if (isset($item['route']) && !$hasChildren) {
        $target = isset($item['external']) && $item['external'] ? ' target="_blank"' : '';
        $url = isset($item['external']) && $item['external'] ? $item['route'] : url($item['route']);
        $html .= '<a href="' . htmlspecialchars($url) . '"' . $target . ' class="' . $itemClasses . '">';
    } else {
        $html .= '<button type="button" class="' . $itemClasses . ' w-full" data-submenu-toggle>';
    }
    
    // Icono
    if (isset($item['icon'])) {
        $icon = htmlspecialchars($item['icon']);
        $html .= '<i class="' . $icon . ' w-5 h-5 mr-3 flex-shrink-0 ' . $iconClasses . '"></i>';
    } else {
        // Iconos por defecto según el nivel
        if ($level === 0) {
            $html .= '<i class="fas fa-circle w-5 h-5 mr-3 flex-shrink-0 ' . $iconClasses . '"></i>';
        } elseif ($level === 1) {
            $html .= '<i class="far fa-circle w-5 h-5 mr-3 flex-shrink-0 ' . $iconClasses . '"></i>';
        } else {
            $html .= '<i class="far fa-dot-circle w-5 h-5 mr-3 flex-shrink-0 ' . $iconClasses . '"></i>';
        }
    }
    
    // Título
    $html .= '<span class="flex-1 sidebar-text ' . $textClasses . '">' . htmlspecialchars($item['title']) . '</span>';
    
    // Flecha para items con hijos
    if ($hasChildren) {
        $arrowClasses = 'w-4 h-4 flex-shrink-0 transition-transform duration-200' . ($isOpen ? ' rotate-90' : '');
        $html .= '<i class="fas fa-chevron-right ' . $arrowClasses . ' sidebar-text ' . $iconClasses . '"></i>';
    }
    
    $html .= isset($item['route']) && !$hasChildren ? '</a>' : '</button>';
    
    // Renderizar hijos si existen
    if ($hasChildren) {
        $submenuClasses = 'submenu space-y-1 mt-1' . ($isOpen ? ' block' : ' hidden');
        $html .= '<div class="' . $submenuClasses . '">';
        
        foreach ($item['children'] as $child) {
            // Solo renderizar hijos que tengan permisos
            if (hasMenuAccess($child['permission'] ?? null)) {
                $html .= renderMenuItemTailwind($child, $level + 1);
            }
        }
        
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Verificar recursivamente si un item del menú está activo (actualizada con permisos)
 */
function isMenuItemActiveTailwind($item, $currentPath) {
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
            if (hasMenuAccess($child['permission'] ?? null) && isMenuItemActiveTailwind($child, $currentPath)) {
                return true;
            }
        }
    }
    
    return false;
}
?>

<div class="space-y-1">
    <?php foreach ($menu as $item): ?>
        <?php 
        // Solo renderizar items con permisos válidos
        $renderedItem = renderMenuItemTailwind($item);
        if (!empty($renderedItem)) {
            echo $renderedItem;
        }
        ?>
    <?php endforeach; ?>
</div>