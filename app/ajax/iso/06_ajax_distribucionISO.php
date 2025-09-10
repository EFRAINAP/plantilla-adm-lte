<?php 
$page_title = '06_ajax_distribucionISO.php';
require_once(__DIR__ . '/../01_ajax_connection.php');

// Crear la conexión
$objeto = new Connection_Ajax();
$conexion = $objeto->Conectar();

// Validar datos de entrada
$var_perfil = $_POST['var_perfil'] ?? null;

// Inicializar la consulta
$query = "";
$params = [];

$query = "
	SELECT * FROM documentos T0
	INNER JOIN perfiles_detalle T1 ON T0.cod_documento = T1.cod_documento
	WHERE T1.perfil = :perfil
	AND T0.estado_documento = 1
	ORDER BY T0.cod_documento ASC; ";
	
	$params[':perfil'] = $var_perfil;

	// Verificar si se construyó la consulta
	if (empty($query)) {
		echo json_encode(['error' => 'No se pudo construir la consulta SQL.']);
		exit;
	}

	try {
		// Preparar y ejecutar la consulta
		$resultado = $conexion->prepare($query);
		$resultado->execute($params);

		// Obtener los resultados y enviarlos como JSON
		$data = $resultado->fetchAll(PDO::FETCH_ASSOC);
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	} catch (PDOException $e) {
		echo json_encode(['error' => 'Error al ejecutar la consulta: ' . $e->getMessage()]);
	}

	// Cerrar la conexión
	$conexion = null;

?>