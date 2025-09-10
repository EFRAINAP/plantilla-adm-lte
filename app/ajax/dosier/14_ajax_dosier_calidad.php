<?php

require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new connection_ajax();
$conexion = $objeto->conectar();

// Definir la cabecera para devolver datos en formato JSON
header('Content-Type: application/json; charset=UTF-8');

// Preparar la consulta para evitar inyecciones SQL
$query = "SELECT * FROM dosier_calidad ORDER BY cod_dosier DESC";
$stmt = $conexion->prepare($query);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
echo json_encode($data, JSON_UNESCAPED_UNICODE);

?>
