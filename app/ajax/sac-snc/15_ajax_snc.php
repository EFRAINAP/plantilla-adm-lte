<?php
require_once '01_ajax_connection.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit;
}

$accion = $_POST['accion'] ?? '';
$operacion = $_POST['operacion'] ?? '';

switch ($accion) {
    case 'listarSNC':
        listarSNC();
        break;
    case 'crear_snc':
        crearSNC();
        break;
    case 'actualizar_snc':
        actualizarSNC();
        break;
    case 'obtenerDetalleSNC':
        obtenerDetalleSNC();
        break;
    case 'generar_codigo_snc':
        generarCodigoSNCAjax();
        break;
    default:
        // Mantener compatibilidad con operaciones del listado
        switch ($operacion) {
            case 'listar_snc':
                listarSNCTabla();
                break;
            case 'obtener_detalle_snc':
                obtenerDetalleSNCModal();
                break;
            case 'eliminar_snc':
                eliminarSNC();
                break;
            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        }
}

function listarSNC() {
    global $connection;
    
    try {
        // Parámetros de DataTables
        $draw = intval($_POST['draw'] ?? 1);
        $start = intval($_POST['start'] ?? 0);
        $length = intval($_POST['length'] ?? 10);
        $search_value = $_POST['search']['value'] ?? '';
        
        // Filtros adicionales
        $filtro_proceso = $_POST['filtro_proceso'] ?? '';
        $filtro_estado = $_POST['filtro_estado'] ?? '';
        $filtro_fecha_desde = $_POST['filtro_fecha_desde'] ?? '';
        $filtro_fecha_hasta = $_POST['filtro_fecha_hasta'] ?? '';
        
        // Columnas para ordenamiento
        $columns = [
            'codigo_snc',
            'nombre_proceso',
            'descripcion_no_conformidad',
            'estado',
            'fecha_deteccion',
            'fecha_limite',
            'responsable_seguimiento'
        ];
        
        $order_column = $columns[$_POST['order'][0]['column'] ?? 4] ?? 'fecha_deteccion';
        $order_dir = $_POST['order'][0]['dir'] ?? 'desc';
        
        // Query base
        $base_query = "FROM snc s 
                       INNER JOIN proceso p ON s.proceso_id = p.proceso_id";
        
        // Condiciones WHERE
        $where_conditions = [];
        $params = [];
        
        // Filtro por permisos de usuario
        if ($_SESSION['perfil'] != 'superadmin' && $_SESSION['perfil'] != 'admin') {
            $where_conditions[] = "s.proceso_id IN (SELECT proceso_id FROM usuarios_procesos WHERE usuario_id = :usuario_id)";
            $params[':usuario_id'] = $_SESSION['usuario_id'];
        }
        
        // Filtro por proceso
        if (!empty($filtro_proceso)) {
            $where_conditions[] = "s.proceso_id = :filtro_proceso";
            $params[':filtro_proceso'] = $filtro_proceso;
        }
        
        // Filtro por estado
        if (!empty($filtro_estado)) {
            $where_conditions[] = "s.estado = :filtro_estado";
            $params[':filtro_estado'] = $filtro_estado;
        }
        
        // Filtro por fecha desde
        if (!empty($filtro_fecha_desde)) {
            $where_conditions[] = "s.fecha_deteccion >= :filtro_fecha_desde";
            $params[':filtro_fecha_desde'] = $filtro_fecha_desde;
        }
        
        // Filtro por fecha hasta
        if (!empty($filtro_fecha_hasta)) {
            $where_conditions[] = "s.fecha_deteccion <= :filtro_fecha_hasta";
            $params[':filtro_fecha_hasta'] = $filtro_fecha_hasta;
        }
        
        // Filtro por búsqueda general
        if (!empty($search_value)) {
            $where_conditions[] = "(s.codigo_snc LIKE :search 
                                   OR p.nombre_proceso LIKE :search 
                                   OR s.descripcion_no_conformidad LIKE :search 
                                   OR s.responsable_seguimiento LIKE :search)";
            $params[':search'] = "%$search_value%";
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        // Contar total de registros
        $count_query = "SELECT COUNT(*) as total $base_query $where_clause";
        $stmt = $connection->prepare($count_query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $total_records = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Query principal con paginación
        $data_query = "SELECT s.snc_id, s.codigo_snc, p.nombre_proceso, s.descripcion_no_conformidad,
                              s.estado, s.fecha_deteccion, s.fecha_limite, s.responsable_seguimiento,
                              CASE 
                                  WHEN s.fecha_limite < CURDATE() AND s.estado != 'Cerrado' THEN 1 
                                  ELSE 0 
                              END as es_vencido
                       $base_query $where_clause 
                       ORDER BY $order_column $order_dir 
                       LIMIT $start, $length";
        
        $stmt = $connection->prepare($data_query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear datos para DataTables
        $data = [];
        foreach ($records as $record) {
            $data[] = [
                'snc_id' => $record['snc_id'],
                'codigo_snc' => $record['codigo_snc'],
                'nombre_proceso' => $record['nombre_proceso'],
                'descripcion_no_conformidad' => $record['descripcion_no_conformidad'],
                'estado' => $record['estado'],
                'fecha_deteccion' => $record['fecha_deteccion'],
                'fecha_limite' => $record['fecha_limite'],
                'responsable_seguimiento' => $record['responsable_seguimiento'],
                'es_vencido' => $record['es_vencido']
            ];
        }
        
        $response = [
            'draw' => $draw,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $data
        ];
        
        echo json_encode($response);
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener los datos: ' . $e->getMessage()
        ]);
    }
}

