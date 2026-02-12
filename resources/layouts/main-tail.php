<?php
// resources/layouts/main-tail.php - Layout principal con Tailwind CSS
$title = $title ?? 'Sistema Administrativo';
$user = current_user();

$user = [
    'name' => $user['name'] ?? 'Usuario',
    'area' => $user['area'] ?? 'Área Desconocida',
    'last_login' => $user['last_login'] ?? 'Nunca',
    'email' => $user['email'] ?? 'No registrado',
    'image' => assetPublicImages( '/uploads/' . ($user['image'] ?? '3-1.png')),
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title) ?> | EMPRESA</title>
  <link rel="icon" href="<?= assetPublicImages('favicon.ico') ?>" type="image/x-icon">

  <!-- Google Font: Inter -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= fontawesome('css/all.min.css') ?>">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="<?= asset('DataTables-2.1.8/datatables.min.css') ?>">
  <!-- Select2 CSS -->
  <link rel="stylesheet" href="<?= asset('select2/select2.min.css') ?>" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Driver.js CSS y JS (CDN) -->
  <script src="https://cdn.jsdelivr.net/npm/driver.js@latest/dist/driver.js.iife.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@latest/dist/driver.css"/>
  <!-- Tailwind CSS -->
  <link rel="stylesheet" href="<?= asset('tailwind.css') ?>">
  <!-- Estilos específicos de página -->
  <?= $pageStyles ?? '' ?>
</head>
<body class="bg-gray-50 font-inter antialiased">

