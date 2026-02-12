<?php
// resources/modules/landing/servicios.php
$title = 'Servicios';
$activePage = 'Servicios'; // Para resaltar el menú activo
$pageTitle = 'Nuestros Servicios';
$breadcrumbs = ['Inicio', 'Servicios'];

ob_start();
?>

<!-- Hero Section -->
<div class="bg-gradient-to-r from-slate-900 to-slate-700 text-white">
    <div class="container mx-auto px-6 py-20">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">
                Nuestros Servicios
            </h1>
            <p class="text-xl text-slate-200 mb-8 leading-relaxed">
                Ofrecemos soluciones integrales de ingeniería con más de 10 años de experiencia 
                en el sector industrial y tecnológico.
            </p>
            <div class="inline-flex items-center bg-white text-slate-900 px-6 py-3 rounded-lg font-medium hover:bg-slate-100 transition-colors">
                <i class="fas fa-arrow-down mr-2"></i>
                Ver Servicios
            </div>
        </div>
    </div>
</div>

<!-- Servicios Section -->
<div class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Ingeniería Industrial -->
            <div class="bg-white rounded-lg p-8 shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-industry text-2xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Ingeniería Industrial</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Optimización de procesos, mejora continua, análisis de productividad 
                    y implementación de sistemas de gestión de calidad.
                </p>
                <ul class="space-y-2 mb-6 text-gray-600">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Análisis de procesos
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Sistemas de gestión
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Mejora continua
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Control de calidad
                    </li>
                </ul>
                <button class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Solicitar Cotización
                </button>
            </div>

            <!-- Consultoría Tecnológica -->
            <div class="bg-white rounded-lg p-8 shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-16 h-16 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-laptop-code text-2xl text-indigo-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Consultoría Tecnológica</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Desarrollo de software especializado, automatización de procesos 
                    y implementación de tecnologías emergentes.
                </p>
                <ul class="space-y-2 mb-6 text-gray-600">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Desarrollo de software
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Automatización
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Integración de sistemas
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Capacitación técnica
                    </li>
                </ul>
                <button class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                    Solicitar Cotización
                </button>
            </div>

            <!-- Gestión de Proyectos -->
            <div class="bg-white rounded-lg p-8 shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-16 h-16 bg-amber-100 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-project-diagram text-2xl text-amber-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Gestión de Proyectos</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Planificación, ejecución y control de proyectos industriales 
                    con metodologías ágiles y tradicionales.
                </p>
                <ul class="space-y-2 mb-6 text-gray-600">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Planificación estratégica
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Control de avance
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Gestión de riesgos
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Metodologías ágiles
                    </li>
                </ul>
                <button class="w-full bg-amber-600 text-white py-3 px-6 rounded-lg hover:bg-amber-700 transition-colors font-medium">
                    Solicitar Cotización
                </button>
            </div>

            <!-- Capacitación -->
            <div class="bg-white rounded-lg p-8 shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-chalkboard-teacher text-2xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Capacitación Empresarial</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Programas de entrenamiento especializado para equipos de trabajo 
                    en herramientas industriales y tecnológicas.
                </p>
                <ul class="space-y-2 mb-6 text-gray-600">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Cursos especializados
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Workshops prácticos
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Certificaciones
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Mentoring técnico
                    </li>
                </ul>
                <button class="w-full bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition-colors font-medium">
                    Solicitar Información
                </button>
            </div>

            <!-- Auditoría y Compliance -->
            <div class="bg-white rounded-lg p-8 shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-16 h-16 bg-red-100 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-shield-alt text-2xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Auditoría y Compliance</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Evaluación de procesos, cumplimiento normativo y certificaciones 
                    de calidad según estándares internacionales.
                </p>
                <ul class="space-y-2 mb-6 text-gray-600">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Auditorías internas
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        ISO 9001, 14001
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Compliance normativo
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Documentación
                    </li>
                </ul>
                <button class="w-full bg-red-600 text-white py-3 px-6 rounded-lg hover:bg-red-700 transition-colors font-medium">
                    Solicitar Auditoría
                </button>
            </div>

            <!-- Soporte Técnico -->
            <div class="bg-white rounded-lg p-8 shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mb-6">
                    <i class="fas fa-tools text-2xl text-gray-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Soporte Técnico</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">
                    Mantenimiento preventivo y correctivo de sistemas, 
                    soporte remoto 24/7 y atención especializada.
                </p>
                <ul class="space-y-2 mb-6 text-gray-600">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Soporte 24/7
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Mantenimiento preventivo
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        Diagnóstico remoto
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        SLA garantizado
                    </li>
                </ul>
                <button class="w-full bg-gray-600 text-white py-3 px-6 rounded-lg hover:bg-gray-700 transition-colors font-medium">
                    Contratar Soporte
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="py-16 bg-white">
    <div class="container mx-auto px-6 text-center">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                ¿Listo para transformar tu empresa?
            </h2>
            <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                Contacta con nuestro equipo de expertos y descubre cómo podemos 
                ayudarte a optimizar tus procesos y alcanzar tus objetivos.
            </p>
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <button class="bg-blue-600 text-white px-8 py-4 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <i class="fas fa-phone mr-2"></i>
                    Solicitar Consulta Gratuita
                </button>
                <button class="border-2 border-blue-600 text-blue-600 px-8 py-4 rounded-lg hover:bg-blue-50 transition-colors font-medium">
                    <i class="fas fa-calculator mr-2"></i>
                    Cotizar Proyecto
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Usar layout de landing (para páginas públicas)
include RESOURCES_PATH . '/layouts/landing.php';
?>