function crearSNC() {
    global $connection;
    
    try {
        // Validar campos requeridos
        $campos_requeridos = [
            'fecha_apertura', 'codigo_area', 'codigo_proceso', 'identifica_snc',
            'fuente_snc', 'fecha_suceso', 'descripcion_hallazgo', 
            'decide_tratamiento', 'clasificacion_hallazgo', 'estado_registro_snc'
        ];
        
        foreach ($campos_requeridos as $campo) {
            if (empty($_POST[$campo])) {
                echo json_encode(['status' => 'error', 'message' => "El campo $campo es requerido"]);
                return;
            }
        }
        
        // Generar código SNC si no se proporciona
        $codigo_snc = !empty($_POST['codigo_snc']) ? $_POST['codigo_snc'] : generateAutoCodigoSNC();
        
        // Preparar datos para inserción
        $query = "INSERT INTO snc (
                    fecha_apertura, codigo_snc, codigo_area, codigo_proceso, identifica_snc,
                    orden_trabajo, proyecto_actividad, fuente_snc, normativa, fecha_suceso,
                    descripcion_hallazgo, decide_tratamiento, fecha_decision, tipo_tratamiento,
                    tratamiento_inmediato, costo_no_calidad, es_eficaz, numero_sac,
                    estado_acciones_snc, estado_registro_snc, clasificacion_hallazgo,
                    fecha_respuesta_snc, fecha_aprobacion_snc, fecha_cierre_snc,
                    observaciones, created_by, created_at
                  ) VALUES (
                    :fecha_apertura, :codigo_snc, :codigo_area, :codigo_proceso, :identifica_snc,
                    :orden_trabajo, :proyecto_actividad, :fuente_snc, :normativa, :fecha_suceso,
                    :descripcion_hallazgo, :decide_tratamiento, :fecha_decision, :tipo_tratamiento,
                    :tratamiento_inmediato, :costo_no_calidad, :es_eficaz, :numero_sac,
                    :estado_acciones_snc, :estado_registro_snc, :clasificacion_hallazgo,
                    :fecha_respuesta_snc, :fecha_aprobacion_snc, :fecha_cierre_snc,
                    :observaciones, :created_by, NOW()
                  )";
        
        $stmt = $connection->prepare($query);
        
        // Vincular parámetros
        $stmt->bindValue(':fecha_apertura', $_POST['fecha_apertura']);
        $stmt->bindValue(':codigo_snc', $codigo_snc);
        $stmt->bindValue(':codigo_area', $_POST['codigo_area']);
        $stmt->bindValue(':codigo_proceso', $_POST['codigo_proceso']);
        $stmt->bindValue(':identifica_snc', $_POST['identifica_snc']);
        $stmt->bindValue(':orden_trabajo', $_POST['orden_trabajo'] ?? null);
        $stmt->bindValue(':proyecto_actividad', $_POST['proyecto_actividad'] ?? null);
        $stmt->bindValue(':fuente_snc', $_POST['fuente_snc']);
        $stmt->bindValue(':normativa', $_POST['normativa'] ?? null);
        $stmt->bindValue(':fecha_suceso', $_POST['fecha_suceso']);
        $stmt->bindValue(':descripcion_hallazgo', $_POST['descripcion_hallazgo']);
        $stmt->bindValue(':decide_tratamiento', $_POST['decide_tratamiento']);
        $stmt->bindValue(':fecha_decision', $_POST['fecha_decision'] ?? null);
        $stmt->bindValue(':tipo_tratamiento', $_POST['tipo_tratamiento'] ?? null);
        $stmt->bindValue(':tratamiento_inmediato', $_POST['tratamiento_inmediato'] ?? null);
        $stmt->bindValue(':costo_no_calidad', $_POST['costo_no_calidad'] ?? 0.00);
        $stmt->bindValue(':es_eficaz', $_POST['es_eficaz'] ?? 'Pendiente Evaluacion');
        $stmt->bindValue(':numero_sac', $_POST['numero_sac'] ?? null);
        $stmt->bindValue(':estado_acciones_snc', $_POST['estado_acciones_snc'] ?? 'Pendiente');
        $stmt->bindValue(':estado_registro_snc', $_POST['estado_registro_snc']);
        $stmt->bindValue(':clasificacion_hallazgo', $_POST['clasificacion_hallazgo']);
        $stmt->bindValue(':fecha_respuesta_snc', $_POST['fecha_respuesta_snc'] ?? null);
        $stmt->bindValue(':fecha_aprobacion_snc', $_POST['fecha_aprobacion_snc'] ?? null);
        $stmt->bindValue(':fecha_cierre_snc', $_POST['fecha_cierre_snc'] ?? null);
        $stmt->bindValue(':observaciones', $_POST['observaciones'] ?? null);
        $stmt->bindValue(':created_by', $_SESSION['username'] ?? 'system');
        
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => "SNC creada exitosamente con código: $codigo_snc"
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al crear la SNC']);
        }
        
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ]);
    }
}

