<?php
require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new Connection_Ajax();
$conexion = $objeto->Conectar();

// Obtener la operación solicitada
$operacion = isset($_POST['operacion']) ? $_POST['operacion'] : '';

try {
    switch ($operacion) {
        
        case 'listar_sac':
            listarSAC($conexion);
            break;
            
        case 'crear_sac':
            crearSAC($conexion);
            break;
            
        case 'actualizar_sac':
            actualizarSAC($conexion);
            break;
            
        case 'eliminar_sac':
            eliminarSAC($conexion);
            break;
            
        default:
            echo json_encode(['status' => 'error', 'message' => 'Operación no válida']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

function listarSAC($conexion) {
    // Parámetros DataTables
    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 25;
    $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
    
    // Filtros adicionales
    $filtro_estado = isset($_POST['filtro_estado']) ? $_POST['filtro_estado'] : '';
    $filtro_proceso = isset($_POST['filtro_proceso']) ? $_POST['filtro_proceso'] : '';
    $filtro_clasificacion = isset($_POST['filtro_clasificacion']) ? $_POST['filtro_clasificacion'] : '';
    $user_processes = isset($_POST['user_processes']) ? $_POST['user_processes'] : [];
    
    // Construcción de la consulta
    $where = "WHERE 1=1";
    $params = [];
    
    // Filtro por procesos del usuario
    if (!empty($user_processes)) {
        $placeholders = implode(',', array_fill(0, count($user_processes), '?'));
        $where .= " AND s.codigo_proceso IN ($placeholders)";
        $params = array_merge($params, $user_processes);
    }
    
    // Filtros específicos
    if (!empty($filtro_estado)) {
        $where .= " AND s.estado_registro_sac = ?";
        $params[] = $filtro_estado;
    }
    
    if (!empty($filtro_proceso)) {
        $where .= " AND s.codigo_proceso = ?";
        $params[] = $filtro_proceso;
    }
    
    if (!empty($filtro_clasificacion)) {
        $where .= " AND s.clasificacion_hallazgo = ?";
        $params[] = $filtro_clasificacion;
    }
    
    // Búsqueda general
    if (!empty($searchValue)) {
        $where .= " AND (s.codigo_sac LIKE ? OR s.identifica_sac LIKE ? OR s.descripcion_hallazgo LIKE ?)";
        $searchParam = "%$searchValue%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }
    
    // Consulta principal
    $sql = "SELECT s.*, p.descripcion_proceso 
            FROM sac s 
            LEFT JOIN proceso p ON s.codigo_proceso = p.proceso 
            $where 
            ORDER BY s.fecha_apertura DESC 
            LIMIT $start, $length";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Contar total de registros
    $sqlCount = "SELECT COUNT(*) as total FROM sac s LEFT JOIN proceso p ON s.codigo_proceso = p.proceso $where";
    $stmtCount = $conexion->prepare($sqlCount);
    $stmtCount->execute($params);
    $totalRecords = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Contar registros filtrados
    $recordsFiltered = $totalRecords;
    
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $recordsFiltered,
        'data' => $data
    ]);
}

function crearSAC($conexion) {
    // Generar código SAC automático
    $anio = date('Y');
    $sql = "SELECT COUNT(*) + 1 as siguiente FROM sac WHERE YEAR(fecha_apertura) = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$anio]);
    $siguiente = $stmt->fetch(PDO::FETCH_ASSOC)['siguiente'];
    $codigo_sac = 'SAC-' . $anio . '-' . str_pad($siguiente, 3, '0', STR_PAD_LEFT);
    
    // Validar campos requeridos
    $campos_requeridos = ['fecha_apertura', 'codigo_proceso', 'identifica_sac', 'tipo_sac', 'fuente_sac', 'fecha_suceso', 'descripcion_hallazgo', 'decide_tratamiento', 'clasificacion_hallazgo'];
    
    foreach ($campos_requeridos as $campo) {
        if (empty($_POST[$campo])) {
            echo json_encode(['status' => 'error', 'message' => 'El campo ' . $campo . ' es requerido']);
            return;
        }
    }
    
    // Insertar SAC
    $sql = "INSERT INTO sac (
                fecha_apertura, codigo_sac, codigo_area, codigo_proceso, identifica_sac, 
                orden_trabajo, proyecto_actividad, tipo_sac, fuente_sac, normativa, 
                fecha_suceso, descripcion_hallazgo, decide_tratamiento, fecha_decision, 
                tipo_tratamiento, tratamiento_inmediato, costo_no_calidad, 
                estado_acciones_inmediatas, estado_registro_sac, clasificacion_hallazgo, 
                fecha_respuesta_sac, fecha_cierre_sac, observaciones, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $_POST['fecha_apertura'],
        $codigo_sac,
        $_POST['codigo_proceso'], // Usar proceso como área por defecto
        $_POST['codigo_proceso'],
        $_POST['identifica_sac'],
        $_POST['orden_trabajo'] ?? null,
        $_POST['proyecto_actividad'] ?? null,
        $_POST['tipo_sac'],
        $_POST['fuente_sac'],
        $_POST['normativa'] ?? null,
        $_POST['fecha_suceso'],
        $_POST['descripcion_hallazgo'],
        $_POST['decide_tratamiento'],
        !empty($_POST['fecha_decision']) ? $_POST['fecha_decision'] : null,
        $_POST['tipo_tratamiento'] ?? null,
        $_POST['tratamiento_inmediato'] ?? null,
        $_POST['costo_no_calidad'] ?? 0.00,
        $_POST['estado_acciones_inmediatas'] ?? 'Pendiente',
        $_POST['estado_registro_sac'] ?? 'Abierto',
        $_POST['clasificacion_hallazgo'],
        !empty($_POST['fecha_respuesta_sac']) ? $_POST['fecha_respuesta_sac'] : null,
        !empty($_POST['fecha_cierre_sac']) ? $_POST['fecha_cierre_sac'] : null,
        $_POST['observaciones'] ?? null,
        $_SESSION['username'] ?? 'sistema'
    ];
    
    $stmt = $conexion->prepare($sql);
    
    if ($stmt->execute($params)) {
        echo json_encode(['status' => 'success', 'message' => 'SAC creado exitosamente con código: ' . $codigo_sac]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al crear el SAC']);
    }
}

function actualizarSAC($conexion) {
    $id = $_POST['id'];
    
    // Validar que el SAC existe
    $sql = "SELECT id FROM sac WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'SAC no encontrado']);
        return;
    }
    
    // Actualizar SAC
    $sql = "UPDATE sac SET 
                fecha_apertura = ?, codigo_area = ?, codigo_proceso = ?, identifica_sac = ?, 
                orden_trabajo = ?, proyecto_actividad = ?, tipo_sac = ?, fuente_sac = ?, 
                normativa = ?, fecha_suceso = ?, descripcion_hallazgo = ?, decide_tratamiento = ?, 
                fecha_decision = ?, tipo_tratamiento = ?, tratamiento_inmediato = ?, 
                costo_no_calidad = ?, estado_acciones_inmediatas = ?, estado_registro_sac = ?, 
                clasificacion_hallazgo = ?, fecha_respuesta_sac = ?, fecha_cierre_sac = ?, 
                observaciones = ?, es_eficaz = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?";
    
    $params = [
        $_POST['fecha_apertura'],
        $_POST['codigo_proceso'], // Usar proceso como área
        $_POST['codigo_proceso'],
        $_POST['identifica_sac'],
        $_POST['orden_trabajo'] ?? null,
        $_POST['proyecto_actividad'] ?? null,
        $_POST['tipo_sac'],
        $_POST['fuente_sac'],
        $_POST['normativa'] ?? null,
        $_POST['fecha_suceso'],
        $_POST['descripcion_hallazgo'],
        $_POST['decide_tratamiento'],
        !empty($_POST['fecha_decision']) ? $_POST['fecha_decision'] : null,
        $_POST['tipo_tratamiento'] ?? null,
        $_POST['tratamiento_inmediato'] ?? null,
        $_POST['costo_no_calidad'] ?? 0.00,
        $_POST['estado_acciones_inmediatas'] ?? 'Pendiente',
        $_POST['estado_registro_sac'] ?? 'Abierto',
        $_POST['clasificacion_hallazgo'],
        !empty($_POST['fecha_respuesta_sac']) ? $_POST['fecha_respuesta_sac'] : null,
        !empty($_POST['fecha_cierre_sac']) ? $_POST['fecha_cierre_sac'] : null,
        $_POST['observaciones'] ?? null,
        $_POST['es_eficaz'] ?? 'Pendiente Evaluacion',
        $id
    ];
    
    $stmt = $conexion->prepare($sql);
    
    if ($stmt->execute($params)) {
        echo json_encode(['status' => 'success', 'message' => 'SAC actualizado exitosamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el SAC']);
    }
}

function eliminarSAC($conexion) {
    $id = $_POST['id'];
    
    $sql = "DELETE FROM sac WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    
    if ($stmt->execute([$id])) {
        echo json_encode(['status' => 'success', 'message' => 'SAC eliminado exitosamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el SAC']);
    }
}

?>
