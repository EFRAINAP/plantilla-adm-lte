<?php
/**
 * Página de Servicios - Sitio Público
 * resources/modules/landing/servicios.php
 */

$page_title = 'Servicios - EMPRESA Ingenieros';
$page_description = 'Conoce todos nuestros servicios de ingeniería, consultoría y desarrollo industrial.';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="<?= $page_description ?>">
    
    <!-- Bootstrap CSS -->
    <link href="<?= bootstrap('css/bootstrap.min.css') ?>" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="<?= fontawesome('css/all.min.css') ?>" rel="stylesheet">
    
    <style>
        .hero-services {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0 60px;
        }
        
        .service-card {
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .service-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .cta-section {
            background: #f8f9fa;
            padding: 80px 0;
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero-services">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">Nuestros Servicios</h1>
                    <p class="lead mb-5">
                        Ofrecemos soluciones integrales de ingeniería con más de 10 años de experiencia 
                        en el sector industrial y tecnológico.
                    </p>
                    <a href="#servicios" class="btn btn-light btn-lg px-5 py-3">
                        <i class="fas fa-arrow-down me-2"></i>
                        Ver Servicios
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Servicios Section -->
    <section id="servicios" class="py-5">
        <div class="container">
            <div class="row g-4">
                
                <!-- Ingeniería Industrial -->
                <div class="col-lg-4 col-md-6">
                    <div class="card service-card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="service-icon text-primary">
                                <i class="fas fa-industry"></i>
                            </div>
                            <h5 class="card-title mb-3">Ingeniería Industrial</h5>
                            <p class="card-text text-muted mb-4">
                                Optimización de procesos, mejora continua, análisis de productividad 
                                y implementación de sistemas de gestión de calidad.
                            </p>
                            <ul class="list-unstyled text-start mb-4">
                                <li><i class="fas fa-check text-success me-2"></i> Análisis de procesos</li>
                                <li><i class="fas fa-check text-success me-2"></i> Sistemas de gestión</li>
                                <li><i class="fas fa-check text-success me-2"></i> Mejora continua</li>
                                <li><i class="fas fa-check text-success me-2"></i> Control de calidad</li>
                            </ul>
                            <a href="<?= Config::url('/contacto') ?>" class="btn btn-outline-primary">
                                Solicitar Cotización
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Consultoría Tecnológica -->
                <div class="col-lg-4 col-md-6">
                    <div class="card service-card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="service-icon text-info">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                            <h5 class="card-title mb-3">Consultoría Tecnológica</h5>
                            <p class="card-text text-muted mb-4">
                                Desarrollo de software especializado, automatización de procesos 
                                y implementación de tecnologías emergentes.
                            </p>
                            <ul class="list-unstyled text-start mb-4">
                                <li><i class="fas fa-check text-success me-2"></i> Desarrollo de software</li>
                                <li><i class="fas fa-check text-success me-2"></i> Automatización</li>
                                <li><i class="fas fa-check text-success me-2"></i> Integración de sistemas</li>
                                <li><i class="fas fa-check text-success me-2"></i> Capacitación técnica</li>
                            </ul>
                            <a href="<?= Config::url('/contacto') ?>" class="btn btn-outline-info">
                                Solicitar Cotización
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Gestión de Proyectos -->
                <div class="col-lg-4 col-md-6">
                    <div class="card service-card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="service-icon text-warning">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <h5 class="card-title mb-3">Gestión de Proyectos</h5>
                            <p class="card-text text-muted mb-4">
                                Planificación, ejecución y control de proyectos industriales 
                                con metodologías ágiles y tradicionales.
                            </p>
                            <ul class="list-unstyled text-start mb-4">
                                <li><i class="fas fa-check text-success me-2"></i> Planificación estratégica</li>
                                <li><i class="fas fa-check text-success me-2"></i> Control de avance</li>
                                <li><i class="fas fa-check text-success me-2"></i> Gestión de riesgos</li>
                                <li><i class="fas fa-check text-success me-2"></i> Metodologías ágiles</li>
                            </ul>
                            <a href="<?= Config::url('/contacto') ?>" class="btn btn-outline-warning">
                                Solicitar Cotización
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Capacitación -->
                <div class="col-lg-4 col-md-6">
                    <div class="card service-card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="service-icon text-success">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <h5 class="card-title mb-3">Capacitación Empresarial</h5>
                            <p class="card-text text-muted mb-4">
                                Programas de entrenamiento especializado para equipos de trabajo 
                                en herramientas industriales y tecnológicas.
                            </p>
                            <ul class="list-unstyled text-start mb-4">
                                <li><i class="fas fa-check text-success me-2"></i> Cursos especializados</li>
                                <li><i class="fas fa-check text-success me-2"></i> Workshops prácticos</li>
                                <li><i class="fas fa-check text-success me-2"></i> Certificaciones</li>
                                <li><i class="fas fa-check text-success me-2"></i> Mentoring técnico</li>
                            </ul>
                            <a href="<?= Config::url('/contacto') ?>" class="btn btn-outline-success">
                                Solicitar Información
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Auditoría y Compliance -->
                <div class="col-lg-4 col-md-6">
                    <div class="card service-card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="service-icon text-danger">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h5 class="card-title mb-3">Auditoría y Compliance</h5>
                            <p class="card-text text-muted mb-4">
                                Evaluación de procesos, cumplimiento normativo y certificaciones 
                                de calidad según estándares internacionales.
                            </p>
                            <ul class="list-unstyled text-start mb-4">
                                <li><i class="fas fa-check text-success me-2"></i> Auditorías internas</li>
                                <li><i class="fas fa-check text-success me-2"></i> ISO 9001, 14001</li>
                                <li><i class="fas fa-check text-success me-2"></i> Compliance normativo</li>
                                <li><i class="fas fa-check text-success me-2"></i> Documentación</li>
                            </ul>
                            <a href="<?= Config::url('/contacto') ?>" class="btn btn-outline-danger">
                                Solicitar Auditoría
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Soporte Técnico -->
                <div class="col-lg-4 col-md-6">
                    <div class="card service-card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="service-icon text-secondary">
                                <i class="fas fa-tools"></i>
                            </div>
                            <h5 class="card-title mb-3">Soporte Técnico</h5>
                            <p class="card-text text-muted mb-4">
                                Mantenimiento preventivo y correctivo de sistemas, 
                                soporte remoto 24/7 y atención especializada.
                            </p>
                            <ul class="list-unstyled text-start mb-4">
                                <li><i class="fas fa-check text-success me-2"></i> Soporte 24/7</li>
                                <li><i class="fas fa-check text-success me-2"></i> Mantenimiento preventivo</li>
                                <li><i class="fas fa-check text-success me-2"></i> Diagnóstico remoto</li>
                                <li><i class="fas fa-check text-success me-2"></i> SLA garantizado</li>
                            </ul>
                            <a href="<?= Config::url('/contacto') ?>" class="btn btn-outline-secondary">
                                Contratar Soporte
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="h1 mb-4">¿Listo para transformar tu empresa?</h2>
                    <p class="lead text-muted mb-5">
                        Contacta con nuestro equipo de expertos y descubre cómo podemos 
                        ayudarte a optimizar tus procesos y alcanzar tus objetivos.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="<?= Config::url('/contacto') ?>" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-phone me-2"></i>
                            Solicitar Consulta Gratuita
                        </a>
                        <a href="<?= Config::url('/cotiza') ?>" class="btn btn-outline-primary btn-lg px-5">
                            <i class="fas fa-calculator me-2"></i>
                            Cotizar Proyecto
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Navigation Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 EMPRESA Ingenieros. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex gap-3 justify-content-md-end">
                        <a href="<?= Config::url('/') ?>" class="text-white">Inicio</a>
                        <a href="<?= Config::url('/nosotros') ?>" class="text-white">Nosotros</a>
                        <a href="<?= Config::url('/servicios') ?>" class="text-white">Servicios</a>
                        <a href="<?= Config::url('/contacto') ?>" class="text-white">Contacto</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="<?= bootstrap('js/bootstrap.bundle.min.js') ?>"></script>

</body>
</html>