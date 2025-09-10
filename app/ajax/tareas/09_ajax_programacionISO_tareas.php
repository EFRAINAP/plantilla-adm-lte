<?php

require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new connection_ajax();
$conexion = $objeto->conectar();

// Obtener el código para la solicitud GET
$proceso = $_GET['proceso'] ?? '';

// Definir la cabecera para devolver datos en formato JSON
header('Content-Type: application/json; charset=UTF-8');

// Preparar la consulta para evitar inyecciones SQL
$query = " SELECT * FROM tareas T0
		   WHERE T0.cod_tarea NOT IN (
				 SELECT T1.cod_tarea 
				 FROM programacion_detalle T1)
		   AND T0.proceso = :proceso 
		   ORDER BY T0.cod_tarea ASC ";
	
$stmt = $conexion->prepare($query);
$stmt->bindParam(':proceso', $proceso, PDO::PARAM_STR);
$stmt->execute();

// Obtener y devolver los datos
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($data, JSON_UNESCAPED_UNICODE);

?>