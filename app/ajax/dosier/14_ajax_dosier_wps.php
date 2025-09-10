<?php
$name_page_principal = '01_AdministrarDosier.php';
require_once('../01_General/00_load.php');
$user = current_user();

require_once('01_ajax_connection.php');

// Crear la conexión
$objeto = new Connection_Ajax();
$conexion = $objeto->Conectar();

// Obtener la operación solicitada
$operacion = isset($_POST['operacion']) ? $_POST['operacion'] : '';

try {
    switch($operacion) {
        case 'obtener_wps_disponibles':
            // Obtener todos los WPS disponibles
            $query = "SELECT numero, revision, norma, proceso_soldadura, clasificacion 
                      FROM wps 
                      ORDER BY numero ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute();
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'obtener_wps_asignados':
            // Obtener WPS ya asignados a un dosier específico
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            
            if (empty($cod_dosier)) {
                echo json_encode(['error' => true, 'message' => 'Código de dosier requerido']);
                exit;
            }

            $query = "SELECT w.numero, w.revision, w.norma, w.proceso_soldadura, w.clasificacion 
                      FROM wps w
                      INNER JOIN wps_dosier wd ON w.numero = wd.numero_wps
                      WHERE wd.cod_dosier = ?
                      ORDER BY w.numero ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute([$cod_dosier]);
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'asignar_wps':
            if(!has_access_with_permissions($name_page_principal, 'editar')) {
                echo json_encode(['error' => true, 'message' => 'No tienes permiso para modificar estos datos.']);
                exit;
            }
            // Asignar WPS seleccionados a un dosier
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            $wps_seleccionados = isset($_POST['wps_seleccionados']) ? $_POST['wps_seleccionados'] : [];
            $id_dosier_calidad = isset($_POST['id_dosier_calidad']) ? $_POST['id_dosier_calidad'] : '';
            $funcion = isset($_POST['funcion']) ? $_POST['funcion'] : '';

            if (empty($cod_dosier)) {
                echo json_encode(['error' => true, 'message' => 'Código de dosier requerido']);
                exit;
            }

            // extraer datos del usuario asignado actual y nivel
            if (!puedeModificarDatosEspecificos($user, $cod_dosier)) {
                echo json_encode([
                    'error' => true,
                    'message' => 'No tienes permiso para modificar estos datos.'
                ]);
                exit;
            }

            /*if (empty($wps_seleccionados)) {
                echo json_encode(['error' => true, 'message' => 'Debe seleccionar al menos un WPS']);
                exit;
            }*/

            $conexion->beginTransaction();

            try {
                // Obtener WPS actualmente asignados al dosier
                $select_query = "SELECT numero_wps FROM wps_dosier WHERE cod_dosier = ?";
                $select_stmt = $conexion->prepare($select_query);
                $select_stmt->execute([$cod_dosier]);
                $wps_actuales = $select_stmt->fetchAll(PDO::FETCH_COLUMN);

                // Determinar qué WPS eliminar (están en BD pero no en selección)
                $wps_a_eliminar = array_diff($wps_actuales, $wps_seleccionados);
                
                // Determinar qué WPS agregar (están en selección pero no en BD)
                $wps_a_agregar = array_diff($wps_seleccionados, $wps_actuales);

                // Eliminar WPS que ya no están seleccionados
                if (!empty($wps_a_eliminar)) {
                    $placeholders = str_repeat('?,', count($wps_a_eliminar) - 1) . '?';
                    $delete_query = "DELETE FROM wps_dosier WHERE cod_dosier = ? AND numero_wps IN ($placeholders)";
                    $delete_stmt = $conexion->prepare($delete_query);
                    $delete_params = array_merge([$cod_dosier], $wps_a_eliminar);
                    $delete_stmt->execute($delete_params);
                }

                // Agregar nuevos WPS seleccionados
                if (!empty($wps_a_agregar)) {
                    $insert_query = "INSERT INTO wps_dosier (cod_dosier, numero_wps) VALUES (?, ?)";
                    $insert_stmt = $conexion->prepare($insert_query);
                    
                    foreach ($wps_a_agregar as $numero_wps) {
                        $insert_stmt->execute([$cod_dosier, $numero_wps]);
                    }
                }

                $eliminados = count($wps_a_eliminar);
                $agregados = count($wps_a_agregar);
                $mantenidos = count($wps_seleccionados) - $agregados;

                // actualizar estado de los PQR
                $completar = ($agregados > 0 || $mantenidos > 0) ? true : false;
                $update_query = "UPDATE dosier_indice_detalle did 
                                INNER JOIN indice_dosier id ON id.id = did.id_indice_dosier 
                                SET did.completar = ? 
                                WHERE did.id_dosier_calidad = ? AND id.funcion = ?";
                $update_stmt = $conexion->prepare($update_query);
                $update_stmt->execute([$completar, $id_dosier_calidad, $funcion]);
            
                $conexion->commit();
                
                echo json_encode([
                    'success' => true, 
                    'message' => "WPS actualizados: $agregados agregados, $eliminados eliminados, $mantenidos mantenidos"
                ]);

            } catch (Exception $e) {
                $conexion->rollback();
                echo json_encode(['error' => true, 'message' => 'Error al asignar WPS: ' . $e->getMessage()]);
            }
            break;

        case 'obtener_wps_con_estado':
            // Obtener WPS con su estado de asignación para un dosier específico
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            
            $query = "SELECT w.*,
                             CASE WHEN wd.numero_wps IS NOT NULL THEN 1 ELSE 0 END as asignado
                      FROM wps w
                      LEFT JOIN wps_dosier wd ON w.numero = wd.numero_wps AND wd.cod_dosier = ?
                      ORDER BY w.numero ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute([$cod_dosier]);
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        default:
            echo json_encode(['error' => true, 'message' => 'Operación no válida']);
            break;
    }

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => 'Error en la consulta: ' . $e->getMessage()]);
}

// Cerrar la conexión
$conexion = null;

?>
