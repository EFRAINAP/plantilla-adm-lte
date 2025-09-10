<?php

require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new connection_ajax();
$conexion = $objeto->conectar();

// Obtener el código para la solicitud GET
$cod_programa = $_GET['cod_programa'] ?? '';

// Definir la cabecera para devolver datos en formato JSON
header('Content-Type: application/json; charset=UTF-8');

// Preparar la consulta para evitar inyecciones SQL
$query = " SELECT 
		T0.cod_tarea,
		T1.descripcion_tarea,
		T1.cod_documento,
		T1.frecuencia,
		'P' AS tipo,
		MAX(CASE WHEN T0.semana = 's01' THEN T0.fecha_programada END) AS s01,
		MAX(CASE WHEN T0.semana = 's02' THEN T0.fecha_programada END) AS s02,
		MAX(CASE WHEN T0.semana = 's03' THEN T0.fecha_programada END) AS s03,
		MAX(CASE WHEN T0.semana = 's04' THEN T0.fecha_programada END) AS s04,
		MAX(CASE WHEN T0.semana = 's05' THEN T0.fecha_programada END) AS s05,
		MAX(CASE WHEN T0.semana = 's06' THEN T0.fecha_programada END) AS s06,
		MAX(CASE WHEN T0.semana = 's07' THEN T0.fecha_programada END) AS s07,
		MAX(CASE WHEN T0.semana = 's08' THEN T0.fecha_programada END) AS s08,
		MAX(CASE WHEN T0.semana = 's09' THEN T0.fecha_programada END) AS s09,
		MAX(CASE WHEN T0.semana = 's10' THEN T0.fecha_programada END) AS s10,
		MAX(CASE WHEN T0.semana = 's11' THEN T0.fecha_programada END) AS s11,
		MAX(CASE WHEN T0.semana = 's12' THEN T0.fecha_programada END) AS s12,
		MAX(CASE WHEN T0.semana = 's13' THEN T0.fecha_programada END) AS s13,
		MAX(CASE WHEN T0.semana = 's14' THEN T0.fecha_programada END) AS s14,
		MAX(CASE WHEN T0.semana = 's15' THEN T0.fecha_programada END) AS s15,
		MAX(CASE WHEN T0.semana = 's16' THEN T0.fecha_programada END) AS s16,
		MAX(CASE WHEN T0.semana = 's17' THEN T0.fecha_programada END) AS s17,
		MAX(CASE WHEN T0.semana = 's18' THEN T0.fecha_programada END) AS s18,
		MAX(CASE WHEN T0.semana = 's19' THEN T0.fecha_programada END) AS s19,
		MAX(CASE WHEN T0.semana = 's20' THEN T0.fecha_programada END) AS s20,
		MAX(CASE WHEN T0.semana = 's21' THEN T0.fecha_programada END) AS s21,
		MAX(CASE WHEN T0.semana = 's22' THEN T0.fecha_programada END) AS s22,
		MAX(CASE WHEN T0.semana = 's23' THEN T0.fecha_programada END) AS s23,
		MAX(CASE WHEN T0.semana = 's24' THEN T0.fecha_programada END) AS s24,
		MAX(CASE WHEN T0.semana = 's25' THEN T0.fecha_programada END) AS s25,
		MAX(CASE WHEN T0.semana = 's26' THEN T0.fecha_programada END) AS s26,
		MAX(CASE WHEN T0.semana = 's27' THEN T0.fecha_programada END) AS s27,
		MAX(CASE WHEN T0.semana = 's28' THEN T0.fecha_programada END) AS s28,
		MAX(CASE WHEN T0.semana = 's29' THEN T0.fecha_programada END) AS s29,
		MAX(CASE WHEN T0.semana = 's30' THEN T0.fecha_programada END) AS s30,
		MAX(CASE WHEN T0.semana = 's31' THEN T0.fecha_programada END) AS s31,
		MAX(CASE WHEN T0.semana = 's32' THEN T0.fecha_programada END) AS s32,
		MAX(CASE WHEN T0.semana = 's33' THEN T0.fecha_programada END) AS s33,
		MAX(CASE WHEN T0.semana = 's34' THEN T0.fecha_programada END) AS s34,
		MAX(CASE WHEN T0.semana = 's35' THEN T0.fecha_programada END) AS s35,
		MAX(CASE WHEN T0.semana = 's36' THEN T0.fecha_programada END) AS s36,
		MAX(CASE WHEN T0.semana = 's37' THEN T0.fecha_programada END) AS s37,
		MAX(CASE WHEN T0.semana = 's38' THEN T0.fecha_programada END) AS s38,
		MAX(CASE WHEN T0.semana = 's39' THEN T0.fecha_programada END) AS s39,
		MAX(CASE WHEN T0.semana = 's40' THEN T0.fecha_programada END) AS s40,
		MAX(CASE WHEN T0.semana = 's41' THEN T0.fecha_programada END) AS s41,
		MAX(CASE WHEN T0.semana = 's42' THEN T0.fecha_programada END) AS s42,
		MAX(CASE WHEN T0.semana = 's43' THEN T0.fecha_programada END) AS s43,
		MAX(CASE WHEN T0.semana = 's44' THEN T0.fecha_programada END) AS s44,
		MAX(CASE WHEN T0.semana = 's45' THEN T0.fecha_programada END) AS s45,
		MAX(CASE WHEN T0.semana = 's46' THEN T0.fecha_programada END) AS s46,
		MAX(CASE WHEN T0.semana = 's47' THEN T0.fecha_programada END) AS s47,
		MAX(CASE WHEN T0.semana = 's48' THEN T0.fecha_programada END) AS s48,
		MAX(CASE WHEN T0.semana = 's49' THEN T0.fecha_programada END) AS s49,
		MAX(CASE WHEN T0.semana = 's50' THEN T0.fecha_programada END) AS s50,
		MAX(CASE WHEN T0.semana = 's51' THEN T0.fecha_programada END) AS s51,
		MAX(CASE WHEN T0.semana = 's52' THEN T0.fecha_programada END) AS s52
		FROM 
			programacion_detalle T0
		INNER JOIN 
			tareas T1 ON T0.cod_tarea = T1.cod_tarea
		WHERE
			T0.cod_programa = :cod_programa
		GROUP BY 
			T0.cod_tarea, T1.descripcion_tarea, T1.cod_documento, T1.frecuencia
		UNION ALL
		SELECT 
		T0.cod_tarea,
		T1.descripcion_tarea,
		T1.cod_documento,
		T1.frecuencia,
		'R' AS tipo,
		MAX(CASE WHEN T0.semana = 's01' THEN T0.fecha_real END) AS s01,
		MAX(CASE WHEN T0.semana = 's02' THEN T0.fecha_real END) AS s02,
		MAX(CASE WHEN T0.semana = 's03' THEN T0.fecha_real END) AS s03,
		MAX(CASE WHEN T0.semana = 's04' THEN T0.fecha_real END) AS s04,
		MAX(CASE WHEN T0.semana = 's05' THEN T0.fecha_real END) AS s05,
		MAX(CASE WHEN T0.semana = 's06' THEN T0.fecha_real END) AS s06,
		MAX(CASE WHEN T0.semana = 's07' THEN T0.fecha_real END) AS s07,
		MAX(CASE WHEN T0.semana = 's08' THEN T0.fecha_real END) AS s08,
		MAX(CASE WHEN T0.semana = 's09' THEN T0.fecha_real END) AS s09,
		MAX(CASE WHEN T0.semana = 's10' THEN T0.fecha_real END) AS s10,
		MAX(CASE WHEN T0.semana = 's11' THEN T0.fecha_real END) AS s11,
		MAX(CASE WHEN T0.semana = 's12' THEN T0.fecha_real END) AS s12,
		MAX(CASE WHEN T0.semana = 's13' THEN T0.fecha_real END) AS s13,
		MAX(CASE WHEN T0.semana = 's14' THEN T0.fecha_real END) AS s14,
		MAX(CASE WHEN T0.semana = 's15' THEN T0.fecha_real END) AS s15,
		MAX(CASE WHEN T0.semana = 's16' THEN T0.fecha_real END) AS s16,
		MAX(CASE WHEN T0.semana = 's17' THEN T0.fecha_real END) AS s17,
		MAX(CASE WHEN T0.semana = 's18' THEN T0.fecha_real END) AS s18,
		MAX(CASE WHEN T0.semana = 's19' THEN T0.fecha_real END) AS s19,
		MAX(CASE WHEN T0.semana = 's20' THEN T0.fecha_real END) AS s20,
		MAX(CASE WHEN T0.semana = 's21' THEN T0.fecha_real END) AS s21,
		MAX(CASE WHEN T0.semana = 's22' THEN T0.fecha_real END) AS s22,
		MAX(CASE WHEN T0.semana = 's23' THEN T0.fecha_real END) AS s23,
		MAX(CASE WHEN T0.semana = 's24' THEN T0.fecha_real END) AS s24,
		MAX(CASE WHEN T0.semana = 's25' THEN T0.fecha_real END) AS s25,
		MAX(CASE WHEN T0.semana = 's26' THEN T0.fecha_real END) AS s26,
		MAX(CASE WHEN T0.semana = 's27' THEN T0.fecha_real END) AS s27,
		MAX(CASE WHEN T0.semana = 's28' THEN T0.fecha_real END) AS s28,
		MAX(CASE WHEN T0.semana = 's29' THEN T0.fecha_real END) AS s29,
		MAX(CASE WHEN T0.semana = 's30' THEN T0.fecha_real END) AS s30,
		MAX(CASE WHEN T0.semana = 's31' THEN T0.fecha_real END) AS s31,
		MAX(CASE WHEN T0.semana = 's32' THEN T0.fecha_real END) AS s32,
		MAX(CASE WHEN T0.semana = 's33' THEN T0.fecha_real END) AS s33,
		MAX(CASE WHEN T0.semana = 's34' THEN T0.fecha_real END) AS s34,
		MAX(CASE WHEN T0.semana = 's35' THEN T0.fecha_real END) AS s35,
		MAX(CASE WHEN T0.semana = 's36' THEN T0.fecha_real END) AS s36,
		MAX(CASE WHEN T0.semana = 's37' THEN T0.fecha_real END) AS s37,
		MAX(CASE WHEN T0.semana = 's38' THEN T0.fecha_real END) AS s38,
		MAX(CASE WHEN T0.semana = 's39' THEN T0.fecha_real END) AS s39,
		MAX(CASE WHEN T0.semana = 's40' THEN T0.fecha_real END) AS s40,
		MAX(CASE WHEN T0.semana = 's41' THEN T0.fecha_real END) AS s41,
		MAX(CASE WHEN T0.semana = 's42' THEN T0.fecha_real END) AS s42,
		MAX(CASE WHEN T0.semana = 's43' THEN T0.fecha_real END) AS s43,
		MAX(CASE WHEN T0.semana = 's44' THEN T0.fecha_real END) AS s44,
		MAX(CASE WHEN T0.semana = 's45' THEN T0.fecha_real END) AS s45,
		MAX(CASE WHEN T0.semana = 's46' THEN T0.fecha_real END) AS s46,
		MAX(CASE WHEN T0.semana = 's47' THEN T0.fecha_real END) AS s47,
		MAX(CASE WHEN T0.semana = 's48' THEN T0.fecha_real END) AS s48,
		MAX(CASE WHEN T0.semana = 's49' THEN T0.fecha_real END) AS s49,
		MAX(CASE WHEN T0.semana = 's50' THEN T0.fecha_real END) AS s50,
		MAX(CASE WHEN T0.semana = 's51' THEN T0.fecha_real END) AS s51,
		MAX(CASE WHEN T0.semana = 's52' THEN T0.fecha_real END) AS s52
	FROM 
		programacion_detalle T0
	INNER JOIN 
		tareas T1 ON T0.cod_tarea = T1.cod_tarea
	WHERE
		T0.cod_programa = :cod_programa
	GROUP BY 
		T0.cod_tarea, T1.descripcion_tarea, T1.cod_documento, T1.frecuencia
	ORDER BY 
		cod_tarea, tipo";

			
$stmt = $conexion->prepare($query);
$stmt->bindParam(':cod_programa', $cod_programa, PDO::PARAM_STR);
$stmt->execute();

// Obtener y devolver los datos
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($data, JSON_UNESCAPED_UNICODE);

?>
