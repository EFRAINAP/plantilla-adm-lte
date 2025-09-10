<?php

require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new connection_ajax();
$conexion = $objeto->conectar();

// Obtener el código para la solicitud GET
$var_consulta = $_GET['var_consulta'] ?? '';
$cod_programa = $_GET['cod_programa'] ?? '';
$cod_tarea = $_GET['cod_tarea'] ?? '';

// Definir la cabecera para devolver datos en formato JSON
header('Content-Type: application/json; charset=UTF-8');

switch ($var_consulta) {
	
	case "A":
		$query = "SELECT DISTINCT T1.cod_programa AS cod_programa , T1.cod_tarea AS cod_tarea , T2.descripcion_tarea AS descripcion_tarea, 
                      T2.cod_documento AS cod_documento  , T2.frecuencia AS frecuencia,  T1.alerta AS alerta,
                      -- Agregar campo para detectar pendientes
                      (SELECT COUNT(*) FROM programacion_detalle T3 
                       WHERE T3.cod_programa = T1.cod_programa 
                       AND T3.cod_tarea = T1.cod_tarea AND T3.fecha_programada <= CURDATE()
                       AND (T3.fecha_real IS NULL OR T3.fecha_real = '')) AS tiene_pendientes
            FROM programacion_anual T0
            INNER JOIN programacion_detalle T1 ON T0.cod_programa = T1.cod_programa
            INNER JOIN tareas T2 ON T1.cod_tarea = T2.cod_tarea
            WHERE T0.cod_programa = :cod_programa
            ORDER BY T1.cod_tarea, T1.fecha_programada  ASC";

		$stmt = $conexion->prepare($query);
		$stmt->bindParam(':cod_programa', $cod_programa);

		break;
		
	case "B":
	
		$query = "SELECT * 
            FROM programacion_anual T0
            INNER JOIN programacion_detalle T1 ON T0.cod_programa = T1.cod_programa
            INNER JOIN tareas T2 ON T1.cod_tarea = T2.cod_tarea
            WHERE T0.cod_programa = :cod_programa
            AND T1.cod_tarea = :cod_tarea
            ORDER BY 
                T1.fecha_real IS NULL DESC,  -- No completadas primero (1), completadas después (0)
                CASE 
                    WHEN T1.fecha_real IS NULL AND T1.fecha_programada < CURDATE() THEN 0  -- Vencidas primero
                    WHEN T1.fecha_real IS NULL AND T1.fecha_programada >= CURDATE() THEN 1  -- Pendientes después
                    ELSE 2  -- Completadas al final
                END,
                T1.cod_tarea, 
                T1.fecha_programada ASC";

		$stmt = $conexion->prepare($query);
		$stmt->bindParam(':cod_programa', $cod_programa);	
		$stmt->bindParam(':cod_tarea', $cod_tarea);			
				
		break;

	default:
	
		echo "Opción no válida.";
		break;
}

	$stmt->execute();		
	$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver datos en formato JSON
echo json_encode($data, JSON_UNESCAPED_UNICODE);

?>