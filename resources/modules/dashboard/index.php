<?php
/**
 * Dashboard Principal
 * resources/modules/dashboard/index.php
 */
$title = $title ?? "Dashboard";


// Obtener información del usuario actual
$user = current_user();
$user_name = $user['name'] ?? $user['username'] ?? 'Usuario';
$user_level = $user['user_level'] ?? 0;
$is_admin = ($user_level == 1);
  
  // Definir módulos principales del sistema
  $modules = [
    [
      'name' => 'Gestión de Usuarios',
      'description' => 'Administrar usuarios, perfiles y accesos del sistema',
      'icon' => 'bi-people-fill',
      'url' => '04_Usuarios/01_AdministrarUsuarios.php',
      'color' => 'primary',
      'permission_page' => '01_AdministrarUsuarios.php'
    ],
    [
      'name' => 'Documentos ISO',
      'description' => 'Gestión de documentos del sistema de calidad ISO',
      'icon' => 'bi-file-earmark-text-fill',
      'url' => '06_DocumentosISO/01_ListadoDocumentos.php',
      'color' => 'success',
      'permission_page' => '01_ListadoDocumentos.php'
    ],
    /*[
      'name' => 'Calendario ISO',
      'description' => 'Visualización de eventos y fechas importantes',
      'icon' => 'bi-calendar-event-fill',
      'url' => '07_CalendarioISO/01_CalendarioISO.php',
      'color' => 'info',
      'permission_page' => '01_CalendarioISO.php'
    ],*/
    /*[
      'name' => 'Tareas ISO',
      'description' => 'Gestión y seguimiento de tareas del sistema',
      'icon' => 'bi-list-check',
      'url' => '08_TareasISO/01_AdministrarTareas.php',
      'color' => 'warning',
      'permission_page' => '01_AdministrarTareas.php'
    ],*/
    [
      'name' => 'Programación ISO',
      'description' => 'Programación y planificación de actividades',
      'icon' => 'bi-calendar2-week-fill',
      'url' => '09_ProgramacionISO/01_AdministrarProgramacion.php',
      'color' => 'purple',
      'permission_page' => '01_AdministrarProgramacion.php'
    ],
    [
      'name' => 'Biblioteca Virtual',
      'description' => 'Acceso a documentos y recursos digitales',
      'icon' => 'bi-book-fill',
      'url' => '10_BibliotecaVirtual/01_ListadoBiblioteca.php',
      'color' => 'teal',
      'permission_page' => '01_ListadoBiblioteca.php'
    ],
    [
      'name' => 'Manual de Funciones',
      'description' => 'Consulta del manual de funciones organizacional',
      'icon' => 'bi-journal-bookmark-fill',
      'url' => '11_ManualFunciones/01_ManualFunciones.php',
      'color' => 'orange',
      'permission_page' => '01_ManualFunciones.php'
    ],
    /*[
      'name' => 'Capacitaciones',
      'description' => 'Gestión de programas de capacitación',
      'icon' => 'bi-mortarboard-fill',
      'url' => '13_ProgramacionCapacitacion/01_AdministrarCapacitacion.php',
      'color' => 'pink',
      'permission_page' => '01_AdministrarCapacitacion.php'
    ],*/
    [
      'name' => 'Gestión Dosier',
      'description' => 'Gestión integral de expedientes y dosiers',
      'icon' => 'bi-folder-fill',
      'url' => '14_GestionDosier/01_AdministrarDosier.php',
      'color' => 'dark',
      'permission_page' => '01_AdministrarDosier.php'
    ]
  ];
  
  // Filtrar módulos según permisos
  $available_modules = [];
  foreach ($modules as $module) {
    if ($is_admin || has_access($module['permission_page'])) {
      $available_modules[] = $module;
    }
  }

  // Usar el script de tareas pendientes
  $indicadores_tareas = null;
  try {
    $indicadores_tareas = include( BASE_PATH . '/app/function/indicadores/tareas-pendientes.php');
  } catch (Exception $e) {
    error_log("Error al cargar indicadores de tareas: " . $e->getMessage());
  }

  // Obtener estadísticas básicas
  $stats = [
    'total_users' => 0,
    'total_documents' => 0,
    'pending_tasks' => 0,
    'active_trainings' => 0,
    'eficacia_promedio' => '0.00',
    'estado_general' => 'sin_datos'
  ];
  
  // Aplicar los datos de indicadores de tareas si están disponibles
  if ($indicadores_tareas && isset($indicadores_tareas['general'])) {
    $general = $indicadores_tareas['general'];
    $stats['pending_tasks'] = $general['tareas_pendientes'];
    $stats['eficacia_promedio'] = $general['eficacia'];
    $stats['estado_general'] = ($general['tareas_pendientes'] == 0) ? 'excelente' : 
                              (($general['tareas_pendientes'] <= 5) ? 'bueno' : 
                              (($general['tareas_pendientes'] <= 10) ? 'regular' : 'critico'));
  }
  
  // Intentar obtener estadísticas reales si las tablas existen
  try {
    global $db; // Declarar la variable global
    if (tableExists('users')) {
      $result = "SELECT COUNT(*) as total FROM users WHERE estado_user = 1";
        $result = $db->query($result);
      if ($result) {
        $row = $db->fetch_assoc($result);
        $stats['total_users'] = $row['total'] ?? 0;
      }
    }
  } catch (Exception $e) {
    // Silenciosamente manejar errores de base de datos
  }
  
  // Líneas informativas dinámicas
  $info_lines = [
    [
      'type' => 'safety',
      'icon' => 'bi-shield-check',
      'title' => 'Seguridad Primero',
      'message' => 'Recuerda siempre seguir los protocolos de seguridad establecidos en nuestros procedimientos.',
      'color' => 'danger'
    ],
    [
      'type' => 'quality',
      'icon' => 'bi-award',
      'title' => 'Compromiso con la Calidad',
      'message' => 'Nuestra certificación ISO es el resultado del esfuerzo y dedicación de todo el equipo.',
      'color' => 'success'
    ],
    [
      'type' => 'motivation',
      'icon' => 'bi-star',
      'title' => 'Excelencia Operacional',
      'message' => 'Cada día es una oportunidad para mejorar nuestros procesos y superar expectativas.',
      'color' => 'primary'
    ],
    [
      'type' => 'company',
      'icon' => 'bi-building',
      'title' => 'TAMA - Líder en Innovación',
      'message' => 'Construyendo el futuro con tecnología de vanguardia y un equipo excepcional.',
      'color' => 'info'
    ]
  ];