function generateAutoCodigoSNC() {
    global $connection;
    
    try {
        $año = date('Y');
        
        // Obtener el siguiente número secuencial
        $stmt = $connection->prepare("SELECT COUNT(*) + 1 as siguiente FROM snc WHERE YEAR(fecha_apertura) = :año");
        $stmt->bindValue(':año', $año, PDO::PARAM_INT);
        $stmt->execute();
        $siguiente = $stmt->fetch(PDO::FETCH_ASSOC)['siguiente'];
        
        return sprintf("SNC-%04d-%03d", $año, $siguiente);
        
    } catch (PDOException $e) {
        return "SNC-" . date('Y') . "-" . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    }
}

function actualizarSNC() {
    global $connection;
    
    try {
        // Validar que se proporcione el ID
        if (empty($_POST['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'ID de SNC no proporcionado']);
            return;
        }
        
        $snc_id = $_POST['id'];
        
        // Verificar que la SNC existe
        $check_query = "SELECT id FROM snc WHERE id = :snc_id";
        $stmt = $connection->prepare($check_query);
        $stmt->bindValue(':snc_id', $snc_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if (!$stmt->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'SNC no encontrada']);
            return;
        }
        
        // Validar campos requeridos
        $campos_requeridos = [
            'fecha_apertura', 'codigo_area', 'codigo_proceso', 'identifica_snc',
            'fuente_snc', 'fecha_suceso', 'descripcion_hallazgo', 
            'decide_tratamiento', 'clasificacion_hallazgo', 'estado_registro_snc'
        ];
        
        foreach ($campos_requeridos as $campo) {
            if (empty($_POST[$campo])) {
                echo json_encode(['status' => 'error', 'message' => "El campo $campo es requerido"]);
                return;
            }
        }
        
        // Preparar consulta de actualización
        $query = "UPDATE snc SET
                    fecha_apertura = :fecha_apertura,
                    codigo_area = :codigo_area,
                    codigo_proceso = :codigo_proceso,
                    identifica_snc = :identifica_snc,
                    orden_trabajo = :orden_trabajo,
                    proyecto_actividad = :proyecto_actividad,
                    fuente_snc = :fuente_snc,
                    normativa = :normativa,
                    fecha_suceso = :fecha_suceso,
                    descripcion_hallazgo = :descripcion_hallazgo,
                    decide_tratamiento = :decide_tratamiento,
                    fecha_decision = :fecha_decision,
                    tipo_tratamiento = :tipo_tratamiento,
                    tratamiento_inmediato = :tratamiento_inmediato,
                    costo_no_calidad = :costo_no_calidad,
                    es_eficaz = :es_eficaz,
                    numero_sac = :numero_sac,
                    estado_acciones_snc = :estado_acciones_snc,
                    estado_registro_snc = :estado_registro_snc,
                    clasificacion_hallazgo = :clasificacion_hallazgo,
                    fecha_respuesta_snc = :fecha_respuesta_snc,
                    fecha_aprobacion_snc = :fecha_aprobacion_snc,
                    fecha_cierre_snc = :fecha_cierre_snc,
                    observaciones = :observaciones,
                    updated_at = NOW()
                  WHERE id = :snc_id";
        
        $stmt = $connection->prepare($query);
        
        // Vincular parámetros
        $stmt->bindValue(':fecha_apertura', $_POST['fecha_apertura']);
        $stmt->bindValue(':codigo_area', $_POST['codigo_area']);
        $stmt->bindValue(':codigo_proceso', $_POST['codigo_proceso']);
        $stmt->bindValue(':identifica_snc', $_POST['identifica_snc']);
        $stmt->bindValue(':orden_trabajo', $_POST['orden_trabajo'] ?? null);
        $stmt->bindValue(':proyecto_actividad', $_POST['proyecto_actividad'] ?? null);
        $stmt->bindValue(':fuente_snc', $_POST['fuente_snc']);
        $stmt->bindValue(':normativa', $_POST['normativa'] ?? null);
        $stmt->bindValue(':fecha_suceso', $_POST['fecha_suceso']);
        $stmt->bindValue(':descripcion_hallazgo', $_POST['descripcion_hallazgo']);
        $stmt->bindValue(':decide_tratamiento', $_POST['decide_tratamiento']);
        $stmt->bindValue(':fecha_decision', $_POST['fecha_decision'] ?? null);
        $stmt->bindValue(':tipo_tratamiento', $_POST['tipo_tratamiento'] ?? null);
        $stmt->bindValue(':tratamiento_inmediato', $_POST['tratamiento_inmediato'] ?? null);
        $stmt->bindValue(':costo_no_calidad', $_POST['costo_no_calidad'] ?? 0.00);
        $stmt->bindValue(':es_eficaz', $_POST['es_eficaz'] ?? 'Pendiente Evaluacion');
        $stmt->bindValue(':numero_sac', $_POST['numero_sac'] ?? null);
        $stmt->bindValue(':estado_acciones_snc', $_POST['estado_acciones_snc'] ?? 'Pendiente');
        $stmt->bindValue(':estado_registro_snc', $_POST['estado_registro_snc']);
        $stmt->bindValue(':clasificacion_hallazgo', $_POST['clasificacion_hallazgo']);
        $stmt->bindValue(':fecha_respuesta_snc', $_POST['fecha_respuesta_snc'] ?? null);
        $stmt->bindValue(':fecha_aprobacion_snc', $_POST['fecha_aprobacion_snc'] ?? null);
        $stmt->bindValue(':fecha_cierre_snc', $_POST['fecha_cierre_snc'] ?? null);
        $stmt->bindValue(':observaciones', $_POST['observaciones'] ?? null);
        $stmt->bindValue(':snc_id', $snc_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'SNC actualizada exitosamente'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar la SNC']);
        }
        
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ]);
    }
}