<!-- Layout Container -->
<div class="min-h-screen bg-gray-50 flex">
  
  <!-- Sidebar Mobile Overlay -->
  <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>
  
  <!-- Sidebar -->
  <aside id="sidebar" class="w-44 bg-slate-800 flex-shrink-0 h-screen relative z-40 transition-all duration-300 ease-in-out flex-col hidden lg:flex">
    <!-- Logo -->
    <div class="flex items-center h-16 px-3 bg-slate-900 border-b border-slate-700">
      <div class="flex items-center space-x-2">
        <img src="<?= assetPublicImages('logito.png') ?>" alt="Logo" class="w-5 h-5 object-contain">
        <span class="text-white font-medium text-sm sidebar-text">EMPRESA</span>
      </div>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto">
      <?php include RESOURCES_PATH . '/partials/sidebar-menu-tail.php'; ?>
    </nav>
    
    <!-- Sidebar Footer -->
    <div class="p-3 border-t border-slate-700">
      <div class="text-xs text-slate-400 sidebar-text">
        <div>V2.0</div>
        <div>© 2025</div>
      </div>
    </div>
  </aside>

  <!-- Main Content Area -->
  <div id="main-content" class="flex-1 flex flex-col h-screen w-full">
    
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0 w-full">
      <div class="px-4 sm:px-6 lg:px-8 xl:px-12 w-full">
        <div class="flex items-center justify-between h-16 w-full">
          <!-- Left: Menu buttons -->
          <div class="flex items-center space-x-3">
            <button id="mobile-menu-btn" class="lg:hidden p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100">
              <i class="fas fa-bars text-sm"></i>
            </button>
            <button id="sidebar-toggle" class="hidden lg:block p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100" title="Contraer/Expandir Sidebar">
              <i class="fas fa-bars text-sm"></i>
            </button>
            <button id="sidebar-hide" class="hidden lg:block p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100" title="Ocultar Sidebar">
              <i class="fas fa-eye-slash text-sm"></i>
            </button>
            <div class="hidden lg:block text-sm text-gray-500">
              <span>Sistema Administrativo</span>
            </div>
          </div>
          
          <!-- Right: User menu and actions -->
          <div class="flex items-center space-x-6">
            <!-- Fullscreen -->
            <button id="fullscreen-btn" class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100" title="Pantalla Completa">
              <i class="fas fa-expand text-sm"></i>
            </button>
            
            <!-- Messages -->
            <div class="relative">
              <button class="dropdown-toggle p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100" data-target="#messages-dropdown">
                <i class="fas fa-envelope text-sm"></i>
                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-red-500 rounded-full">3</span>
              </button>
              <div id="messages-dropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border hidden z-50">
                <div class="p-4 border-b">
                  <h3 class="text-sm font-semibold text-gray-900">Mensajes</h3>
                  <p class="text-xs text-gray-500 mt-2">3 mensajes nuevos</p>
                </div>
                <div class="p-4">
                  <p class="text-sm text-gray-500">No hay mensajes nuevos</p>
                </div>
              </div>
            </div>
            
            <!-- Notifications -->
            <div class="relative">
              <button class="dropdown-toggle p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100" data-target="#notifications-dropdown">
                <i class="fas fa-bell text-sm"></i>
                <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-yellow-500 rounded-full">5</span>
              </button>
              <div id="notifications-dropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border hidden z-50">
                <div class="p-4 border-b">
                  <h3 class="text-sm font-semibold text-gray-900">Notificaciones</h3>
                  <p class="text-xs text-gray-500 mt-1">5 notificaciones nuevas</p>
                </div>
                <div class="p-4">
                  <p class="text-sm text-gray-500">No hay notificaciones nuevas</p>
                </div>
              </div>
            </div>
            
            <!-- User Menu -->
            <div class="relative">
              <button class="dropdown-toggle flex items-center space-x-2 px-2 py-1 rounded-lg hover:bg-gray-50 transition-colors duration-200" data-target="#user-menu-dropdown">
                <div class="relative">
                  <img src="<?= $user['image'] ?>" alt="Avatar" class="w-10 h-10 rounded-full object-cover ring-1 ring-gray-300">
                  <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-400 rounded-full border-2 border-white"></div>
                </div>
                <div class="md:block text-left">
                  <p class="text-sm font-medium text-gray-700"><?= htmlspecialchars($user['name']) ?></p>
                  <p class="text-xs text-gray-500"><?= htmlspecialchars($user['area']) ?></p>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
              </button>
              <div id="user-menu-dropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 hidden z-50">
                <div class="p-4 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
                  <div class="items-start space-x-3 items-center">
                    <div class="relative flex-shrink-0 w-10 h-10">
                      <img src="<?= $user['image'] ?>" alt="Avatar" class="w-10 h-10 rounded-full object-cover ring-2 ring-white shadow-sm">
                      <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-400 rounded-full border-2 border-white"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-semibold text-gray-900 truncate"><?= htmlspecialchars($user['name']) ?></p>
                      <p class="text-xs text-blue-600 font-medium"><?= htmlspecialchars($user['area']) ?></p>
                      <p class="text-xs text-gray-500 mt-0.5"><?= htmlspecialchars($user['email']) ?></p>
                      <div class="flex items-center mt-1">
                        <div class="w-2 h-2 bg-green-400 rounded-full mr-1.5"></div>
                        <span class="text-xs text-gray-500">En línea</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="py-2">
                  <a href="<?= url('mi-perfil') ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-user w-4 h-4 mr-3 text-gray-400"></i>Mi Perfil
                  </a>
                  <div class="border-t border-gray-100 my-1"></div>
                  <a href="<?= url('app/auth/logout.php') ?>" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                    <i class="fas fa-sign-out-alt w-4 h-4 mr-3 text-red-500"></i>Cerrar Sesión
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200 flex-shrink-0 w-full">
      <div class="px-4 py-4 sm:px-6 lg:px-8 xl:px-12 w-full">
        <div class="flex items-center justify-between w-full">
          <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($title) ?></h1>
            <p class="mt-1 text-sm text-gray-600">Gestiona tu sistema administrativo</p>
          </div>
          <nav class="flex items-center space-x-2" aria-label="Breadcrumb">
            <a href="<?= url('dashboard') ?>" class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">
              <i class="fas fa-home mr-2"></i>Inicio
            </a>
            <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
            <span class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-600 bg-white rounded-lg shadow-sm">
              <?= htmlspecialchars($title) ?>
            </span>
          </nav>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto w-full flex flex-col">
      <div class="flex-1 px-4 py-4 sm:px-6 lg:px-8 xl:px-12 w-full">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 h-full min-h-[calc(100vh-200px)] w-full">
          <div class="p-4 sm:p-6 w-full h-full">
            <?= $content ?? '<div class="text-center py-12"><p class="text-gray-500">Contenido no disponible</p></div>' ?>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 w-full mt-auto bottom-0 fixed">
      <div class="px-4 py-3 sm:px-6 lg:px-8 xl:px-12">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-2 md:space-y-0 w-full">
          <div class="flex items-center space-x-3">
            <img src="<?= assetPublicImages('logito.png') ?>" alt="Logo" class="w-10 h-10 object-contain ">
            <div class="text-sm text-gray-600">
              <strong>Copyright © 2025 <a href="#" target="_blank" class="text-blue-600 hover:text-blue-800 transition-colors">EMPRESA Ingenieros</a>.</strong>
              <div class="text-xs text-gray-500 mt-1">Todos los derechos reservados.</div>
            </div>
            <div class="text-sm text-gray-500 ml-4 border-l border-gray-300 pl-4">
              <span class="font-semibold">Version</span> 2.0 Tailwind
            </div>
            <div class="flex space-x-2 ml-4 border-l border-gray-300 pl-4">
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                Online
              </span>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
</div>

<!-- jQuery -->
<script src="<?= asset('jquery/jquery.min.js') ?>"></script>
<!-- Moment.js -->
<script src="<?= asset('moment/moment.min.js') ?>"></script>
<!-- DataTables -->
<script src="<?= asset('DataTables-2.1.8/datatables.min.js') ?>"></script>
<!-- Select2 -->
<script src="<?= asset('select2/select2.min.js') ?>"></script>

<!-- Scripts específicos de página -->
<?= $pageScripts ?? '' ?>

