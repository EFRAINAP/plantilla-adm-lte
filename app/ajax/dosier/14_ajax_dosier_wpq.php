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
        case 'obtener_soldadores':
            // Obtener todos los soldadores
            $query = "SELECT *
                      FROM personal
                      ORDER BY estampa ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute();
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'obtener_wpq_disponibles':
            // Obtener WPQ disponibles filtrados por estampa
            $estampa = isset($_POST['estampa']) ? $_POST['estampa'] : '';
            // Obtener todos los WPQ disponibles
            $query = "SELECT *
                      FROM wpq 
                        WHERE estampa = ?
                      ORDER BY wps ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute([$estampa]);
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'obtener_wpq_asignados':
            // Obtener WPQ ya asignados a un dosier específico
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            
            if (empty($cod_dosier)) {
                echo json_encode(['error' => true, 'message' => 'Código de dosier requerido']);
                exit;
            }

            $query = "SELECT w.numero, w.revision, w.norma, w.proceso_soldadura, w.clasificacion, w.nombre_archivo
                      FROM wpq w
                      INNER JOIN wpq_dosier wd ON w.numero = wd.numero_wpq
                      WHERE wd.cod_dosier = ?
                      ORDER BY w.numero ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute([$cod_dosier]);
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'asignar_wpq':
            if(!has_access_with_permissions($name_page_principal, 'editar')) {
                echo json_encode(['error' => true, 'message' => 'No tienes permiso para modificar estos datos.']);
                exit;
            }
            // Asignar WPQ seleccionados a un dosier
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            $wpq_seleccionados = isset($_POST['wpq_seleccionados']) ? $_POST['wpq_seleccionados'] : [];
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
                // Obtener WPQ actualmente asignados al dosier
                $select_query = "SELECT id_wpq FROM wpq_dosier WHERE cod_dosier = ?";
                $select_stmt = $conexion->prepare($select_query);
                $select_stmt->execute([$cod_dosier]);
                $wpq_actuales = $select_stmt->fetchAll(PDO::FETCH_COLUMN);

                // Determinar qué WPQ eliminar (están en BD pero no en selección)
                $wpq_a_eliminar = array_diff($wpq_actuales, $wpq_seleccionados);
                
                // Determinar qué WPQ agregar (están en selección pero no en BD)
                $wpq_a_agregar = array_diff($wpq_seleccionados, $wpq_actuales);

                // Eliminar WPQ que ya no están seleccionados
                if (!empty($wpq_a_eliminar)) {
                    $placeholders = str_repeat('?,', count($wpq_a_eliminar) - 1) . '?';
                    $delete_query = "DELETE FROM wpq_dosier WHERE cod_dosier = ? AND id_wpq IN ($placeholders)";
                    $delete_stmt = $conexion->prepare($delete_query);
                    $delete_params = array_merge([$cod_dosier], $wpq_a_eliminar);
                    $delete_stmt->execute($delete_params);
                }

                // Agregar nuevos WPQ seleccionados
                if (!empty($wpq_a_agregar)) {
                    $insert_query = "INSERT INTO wpq_dosier (cod_dosier, id_wpq) VALUES (?, ?)";
                    $insert_stmt = $conexion->prepare($insert_query);

                    foreach ($wpq_a_agregar as $id_wpq) {
                        $insert_stmt->execute([$cod_dosier, $id_wpq]);
                    }
                }
                
                $eliminados = count($wpq_a_eliminar);
                $agregados = count($wpq_a_agregar);
                $mantenidos = count($wpq_seleccionados) - $agregados;

                // actualizar estado de los WPQ
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
                    'message' => "WPQ actualizados: $agregados agregados, $eliminados eliminados, $mantenidos mantenidos"
                ]);

            } catch (Exception $e) {
                $conexion->rollback();
                echo json_encode(['error' => true, 'message' => 'Error al asignar WPQ: ' . $e->getMessage()]);
            }
            break;

        case 'obtener_wpq_con_estado':
            // Obtener WPQ con su estado de asignación para un dosier específico
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';

            $query = "SELECT w.* , p.nombre, p.apellido,
                             CASE WHEN wd.id_wpq IS NOT NULL THEN 1 ELSE 0 END as asignado
                      FROM wpq w
                      INNER JOIN personal p ON w.estampa = p.estampa
                      LEFT JOIN wpq_dosier wd ON w.id = wd.id_wpq AND wd.cod_dosier = ?
                      ORDER BY w.id ASC";
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