function obtenerDetalleSNC() {
    global $connection;
    
    try {
        if (empty($_POST['snc_id'])) {
            echo json_encode(['success' => false, 'message' => 'ID de SNC no proporcionado']);
            return;
        }
        
        $snc_id = $_POST['snc_id'];
        
        // Obtener datos completos de la SNC
        $query = "SELECT s.*, p.nombre_proceso,
                         CASE 
                             WHEN s.fecha_limite < CURDATE() AND s.estado != 'Cerrado' THEN 1 
                             ELSE 0 
                         END as es_vencido,
                         u_creacion.nombre as nombre_usuario_creacion,
                         u_modificacion.nombre as nombre_usuario_modificacion
                  FROM snc s 
                  INNER JOIN proceso p ON s.proceso_id = p.proceso_id
                  LEFT JOIN usuarios u_creacion ON s.usuario_creacion = u_creacion.usuario_id
                  LEFT JOIN usuarios u_modificacion ON s.usuario_modificacion = u_modificacion.usuario_id
                  WHERE s.snc_id = :snc_id";
        
        if ($_SESSION['perfil'] != 'superadmin' && $_SESSION['perfil'] != 'admin') {
            $query .= " AND s.proceso_id IN (SELECT proceso_id FROM usuarios_procesos WHERE usuario_id = :usuario_id)";
        }
        
        $stmt = $connection->prepare($query);
        $stmt->bindValue(':snc_id', $snc_id, PDO::PARAM_INT);
        if ($_SESSION['perfil'] != 'superadmin' && $_SESSION['perfil'] != 'admin') {
            $stmt->bindValue(':usuario_id', $_SESSION['usuario_id'], PDO::PARAM_INT);
        }
        $stmt->execute();
        
        $snc = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$snc) {
            echo json_encode(['success' => false, 'message' => 'SNC no encontrada']);
            return;
        }
        
        // Generar HTML para el modal
        $html = generarHTMLDetalleSNC($snc);
        
        echo json_encode([
            'success' => true,
            'html' => $html
        ]);
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener los detalles: ' . $e->getMessage()
        ]);
    }
}

