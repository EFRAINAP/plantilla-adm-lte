<?php

require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new Connection_Ajax();
$conexion = $objeto->Conectar();
// Inicializar la consulta
$query = "";

$query = "SELECT * FROM pqr T0 
		  ORDER BY T0.numero ASC;";		 

// Preparar y ejecutar la consulta
$resultado = $conexion->prepare($query);
$resultado->execute();

// Obtener los resultados y enviarlos como JSON
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);
print json_encode($data, JSON_UNESCAPED_UNICODE);

// Cerrar la conexión
$conexion = null;

?>