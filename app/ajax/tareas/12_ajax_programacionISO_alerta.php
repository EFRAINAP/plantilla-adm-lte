<?php

require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new connection_ajax();
$conexion = $objeto->conectar();

// Obtener el código de proceso desde el POST
$proceso = $_POST['proceso'] ?? '';

// Definir la cabecera para devolver datos en formato JSON
header('Content-Type: application/json; charset=UTF-8');

	$query = "SELECT T0.*, T1.*, T2.*, 
			DATEDIFF(CURDATE(), T1.fecha_programada) AS vencimiento
			FROM programacion_anual T0
			INNER JOIN programacion_detalle T1 ON T0.cod_programa = T1.cod_programa
			INNER JOIN tareas T2 ON T1.cod_tarea = T2.cod_tarea
			WHERE T0.proceso = :proceso
			AND T1.fecha_real IS NULL
			AND T1.fecha_programada <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) 
			ORDER BY T1.cod_programa, T1.cod_tarea, T1.fecha_programada  ASC";

	$stmt = $conexion->prepare($query);
	$stmt->bindParam(':proceso', $proceso);	
	
	$stmt->execute();		
	$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver datos en formato JSON
echo json_encode($data, JSON_UNESCAPED_UNICODE);

?>