function generarCodigoSNC($proceso_id) {
    global $connection;
    
    try {
        // Obtener el código del proceso
        $stmt = $connection->prepare("SELECT codigo_proceso FROM proceso WHERE proceso_id = :proceso_id");
        $stmt->bindValue(':proceso_id', $proceso_id, PDO::PARAM_INT);
        $stmt->execute();
        $proceso = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $codigo_proceso = $proceso['codigo_proceso'] ?? 'GEN';
        $año = date('Y');
        
        // Obtener el siguiente número secuencial
        $stmt = $connection->prepare("SELECT COUNT(*) + 1 as siguiente FROM snc WHERE YEAR(fecha_creacion) = :año AND proceso_id = :proceso_id");
        $stmt->bindValue(':año', $año, PDO::PARAM_INT);
        $stmt->bindValue(':proceso_id', $proceso_id, PDO::PARAM_INT);
        $stmt->execute();
        $siguiente = $stmt->fetch(PDO::FETCH_ASSOC)['siguiente'];
        
        return sprintf("SNC-%s-%04d-%03d", $codigo_proceso, $año, $siguiente);
        
    } catch (PDOException $e) {
        return "SNC-GEN-" . date('Y') . "-" . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    }
}

function generarCodigoSNCAjax() {
    global $connection;
    
    try {
        $año = date('Y');
        
        // Obtener el siguiente número secuencial general
        $stmt = $connection->prepare("SELECT COUNT(*) + 1 as siguiente FROM snc WHERE YEAR(fecha_apertura) = :año");
        $stmt->bindValue(':año', $año, PDO::PARAM_INT);
        $stmt->execute();
        $siguiente = $stmt->fetch(PDO::FETCH_ASSOC)['siguiente'];
        
        $codigo = sprintf("SNC-%04d-%03d", $año, $siguiente);
        
        echo json_encode([
            'status' => 'success',
            'codigo' => $codigo
        ]);
        
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error al generar código: ' . $e->getMessage()
        ]);
    }
}

