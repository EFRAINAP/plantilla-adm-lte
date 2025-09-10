<?php

require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new connection_ajax();
$conexion = $objeto->conectar();

// Obtener el código de distribución de la solicitud GET
//$var_ot = $_GET['ot'] ?? '';
$var_ot = isset($_GET['ot']) ? $_GET['ot'] : 'OTC-004487';

// Definir la cabecera para devolver datos en formato JSON
header('Content-Type: application/json; charset=UTF-8');

    // Si hay un código de distribución, hacer la consulta filtrada
	$query = "SELECT * FROM orden_fabricacion T0
              WHERE T0.ot = :ot 
              ORDER BY T0.id ASC";

    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':ot', $var_ot);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE); // Devolver datos en formato JSON

?>
