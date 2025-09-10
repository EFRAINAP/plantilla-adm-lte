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
        case 'obtener_documentos_iso_disponibles':
            // Obtener todos los documentos ISO disponibles
            $query = "SELECT cod_documento, descripcion_documento, version
                      FROM documentos d
                      ORDER BY d.cod_documento ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute();
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'obtener_documentos_iso_asignados':
            // Obtener documentos ISO ya asignados a un dosier específico
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            
            if (empty($cod_dosier)) {
                echo json_encode(['error' => true, 'message' => 'Código de dosier requerido']);
                exit;
            }

            $query = "SELECT d.cod_documento, d.descripcion_documento, d.version
                      FROM documentos d
                      INNER JOIN dosier_documentos_iso dd ON d.cod_documento = dd.cod_documento
                      WHERE dd.cod_dosier = ?
                      ORDER BY d.cod_documento ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute([$cod_dosier]);
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'asignar_documentos_iso':
            $name_page_principal = '01_AdministrarDosier.php';
            if(!has_access_with_permissions($name_page_principal, 'editar')) {
                echo json_encode(['error' => true, 'message' => 'No tienes permiso para modificar estos datos.']);
                exit;
            }
            // Asignar documentos ISO seleccionados a un dosier por tipo de capítulo
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            $documentos_seleccionados = isset($_POST['documentos_seleccionados']) ? $_POST['documentos_seleccionados'] : [];
            $funcion = isset($_POST['funcion']) ? $_POST['funcion'] : '';
            $id_dosier_calidad = isset($_POST['id_dosier_calidad']) ? $_POST['id_dosier_calidad'] : '';

            if (empty($cod_dosier)) {
                echo json_encode(['error' => true, 'message' => 'Código de dosier requerido']);
                exit;
            }

            if (!puedeModificarDatosEspecificos($user, $cod_dosier)) {
                echo json_encode(['error' => true, 'message' => 'No tienes permiso para modificar estos datos.']);
                exit;
            }

            /*if (empty($documentos_seleccionados)) {
                echo json_encode(['error' => true, 'message' => 'Debe seleccionar al menos un documento ISO']);
                exit;
            }*/

            $conexion->beginTransaction();

            try {
                // Obtener documentos ISO actualmente asignados al dosier por tipo de capítulo
                $filtros_select = [
                    '2' => "AND (dd.cod_documento LIKE 'T-AC-PT-07%' OR dd.cod_documento LIKE 'T-AC-PT-08%' OR dd.cod_documento LIKE 'T-AC-PT-09%' OR dd.cod_documento LIKE 'T-AC-PT-10%')",
                    '3' => "AND (dd.cod_documento LIKE 'T-AC-PT-11%' OR dd.cod_documento LIKE 'T-AC-PT-12%' OR dd.cod_documento LIKE 'T-AC-PT-13%')",
                    '4' => "AND (dd.cod_documento LIKE 'T-AC-PT-14%' OR dd.cod_documento LIKE 'T-AC-PT-15%' OR dd.cod_documento LIKE 'T-AC-PT-16%')"
                ];

                $filtro_select = isset($filtros_select[$funcion]) ? $filtros_select[$funcion] : '';
                
                $select_query = "SELECT dd.cod_documento FROM dosier_documentos_iso dd WHERE dd.cod_dosier = ? $filtro_select";
                $select_stmt = $conexion->prepare($select_query);
                $select_stmt->execute([$cod_dosier]);
                $documentos_actuales = $select_stmt->fetchAll(PDO::FETCH_COLUMN);

                // Determinar qué documentos eliminar (están en BD pero no en selección)
                $documentos_a_eliminar = array_diff($documentos_actuales, $documentos_seleccionados);
                
                // Determinar qué documentos agregar (están en selección pero no en BD)
                $documentos_a_agregar = array_diff($documentos_seleccionados, $documentos_actuales);

                // Eliminar documentos que ya no están seleccionados
                if (!empty($documentos_a_eliminar)) {
                    $placeholders = str_repeat('?,', count($documentos_a_eliminar) - 1) . '?';
                    $delete_query = "DELETE FROM dosier_documentos_iso WHERE cod_dosier = ? AND cod_documento IN ($placeholders)";
                    $delete_stmt = $conexion->prepare($delete_query);
                    $delete_params = array_merge([$cod_dosier], $documentos_a_eliminar);
                    $delete_stmt->execute($delete_params);
                }

                // Agregar nuevos documentos seleccionados
                if (!empty($documentos_a_agregar)) {
                    $insert_query = "INSERT INTO dosier_documentos_iso (cod_dosier, cod_documento) VALUES (?, ?)";
                    $insert_stmt = $conexion->prepare($insert_query);
                    
                    foreach ($documentos_a_agregar as $cod_documento) {
                        $insert_stmt->execute([$cod_dosier, $cod_documento]);
                    }
                }
                
                $eliminados = count($documentos_a_eliminar);
                $agregados = count($documentos_a_agregar);
                $mantenidos = count($documentos_seleccionados) - $agregados;

                // actualizar estado de los Documentos ISO
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
                    'message' => "Documentos ISO actualizados: $agregados agregados, $eliminados eliminados, $mantenidos mantenidos"
                ]);

            } catch (Exception $e) {
                $conexion->rollback();
                echo json_encode(['error' => true, 'message' => 'Error al asignar Documentos ISO: ' . $e->getMessage()]);
            }
            break;

        case 'obtener_documentos_iso_con_estado':
            // Obtener Documentos ISO con su estado de asignación para un dosier específico
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';

            $query = "SELECT d.cod_documento, d.descripcion_documento, d.version,
                             CASE WHEN dd.cod_documento IS NOT NULL THEN 1 ELSE 0 END as asignado
                      FROM documentos d
                      LEFT JOIN dosier_documentos_iso dd ON d.cod_documento = dd.cod_documento AND dd.cod_dosier = ?
                      ORDER BY d.cod_documento ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute([$cod_dosier]);
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'obtener_documentos_iso_con_estado_filtrado':
            // Obtener Documentos ISO filtrados por tipo de capítulo con su estado de asignación
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            $funcion = isset($_POST['funcion']) ? $_POST['funcion'] : '';

            if (empty($cod_dosier)) {
                echo json_encode(['error' => true, 'message' => 'Código de dosier requerido']);
                exit;
            }

            // Definir filtros SQL por tipo de capítulo
            $filtros_sql = [
                '2' => "AND (d.cod_documento LIKE 'T-AC-PT-07%' OR d.cod_documento LIKE 'T-AC-PT-08%' OR d.cod_documento LIKE 'T-AC-PT-09%' OR d.cod_documento LIKE 'T-AC-PT-10%')",
                '3' => "AND (d.cod_documento LIKE 'T-AC-PT-11%' OR d.cod_documento LIKE 'T-AC-PT-12%' OR d.cod_documento LIKE 'T-AC-PT-13%')",
                '4' => "AND (d.cod_documento LIKE 'T-AC-PT-14%' OR d.cod_documento LIKE 'T-AC-PT-15%' OR d.cod_documento LIKE 'T-AC-PT-16%')"
            ];

            $filtro = isset($filtros_sql[$funcion]) ? $filtros_sql[$funcion] : '';

            $query = "SELECT d.cod_documento, d.descripcion_documento, d.version,
                             CASE WHEN dd.cod_documento IS NOT NULL THEN 1 ELSE 0 END as asignado
                      FROM documentos d
                      LEFT JOIN dosier_documentos_iso dd ON d.cod_documento = dd.cod_documento AND dd.cod_dosier = ?
                      WHERE 1=1 $filtro
                      ORDER BY d.cod_documento ASC";
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