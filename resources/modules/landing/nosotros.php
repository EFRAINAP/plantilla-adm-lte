<?php
// resources/modules/landing/nosotros.php
$title = 'Nosotros';
$activePage = 'Nosotros'; // Para resaltar el menú activo
$pageTitle = 'Acerca de Nosotros';
$breadcrumbs = ['Inicio', 'Nosotros'];

ob_start();
?>

<!-- Hero Section EXTREMO -->
<div class="bg-gradient-to-br from-pink-600 via-purple-700 via-blue-600 via-green-500 to-yellow-500 text-white relative overflow-hidden animate-gradient-x">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="container mx-auto px-6 py-20 relative z-10">
        <div class="text-center">
            <h1 class="text-5xl md:text-8xl font-black mb-6 animate-pulse">
                🔥 CONOCE NUESTRO <span class="text-yellow-300 animate-bounce inline-block">SÚPER EQUIPO</span> 🔥
            </h1>
            <p class="text-2xl md:text-4xl mb-10 text-white font-bold animate-wiggle">
                🚀 Transformamos ideas en MAGIA TECNOLÓGICA 🚀
            </p>
            <div class="flex justify-center mb-8">
                <div class="w-32 h-2 bg-gradient-to-r from-red-400 to-yellow-400 rounded-full animate-pulse"></div>
            </div>
            <div class="text-6xl animate-spin">
                ⚡🌈⚡
            </div>
        </div>
    </div>
</div>

<!-- Nuestra Historia -->
<div class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    Nuestra Historia
                </h2>
                <p class="text-lg text-gray-600">
                    Más de 10 años creando soluciones que marcan la diferencia
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="prose prose-lg text-gray-700">
                        <p class="text-xl leading-relaxed mb-6">
                            Desde <strong class="text-blue-600">2014</strong>, nos hemos dedicado a desarrollar 
                            sistemas administrativos que simplifican la gestión empresarial.
                        </p>
                        <p class="mb-6">
                            Comenzamos como un pequeño equipo con una gran visión: crear herramientas 
                            <strong class="text-yellow-600">tecnológicas</strong> que realmente mejoren la productividad de las empresas.
                        </p>
                        <p class="mb-6">
                            Hoy, con <span class="font-semibold text-green-600">más de 500 clientes satisfechos</span> 
                            y proyectos en toda Latinoamérica, seguimos <strong class="text-purple-300">tecnológicas</strong> para ofrecerte las mejores soluciones.
                        </p>
                    </div>
                </div>
                
                <div class="relative">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8">
                        <div class="grid grid-cols-2 gap-6 text-center">
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div class="text-3xl font-bold text-blue-600 mb-1">500+</div>
                                <div class="text-sm text-gray-600">Clientes</div>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div class="text-3xl font-bold text-green-600 mb-1">10+</div>
                                <div class="text-sm text-gray-600">Años</div>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div class="text-3xl font-bold text-purple-600 mb-1">1000+</div>
                                <div class="text-sm text-gray-600">Proyectos</div>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <div class="text-3xl font-bold text-orange-600 mb-1">50+</div>
                                <div class="text-sm text-gray-600">Empresas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Nuestros Valores -->
<div class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                Nuestros Valores
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Los principios que guían cada decisión y cada línea de código que escribimos
            </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Valor 1 -->
            <div class="bg-white rounded-xl p-8 shadow-sm hover:shadow-lg transition-shadow duration-300">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-lightbulb text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4 text-center">Innovación</h3>
                <p class="text-gray-600 text-center">
                    Buscamos constantemente nuevas formas de resolver problemas y mejorar procesos 
                    con tecnología de vanguardia.
                </p>
            </div>
            
            <!-- Valor 2 -->
            <div class="bg-white rounded-xl p-8 shadow-sm hover:shadow-lg transition-shadow duration-300">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-handshake text-2xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4 text-center">Compromiso</h3>
                <p class="text-gray-600 text-center">
                    Nos comprometemos al 100% con cada proyecto, desde la primera reunión 
                    hasta el soporte post-implementación.
                </p>
            </div>
            
            <!-- Valor 3 -->
            <div class="bg-white rounded-xl p-8 shadow-sm hover:shadow-lg transition-shadow duration-300">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-users text-2xl text-purple-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4 text-center">Trabajo en Equipo</h3>
                <p class="text-gray-600 text-center">
                    Creemos en la colaboración y el intercambio de ideas para crear 
                    soluciones más robustas y creativas.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Nuestro Equipo -->
<div class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                Conoce al Equipo
            </h2>
            <p class="text-lg text-gray-600">
                Profesionales apasionados por la tecnología y la excelencia
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Miembro 1 -->
            <div class="text-center group">
                <div class="relative mb-6">
                    <div class="w-32 h-32 mx-auto bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                        <i class="fas fa-user text-4xl text-white"></i>
                    </div>
                    <div class="absolute inset-0 bg-blue-600 rounded-full opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Ana García</h3>
                <p class="text-blue-600 font-medium mb-2">CEO & Fundadora</p>
                <p class="text-gray-600 text-sm">
                    Especialista en gestión empresarial con más de 15 años de experiencia
                </p>
            </div>
            
            <!-- Miembro 2 -->
            <div class="text-center group">
                <div class="relative mb-6">
                    <div class="w-32 h-32 mx-auto bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                        <i class="fas fa-code text-4xl text-white"></i>
                    </div>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Carlos Rodríguez</h3>
                <p class="text-green-600 font-medium mb-2">CTO & Lead Developer</p>
                <p class="text-gray-600 text-sm">
                    Arquitecto de software con expertise en PHP, JavaScript y sistemas distribuidos
                </p>
            </div>
            
            <!-- Miembro 3 -->
            <div class="text-center group">
                <div class="relative mb-6">
                    <div class="w-32 h-32 mx-auto bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                        <i class="fas fa-palette text-4xl text-white"></i>
                    </div>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">María López</h3>
                <p class="text-purple-600 font-medium mb-2">UX/UI Designer</p>
                <p class="text-gray-600 text-sm">
                    Diseñadora creativa enfocada en crear experiencias intuitivas y atractivas
                </p>
            </div>
            
            <!-- Miembro 4 -->
            <div class="text-center group">
                <div class="relative mb-6">
                    <div class="w-32 h-32 mx-auto bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                        <i class="fas fa-chart-line text-4xl text-white"></i>
                    </div>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Pedro Martínez</h3>
                <p class="text-orange-600 font-medium mb-2">Business Analyst</p>
                <p class="text-gray-600 text-sm">
                    Experto en análisis de procesos y optimización de flujos de trabajo empresariales
                </p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="py-16 bg-gradient-to-r from-blue-600 to-purple-600">
    <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
            ¿Listo para Transformar tu Empresa?
        </h2>
        <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
            Únete a las más de 500 empresas que ya confían en nuestras soluciones tecnológicas
        </p>
        <div class="space-y-4 md:space-y-0 md:space-x-4 md:flex md:justify-center">
            <button class="bg-white text-blue-600 font-semibold py-3 px-8 rounded-lg hover:bg-blue-50 transition-colors duration-200 shadow-lg hover:shadow-xl">
                <i class="fas fa-phone mr-2"></i>
                Contáctanos
            </button>
            <button class="bg-transparent border-2 border-white text-white font-semibold py-3 px-8 rounded-lg hover:bg-white hover:text-blue-600 transition-all duration-200">
                <i class="fas fa-play mr-2"></i>
                Ver Demo
            </button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Usar layout de landing (para páginas públicas)
include RESOURCES_PATH . '/layouts/landing.php';
?>