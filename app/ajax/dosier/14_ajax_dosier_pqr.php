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
        case 'obtener_pqr_disponibles':
            // Obtener todos los PQR disponibles
            $query = "SELECT numero, revision, norma, proceso_soldadura, clasificacion 
                      FROM pqr 
                      ORDER BY numero ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute();
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'obtener_pqr_asignados':
            // Obtener PQR ya asignados a un dosier específico
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            
            if (empty($cod_dosier)) {
                echo json_encode(['error' => true, 'message' => 'Código de dosier requerido']);
                exit;
            }

            $query = "SELECT p.numero, p.revision, p.norma, p.proceso_soldadura, p.clasificacion 
                      FROM pqr p
                      INNER JOIN pqr_dosier pd ON p.numero = pd.numero_pqr
                      WHERE pd.cod_dosier = ?
                      ORDER BY p.numero ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute([$cod_dosier]);
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'asignar_pqr':
            $name_page_principal = '01_AdministrarDosier.php';
            if(!has_access_with_permissions($name_page_principal, 'editar')) {
                echo json_encode(['error' => true, 'message' => 'No tienes permiso para modificar estos datos.']);
                exit;
            }

            // Asignar PQR seleccionados a un dosier
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            $pqr_seleccionados = isset($_POST['pqr_seleccionados']) ? $_POST['pqr_seleccionados'] : [];
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
                // Obtener PQR actualmente asignados al dosier
                $select_query = "SELECT numero_pqr FROM pqr_dosier WHERE cod_dosier = ?";
                $select_stmt = $conexion->prepare($select_query);
                $select_stmt->execute([$cod_dosier]);
                $pqr_actuales = $select_stmt->fetchAll(PDO::FETCH_COLUMN);

                // Determinar qué PQR eliminar (están en BD pero no en selección)
                $pqr_a_eliminar = array_diff($pqr_actuales, $pqr_seleccionados);
                
                // Determinar qué PQR agregar (están en selección pero no en BD)
                $pqr_a_agregar = array_diff($pqr_seleccionados, $pqr_actuales);

                // Eliminar PQR que ya no están seleccionados
                if (!empty($pqr_a_eliminar)) {
                    $placeholders = str_repeat('?,', count($pqr_a_eliminar) - 1) . '?';
                    $delete_query = "DELETE FROM pqr_dosier WHERE cod_dosier = ? AND numero_pqr IN ($placeholders)";
                    $delete_stmt = $conexion->prepare($delete_query);
                    $delete_params = array_merge([$cod_dosier], $pqr_a_eliminar);
                    $delete_stmt->execute($delete_params);
                }

                // Agregar nuevos PQR seleccionados
                if (!empty($pqr_a_agregar)) {
                    $insert_query = "INSERT INTO pqr_dosier (cod_dosier, numero_pqr) VALUES (?, ?)";
                    $insert_stmt = $conexion->prepare($insert_query);
                    
                    foreach ($pqr_a_agregar as $numero_pqr) {
                        $insert_stmt->execute([$cod_dosier, $numero_pqr]);
                    }
                }
                
                $eliminados = count($pqr_a_eliminar);
                $agregados = count($pqr_a_agregar);
                $mantenidos = count($pqr_seleccionados) - $agregados;

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
                    'message' => "PQR actualizados: $agregados agregados, $eliminados eliminados, $mantenidos mantenidos"
                ]);

            } catch (Exception $e) {
                $conexion->rollback();
                echo json_encode(['error' => true, 'message' => 'Error al asignar PQR: ' . $e->getMessage()]);
            }
            break;

        case 'obtener_pqr_con_estado':
            // Obtener PQR con su estado de asignación para un dosier específico
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            
            $query = "SELECT p.*,
                             CASE WHEN pd.numero_pqr IS NOT NULL THEN 1 ELSE 0 END as asignado
                      FROM pqr p
                      LEFT JOIN pqr_dosier pd ON p.numero = pd.numero_pqr AND pd.cod_dosier = ?
                      ORDER BY p.numero ASC";
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
