<?php

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 3));
}
require_once BASE_PATH . '/app/core/load.php';

$user = current_user();
if (!$session->isUserLoggedIn(true)) { 
    redirectTo('', false);
}

try {
    // Crear conexión utilizando PDO
    $con = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Configurar el modo de error de PDO para que lance excepciones
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Recuperar datos del formulario
    $operacion = $_POST['var_operacion'] ?? '';
    // Iniciar la transacción
    $con->beginTransaction();

    try {
        switch ($operacion) {

            // Crear un nuevo conjuto de actividades asignadas de acuerdo al programa y tarea
            
            case "cambiar_avatar":
			
			    $avatar = $_POST['avatar'];	
				
                $stmt = $con->prepare("UPDATE users SET image = ? WHERE username = ?");
                $stmt->execute([$avatar, $user['username']]);
				
            break;

            case "actualizar_datos":
                // Aquí iría el código para actualizar otros datos del usuario
                $name = $_POST['name'] ?? '';
                $stmt = $con->prepare("UPDATE users SET name = ? WHERE username = ?");
                $stmt->execute([$name, $user['username']]);
            break;

            default:
                echo json_encode(array("error" => true, "message" => "Operación no reconocida."));

                break;
        }

        // Confirmar la transacción
        $con->commit();

        echo json_encode(array("success" => true, "message" => "Operación realizada con éxito."));

    } catch (Exception $e) {
        // Revertir cambios si hay un error
        $con->rollBack();
        echo json_encode(array("error" => true, "message" => "Error al realizar la operación: " . $e->getMessage()));
    }
    
    // Cerrar la conexión
    $con = null; // Esto cerrará la conexión PDO
} else {
    echo json_encode(array("error" => true, "message" => "Método de solicitud no permitido."));
}
?>