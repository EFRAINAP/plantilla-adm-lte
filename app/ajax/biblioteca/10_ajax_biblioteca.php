<?php

require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new Connection_Ajax();
$conexion = $objeto->Conectar();

// Obtener el filtro de categoría desde la solicitud AJAX
$categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';

// Construir la consulta
if ($categoria != '') {
    // Si se ha seleccionado una categoría, filtrar por esa categoría
    $query = "SELECT * FROM biblioteca T0 
              WHERE T0.categoria = :categoria 
              ORDER BY T0.categoria, T0.clasificacion, T0.cod_documento ASC;";
} else {
    // Si no hay filtro de categoría, recuperar todos los registros
    $query = "SELECT * FROM biblioteca T0 
              ORDER BY T0.categoria, T0.clasificacion, T0.cod_documento ASC;";
}

// Preparar la consulta
$resultado = $conexion->prepare($query);

// Asignar el valor del filtro de categoría si está presente
if ($categoria != '') {
    $resultado->bindParam(':categoria', $categoria, PDO::PARAM_STR);
}

// Ejecutar la consulta
$resultado->execute();

// Obtener los resultados y enviarlos como JSON
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($data, JSON_UNESCAPED_UNICODE);

// Cerrar la conexión
$conexion = null;

?>