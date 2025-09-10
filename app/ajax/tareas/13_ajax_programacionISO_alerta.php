<?php

require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new connection_ajax();
$conexion = $objeto->conectar();

// Obtener los parámetros desde el POST
$proceso = $_POST['proceso'] ?? null;
$tipo_alerta = $_POST['tipo_alerta'] ?? null;

// Definir la cabecera para devolver datos en formato JSON
header('Content-Type: application/json; charset=UTF-8');

// Inicializar la variable de respuesta
$response = [];

try {
    // Validar que $tipo_alerta no sea nulo o vacío
    if (!$tipo_alerta) {
        throw new Exception("El parámetro 'tipo_alerta' es obligatorio.");
    }

    switch ($tipo_alerta) {
        case "Total":
            // Consulta para obtener todas las capacitaciones próximas a vencer
            $query = "SELECT T0.*, DATEDIFF(CURDATE(), T0.fecha_programada) AS vencimiento
                      FROM programacion_capacitacion T0
                      WHERE T0.fecha_real IS NULL
                      AND T0.fecha_programada <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                      ORDER BY T0.fecha_programada ASC";
            $stmt = $conexion->prepare($query);
            $stmt->execute();
            break;

        case "Proceso":
            // Validar que $proceso no sea nulo o vacío
            if (!$proceso) {
                throw new Exception("El parámetro 'proceso' es obligatorio para esta opción.");
            }
            // Consulta para obtener capacitaciones de un proceso específico
            $query = "SELECT T0.*, DATEDIFF(CURDATE(), T0.fecha_programada) AS vencimiento
                      FROM programacion_capacitacion T0
                      WHERE T0.proceso = :proceso
                      AND T0.fecha_real IS NULL
                      AND T0.fecha_programada <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                      ORDER BY T0.fecha_programada ASC";
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':proceso', $proceso, PDO::PARAM_STR);
            $stmt->execute();
            break;

        default:
            // Respuesta en caso de opción no válida
            throw new Exception("Opción no válida para 'tipo_alerta'.");
    }

    // Obtener y devolver los datos
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    // Manejo de errores
    http_response_code(400); // Código HTTP para errores del cliente
    $response = [
        "error" => true,
        "message" => $e->getMessage()
    ];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

?>
