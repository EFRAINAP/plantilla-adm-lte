<?php
// resources/partials/sidebar-menu.php

// Configuración del menú dinámico jerárquico con control de permisos
$menu = [
    [
        'title' => 'Dashboard', 
        'icon' => 'fas fa-tachometer-alt',
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
                'route' => 'usuarios', 
                'icon' => 'fas fa-user-cog',
                'permission' => '01_AdministrarUsuarios.php'
            ],
            [
                'title' => 'Perfiles', 
                'route' => 'usuarios/perfiles', 
                'icon' => 'fas fa-user-shield',
                'permission' => '01_AdministrarPerfiles.php'
            ],
        ]
    ],
    [
        'title' => 'Documentos ISO',
        'icon' => 'fas fa-file-alt',
        'permission' => ['01_ListadoDocumentosTransversales.php', '01_ListadoDocumentos.php', '03_AdministrarDocumentos.php'],
        'children' => [
            [
                'title' => 'Transversales', 
                'route' => 'iso/transversales',
                'permission' => '01_ListadoDocumentosTransversales.php'
            ],
            [
                'title' => 'Asignados', 
                'route' => 'iso/asignados',
                'permission' => '01_ListadoDocumentos.php'
            ],
            [
                'title' => 'Gestionar', 
                'route' => 'iso/gestionar',
                'permission' => '03_AdministrarDocumentos.php'
            ],
        ]
    ],
    [
        'title' => 'Biblioteca Virtual',
        'icon' => 'fas fa-book',
        'permission' => ['01_ListadoBiblioteca.php', '03_AdministrarBiblioteca.php'],
        'children' => [
            [
                'title' => 'Listado', 
                'route' => 'biblioteca/listado',
                'permission' => '01_ListadoBiblioteca.php'
            ],
            [
                'title' => 'Administrar', 
                'route' => 'biblioteca/administrar',
                'permission' => '03_AdministrarBiblioteca.php'
            ],
        ]
    ],
    [
        'title' => 'Manual de Funciones',
        'icon' => 'fas fa-cogs',
        'permission' => '01_ManualFunciones.php',
        'children' => [
            [
                'title' => 'Ver Manual', 
                'route' => 'manual/funciones',
                'permission' => '01_ManualFunciones.php'
            ],
        ]
    ],
    [
        'title' => 'Gestión Tareas',
        'icon' => 'fas fa-clock',
        'permission' => ['01_AdministrarTareas.php', '01_AdministrarProgramacion.php', '01_CalendarioTama.php'],
        'children' => [
            [
                'title' => 'Calendario', 
                'route' => 'calendario/tama',
                'permission' => '01_CalendarioTama.php'
            ],
            [
                'title' => 'Tareas', 
                'route' => 'tareas/administrar',
                'permission' => '01_AdministrarTareas.php'
            ],
            [
                'title' => 'Programación', 
                'route' => 'programacion/administrar',
                'permission' => '01_AdministrarProgramacion.php'
            ],
            [
                'title' => 'Ver Programación', 
                'route' => 'programacion/visualizar',
                'permission' => '09_VisualizarProgramacion.php'
            ],
            [
                'title' => 'Alertas', 
                'route' => 'alertas/programa',
                'permission' => '01_AlertaPrograma.php'
            ],
        ]
    ],
    [
        'title' => 'Capacitaciones',
        'icon' => 'fas fa-graduation-cap',
        'permission' => ['01_AdministrarCapacitaciones.php', '06_AlertaCapacitacionesTotal.php', '06_AlertaCapacitacionesProceso.php'],
        'children' => [
            [
                'title' => 'Administrar', 
                'route' => 'capacitaciones/administrar',
                'permission' => '01_AdministrarCapacitaciones.php'
            ],
            [
                'title' => 'Alertas Total', 
                'route' => 'capacitaciones/alertas-total',
                'permission' => '06_AlertaCapacitacionesTotal.php'
            ],
            [
                'title' => 'Alertas Proceso', 
                'route' => 'capacitaciones/alertas-proceso',
                'permission' => '06_AlertaCapacitacionesProceso.php'
            ],
        ]
    ],
    [
        'title' => 'Gestión Dosier',
        'icon' => 'fas fa-folder',
        'permission' => ['01_AdministrarDosier.php', '02_AdmPQR.php', '02_AdmWPS.php', '02_AdmWPQ.php', '02_AdmRGC.php'],
        'children' => [
            [
                'title' => 'Administrar', 
                'route' => 'dosier/administrar',
                'permission' => '01_AdministrarDosier.php'
            ],
            [
                'title' => 'PQR', 
                'route' => 'dosier/pqr',
                'permission' => '02_AdmPQR.php'
            ],
            [
                'title' => 'WPS', 
                'route' => 'dosier/wps',
                'permission' => '02_AdmWPS.php'
            ],
            [
                'title' => 'WPQ', 
                'route' => 'dosier/wpq',
                'permission' => '02_AdmWPQ.php'
            ],
            [
                'title' => 'RGC', 
                'route' => 'dosier/rgc',
                'permission' => '02_AdmRGC.php'
            ],
        ]
    ],
    [
        'title' => 'Consumibles',
        'icon' => 'fas fa-box',
        'permission' => ['01_AdministrarConsumibles.php', '02_VerConsumibles.php'],
        'children' => [
            [
                'title' => 'Administrar', 
                'route' => 'consumibles/administrar',
                'permission' => '01_AdministrarConsumibles.php'
            ],
            [
                'title' => 'Ver', 
                'route' => 'consumibles/ver',
                'permission' => '02_VerConsumibles.php'
            ],
        ]
    ],
    [
        'title' => 'SAC - SNC',
        'icon' => 'fas fa-sync-alt',
        'permission' => ['01_ListadoSAC.php', '02_AdministrarSAC.php', '03_ListadoSNC.php', '04_AdministrarSNC.php', '05_Dashboard.php'],
        'children' => [
            [
                'title' => 'Dashboard', 
                'route' => 'sac-snc/dashboard',
                'permission' => '05_Dashboard.php'
            ],
            [
                'title' => 'Listado SAC', 
                'route' => 'sac-snc/listado-sac',
                'permission' => '01_ListadoSAC.php'
            ],
            [
                'title' => 'Administrar SAC', 
                'route' => 'sac-snc/administrar-sac',
                'permission' => '02_AdministrarSAC.php'
            ],
            [
                'title' => 'Listado SNC', 
                'route' => 'sac-snc/listado-snc',
                'permission' => '03_ListadoSNC.php'
            ],
            [
                'title' => 'Administrar SNC', 
                'route' => 'sac-snc/administrar-snc',
                'permission' => '04_AdministrarSNC.php'
            ],
        ]
    ],
    ['separator' => 'Reportes'],
    [
        'title' => 'Reportes',
        'icon' => 'fas fa-chart-bar',
        'route' => 'reportes',
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
