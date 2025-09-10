<?php
/**
 * AJAX: Gestión de accesos de páginas
 * Controla qué usuarios pueden ejecutar qué scripts/páginas
 */

// Configuración de errores y headers
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=UTF-8');

// Cargar el sistema
require_once __DIR__ . '/../../core/00_load.php';

// Verificar sesión
if (!$session->isUserLoggedIn(true)) {
    echo json_encode(['error' => true, 'message' => 'Sesión no válida']);
    exit;
}

$user = current_user();

// Verificar permisos para gestión de usuarios
$name_page_principal = 'usuarios';
$permiso = 'editar';

if (!has_access($name_page_principal) || !has_access_with_permissions($name_page_principal, $permiso)) {
    echo json_encode(['error' => true, 'message' => 'No tiene permisos para gestionar accesos de usuarios']);
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
        case 'asignar_acceso':
            $username = remove_junk($db->escape($_POST['username']));
            $permisos = json_decode($_POST['permisos'], true);
            
            if (empty($username) || !is_array($permisos)) {
                echo json_encode(['error' => true, 'message' => 'Datos inválidos']);
                exit;
            }

            try {
                // Iniciar transacción
                $conexion->beginTransaction();

                // Eliminar todos los accesos existentes del usuario
                $delete_sql = "DELETE FROM acceso_paginas WHERE username = :username";
                $stmt_delete = $conexion->prepare($delete_sql);
                $stmt_delete->bindParam(':username', $username);
                $stmt_delete->execute();
                
                // Insertar nuevos accesos
                $insert_sql = "INSERT INTO acceso_paginas (username, pagina, editar, eliminar, adicionar, seguimiento) 
                              VALUES (:username, :pagina, :editar, :eliminar, :adicionar, :seguimiento)";
                $stmt_insert = $conexion->prepare($insert_sql);
                
                foreach ($permisos as $permiso) {
                    $pagina = remove_junk($permiso['pagina']);
                    $editar = $permiso['editar'] ? 'Si' : 'No';
                    $eliminar = $permiso['eliminar'] ? 'Si' : 'No';
                    $adicionar = $permiso['adicionar'] ? 'Si' : 'No';
                    $seguimiento = $permiso['seguimiento'] ? 'Si' : 'No';
                    
                    $stmt_insert->execute([
                        ':username' => $username,
                        ':pagina' => $pagina,
                        ':editar' => $editar,
                        ':eliminar' => $eliminar,
                        ':adicionar' => $adicionar,
                        ':seguimiento' => $seguimiento
                    ]);
                }
                
                // Confirmar transacción
                $conexion->commit();
                echo json_encode(['success' => true, 'message' => 'Accesos actualizados correctamente']);
                
            } catch (Exception $e) {
                $conexion->rollBack();
                echo json_encode(['error' => true, 'message' => 'Error al actualizar accesos: ' . $e->getMessage()]);
            }
            break;
        case 'obtener_todas_paginas':
            // Obtener todas las páginas disponibles del sistema
                $var_username = $_POST['username'] ?? '';
                $query_todas_paginas = "SELECT pagina, descripcion_pagina FROM paginas ORDER BY descripcion_pagina ASC";
                $stmt_todas = $conexion->prepare($query_todas_paginas);
                $stmt_todas->execute();
                $todas_paginas = $stmt_todas->fetchAll(PDO::FETCH_ASSOC);
                
                $result = [];
                
                if (!empty($var_username)) {
                    // Obtener los accesos actuales del usuario
                    $query_accesos = "SELECT pagina, editar, eliminar, adicionar, seguimiento 
                                    FROM acceso_paginas 
                                    WHERE username = :username";
                    $stmt_accesos = $conexion->prepare($query_accesos);
                    $stmt_accesos->bindParam(':username', $var_username);
                    $stmt_accesos->execute();
                    $accesos_actuales = $stmt_accesos->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Crear un mapa de accesos actuales
                    $mapa_accesos = [];
                    foreach ($accesos_actuales as $acceso) {
                        $mapa_accesos[$acceso['pagina']] = [
                            'editar' => ($acceso['editar'] === 'Si'),
                            'eliminar' => ($acceso['eliminar'] === 'Si'),
                            'adicionar' => ($acceso['adicionar'] === 'Si'),
                            'seguimiento' => ($acceso['seguimiento'] === 'Si')
                        ];
                    }
                    
                    // Combinar todas las páginas con los accesos actuales
                    foreach ($todas_paginas as $pagina) {
                        $pagina_nombre = $pagina['pagina'];
                        $tiene_acceso = isset($mapa_accesos[$pagina_nombre]);
                        
                        $result[] = [
                            'username' => $var_username,
                            'pagina' => $pagina_nombre,
                            'descripcion_pagina' => $pagina['descripcion_pagina'],
                            'tiene_acceso' => $tiene_acceso,
                            'editar' => $tiene_acceso ? $mapa_accesos[$pagina_nombre]['editar'] : false,
                            'eliminar' => $tiene_acceso ? $mapa_accesos[$pagina_nombre]['eliminar'] : false,
                            'adicionar' => $tiene_acceso ? $mapa_accesos[$pagina_nombre]['adicionar'] : false,
                            'seguimiento' => $tiene_acceso ? $mapa_accesos[$pagina_nombre]['seguimiento'] : false
                        ];
                    }
                } else {
                    // Si no hay username, devolver solo las páginas disponibles
                    foreach ($todas_paginas as $pagina) {
                        $result[] = [
                            'username' => '',
                            'pagina' => $pagina['pagina'],
                            'descripcion_pagina' => $pagina['descripcion_pagina'],
                            'tiene_acceso' => false,
                            'editar' => false,
                            'eliminar' => false,
                            'adicionar' => false,
                            'seguimiento' => false
                        ];
                    }
                }
                
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;
            
        default:
            echo json_encode(['error' => true, 'message' => 'Operación no válida']);
            break;
    }
    
} catch (Exception $e) {
    if (isset($conexion) && $conexion->inTransaction()) {
        $conexion->rollBack();
    }
    echo json_encode(['error' => true, 'message' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
}

?>
