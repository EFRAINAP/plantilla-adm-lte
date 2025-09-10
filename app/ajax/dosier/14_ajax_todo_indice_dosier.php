<?php

require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new connection_ajax();
$conexion = $objeto->conectar();

// Obtener el id de la solicitud GET
$var_id = $_GET['id'];
$var_tipo = $_GET['tipo'];

// Definir la cabecera para devolver datos en formato JSON
header('Content-Type: application/json; charset=UTF-8');

try {
    // Obtener todos los índices de dosier ordenados por capitulo, orden y suborden
    $query_todos_indice = "SELECT id, titulo, funcion, orden, capitulo, suborden FROM indice_dosier WHERE tipo = :tipo ORDER BY capitulo ASC, orden ASC, suborden ASC";
    $stmt_todo = $conexion->prepare($query_todos_indice);
    $stmt_todo->bindParam(':tipo', $var_tipo);
    $stmt_todo->execute();
    $todo_indice_dosier = $stmt_todo->fetchAll(PDO::FETCH_ASSOC);

    $result = [];

    if (!empty($var_id)) {
        // Obtener los índices actuales del dosier
        $query_indices = "SELECT id, id_indice_dosier, completar
                          FROM dosier_indice_detalle
                          WHERE id_dosier_calidad = :id";
        $stmt_indices = $conexion->prepare($query_indices);
        $stmt_indices->bindParam(':id', $var_id);
        $stmt_indices->execute();
        $indices_actuales = $stmt_indices->fetchAll(PDO::FETCH_ASSOC);
        
        // Crear un mapa de índices actuales
        $mapa_indices = [];
        foreach ($indices_actuales as $indice) {
            $mapa_indices[$indice['id_indice_dosier']] = [
                'completar' => ($indice['completar'] == 1)
            ];
        }
        
        // Combinar todos los índices con los asignados actuales
        foreach ($todo_indice_dosier as $indice) {
            $id_indice = $indice['id'];
            $tiene_acceso = isset($mapa_indices[$id_indice]); 
            
            $result[] = [
                'id_dosier_calidad' => $var_id,
                'id_indice_dosier' => $id_indice,
                'titulo' => $indice['titulo'],
                'tiene_acceso' => $tiene_acceso,
                'completar' => $tiene_acceso ? $mapa_indices[$id_indice]['completar'] : false,
                'funcion' => $indice['funcion'] ?? null, // Agregar función si está disponible
                'orden' => $indice['orden'] ?? null, // Agregar orden si está disponible
                'capitulo' => $indice['capitulo'] ?? null, // Agregar capítulo si está disponible
                'suborden' => $indice['suborden'] ?? 0 // Agregar suborden si está disponible
            ];
        }
    } else {
        // Si no hay id, devolver solo los índices disponibles
        foreach ($todo_indice_dosier as $indice) {
            $result[] = [
                'id_dosier_calidad' => '',
                'id_indice_dosier' => $indice['id'],
                'titulo' => $indice['titulo'],
                'tiene_acceso' => false,
                'completar' => false,
                'funcion' => $indice['funcion'] ?? null, // Agregar función si está disponible
                'orden' => $indice['orden'] ?? null, // Agregar orden si está disponible
                'capitulo' => $indice['capitulo'] ?? null, // Agregar capítulo si está disponible
                'suborden' => $indice['suborden'] ?? 0 // Agregar suborden si está disponible
            ];
        }
    }
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al obtener los índices: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

?>
