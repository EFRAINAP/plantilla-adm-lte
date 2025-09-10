<?php

// usemos __DIR__ para la url
require_once(__DIR__ . '/../../core/01_constants.php');

// obtener el usuario actual
$user = current_user();

// Verificar si el usuario tiene acceso a esta página

// obtener el proceso del usuario
$username = $user['username'];
$user_level = $user['user_level'];

if ($user_level == '1') {
    $all_proceso = find_table_array('proceso');
} else {
    $all_proceso = find_table_2_field_1_array('acceso_perfiles', 'perfiles', 'perfil','perfil', 'username', $username);
    $procesos_asignados = array_unique(array_column($all_proceso, 'proceso'));
    // Ahora obtenemos todos los procesos con descripción
    $procesos_full = find_table_array('proceso');

    // Filtramos solo los procesos asignados
    $procesos_filtrados = array_filter($procesos_full, function($proceso) use ($procesos_asignados) {
        return in_array($proceso['proceso'], $procesos_asignados);
    });

    // Si quieres reindexar el array:
    $all_proceso = array_values($procesos_filtrados);
}

if (!$all_proceso) {
    return [
        'general' => [
            'tareas_pendientes' => 0,
            'eficacia' => '0.00',
            'procesos_count' => 0
        ],
        'por_proceso' => [],
        'mostrar_detalle' => false
    ];
}

function calcularTareasProceso($proceso_id) {
    $db = new Pdo_DB();
    
    try {
        // Tareas completadas
        $sqlCompletadas = "SELECT COUNT(*) as total FROM programacion_detalle T1
            INNER JOIN programacion_anual T2 ON T1.cod_programa = T2.cod_programa
            WHERE T2.proceso = :proceso
            AND T1.fecha_real IS NOT NULL
            AND T1.fecha_programada <= CURDATE() 
            AND YEAR(T1.fecha_programada) = YEAR(CURDATE())";
        
        $stmtCompletadas = $db->query($sqlCompletadas, [':proceso' => $proceso_id]);
        $rowCompletadas = $db->fetch_assoc($stmtCompletadas);
        $tareas_completadas = $rowCompletadas['total'] ?? 0;

        // Tareas programadas
        $sqlProgramadas = "SELECT COUNT(*) as total FROM programacion_detalle T1
            INNER JOIN programacion_anual T2 ON T1.cod_programa = T2.cod_programa
            WHERE T2.proceso = :proceso
            AND T1.fecha_programada IS NOT NULL
            AND T1.fecha_programada <= CURDATE()
            AND YEAR(T1.fecha_programada) = YEAR(CURDATE())";
        
        $stmtProgramadas = $db->query($sqlProgramadas, [':proceso' => $proceso_id]);
        $rowProgramadas = $db->fetch_assoc($stmtProgramadas);
        $tareas_programadas = $rowProgramadas['total'] ?? 0;

        // Calcular
        $tareas_pendientes = $tareas_programadas - $tareas_completadas;
        $eficacia = ($tareas_programadas > 0) ? (($tareas_completadas / $tareas_programadas) * 100) : 0;

        return [
            'tareas_pendientes' => $tareas_pendientes,
            'eficacia' => number_format($eficacia, 2),
            'tareas_completadas' => $tareas_completadas,
            'tareas_programadas' => $tareas_programadas
        ];
        
    } catch (Exception $e) {
        return [
            'tareas_pendientes' => 0,
            'eficacia' => '0.00',
            'tareas_completadas' => 0,
            'tareas_programadas' => 0
        ];
    }
}

// Calcular para cada proceso
$total_tareas_pendientes = 0;
$total_tareas_completadas = 0;
$total_tareas_programadas = 0;
$procesos_data = [];

foreach ($all_proceso as $proceso) {
    $proceso_id = isset($proceso['proceso']) ? $proceso['proceso'] : (isset($proceso['id']) ? $proceso['id'] : $proceso);
    $proceso_nombre = isset($proceso['descripcion_proceso']) ? $proceso['descripcion_proceso'] : $proceso;
    
    $datos = calcularTareasProceso($proceso_id);
    
    // Acumular totales reales (no promedios)
    $total_tareas_pendientes += $datos['tareas_pendientes'];
    $total_tareas_completadas += $datos['tareas_completadas'];
    $total_tareas_programadas += $datos['tareas_programadas'];
    
    // Guardar datos del proceso
    $procesos_data[] = [
        'nombre' => $proceso_nombre,
        'tareas_pendientes' => $datos['tareas_pendientes'],
        'eficacia' => $datos['eficacia']
    ];
}

// Calcular eficacia promedio general
$procesos_count = count($all_proceso);
$eficacia_general = ($total_tareas_programadas > 0) ? (($total_tareas_completadas / $total_tareas_programadas) * 100) : 0;

// Determinar si mostrar detalle por proceso (solo si tiene 2+ procesos)
$mostrar_detalle = ($procesos_count >= 2);

// Devolver datos
return [
    'general' => [
        'tareas_pendientes' => $total_tareas_pendientes,
        'eficacia' => number_format($eficacia_general, 2),
        'procesos_count' => $procesos_count
    ],
    'por_proceso' => $procesos_data,
    'mostrar_detalle' => $mostrar_detalle
];