<script>
$(document).ready(function() {
    // Restaurar estado del sidebar
    const sidebarState = localStorage.getItem('sidebar-state-tailwind');
    const $sidebar = $('#sidebar');
    
    if (sidebarState === 'collapsed') {
        $sidebar.removeClass('w-44').addClass('w-12');
        $('.sidebar-text').addClass('hidden');
        $('body').addClass('sidebar-collapse');
    }
    
    // Toggle sidebar (contraer/expandir)
    $('#sidebar-toggle').click(function(e) {
        e.preventDefault();
        const $sidebar = $('#sidebar');
        const isCollapsed = $sidebar.hasClass('w-12');
        
        if (isCollapsed) {
            // Expandir
            $sidebar.removeClass('w-12').addClass('w-44');
            $('.sidebar-text').removeClass('hidden');
            $('body').removeClass('sidebar-collapse');
            localStorage.setItem('sidebar-state-tailwind', 'expanded');
        } else {
            // Contraer  
            $sidebar.removeClass('w-44').addClass('w-12');
            $('.sidebar-text').addClass('hidden');
            $('body').addClass('sidebar-collapse');
            // Cerrar submenús al contraer
            $('.submenu').addClass('hidden').removeClass('block');
            $('.fa-chevron-right').removeClass('rotate-90');
            localStorage.setItem('sidebar-state-tailwind', 'collapsed');
        }
    });
    
    // Ocultar sidebar completamente (solo en mobile)
    $('#sidebar-hide').click(function(e) {
        e.preventDefault();
        const $sidebar = $('#sidebar');
        
        // Solo funciona en mobile
        if ($(window).width() < 1024) {
            $sidebar.toggleClass('hidden');
            $('#sidebar-overlay').addClass('hidden');
        }
    });
    
    // Mobile menu
    $('#mobile-menu-btn').click(function() {
        const $sidebar = $('#sidebar');
        const $overlay = $('#sidebar-overlay');
        
        // Mostrar sidebar como overlay en mobile
        $sidebar.removeClass('hidden lg:block lg:flex').addClass('fixed block flex');
        $sidebar.css({
            'top': '0',
            'left': '0',
            'width': '11rem', // w-44
            'z-index': '50'
        });
        $overlay.removeClass('hidden');
    });
    
    $('#sidebar-overlay').click(function() {
        const $sidebar = $('#sidebar');
        $(this).addClass('hidden');
        
        // Restaurar sidebar a estado original
        $sidebar.removeClass('fixed block flex').addClass('hidden lg:block lg:flex');
        $sidebar.css({
            'top': '',
            'left': '',
            'width': '',
            'z-index': ''
        });
    });
    
    // Dropdowns
    $('.dropdown-toggle').click(function(e) {
        e.stopPropagation();
        const target = $(this).data('target');
        const dropdown = $(target);
        
        // Cerrar otros dropdowns
        $('[id$="-dropdown"]').not(dropdown).addClass('hidden');
        
        // Toggle este dropdown
        dropdown.toggleClass('hidden');
    });
    
    $(document).click(function() {
        $('[id$="-dropdown"]').addClass('hidden');
    });
    
    $('[id$="-dropdown"]').click(function(e) {
        e.stopPropagation();
    });
    
    // Fullscreen
    $('#fullscreen-btn').click(function() {
        if (document.fullscreenElement) {
            document.exitFullscreen();
            $(this).find('i').removeClass('fa-compress').addClass('fa-expand');
        } else {
            document.documentElement.requestFullscreen();
            $(this).find('i').removeClass('fa-expand').addClass('fa-compress');
        }
    });
    
    // Toggle submenús del sidebar
    $('[data-submenu-toggle]').click(function(e) {
        e.preventDefault();
        const $button = $(this);
        const $navItem = $button.closest('.nav-item');
        const $submenu = $navItem.find('.submenu');
        const $arrow = $button.find('.fa-chevron-right');
        
        // Cerrar otros submenús del mismo nivel
        $navItem.siblings('.nav-item').each(function() {
            $(this).find('.submenu').addClass('hidden').removeClass('block');
            $(this).find('.fa-chevron-right').removeClass('rotate-90');
        });
        
        // Toggle este submenú
        if ($submenu.hasClass('hidden')) {
            $submenu.removeClass('hidden').addClass('block');
            $arrow.addClass('rotate-90');
        } else {
            $submenu.addClass('hidden').removeClass('block');
            $arrow.removeClass('rotate-90');
        }
    });
});
</script>

<style>
/* Sidebar contraído - tooltip */
.sidebar-collapse .sidebar-text {
  display: none;
}

.sidebar-collapse #sidebar:hover .nav-item .sidebar-text {
  display: block !important;
  position: absolute;
  left: calc(100% + 0.5rem);
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0, 0, 0, 0.9);
  color: white;
  padding: 0.5rem 0.75rem;
  border-radius: 0.375rem;
  white-space: nowrap;
  z-index: 1000;
}

/* Transiciones suaves */
#sidebar {
  transition: width 0.3s ease;
}

/* Mobile - no tooltips */
@media (max-width: 1023px) {
  .sidebar-collapse #sidebar:hover .nav-item .sidebar-text {
    display: none !important;
  }
  
  /* Sidebar oculto por defecto en mobile */
  #sidebar {
    display: none;
  }
  
  /* Cuando se muestra en mobile, usar posición fija */
  #sidebar.fixed {
    display: flex !important;
    position: fixed !important;
  }
}
</style>

</body>
</html>