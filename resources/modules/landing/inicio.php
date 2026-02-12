<?php
// resources/modules/landing/inicio.php
$title = 'Inicio';
$activePage = 'Inicio'; // Para resaltar el menú activo
$pageTitle = 'Bienvenido';
$breadcrumbs = ['Inicio'];

ob_start();
?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
                Soluciones <span class="text-blue-200">Tecnológicas</span> Innovadoras
            </h1>
            <p class="text-xl md:text-2xl text-blue-100 mb-8 leading-relaxed max-w-3xl mx-auto">
                Optimizamos procesos empresariales con tecnología de vanguardia. 
                Más de 10 años transformando ideas en resultados tangibles.
            </p>
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <a href="<?= url('servicios') ?>" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-blue-50 transition-colors shadow-lg">
                    <i class="fas fa-cogs mr-2"></i>
                    Ver Servicios
                </a>
                <a href="<?= url('nosotros') ?>" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors">
                    <i class="fas fa-users mr-2"></i>
                    Conocer Equipo
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Servicios Destacados -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                Nuestros Servicios
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Ofrecemos soluciones integrales para empresas que buscan la excelencia operativa
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg p-8 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-industry text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Ingeniería Industrial</h3>
                <p class="text-gray-600 mb-4">
                    Optimización de procesos, mejora continua y sistemas de gestión de calidad.
                </p>
                <a href="<?= url('servicios') ?>" class="text-blue-600 font-medium hover:text-blue-700">
                    Saber más →
                </a>
            </div>
            
            <div class="bg-white rounded-lg p-8 shadow-sm hover:shadow-md transition-shadow">
                <div class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-laptop-code text-2xl text-indigo-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Consultoría Tecnológica</h3>
                <p class="text-gray-600 mb-4">
                    Desarrollo de software y automatización de procesos empresariales.
                </p>
                <a href="<?= url('servicios') ?>" class="text-indigo-600 font-medium hover:text-indigo-700">
                    Saber más →
                </a>
            </div>
            
            <div class="bg-white rounded-lg p-8 shadow-sm hover:shadow-md transition-shadow md:col-span-2 lg:col-span-1">
                <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-project-diagram text-2xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Gestión de Proyectos</h3>
                <p class="text-gray-600 mb-4">
                    Planificación y control de proyectos con metodologías ágiles.
                </p>
                <a href="<?= url('servicios') ?>" class="text-green-600 font-medium hover:text-green-700">
                    Saber más →
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Estadísticas -->
<section class="py-16 bg-blue-600">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-4 gap-8 text-center text-white">
            <div>
                <div class="text-4xl font-bold mb-2">500+</div>
                <div class="text-blue-200">Clientes Satisfechos</div>
            </div>
            <div>
                <div class="text-4xl font-bold mb-2">10+</div>
                <div class="text-blue-200">Años de Experiencia</div>
            </div>
            <div>
                <div class="text-4xl font-bold mb-2">1000+</div>
                <div class="text-blue-200">Proyectos Completados</div>
            </div>
            <div>
                <div class="text-4xl font-bold mb-2">98%</div>
                <div class="text-blue-200">Satisfacción Cliente</div>
            </div>
        </div>
    </div>
</section>

<!-- Por qué elegirnos -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-6">
        <div class="max-w-6xl mx-auto">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                        ¿Por qué elegir EMPRESA?
                    </h2>
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                <i class="fas fa-star text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">Experiencia Comprobada</h3>
                                <p class="text-gray-600">Más de 10 años optimizando procesos empresariales con resultados medibles.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                <i class="fas fa-users text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">Equipo Especializado</h3>
                                <p class="text-gray-600">Profesionales certificados en las últimas tecnologías y metodologías.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                                <i class="fas fa-rocket text-purple-600"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">Soluciones Innovadoras</h3>
                                <p class="text-gray-600">Implementamos tecnología de vanguardia adaptada a sus necesidades específicas.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-2xl p-8">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-handshake text-3xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">Compromiso Total</h3>
                        <p class="text-gray-600 mb-6">
                            Nos comprometemos con el éxito de su empresa desde la consulta inicial 
                            hasta el soporte post-implementación.
                        </p>
                        <div class="bg-blue-600 text-white px-6 py-3 rounded-lg inline-block">
                            <span class="font-semibold">Garantía de Satisfacción</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Final -->
<section class="py-16 bg-gray-800 text-white">
    <div class="container mx-auto px-6 text-center">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">
                ¿Listo para transformar su empresa?
            </h2>
            <p class="text-xl text-gray-300 mb-8">
                Solicite una consulta gratuita y descubra cómo podemos optimizar sus procesos
            </p>
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <a href="<?= url('sistema') ?>" class="bg-blue-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    <i class="fas fa-calendar mr-2"></i>
                    Agendar Consulta
                </a>
                <a href="<?= url('servicios') ?>" class="border-2 border-gray-300 text-gray-300 px-8 py-4 rounded-lg font-semibold hover:bg-gray-300 hover:text-gray-800 transition-colors">
                    <i class="fas fa-info-circle mr-2"></i>
                    Más Información
                </a>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();

// Usar layout de landing (para páginas públicas)
include RESOURCES_PATH . '/layouts/landing.php';
?>