function generarHTMLDetalleSNC($snc) {
    $estado_badge = '';
    switch($snc['estado']) {
        case 'Abierto':
            $estado_badge = '<span class="badge bg-danger"><i class="bi bi-exclamation-circle-fill me-1"></i>Abierto</span>';
            break;
        case 'En Proceso':
            $estado_badge = '<span class="badge bg-warning text-dark"><i class="bi bi-clock-fill me-1"></i>En Proceso</span>';
            break;
        case 'Cerrado':
            $estado_badge = '<span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Cerrado</span>';
            break;
    }
    
    $fecha_limite_html = date('d/m/Y', strtotime($snc['fecha_limite']));
    if ($snc['es_vencido']) {
        $fecha_limite_html = '<span class="text-danger fw-bold">' . $fecha_limite_html . ' <i class="bi bi-exclamation-triangle-fill"></i></span>';
    }
    
    $html = '
    <div class="row">
        <div class="col-md-6 mb-3">
            <strong>Código SNC:</strong><br>
            <span class="text-primary fs-5">' . htmlspecialchars($snc['codigo_snc']) . '</span>
        </div>
        <div class="col-md-6 mb-3">
            <strong>Estado:</strong><br>
            ' . $estado_badge . '
        </div>
        <div class="col-md-6 mb-3">
            <strong>Proceso:</strong><br>
            ' . htmlspecialchars($snc['nombre_proceso']) . '
        </div>
        <div class="col-md-6 mb-3">
            <strong>Fecha de Detección:</strong><br>
            ' . date('d/m/Y', strtotime($snc['fecha_deteccion'])) . '
        </div>
        <div class="col-12 mb-3">
            <strong>Descripción de la No Conformidad:</strong><br>
            <div class="p-2 bg-light rounded">' . nl2br(htmlspecialchars($snc['descripcion_no_conformidad'])) . '</div>
        </div>';
    
    if (!empty($snc['analisis_causa_raiz'])) {
        $html .= '
        <div class="col-12 mb-3">
            <strong>Análisis de Causa Raíz:</strong><br>
            <div class="p-2 bg-light rounded">' . nl2br(htmlspecialchars($snc['analisis_causa_raiz'])) . '</div>
        </div>';
    }
    
    if (!empty($snc['metodo_analisis'])) {
        $html .= '
        <div class="col-md-6 mb-3">
            <strong>Método de Análisis:</strong><br>
            ' . htmlspecialchars($snc['metodo_analisis']) . '
        </div>';
    }
    
    if (!empty($snc['categoria_causa'])) {
        $html .= '
        <div class="col-md-6 mb-3">
            <strong>Categoría de la Causa:</strong><br>
            ' . htmlspecialchars($snc['categoria_causa']) . '
        </div>';
    }
    
    $html .= '
        <div class="col-12 mb-3">
            <strong>Acción Correctiva:</strong><br>
            <div class="p-2 bg-light rounded">' . nl2br(htmlspecialchars($snc['accion_correctiva'])) . '</div>
        </div>
        <div class="col-md-6 mb-3">
            <strong>Responsable de la Acción:</strong><br>
            ' . htmlspecialchars($snc['responsable_accion']) . '
        </div>
        <div class="col-md-6 mb-3">
            <strong>Fecha Límite:</strong><br>
            ' . $fecha_limite_html . '
        </div>
        <div class="col-md-6 mb-3">
            <strong>Responsable del Seguimiento:</strong><br>
            ' . htmlspecialchars($snc['responsable_seguimiento']) . '
        </div>';
    
    if (!empty($snc['seguimiento'])) {
        $html .= '
        <div class="col-12 mb-3">
            <strong>Seguimiento:</strong><br>
            <div class="p-2 bg-light rounded">' . nl2br(htmlspecialchars($snc['seguimiento'])) . '</div>
        </div>';
    }
    
    if (!empty($snc['verificacion_eficacia'])) {
        $html .= '
        <div class="col-12 mb-3">
            <strong>Verificación de Eficacia:</strong><br>
            <div class="p-2 bg-light rounded">' . nl2br(htmlspecialchars($snc['verificacion_eficacia'])) . '</div>
        </div>';
    }
    
    if (!empty($snc['fecha_cierre'])) {
        $html .= '
        <div class="col-md-6 mb-3">
            <strong>Fecha de Cierre:</strong><br>
            ' . date('d/m/Y', strtotime($snc['fecha_cierre'])) . '
        </div>';
    }
    
    if (!empty($snc['dias_resolucion'])) {
        $html .= '
        <div class="col-md-6 mb-3">
            <strong>Días de Resolución:</strong><br>
            ' . $snc['dias_resolucion'] . ' días
        </div>';
    }
    
    if (!empty($snc['accion_preventiva'])) {
        $html .= '
        <div class="col-12 mb-3">
            <strong>Acción Preventiva:</strong><br>
            <div class="p-2 bg-light rounded">' . nl2br(htmlspecialchars($snc['accion_preventiva'])) . '</div>
        </div>';
    }
    
    if (!empty($snc['observaciones'])) {
        $html .= '
        <div class="col-12 mb-3">
            <strong>Observaciones:</strong><br>
            <div class="p-2 bg-light rounded">' . nl2br(htmlspecialchars($snc['observaciones'])) . '</div>
        </div>';
    }
    
    $html .= '
        <div class="col-12">
            <hr>
            <small class="text-muted">
                <strong>Creado por:</strong> ' . htmlspecialchars($snc['nombre_usuario_creacion'] ?? 'N/A') . ' 
                el ' . date('d/m/Y H:i', strtotime($snc['fecha_creacion'])) . '<br>';
    
    if (!empty($snc['fecha_modificacion'])) {
        $html .= '<strong>Modificado por:</strong> ' . htmlspecialchars($snc['nombre_usuario_modificacion'] ?? 'N/A') . ' 
                  el ' . date('d/m/Y H:i', strtotime($snc['fecha_modificacion']));
    }
    
    $html .= '
            </small>
        </div>
    </div>';
    
    return $html;
}

