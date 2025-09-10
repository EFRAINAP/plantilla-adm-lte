<?php

require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new connection_ajax();
$conexion = $objeto->conectar();

// Obtener el código para la solicitud GET
$var_cod_tarea = $_GET['cod_tarea'] ?? '';

// Definir la cabecera para devolver datos en formato JSON
header('Content-Type: application/json; charset=UTF-8');
// Consulta para obtener los datos
    $query = "SELECT * FROM tareas T0  
              INNER JOIN programacion_detalle T1
              ON T1.cod_tarea = T0.cod_tarea
              WHERE T0.cod_tarea = :cod_tarea 
              ORDER BY T0.cod_tarea ASC";
    
	$stmt = $conexion->prepare($query);
	$stmt->bindParam(':cod_tarea', $var_cod_tarea);
	$stmt->execute();
	$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver datos en formato JSON
echo json_encode($data, JSON_UNESCAPED_UNICODE);

?>

