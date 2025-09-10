<?php
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
        case 'obtener_rgc_disponibles':
            // Obtener todas las RGC disponibles
            $query = "SELECT *
                      FROM rgc
                      ORDER BY numero ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute();
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'obtener_rgc_asignados':
            // Obtener RGC ya asignadas a un dosier específico
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            
            if (empty($cod_dosier)) {
                echo json_encode(['error' => true, 'message' => 'Código de dosier requerido']);
                exit;
            }

            $query = "SELECT r.numero, r.fecha_emision, r.cliente, r.proyecto, r.descripcion, r.nombre_archivo
                      FROM rgc r
                      INNER JOIN rgc_dosier rd ON r.numero = rd.numero_rgc
                      WHERE rd.cod_dosier = ?
                      ORDER BY r.numero ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute([$cod_dosier]);
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'asignar_rgc':
            $name_page_principal = '01_AdministrarDosier.php';
            if(!has_access_with_permissions($name_page_principal, 'editar')) {
                echo json_encode(['error' => true, 'message' => 'No tienes permiso para modificar estos datos.']);
                exit;
            }
            // Asignar RGC seleccionadas a un dosier
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            $rgc_seleccionadas = isset($_POST['rgc_seleccionadas']) ? $_POST['rgc_seleccionadas'] : [];
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

            $conexion->beginTransaction();

            try {
                // Obtener RGC actualmente asignadas al dosier
                $select_query = "SELECT numero_rgc FROM rgc_dosier WHERE cod_dosier = ?";
                $select_stmt = $conexion->prepare($select_query);
                $select_stmt->execute([$cod_dosier]);
                $rgc_actuales = $select_stmt->fetchAll(PDO::FETCH_COLUMN);

                // Determinar qué RGC eliminar (están en BD pero no en selección)
                $rgc_a_eliminar = array_diff($rgc_actuales, $rgc_seleccionadas);
                
                // Determinar qué RGC agregar (están en selección pero no en BD)
                $rgc_a_agregar = array_diff($rgc_seleccionadas, $rgc_actuales);

                // Eliminar RGC que ya no están seleccionadas
                if (!empty($rgc_a_eliminar)) {
                    $placeholders = str_repeat('?,', count($rgc_a_eliminar) - 1) . '?';
                    $delete_query = "DELETE FROM rgc_dosier WHERE cod_dosier = ? AND numero_rgc IN ($placeholders)";
                    $delete_stmt = $conexion->prepare($delete_query);
                    $delete_params = array_merge([$cod_dosier], $rgc_a_eliminar);
                    $delete_stmt->execute($delete_params);
                }

                // Agregar nuevas RGC seleccionadas
                if (!empty($rgc_a_agregar)) {
                    $insert_query = "INSERT INTO rgc_dosier (cod_dosier, numero_rgc) VALUES (?, ?)";
                    $insert_stmt = $conexion->prepare($insert_query);
                    
                    foreach ($rgc_a_agregar as $numero_rgc) {
                        $insert_stmt->execute([$cod_dosier, $numero_rgc]);
                    }
                }
                                
                $eliminadas = count($rgc_a_eliminar);
                $agregadas = count($rgc_a_agregar);
                $mantenidas = count($rgc_seleccionadas) - $agregadas;

                // actualizar estado de los RGC
                $completar = ($agregadas > 0 || $mantenidas > 0) ? true : false;
                $update_query = "UPDATE dosier_indice_detalle did 
                                INNER JOIN indice_dosier id ON id.id = did.id_indice_dosier 
                                SET did.completar = ? 
                                WHERE did.id_dosier_calidad = ? AND id.funcion = ?";
                $update_stmt = $conexion->prepare($update_query);
                $update_stmt->execute([$completar, $id_dosier_calidad, $funcion]);
                
                $conexion->commit();
                
                echo json_encode([
                    'success' => true, 
                    'message' => "RGC actualizadas: $agregadas agregadas, $eliminadas eliminadas, $mantenidas mantenidas"
                ]);

            } catch (Exception $e) {
                $conexion->rollback();
                echo json_encode(['error' => true, 'message' => 'Error al asignar RGC: ' . $e->getMessage()]);
            }
            break;

        case 'obtener_rgc_con_estado':
            // Obtener RGC con su estado de asignación para un dosier específico
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            
            $query = "SELECT r.*,
                             CASE WHEN rd.numero_rgc IS NOT NULL THEN 1 ELSE 0 END as asignado
                      FROM rgc r
                      LEFT JOIN rgc_dosier rd ON r.numero = rd.numero_rgc AND rd.cod_dosier = ?
                      ORDER BY r.numero ASC";
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