function listarSNCTabla() {
    global $connection;
    
    try {
        // Parámetros de DataTables
        $draw = intval($_POST['draw'] ?? 1);
        $start = intval($_POST['start'] ?? 0);
        $length = intval($_POST['length'] ?? 10);
        $search = $_POST['search']['value'] ?? '';
        
        // Filtros
        $filtro_estado = $_POST['filtro_estado'] ?? '';
        $filtro_proceso = $_POST['filtro_proceso'] ?? '';
        $filtro_clasificacion = $_POST['filtro_clasificacion'] ?? '';
        $filtro_fuente = $_POST['filtro_fuente'] ?? '';
        $user_processes = $_POST['user_processes'] ?? [];
        
        // Query base
        $sql = "SELECT s.*, p.descripcion_proceso 
                FROM snc s 
                LEFT JOIN proceso p ON s.codigo_proceso = p.proceso 
                WHERE 1=1";
        
        $params = [];
        
        // Aplicar filtros
        if (!empty($filtro_estado)) {
            $sql .= " AND s.estado_registro_snc = ?";
            $params[] = $filtro_estado;
        }
        
        if (!empty($filtro_proceso)) {
            $sql .= " AND s.codigo_proceso = ?";
            $params[] = $filtro_proceso;
        }
        
        if (!empty($filtro_clasificacion)) {
            $sql .= " AND s.clasificacion_hallazgo = ?";
            $params[] = $filtro_clasificacion;
        }
        
        if (!empty($filtro_fuente)) {
            $sql .= " AND s.fuente_snc = ?";
            $params[] = $filtro_fuente;
        }
        
        // Filtro por procesos del usuario
        if (!empty($user_processes)) {
            $placeholders = str_repeat('?,', count($user_processes) - 1) . '?';
            $sql .= " AND s.codigo_proceso IN ($placeholders)";
            $params = array_merge($params, $user_processes);
        }
        
        // Búsqueda general
        if (!empty($search)) {
            $sql .= " AND (s.codigo_snc LIKE ? OR s.descripcion_hallazgo LIKE ? OR p.descripcion_proceso LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        // Contar total de registros
        $count_sql = str_replace('SELECT s.*, p.descripcion_proceso', 'SELECT COUNT(*)', $sql);
        $stmt = $connection->prepare($count_sql);
        if (!empty($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        $total_records = $stmt->fetchColumn();
        
        // Ordenamiento
        $order_column = $_POST['order'][0]['column'] ?? 1;
        $order_dir = $_POST['order'][0]['dir'] ?? 'desc';
        $columns = ['codigo_snc', 'fecha_apertura', 'codigo_proceso', 'fuente_snc', 'descripcion_hallazgo', 
                   'clasificacion_hallazgo', 'estado_registro_snc', 'es_eficaz', 'fecha_suceso', 'costo_no_calidad'];
        
        if (isset($columns[$order_column])) {
            $sql .= " ORDER BY s." . $columns[$order_column] . " " . $order_dir;
        } else {
            $sql .= " ORDER BY s.fecha_apertura DESC";
        }
        
        // Paginación
        $sql .= " LIMIT $start, $length";
        
        $stmt = $connection->prepare($sql);
        if (!empty($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $data = [];
        foreach ($records as $record) {
            $acciones = '
            <div class="btn-group">
                <button type="button" class="btn btn-info btn-xs" onclick="verDetalleSNC(' . $record['snc_id'] . ')" title="Ver detalle">
                    <i class="glyphicon glyphicon-eye-open"></i>
                </button>
                <button type="button" class="btn btn-warning btn-xs" onclick="editarSNC(' . $record['snc_id'] . ')" title="Editar">
                    <i class="glyphicon glyphicon-edit"></i>
                </button>
                <button type="button" class="btn btn-danger btn-xs" onclick="eliminarSNC(' . $record['snc_id'] . ', \'' . $record['codigo_snc'] . '\')" title="Eliminar">
                    <i class="glyphicon glyphicon-trash"></i>
                </button>
            </div>';
            
            $data[] = [
                'codigo_snc' => $record['codigo_snc'],
                'fecha_apertura' => date('d/m/Y', strtotime($record['fecha_apertura'])),
                'codigo_proceso' => $record['codigo_proceso'] . ' - ' . $record['descripcion_proceso'],
                'fuente_snc' => $record['fuente_snc'],
                'descripcion_hallazgo' => strlen($record['descripcion_hallazgo']) > 50 ? 
                                        substr($record['descripcion_hallazgo'], 0, 50) . '...' : 
                                        $record['descripcion_hallazgo'],
                'clasificacion_hallazgo' => $record['clasificacion_hallazgo'],
                'estado_registro_snc' => $record['estado_registro_snc'],
                'es_eficaz' => $record['es_eficaz'],
                'fecha_suceso' => !empty($record['fecha_suceso']) ? date('d/m/Y', strtotime($record['fecha_suceso'])) : '',
                'costo_no_calidad' => number_format($record['costo_no_calidad'], 2),
                'acciones' => $acciones
            ];
        }
        
        $response = [
            'draw' => $draw,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $data
        ];
        
        echo json_encode($response);
        
    } catch (Exception $e) {
        echo json_encode([
            'draw' => 1,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => $e->getMessage()
        ]);
    }
}

function obtenerDetalleSNCModal() {
    global $connection;
    
    $id = $_POST['id'] ?? 0;
    
    try {
        $sql = "SELECT s.*, p.descripcion_proceso, 
                       uc.nombre as nombre_usuario_creacion,
                       um.nombre as nombre_usuario_modificacion
                FROM snc s 
                LEFT JOIN proceso p ON s.codigo_proceso = p.proceso
                LEFT JOIN usuario uc ON s.usuario_creacion = uc.usuario_id
                LEFT JOIN usuario um ON s.usuario_modificacion = um.usuario_id
                WHERE s.snc_id = ?";
        
        $stmt = $connection->prepare($sql);
        $stmt->execute([$id]);
        $snc = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$snc) {
            echo '<div class="alert alert-danger">No se encontró la SNC especificada.</div>';
            return;
        }
        
        echo generarHTMLDetalleSNC($snc);
        
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Error al cargar los detalles: ' . $e->getMessage() . '</div>';
    }
}

function eliminarSNC() {
    global $connection;
    
    $id = $_POST['id'] ?? 0;
    
    try {
        // Verificar si existe
        $sql = "SELECT codigo_snc FROM snc WHERE snc_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->execute([$id]);
        $snc = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$snc) {
            echo json_encode(['status' => 'error', 'message' => 'SNC no encontrada']);
            return;
        }
        
        // Eliminar
        $sql = "DELETE FROM snc WHERE snc_id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->execute([$id]);
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'SNC ' . $snc['codigo_snc'] . ' eliminada correctamente'
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar: ' . $e->getMessage()]);
    }
}
?>
