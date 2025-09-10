<?php

require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new connection_ajax();
$conexion = $objeto->conectar();

// Obtener el código de proceso desde el POST
$proceso = $_POST['proceso'] ?? '';

// Definir la cabecera para devolver datos en formato JSON
header('Content-Type: application/json; charset=UTF-8');

// Preparar la consulta para evitar inyecciones SQL
$query = "SELECT * FROM programacion_anual WHERE proceso = :proceso ORDER BY cod_programa ASC";
$stmt = $conexion->prepare($query);
$stmt->bindParam(':proceso', $proceso, PDO::PARAM_STR);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Hacemos calculo de eficacia -> Si hay fecha real, se cuenta como completada
// Se calaculará del total tareas programadas, cuántas se han completado
// Para calcular la eficacia debemos considerar hasta la fecha de hoy y deben pertener al mismo proceso y año
// Eficacia = (Tareas Completadas / Tareas Programadas) * 100

$queryEficaciaCompletada = "SELECT COUNT(*) as total FROM programacion_detalle T1
    INNER JOIN programacion_anual T2 ON T1.cod_programa = T2.cod_programa
    WHERE T2.proceso = :proceso
    AND T1.fecha_real IS NOT NULL
    AND T1.fecha_programada <= CURDATE() 
    AND YEAR(T1.fecha_programada) = YEAR(CURDATE())";
$stmtEficaciaCompletada = $conexion->prepare($queryEficaciaCompletada);
$stmtEficaciaCompletada->bindParam(':proceso', $proceso, PDO::PARAM_STR);
$stmtEficaciaCompletada->execute();
$resultEficaciaCompletada = $stmtEficaciaCompletada->fetch(PDO::FETCH_ASSOC);
$eficacia_completada = $resultEficaciaCompletada['total'];

$queryEficaciaProgramada = "SELECT COUNT(*) as total FROM programacion_detalle T1
    INNER JOIN programacion_anual T2 ON T1.cod_programa = T2.cod_programa
    WHERE T2.proceso = :proceso
    AND T1.fecha_programada IS NOT NULL
    AND T1.fecha_programada <= CURDATE()
    AND YEAR(T1.fecha_programada) = YEAR(CURDATE())";
$stmtEficaciaProgramada = $conexion->prepare($queryEficaciaProgramada);
$stmtEficaciaProgramada->bindParam(':proceso', $proceso, PDO::PARAM_STR);
$stmtEficaciaProgramada->execute();
$resultEficaciaProgramada = $stmtEficaciaProgramada->fetch(PDO::FETCH_ASSOC);
// Total de tareas programadas
$TotalTareasProgramadas = $resultEficaciaProgramada['total'];

$eficaciaActual = ($TotalTareasProgramadas > 0) ? (($eficacia_completada / $TotalTareasProgramadas) * 100) : 0;


// Hacemos calculo de efectividad -> Si la fecha real es anterior a la fecha programada, se cuenta como efectiva
// Se calaculará del total tareas programadas, cuántas se han completado efectivamente
// Para calcular la efectividad debemos considerar hasta la fecha de hoy y deben pertener al mismo proceso y año
// Efectividad = (Tareas Efectivas / Tareas Programadas) * 100
// Tareas que tienen 2 de retraso tienen 5% menos de efectividad, 5 días de retraso tienen 10% menos, 
//10 días de retraso tienen 20% menos, 15 días tienen 40% menos y superior a 30 días tienen 60% menos.

$queryEfectividad = "SELECT fecha_real, fecha_programada FROM programacion_detalle T1
    INNER JOIN programacion_anual T2 ON T1.cod_programa = T2.cod_programa
    WHERE T2.proceso = :proceso
    AND T1.fecha_real IS NOT NULL
    AND T1.fecha_programada <= CURDATE()
    AND YEAR(T1.fecha_programada) = YEAR(CURDATE())";
$stmtEfectividad = $conexion->prepare($queryEfectividad);
$stmtEfectividad->bindParam(':proceso', $proceso, PDO::PARAM_STR);
$stmtEfectividad->execute();
$resultEfectividad = $stmtEfectividad->fetchAll(PDO::FETCH_ASSOC);

$efectividadTotalEsperada = 100 * $TotalTareasProgramadas; // Valor inicial de efectividad
$efectividadTotal = 0; // Inicializar el total de efectividad
foreach ($resultEfectividad as $row) {
    // Establecer la efectividad según el retraso
    $efectividadTarea = 100; // Valor inicial de efectividad para cada tarea
    $fechaReal = new DateTime($row['fecha_real']);
    $fechaProgramada = new DateTime($row['fecha_programada']);
    $diferenciaDias = $fechaProgramada->diff($fechaReal)->format('%r%a');
    $diferenciaDias = (int)$diferenciaDias;

    if ($diferenciaDias >= 30) {
        $efectividadTarea -= 60;
    } elseif ($diferenciaDias > 15) {
        $efectividadTarea -= 40;
    } elseif ($diferenciaDias >= 5) {
        $efectividadTarea -= 20;
    } elseif ($diferenciaDias >= 2) {
        $efectividadTarea -= 10;
    } elseif ($diferenciaDias > 0) {        
        $efectividadTarea -= 5;
    }

    $efectividadTotal += $efectividadTarea; // Sumar la efectividad de la tarea
}

// Calcular la efectividad total como porcentaje
$efectividadActual = ($efectividadTotalEsperada > 0) ? (($efectividadTotal / $efectividadTotalEsperada) * 100) : 0;

$eficaciaActual = number_format($eficaciaActual, 2);
$efectividadActual = number_format($efectividadActual, 2);

$processed_data = [];
foreach ($data as $row) {
    $processed_data[] = [
        'cod_programa' => $row['cod_programa'],
        'descripcion_programa' => $row['descripcion_programa'],
        'proceso' => $row['proceso'],
        'anio' => $row['anio'],
        'responsable' => $row['responsable'],
        'correo_alerta_n1' => $row['correo_alerta_n1'],
        'correo_alerta_n2' => $row['correo_alerta_n2'],
        'eficacia' => $row['eficacia'],
        'efectividad' => $row['efectividad'],
        'estado_programa' => $row['estado_programa'],
        'eficacia' => $eficaciaActual ?? 0, // Usar el valor calculado de eficacia
        'efectividad' => $efectividadActual ?? 0,
    ];
}
// Obtener y devolver los datos

echo json_encode($processed_data, JSON_UNESCAPED_UNICODE);

?>
