<?php

require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new connection_ajax();
$conexion = $objeto->conectar();

// Obtener el código de proceso desde el POST
$proceso = $_POST['proceso'] ?? null;

// Definir la cabecera para devolver datos en formato JSON
header('Content-Type: application/json; charset=UTF-8');

$query = "SELECT * FROM puestos T0
          WHERE T0.proceso = :proceso 
          ORDER BY T0.cod_puesto ASC";
$stmt = $conexion->prepare($query);
$stmt->bindParam(':proceso', $proceso, PDO::PARAM_STR);
$stmt->execute();

// Obtener y devolver los datos
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($data, JSON_UNESCAPED_UNICODE);

?>