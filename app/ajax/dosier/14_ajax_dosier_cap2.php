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
        case 'obtener_registros_disponibles':
            // Obtener todos los registros del capítulo 2 disponibles
            $query = "SELECT *
                      FROM cap2_registros 
                      ORDER BY numero ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute();
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'obtener_registro_asignado':
            // Obtener registro asignado a un dosier específico (solo uno)
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            $id_indice_dosier = isset($_POST['id_indice_dosier']) ? $_POST['id_indice_dosier'] : '';
            
            if (empty($cod_dosier) || empty($id_indice_dosier)) {
                echo json_encode(['error' => true, 'message' => 'Código de dosier e id_indice_dosier requeridos']);
                exit;
            }

            // El número debe ser: cod_dosier-id_indice_dosier
            $numero_esperado = $cod_dosier . '-' . $id_indice_dosier;

            $query = "SELECT r.id, r.numero, r.nombre_archivo, r.id_indice_dosier
                      FROM cap2_registros r
                      WHERE r.numero = ?";
            $resultado = $conexion->prepare($query);
            $resultado->execute([$numero_esperado]);
            $data = $resultado->fetch(PDO::FETCH_ASSOC);
            
            // Si no existe, devolver estructura vacía con el número por defecto
            if (!$data) {
                $data = [
                    'id' => null,
                    'numero' => $numero_esperado,
                    'nombre_archivo' => null,
                    'id_indice_dosier' => $id_indice_dosier
                ];
            }
            
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'guardar_registro_cap2':

            if(!has_access_with_permissions($name_page_principal, 'editar')) {
                echo json_encode(['error' => true, 'message' => 'No tienes permiso para modificar estos datos.']);
                exit;
            }

            // Guardar/actualizar registro del capítulo 2 para un dosier específico
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            $id_indice_dosier = isset($_POST['id_indice_dosier']) ? $_POST['id_indice_dosier'] : '';
            $id_dosier_calidad = isset($_POST['id_dosier_calidad']) ? $_POST['id_dosier_calidad'] : '';
            $funcion = isset($_POST['funcion']) ? $_POST['funcion'] : '';

            if (empty($cod_dosier) || empty($id_indice_dosier)) {
                echo json_encode(['error' => true, 'message' => 'Código de dosier e id_indice_dosier son requeridos']);
                exit;
            }

            // El número debe ser: cod_dosier-id_indice_dosier
            $numero = $cod_dosier . '-' . $id_indice_dosier;

            // Verificar permisos específicos del dosier
            if (!puedeModificarDatosEspecificos($user, $cod_dosier)) {
                echo json_encode([
                    'error' => true,
                    'message' => 'No tienes permiso para modificar estos datos.'
                ]);
                exit;
            }

            // Obtener la carpeta de Cap2 para subir archivos
            $carpeta = find_table_field_only('constantes', 'nombre_constante', 'carpeta_cap2');
            if(empty($carpeta['valor_constante'])) {
                echo json_encode(['error' => true, 'message' => 'La carpeta de subida no está definida.']);
                exit;
            }
            $upload_path = '../'.$carpeta['valor_constante'];
            // Asegurar que la ruta termine con slash
            if (substr($upload_path, -1) !== '/') {
                $upload_path .= '/';
            }

            $conexion->beginTransaction();

            try {
                // 1. Verificar si existe el registro con ese número
                $check_registro = "SELECT id, nombre_archivo FROM cap2_registros WHERE numero = ?";
                $check_stmt = $conexion->prepare($check_registro);
                $check_stmt->execute([$numero]);
                $registro_existente = $check_stmt->fetch(PDO::FETCH_ASSOC);

                $nombre_archivo = $registro_existente ? $registro_existente['nombre_archivo'] : null;
                
                // 2. Manejar subida de archivo si se proporciona
                if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
                    $archivo_temp = $_FILES['archivo']['tmp_name'];
                    $archivo_nombre = $_FILES['archivo']['name'];
                    $archivo_extension = strtolower(pathinfo($archivo_nombre, PATHINFO_EXTENSION));
                    
                    // Validar extensión
                    if (!in_array($archivo_extension, ['pdf'])) {
                        throw new Exception('Tipo de archivo no permitido. Solo se permiten PDF.');
                    }
                    
                    // Si existe un archivo anterior, eliminarlo
                    if ($registro_existente && !empty($registro_existente['nombre_archivo'])) {
                        $archivo_anterior = $upload_path . $registro_existente['nombre_archivo'];
                        if (file_exists($archivo_anterior)) {
                            unlink($archivo_anterior);
                        }
                    }
                    
                    // Generar nombre único
                    $nombre_archivo = $numero . '_' . time() . '.' . $archivo_extension;
                    $ruta_destino = $upload_path . $nombre_archivo;
                    
                    // Mover archivo
                    if (!move_uploaded_file($archivo_temp, $ruta_destino)) {
                        throw new Exception('Error al subir el archivo');
                    }
                } else {
                    // No se proporciona un nuevo archivo, mantener el existente
                    echo json_encode(['error' => true, 'message' => 'No se proporcionó un archivo.']);
                    exit;
                }

                if ($registro_existente) {
                    // 2a. El registro existe, actualizarlo
                    if ($nombre_archivo) {
                        $update_query = "UPDATE cap2_registros SET nombre_archivo = ? WHERE numero = ?";
                        $update_stmt = $conexion->prepare($update_query);
                        $update_stmt->execute([$nombre_archivo, $numero]);
                    }
                } else {
                    // 2b. El registro no existe, crearlo
                    $insert_query = "INSERT INTO cap2_registros (numero, id_indice_dosier, nombre_archivo) VALUES (?, ?, ?)";
                    $insert_stmt = $conexion->prepare($insert_query);
                    $insert_stmt->execute([$numero, $id_indice_dosier, $nombre_archivo]);
                }

                // 3. Verificar si ya existe la relación registro-Dosier
                $check_relacion = "SELECT COUNT(*) FROM cap2_registros_dosier WHERE cod_dosier = ? AND numero_registro = ?";
                $check_rel_stmt = $conexion->prepare($check_relacion);
                $check_rel_stmt->execute([$cod_dosier, $numero]);
                
                if ($check_rel_stmt->fetchColumn() == 0) {
                    // 4. Crear la relación registro-Dosier si no existe
                    $insert_relacion = "INSERT INTO cap2_registros_dosier (cod_dosier, numero_registro) VALUES (?, ?)";
                    $rel_stmt = $conexion->prepare($insert_relacion);
                    $rel_stmt->execute([$cod_dosier, $numero]);
                }

                // 5. Actualizar estado del índice del dosier
                $update_indice = "UPDATE dosier_indice_detalle did 
                                 INNER JOIN indice_dosier id ON id.id = did.id_indice_dosier 
                                 SET did.completar = 1 
                                 WHERE did.id_dosier_calidad = ? AND id.id = ?";
                $update_ind_stmt = $conexion->prepare($update_indice);
                $update_ind_stmt->execute([$id_dosier_calidad, $id_indice_dosier]);
                
                $conexion->commit();
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Registro del Capítulo 2 guardado exitosamente'
                ]);

            } catch (Exception $e) {
                $conexion->rollback();
                echo json_encode(['error' => true, 'message' => 'Error al guardar registro: ' . $e->getMessage()]);
            }
            break;

        case 'eliminar_registro_dosier':

            if(!has_access_with_permissions($name_page_principal, 'editar')) {
                echo json_encode(['error' => true, 'message' => 'No tienes permiso para modificar estos datos.']);
                exit;
            }

            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            $id_indice_dosier = isset($_POST['id_indice_dosier']) ? $_POST['id_indice_dosier'] : '';
            $id_dosier_calidad = isset($_POST['id_dosier_calidad']) ? $_POST['id_dosier_calidad'] : '';
            $funcion = isset($_POST['funcion']) ? $_POST['funcion'] : '';

            if (empty($cod_dosier) || empty($id_indice_dosier)) {
                echo json_encode(['error' => true, 'message' => 'Datos requeridos no proporcionados']);
                exit;
            }

            // El número debe ser: cod_dosier-id_indice_dosier
            $numero_registro = $cod_dosier . '-' . $id_indice_dosier;

            // Verificar permisos específicos del dosier
            if (!puedeModificarDatosEspecificos($user, $cod_dosier)) {
                echo json_encode([
                    'error' => true,
                    'message' => 'No tienes permiso para modificar estos datos.'
                ]);
                exit;
            }

            $conexion->beginTransaction();

            try {
                // Obtener información del archivo antes de eliminar la relación
                $get_archivo = "SELECT r.nombre_archivo FROM cap2_registros r 
                               INNER JOIN cap2_registros_dosier rd ON r.numero = rd.numero_registro 
                               WHERE rd.cod_dosier = ? AND rd.numero_registro = ?";
                $archivo_stmt = $conexion->prepare($get_archivo);
                $archivo_stmt->execute([$cod_dosier, $numero_registro]);
                $archivo_info = $archivo_stmt->fetch(PDO::FETCH_ASSOC);

                // Eliminar la relación
                $delete_query = "DELETE FROM cap2_registros_dosier WHERE cod_dosier = ? AND numero_registro = ?";
                $delete_stmt = $conexion->prepare($delete_query);
                $delete_stmt->execute([$cod_dosier, $numero_registro]);

                // Eliminar el archivo físico si existe
                if ($archivo_info && !empty($archivo_info['nombre_archivo'])) {
                    $carpeta = find_table_field_only('constantes', 'nombre_constante', 'carpeta_cap2');
                    if (!empty($carpeta['valor_constante'])) {
                        $upload_path = '../' . $carpeta['valor_constante'];
                        if (substr($upload_path, -1) !== '/') {
                            $upload_path .= '/';
                        }
                        $archivo_path = $upload_path . $archivo_info['nombre_archivo'];
                        if (file_exists($archivo_path)) {
                            unlink($archivo_path);
                        }
                    }
                }

                // Eliminar el registro de la base de datos
                $delete_registro = "DELETE FROM cap2_registros WHERE numero = ?";
                $delete_stmt = $conexion->prepare($delete_registro);
                $delete_stmt->execute([$numero_registro]);

                // Actualizar estado del índice del dosier
                $update_indice = "UPDATE dosier_indice_detalle did 
                                 INNER JOIN indice_dosier id ON id.id = did.id_indice_dosier 
                                 SET did.completar = 0 
                                 WHERE did.id_dosier_calidad = ? AND id.id = ?";
                $update_ind_stmt = $conexion->prepare($update_indice);
                $update_ind_stmt->execute([$id_dosier_calidad, $id_indice_dosier]);

                $conexion->commit();

                echo json_encode([
                    'success' => true,
                    'message' => 'Registro eliminado del dosier exitosamente'
                ]);

            } catch (Exception $e) {
                $conexion->rollback();
                echo json_encode(['error' => true, 'message' => 'Error al eliminar registro: ' . $e->getMessage()]);
            }
            break;

        default:
            echo json_encode(['error' => true, 'message' => 'Operación no válida']);
            break;
    }

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => 'Error del servidor: ' . $e->getMessage()]);
}

$conexion = null;
?>
