<?php
// resources/layouts/landing.php - Layout para páginas públicas/landing
$title = $title ?? 'Sistema Web';
$activePage = $activePage ?? '';
function navActive($route, $title = null) {
    return $title === $route
        ? 'text-blue-600 font-semibold border-b-2 border-blue-600'
        : 'text-gray-600 hover:text-blue-600 font-medium transition-colors';
}

function navActiveMobile($route, $activePage = null) {
    return $activePage === $route
        ? 'block py-2 text-blue-600 font-semibold border-b-2 border-blue-600'
        : 'block py-2 text-gray-600 hover:text-blue-600 font-medium transition-colors';
}

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
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="<?= asset('tailwind.css') ?>">
    
    <!-- Estilos específicos de página -->
    <?= $pageStyles ?? '' ?>
</head>
<body class="bg-white">

    <!-- Navbar Profesional -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <img src="<?= assetPublicImages('logito.png') ?>" alt="Logo" class="h-10 w-auto mr-3">
                    <span class="text-xl font-bold text-gray-800">EMPRESA</span>
                </div>
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="<?= url('inicio') ?>" class="<?= navActive('Inicio', $activePage) ?>">Inicio</a>
                    <a href="<?= url('servicios') ?>" class="<?= navActive('Servicios', $activePage) ?>">Servicios</a>
                    <a href="<?= url('nosotros') ?>" class="<?= navActive('Nosotros', $activePage) ?>">Nosotros</a>
                    <a href="<?= url('sistema') ?>" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">Iniciar Sesión</a>
                </div>
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button class="text-gray-600 hover:text-gray-900" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 pt-4 pb-4">
                <a href="<?= url('inicio') ?>" class="<?= navActiveMobile('Inicio', $activePage) ?>">Inicio</a>
                <a href="<?= url('servicios') ?>" class="<?= navActiveMobile('Servicios', $activePage) ?>">Servicios</a>
                <a href="<?= url('nosotros') ?>" class="<?= navActiveMobile('Nosotros', $activePage) ?>">Nosotros</a>
                <a href="<?= url('sistema') ?>" class="block py-2 mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-center hover:bg-blue-700 transition-colors">Iniciar Sesión</a>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <main>
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <h3 class="text-2xl font-bold mb-4">EMPRESA</h3>
                    <p class="text-gray-300 mb-6 max-w-md">
                        Soluciones tecnológicas innovadoras para empresas que buscan 
                        optimizar sus procesos y alcanzar la excelencia operativa.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Enlaces</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="<?= url('inicio') ?>" class="hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="<?= url('servicios') ?>" class="hover:text-white transition-colors">Servicios</a></li>
                        <li><a href="<?= url('nosotros') ?>" class="hover:text-white transition-colors">Nosotros</a></li>
                        <li><a href="<?= url('sistema') ?>" class="hover:text-white transition-colors">Sistema</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contacto</h4>
                    <ul class="space-y-2 text-gray-300">
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3 text-blue-400"></i> 
                            +1 234 567 8900
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-blue-400"></i> 
                            info@empresa.com
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-3 text-blue-400"></i> 
                            Ciudad, País
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">© <?= date('Y') ?> EMPRESA. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts específicos de página -->
    <?= $pageScripts ?? '' ?>
    
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>