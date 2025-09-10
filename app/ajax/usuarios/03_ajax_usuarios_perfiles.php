<?php
/**
 * AJAX: Gestión de perfiles de usuarios
 * Administra la asignación y eliminación de perfiles a usuarios
 */

// Configuración de errores y headers
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=UTF-8');

// Cargar el sistema
require_once __DIR__ . '/../../core/00_load.php';

$user = current_user();

// Verificar sesión
if (!$session->isUserLoggedIn(true)) {
    echo json_encode(['error' => true, 'message' => 'Sesión no válida']);
    exit;
}

// Verificar permisos para gestión de usuarios
$name_page_principal = 'usuarios';
$permiso = 'editar';

if (!has_access($name_page_principal) || !has_access_with_permissions($name_page_principal, $permiso)) {
    echo json_encode(['error' => true, 'message' => 'No tiene permisos para gestionar perfiles de usuarios']);
    exit;
}

// Conexión a base de datos
require_once __DIR__ . '/../01_ajax_connection.php';

// Crear la conexión
$objeto = new connection_ajax();
$conexion = $objeto->conectar();

// Solo procesar POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => true, 'message' => 'Método no permitido']);
    exit;
}

// Obtener la operación
$operacion = isset($_POST['operacion']) ? $_POST['operacion'] : '';

try {
    switch ($operacion) {
        case 'obtener_perfiles_usuario':
            $var_username = isset($_POST['username']) ? trim($_POST['username']) : '';
            
            if (empty($var_username)) {
                // Si no hay username, devolver array vacío en lugar de error
                echo json_encode([
                    'success' => true,
                    'data' => [],
                    'total' => 0,
                    'message' => 'No hay usuario seleccionado'
                ], JSON_UNESCAPED_UNICODE);
                break;
            }
            
            // Consulta con JOIN para obtener información completa del perfil
            $query = "SELECT *
                     FROM acceso_perfiles T0
                     INNER JOIN perfiles T1 ON T0.perfil = T1.perfil 
                     WHERE T0.username = :username 
                     ORDER BY T0.perfil ASC";
                     
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':username', $var_username, PDO::PARAM_STR);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $data,
                'total' => count($data)
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'asignar_perfil':
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $perfil = isset($_POST['perfil']) ? trim($_POST['perfil']) : '';

            // Validar campos requeridos
            if (empty($username) || empty($perfil)) {
                echo json_encode(['error' => true, 'message' => 'Username y perfil son requeridos']);
                exit;
            }

            // Sanitizar datos
            $username = remove_junk($username);
            $perfil = remove_junk($perfil);

            try {
                // Verificar si el perfil existe
                $check_perfil = "SELECT perfil FROM perfiles WHERE perfil = :perfil";
                $stmt_check = $conexion->prepare($check_perfil);
                $stmt_check->bindParam(':perfil', $perfil, PDO::PARAM_STR);
                $stmt_check->execute();
                
                if ($stmt_check->rowCount() === 0) {
                    echo json_encode(['error' => true, 'message' => 'El perfil seleccionado no existe o está inactivo']);
                    exit;
                }

                // Verificar si ya existe la asignación
                $check_exist = "SELECT username FROM acceso_perfiles WHERE username = :username AND perfil = :perfil";
                $stmt_exist = $conexion->prepare($check_exist);
                $stmt_exist->execute([':username' => $username, ':perfil' => $perfil]);
                
                if ($stmt_exist->rowCount() > 0) {
                    echo json_encode(['error' => true, 'message' => 'El perfil ya está asignado a este usuario']);
                    exit;
                }

                // Insertar nuevo perfil
                $insert_sql = "INSERT INTO acceso_perfiles (username, perfil) VALUES (:username, :perfil)";
                $stmt_insert = $conexion->prepare($insert_sql);
                $result = $stmt_insert->execute([':username' => $username, ':perfil' => $perfil]);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Perfil asignado correctamente']);
                } else {
                    echo json_encode(['error' => true, 'message' => 'Error al asignar el perfil']);
                }
                
            } catch (PDOException $e) {
                echo json_encode(['error' => true, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
            }
            break;

        case 'eliminar_perfil':
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $perfil = isset($_POST['perfil']) ? trim($_POST['perfil']) : '';

            // Validar campos requeridos
            if (empty($username) || empty($perfil)) {
                echo json_encode(['error' => true, 'message' => 'Username y perfil son requeridos']);
                exit;
            }

            // Sanitizar datos
            $username = remove_junk($username);
            $perfil = remove_junk($perfil);

            try {
                // Verificar si existe la asignación antes de eliminar
                $check_exist = "SELECT username FROM acceso_perfiles WHERE username = :username AND perfil = :perfil";
                $stmt_check = $conexion->prepare($check_exist);
                $stmt_check->execute([':username' => $username, ':perfil' => $perfil]);
                
                if ($stmt_check->rowCount() === 0) {
                    echo json_encode(['error' => true, 'message' => 'La asignación de perfil no existe']);
                    exit;
                }

                // Eliminar la asignación de perfil
                $delete_sql = "DELETE FROM acceso_perfiles WHERE username = :username AND perfil = :perfil";
                $stmt_delete = $conexion->prepare($delete_sql);
                $result = $stmt_delete->execute([':username' => $username, ':perfil' => $perfil]);

                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Perfil eliminado correctamente']);
                } else {
                    echo json_encode(['error' => true, 'message' => 'Error al eliminar el perfil']);
                }
                
            } catch (PDOException $e) {
                echo json_encode(['error' => true, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
            }
            break;
        case 'obtener_perfiles_disponibles':
            // Obtener todos los perfiles activos disponibles
            try {
                $query = "SELECT * FROM perfiles";
                $stmt = $conexion->prepare($query);
                $stmt->execute();
                $perfiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'data' => $perfiles,
                    'total' => count($perfiles)
                ], JSON_UNESCAPED_UNICODE);
                
            } catch (PDOException $e) {
                echo json_encode(['error' => true, 'message' => 'Error al obtener perfiles: ' . $e->getMessage()]);
            }
            break;
            
        default:
            echo json_encode(['error' => true, 'message' => 'Operación no válida']);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => 'Error del servidor: ' . $e->getMessage()]);
}

?>