ob_start();
?>

<div class="container-fluid">
    <div class="home-dashboard">
    <!-- Hero Section -->
    <div class="hero-section mb-4">
        <div class="container-fluid">
        <div class="row">
            <div class="col-12">
            <?php echo display_msg($msg); ?>
            <div class="welcome-card">
                <div class="welcome-content">
                <h1 class="welcome-title">
                    <i class="bi bi-house-door"></i>
                    Bienvenido, <?php echo htmlspecialchars($user_name); ?>
                </h1>
                <p class="welcome-subtitle">
                    Sistema de Gestión Documental TAMA
                    <?php if ($is_admin): ?>
                    <span class="badge bg-primary ms-2">Administrador</span>
                    <?php endif; ?>
                </p>
                <div class="welcome-stats">
                    <small class="text-muted">
                    <i class="bi bi-clock"></i>
                    Último acceso: <?php echo date('d/m/Y H:i'); ?>
                    </small>
                </div>
                </div>
                <div class="welcome-actions">
                <button class="btn btn-outline-primary" onclick="showInstructivo()">
                    <i class="bi bi-question-circle"></i>
                    Instructivo del Sistema
                </button>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>

    <!-- Indicadores Section -->
    <div class="indicators-section mb-4">
        <div class="container-fluid">
        <h3 class="section-title">
            <i class="bi bi-graph-up"></i>
            Indicadores del Sistema
        </h3>
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
            <div class="indicator-card bg-primary">
                <div class="indicator-content">
                <div class="indicator-value"><?php echo $stats['total_users']; ?></div>
                <div class="indicator-label">Usuarios Activos</div>
                </div>
                <div class="indicator-icon text-white">
                <i class="bi bi-people"></i>
                </div>
            </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
            <div class="indicator-card bg-success">
                <div class="indicator-content">
                <div class="indicator-value"><?php echo $stats['total_documents']; ?></div>
                <div class="indicator-label">Documentos ISO</div>
                </div>
                <div class="indicator-icon">
                <i class="bi bi-file-text"></i>
                </div>
            </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
            <div class="indicator-card bg-warning">
                <div class="indicator-content">
                <div class="indicator-value"><?php echo $stats['pending_tasks']; ?></div>
                <div class="indicator-label">Tareas Pendientes</div>
                </div>
                <div class="indicator-icon">
                <i class="bi bi-list-task"></i>
                </div>
            </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
            <?php 
            $eficacia = floatval($stats['eficacia_promedio']);
            $eficacia_class = 'bg-info';
            if ($eficacia >= 90) {
                $eficacia_class = 'bg-success';
            } elseif ($eficacia >= 70) {
                $eficacia_class = 'bg-warning';
            } elseif ($eficacia > 0) {
                $eficacia_class = 'bg-danger';
            }
            ?>
            <div class="indicator-card <?php echo $eficacia_class; ?>">
                <div class="indicator-content">
                <div class="indicator-value"><?php echo $stats['eficacia_promedio']; ?>%</div>
                <div class="indicator-label">Eficacia Promedio</div>
                </div>
                <div class="indicator-icon">
                <i class="bi bi-speedometer2"></i>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>

    <!-- Estado General de Tareas -->
    <?php if ($indicadores_tareas && isset($indicadores_tareas['general'])): ?>
    <div class="tasks-status-section mb-4">
        <div class="container-fluid">
        <h3 class="section-title">
            <i class="bi bi-clipboard-data"></i>
            Indicadores de Tareas - Estado General
        </h3>
        <div class="row">
            <div class="col-md-6">
            <?php 
            $estado = $stats['estado_general'];
            $estado_config = [
                'excelente' => ['color' => 'success', 'icon' => 'bi-check-circle-fill', 'titulo' => 'Excelente', 'mensaje' => 'Todas las tareas están al día'],
                'bueno' => ['color' => 'info', 'icon' => 'bi-info-circle-fill', 'titulo' => 'Bueno', 'mensaje' => 'Pocas tareas pendientes'],
                'regular' => ['color' => 'warning', 'icon' => 'bi-exclamation-triangle-fill', 'titulo' => 'Regular', 'mensaje' => 'Algunas tareas requieren atención'],
                'critico' => ['color' => 'danger', 'icon' => 'bi-x-circle-fill', 'titulo' => 'Crítico', 'mensaje' => 'Muchas tareas pendientes'],
                'sin_datos' => ['color' => 'secondary', 'icon' => 'bi-question-circle', 'titulo' => 'Sin datos', 'mensaje' => 'No hay información disponible']
            ];
            $config = $estado_config[$estado] ?? $estado_config['sin_datos'];
            ?>
            <div class="alert alert-<?php echo $config['color']; ?> d-flex align-items-center">
                <i class="<?php echo $config['icon']; ?> me-3" style="font-size: 1.5rem;"></i>
                <div>
                <h5 class="alert-heading mb-1"><?php echo $config['titulo']; ?></h5>
                <p class="mb-0"><?php echo $config['mensaje']; ?></p>
                </div>
            </div>
            </div>
            <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-graph-up"></i> Resumen General</h6>
                </div>
                <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                    <div class="display-6 mb-1 text-warning"><?php echo $stats['pending_tasks']; ?></div>
                    <small class="text-muted">Tareas Pendientes</small>
                    </div>
                    <div class="col-6">
                    <div class="display-6 mb-1 text-<?php echo $config['color']; ?>"><?php echo $stats['eficacia_promedio']; ?>%</div>
                    <small class="text-muted">Eficacia Promedio</small>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        
        <!-- Detalle por proceso (solo si tiene 2+ procesos) -->
        <?php if ($indicadores_tareas['mostrar_detalle']): ?>
        <div class="row mt-4">
            <div class="col-12">
            <h4 class="mb-3">
                <i class="bi bi-list-ul"></i>
                Detalle por Proceso
            </h4>
            <div class="row">
                <?php foreach ($indicadores_tareas['por_proceso'] as $proceso): ?>
                <div class="col-md-6 col-lg-3 mb-3">
                <div class="card">
                    <div class="card-body">
                    <h6 class="card-title"><?php echo htmlspecialchars($proceso['nombre']); ?></h6>
                    <div class="row text-center">
                        <div class="col-6">
                        <div class="h5 mb-1 text-warning"><?php echo $proceso['tareas_pendientes']; ?></div>
                        <small class="text-muted">Pendientes</small>
                        </div>
                        <div class="col-6">
                        <?php 
                        $eficacia_proceso = floatval($proceso['eficacia']);
                        $color_eficacia = ($eficacia_proceso >= 90) ? 'success' : (($eficacia_proceso >= 70) ? 'warning' : 'danger');
                        ?>
                        <div class="h5 mb-1 text-<?php echo $color_eficacia; ?>"><?php echo $proceso['eficacia']; ?>%</div>
                        <small class="text-muted">Eficacia</small>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
                <?php endforeach; ?>
            </div>
            </div>
        </div>
        <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Módulos Principales -->
    <div class="modules-section mb-4">
        <div class="container-fluid">
        <h3 class="section-title">
            <i class="bi bi-grid-3x3-gap"></i>
            Módulos del Sistema
        </h3>
        <div class="row">
            <?php if (empty($available_modules)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i>
                No tienes permisos asignados para ningún módulo del sistema. 
                Contacta al administrador para obtener acceso.
                </div>
            </div>
            <?php else: ?>
            <?php foreach ($available_modules as $module): ?>
                <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                <div class="module-card" onclick="navigateToModule('<?php echo $module['url']; ?>')">
                    <div class="module-header bg-<?php echo $module['color']; ?>">
                    <i class="<?php echo $module['icon']; ?>"></i>
                    </div>
                    <div class="module-body">
                    <h5 class="module-title"><?php echo $module['name']; ?></h5>
                    <p class="module-description"><?php echo $module['description']; ?></p>
                    <div class="module-action">
                        <span class="btn btn-outline-<?php echo $module['color']; ?>">
                        Acceder <i class="bi bi-arrow-right"></i>
                        </span>
                    </div>
                    </div>
                </div>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        </div>
    </div>

    <!-- Líneas Informativas -->
    <div class="info-section mb-5">
        <div class="container-fluid">
        <h3 class="section-title">
            <i class="bi bi-info-square"></i>
            Información Importante
        </h3>
        <div class="info-carousel">
            <?php foreach ($info_lines as $index => $line): ?>
            <div class="info-line bg-<?php echo $line['color']; ?> <?php echo $index === 0 ? 'active' : ''; ?>">
                <div class="info-icon">
                <i class="<?php echo $line['icon']; ?>"></i>
                </div>
                <div class="info-content">
                <h6 class="info-title"><?php echo $line['title']; ?></h6>
                <p class="info-message"><?php echo $line['message']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="carousel-controls">
            <?php foreach ($info_lines as $index => $line): ?>
            <button class="carousel-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                    onclick="showInfoLine(<?php echo $index; ?>)"></button>
            <?php endforeach; ?>
        </div>
        </div>
    </div>
    </div>

    <!-- Modal para Instructivo -->
    <div class="modal fade" id="instructivoModal" tabindex="-1" aria-labelledby="instructivoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="instructivoModalLabel">
            <i class="bi bi-book"></i>
            Instructivo del Sistema
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="instructivo-content">
            <div class="alert alert-info">
                <i class="bi bi-lightbulb"></i>
                <strong>¡Próximamente!</strong> El instructivo completo del sistema estará disponible aquí.
            </div>
            
            <h6>Guía Rápida de Navegación:</h6>
            <ul>
                <li><strong>Dashboard:</strong> Página principal con resumen e indicadores</li>
                <li><strong>Módulos:</strong> Acceso directo a las funcionalidades según tus permisos</li>
                <li><strong>Menú Superior:</strong> Navegación rápida y gestión de cuenta</li>
                <li><strong>Búsqueda:</strong> Encuentra documentos y contenido rápidamente</li>
            </ul>
            
            <h6>Contacto de Soporte:</h6>
            <p>Para asistencia técnica o consultas sobre el sistema, contacta al equipo de IT.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
        </div>
    </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$pageScripts = '
<script>
    // Definir variables globales para JavaScript
    const BASE_URL = "' . BASE_URL . '";
</script>
<script type="text/javascript" src="' . BASE_URL . '/resources/modules/dashboard/dashboard.js"></script>
';
include RESOURCES_PATH . '/layouts/main.php';
?>
