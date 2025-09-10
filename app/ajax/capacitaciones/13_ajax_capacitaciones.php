<?php

require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new connection_ajax();
$conexion = $objeto->conectar();

// Obtener los parámetros desde el POST
$proceso = $_POST['proceso'] ?? null;

// Definir la cabecera para devolver datos en formato JSON
header('Content-Type: application/json; charset=UTF-8');

// Inicializar la variable de respuesta
$response = [];

try {
    // Validar que el proceso no sea nulo o vacío
    if ($proceso === null || $proceso === '') {
        throw new Exception("El código de proceso no está definido o es inválido.");
    }

    // Preparar la consulta base
    $query = "SELECT * FROM programacion_capacitacion";

    // Si el proceso no es 'todos', agregar el filtro por proceso
    if ($proceso !== 'Todos') {
        $query .= " WHERE proceso = :proceso";
    }

    // Añadir el orden por fecha
    $query .= " ORDER BY fecha_programada ASC";

    // Preparar la consulta
    $stmt = $conexion->prepare($query);

    // Si el proceso no es 'todos', bindear el parámetro
    if ($proceso !== 'Todos') {
        $stmt->bindParam(':proceso', $proceso, PDO::PARAM_STR);
    }

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener y devolver los datos
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Manejo de errores
    http_response_code(400); // Devuelve un código HTTP de error
    $response = [
        "error" => true,
        "message" => $e->getMessage()
    ];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

?>



