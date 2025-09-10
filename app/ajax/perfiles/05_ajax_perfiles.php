<?php

require_once(__DIR__ . '/../01_ajax_connection.php');

// Definir la cabecera para devolver datos en formato JSON
header('Content-Type: application/json; charset=UTF-8');

try {
    // Crear la conexión
    $objeto = new connection_ajax();
    $conexion = $objeto->conectar();

    // Obtener la operación
    $operacion = isset($_POST['operacion']) ? $_POST['operacion'] : 'listar_perfiles';

    switch ($operacion){
        case "obtener_detalle_perfil":
            $perfil = $_POST['perfil'];
            if (empty($perfil)) {
                echo json_encode([]);
                exit;
            }
            
            $query = "SELECT * FROM perfiles T0  
              INNER JOIN perfiles_detalle T1 ON T0.perfil = T1.perfil 
              INNER JOIN documentos T2 ON T1.cod_documento = T2.cod_documento
              WHERE T1.perfil = :perfil 
              ORDER BY T1.perfil ASC";
    
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':perfil', $perfil);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // DataTables espera el array directamente, no envuelto en un objeto
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;
        
        case "listar_perfiles":
            $query = "SELECT * FROM perfiles ORDER BY perfil ASC";
            $stmt = $conexion->prepare($query);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case "crear_perfil":
            // Obtener variables necesarias
            $perfil = isset($_POST['perfil']) ? $_POST['perfil'] : '';
            $proceso = isset($_POST['proceso']) ? $_POST['proceso'] : '';
            $estado_perfil = isset($_POST['estado_perfil']) ? $_POST['estado_perfil'] : 1;
            $documentos = isset($_POST['documentos']) ? $_POST['documentos'] : [];

            if (empty($perfil)) {
                echo json_encode(['error' => true, 'message' => 'El perfil es requerido']);
                exit;
            }

            // Insertar perfil
            $stmt = $conexion->prepare("INSERT INTO perfiles (perfil, proceso, estado_perfil) VALUES (?, ?, ?)");
            $stmt->execute([$perfil, $proceso, $estado_perfil]);

            // Guardar los detalles de la distribución
            foreach ($documentos as $documento) {
                $stmt = $conexion->prepare("INSERT INTO perfiles_detalle (perfil, cod_documento, modo_almacenamiento, tiempo_retencion, recuperacion, disposicion) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $perfil,
                    $documento['cod_documento'],
                    $documento['modo_almacenamiento'],
                    $documento['tiempo_retencion'],
                    $documento['recuperacion'],
                    $documento['disposicion']
                ]);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Perfil creado exitosamente'
            ], JSON_UNESCAPED_UNICODE);
            break;

        case "editar_perfil":
            $perfil = isset($_POST['perfil']) ? $_POST['perfil'] : '';
            $proceso = isset($_POST['proceso']) ? $_POST['proceso'] : '';
            $estado_perfil = isset($_POST['estado_perfil']) ? $_POST['estado_perfil'] : 1;
            $documentos = isset($_POST['documentos']) ? $_POST['documentos'] : [];

            if (empty($perfil)) {
                echo json_encode(['error' => true, 'message' => 'No se proporcionó un perfil válido']);
                exit;
            }

            // Actualizar la cabecera
            $stmt = $conexion->prepare("UPDATE perfiles SET proceso = ?, estado_perfil = ? WHERE perfil = ?");
            $stmt->execute([$proceso, $estado_perfil, $perfil]);
            
            // Eliminar detalles de la distribución
            $stmt = $conexion->prepare("DELETE FROM perfiles_detalle WHERE perfil = ?");
            $stmt->execute([$perfil]);				
            
            // Reinsertar los detalles de la distribución
            foreach ($documentos as $documento) {
                $stmt = $conexion->prepare("INSERT INTO perfiles_detalle (perfil, cod_documento, modo_almacenamiento, tiempo_retencion, recuperacion, disposicion) 
                                        VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$perfil,
                    $documento['cod_documento'],
                    $documento['modo_almacenamiento'],
                    $documento['tiempo_retencion'],
                    $documento['recuperacion'],
                    $documento['disposicion']
                ]);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Perfil editado exitosamente'
            ], JSON_UNESCAPED_UNICODE);
            break;
        case "eliminar_perfil":
            $perfil = isset($_POST['perfil']) ? $_POST['perfil'] : '';

            if (empty($perfil)) {
                echo json_encode(['error' => true, 'message' => 'No se proporcionó un perfil válido']);
                exit;
            }

            // Eliminar detalles de la distribución
            $stmt = $conexion->prepare("DELETE FROM perfiles_detalle WHERE perfil = ?");
            $stmt->execute([$perfil]);				

            // Eliminar la cabecera
            $stmt = $conexion->prepare("DELETE FROM perfiles WHERE perfil = ?");
            $stmt->execute([$perfil]);

            echo json_encode([
                'success' => true,
                'message' => 'Perfil eliminado exitosamente'
            ], JSON_UNESCAPED_UNICODE);
            break;

        default:
            echo json_encode(['error' => true, 'message' => 'Operación no válida']);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Error en la consulta: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
