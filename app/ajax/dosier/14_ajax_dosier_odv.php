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
        case 'obtener_odv_disponibles':
            // Obtener todas las ODV disponibles
            $query = "SELECT *
                      FROM odv 
                      ORDER BY numero ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute();
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'obtener_odv_asignado':
            // Obtener ODV asignado a un dosier específico (solo uno)
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            
            if (empty($cod_dosier)) {
                echo json_encode(['error' => true, 'message' => 'Código de dosier requerido']);
                exit;
            }

            $query = "SELECT o.id, o.numero, o.descripcion, o.nombre_archivo
                      FROM odv o
                      INNER JOIN odv_dosier od ON o.numero = od.numero_odv
                      WHERE od.cod_dosier = ?
                      ORDER BY o.numero ASC
                      LIMIT 1";
            $resultado = $conexion->prepare($query);
            $resultado->execute([$cod_dosier]);
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'guardar_odv_dosier':

            if(!has_access_with_permissions($name_page_principal, 'editar')) {
                echo json_encode(['error' => true, 'message' => 'No tienes permiso para modificar estos datos.']);
                exit;
            }

            // Guardar/actualizar ODV para un dosier específico
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            $numero = isset($_POST['numero']) ? trim($_POST['numero']) : '';
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            $numero_anterior = isset($_POST['numero_anterior']) ? $_POST['numero_anterior'] : '';
            $id_dosier_calidad = isset($_POST['id_dosier_calidad']) ? $_POST['id_dosier_calidad'] : '';
            $funcion = isset($_POST['funcion']) ? $_POST['funcion'] : '';

            if (empty($cod_dosier) || empty($numero)) {
                echo json_encode(['error' => true, 'message' => 'Código de dosier y número de ODV son requeridos']);
                exit;
            }

            // Verificar permisos específicos del dosier
            if (!puedeModificarDatosEspecificos($user, $cod_dosier)) {
                echo json_encode([
                    'error' => true,
                    'message' => 'No tienes permiso para modificar estos datos.'
                ]);
                exit;
            }

            // Obtener la carpeta de ODV para subir archivos
            $carpeta = find_table_field_only('constantes', 'nombre_constante', 'carpeta_odv');
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
                // 1. Verificar si existe el ODV con ese número
                $check_odv = "SELECT id FROM odv WHERE numero = ?";
                $check_stmt = $conexion->prepare($check_odv);
                $check_stmt->execute([$numero]);
                $odv_existente = $check_stmt->fetch(PDO::FETCH_ASSOC);

                $nombre_archivo = null;
                
                // Manejo de archivo si se subió uno nuevo
                if (isset($_FILES['archivo_odv']) && $_FILES['archivo_odv']['error'] === UPLOAD_ERR_OK) {
                    $fileinfo = pathinfo($_FILES['archivo_odv']['name']);
                    $extension = strtolower($fileinfo['extension']);

                    // Validar extensión
                    $extensiones_permitidas = ['pdf', 'doc', 'docx'];
                    if (!in_array($extension, $extensiones_permitidas)) {
                        throw new Exception('Tipo de archivo no permitido. Solo se permiten: ' . implode(', ', $extensiones_permitidas));
                    }

                    $nombre_archivo = $numero . '.' . $extension;
                    $file_path = $upload_path . $nombre_archivo;

                    // Verificar que el directorio existe
                    if (!is_dir($upload_path)) {
                        if (!mkdir($upload_path, 0755, true)) {
                            throw new Exception('Error al crear el directorio de destino');
                        }
                    }

                    // Eliminar archivo anterior si existe
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }

                    // Mover archivo
                    if (!move_uploaded_file($_FILES['archivo_odv']['tmp_name'], $file_path)) {
                        throw new Exception('Error al subir el archivo ODV');
                    }
                }

                if ($odv_existente) {
                    // 2a. El ODV existe, actualizarlo
                    $update_query = "UPDATE odv SET descripcion = ?";
                    $params = [$descripcion];
                    
                    if ($nombre_archivo) {
                        $update_query .= ", nombre_archivo = ?";
                        $params[] = $nombre_archivo;
                    }
                    
                    $update_query .= " WHERE numero = ?";
                    $params[] = $numero;
                    
                    $update_stmt = $conexion->prepare($update_query);
                    $update_stmt->execute($params);
                    
                    $odv_id = $odv_existente['id'];
                } else {
                    // 2b. El ODV no existe, crearlo
                    $insert_query = "INSERT INTO odv (numero, descripcion" . ($nombre_archivo ? ", nombre_archivo" : "") . ") VALUES (?, ?" . ($nombre_archivo ? ", ?" : "") . ")";
                    $params = [$numero, $descripcion];
                    if ($nombre_archivo) {
                        $params[] = $nombre_archivo;
                    }
                    
                    $insert_stmt = $conexion->prepare($insert_query);
                    $insert_stmt->execute($params);
                    $odv_id = $conexion->lastInsertId();
                }

                // 3. Si había un ODV anterior diferente, eliminar la relación
                if ($numero_anterior && $numero_anterior !== $numero) {
                    $delete_relacion = "DELETE FROM odv_dosier WHERE cod_dosier = ? AND numero_odv = ?";
                    $delete_stmt = $conexion->prepare($delete_relacion);
                    $delete_stmt->execute([$cod_dosier, $numero_anterior]);
                }

                // 4. Verificar si ya existe la relación ODV-Dosier
                $check_relacion = "SELECT COUNT(*) FROM odv_dosier WHERE cod_dosier = ? AND numero_odv = ?";
                $check_rel_stmt = $conexion->prepare($check_relacion);
                $check_rel_stmt->execute([$cod_dosier, $numero]);
                
                if ($check_rel_stmt->fetchColumn() == 0) {
                    // 5. Crear la relación ODV-Dosier si no existe
                    $insert_relacion = "INSERT INTO odv_dosier (cod_dosier, numero_odv) VALUES (?, ?)";
                    $rel_stmt = $conexion->prepare($insert_relacion);
                    $rel_stmt->execute([$cod_dosier, $numero]);
                }

                // 6. Actualizar estado del índice del dosier
                $update_indice = "UPDATE dosier_indice_detalle did 
                                 INNER JOIN indice_dosier id ON id.id = did.id_indice_dosier 
                                 SET did.completar = 1 
                                 WHERE did.id_dosier_calidad = ? AND id.funcion = ?";
                $update_ind_stmt = $conexion->prepare($update_indice);
                $update_ind_stmt->execute([$id_dosier_calidad, $funcion]);
                
                $conexion->commit();
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'ODV guardado exitosamente'
                ]);

            } catch (Exception $e) {
                $conexion->rollback();
                echo json_encode(['error' => true, 'message' => 'Error al guardar ODV: ' . $e->getMessage()]);
            }
            break;

        case 'eliminar_odv_dosier':

            if(!has_access_with_permissions($name_page_principal, 'editar')) {
                echo json_encode(['error' => true, 'message' => 'No tienes permiso para modificar estos datos.']);
                exit;
            }

            // Eliminar ODV de un dosier específico
            $cod_dosier = isset($_POST['cod_dosier']) ? $_POST['cod_dosier'] : '';
            $id_dosier_calidad = isset($_POST['id_dosier_calidad']) ? $_POST['id_dosier_calidad'] : '';
            $funcion = isset($_POST['funcion']) ? $_POST['funcion'] : '';

            if (empty($cod_dosier)) {
                echo json_encode(['error' => true, 'message' => 'Código de dosier requerido']);
                exit;
            }

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
                // Eliminar la relación ODV-Dosier
                $delete_query = "DELETE FROM odv_dosier WHERE cod_dosier = ?";
                $delete_stmt = $conexion->prepare($delete_query);
                $delete_stmt->execute([$cod_dosier]);

                // Actualizar estado del índice del dosier
                $update_query = "UPDATE dosier_indice_detalle did 
                                INNER JOIN indice_dosier id ON id.id = did.id_indice_dosier 
                                SET did.completar = 0 
                                WHERE did.id_dosier_calidad = ? AND id.funcion = ?";
                $update_stmt = $conexion->prepare($update_query);
                $update_stmt->execute([$id_dosier_calidad, $funcion]);
                
                $conexion->commit();
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'ODV eliminado del dosier exitosamente'
                ]);

            } catch (Exception $e) {
                $conexion->rollback();
                echo json_encode(['error' => true, 'message' => 'Error al eliminar ODV: ' . $e->getMessage()]);
            }
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
