<?php
$name_page_principal = '01_AdministrarConsumibles.php';
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
                      FROM archivos_consumibles
                      ORDER BY id ASC";
            $resultado = $conexion->prepare($query);
            $resultado->execute();
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'obtener_registro_asignado':
            // Obtener registro asignado a un dosier específico (solo uno)
            $user = current_user();
            $id_usuario = $user['username'];
            $user_level = (int)$user['user_level'];

            if ($user_level === 1) {
                // Si es administrador, obtener todos los registros
                $query = "SELECT ac.*
                          FROM archivos_consumibles ac";
                $resultado = $conexion->prepare($query);
                $resultado->execute();
            } else {
                // Si no es administrador, obtener solo el registro asignado al usuario
                // (asumiendo que hay una tabla intermedia 'usuario_consumibles' que relaciona usuarios y consumibles)
                $query = "SELECT ac.*
                          FROM archivos_consumibles ac
                          INNER JOIN usuario_consumibles uc ON ac.id = uc.consumible_id
                          WHERE uc.username = ?";
                $resultado = $conexion->prepare($query);
                $resultado->execute([$id_usuario]);

            }
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case 'guardar_archivo_consumible':

            if(!has_access_with_permissions($name_page_principal, 'editar')) {
                echo json_encode(['error' => true, 'message' => 'No tienes permiso para modificar estos datos.']);
                exit;
            }

            // Guardar/actualizar registro del capítulo 2 para un dosier específico
            $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
            $id_consumible = isset($_POST['id_consumible']) ? $_POST['id_consumible'] : '';

            // Validar que la descripción no esté vacía
            if (empty($descripcion)) {
                echo json_encode(['error' => true, 'message' => 'La descripción es requerida']);
                exit;
            }

            // Obtener la carpeta de consumibles para subir archivos
            $carpeta = find_table_field_only('constantes', 'nombre_constante', 'carpeta_consumibles');
            if(empty($carpeta['valor_constante'])) {
                echo json_encode(['error' => true, 'message' => 'La carpeta de subida no está definida.']);
                exit;
            }
            $upload_path = '../'.$carpeta['valor_constante'];
            // Asegurar que la ruta termine con slash
            if (substr($upload_path, -1) !== '/') {
                $upload_path .= '/';
            }

            // Crear carpeta si no existe
            if (!is_dir($upload_path)) {
                if (!mkdir($upload_path, 0755, true)) {
                    echo json_encode(['error' => true, 'message' => 'Error al crear la carpeta de destino.']);
                    exit;
                }
            }

            $conexion->beginTransaction();

            try {
                $nombre_archivo = null;
                $registro_existente = null;
                $archivo_proporcionado = isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK;

                // Si se proporciona ID, verificar si existe el registro
                if (!empty($id_consumible)) {
                    $check_registro = "SELECT id, nombre_archivo FROM archivos_consumibles WHERE id = ?";
                    $check_stmt = $conexion->prepare($check_registro);
                    $check_stmt->execute([$id_consumible]);
                    $registro_existente = $check_stmt->fetch(PDO::FETCH_ASSOC);
                }

                // Validar archivo para nuevos registros
                if (!$registro_existente && !$archivo_proporcionado) {
                    throw new Exception('Debe seleccionar un archivo PDF para el nuevo consumible');
                }

                // Manejar subida de archivo si se proporciona
                if ($archivo_proporcionado) {
                    $archivo_temp = $_FILES['archivo']['tmp_name'];
                    $archivo_nombre = $_FILES['archivo']['name'];
                    $archivo_extension = strtolower(pathinfo($archivo_nombre, PATHINFO_EXTENSION));
                    
                    // Validar extensión
                    if (!in_array($archivo_extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'xlsm'])) {
                        throw new Exception('Tipo de archivo no permitido. Solo se permiten PDF, DOC, DOCX, XLS, XLSX, XLSM.');
                    }

                    // Si existe un archivo anterior, eliminarlo
                    if ($registro_existente && !empty($registro_existente['nombre_archivo'])) {
                        $archivo_anterior = $upload_path . $registro_existente['nombre_archivo'];
                        if (file_exists($archivo_anterior)) {
                            unlink($archivo_anterior);
                        }
                    }
                    
                    // Generar nombre único
                    if (!empty($id_consumible)) {
                        $nombre_archivo = $id_consumible . '_' . time() . '.' . $archivo_extension;
                    } else {
                        // Para nuevo registro, generar ID temporal
                        $temp_id = 'new_' . time();
                        $nombre_archivo = $temp_id . '.' . $archivo_extension;
                    }
                    
                    $ruta_destino = $upload_path . $nombre_archivo;
                    
                    // Mover archivo
                    if (!move_uploaded_file($archivo_temp, $ruta_destino)) {
                        throw new Exception('Error al subir el archivo');
                    }
                }

                if ($registro_existente) {
                    // Actualizar registro existente
                    if ($archivo_proporcionado) {
                        // Se proporcionó un nuevo archivo
                        $update_query = "UPDATE archivos_consumibles SET descripcion = ?, nombre_archivo = ?, fecha_actualizacion = NOW() WHERE id = ?";
                        $update_stmt = $conexion->prepare($update_query);
                        $update_stmt->execute([$descripcion, $nombre_archivo, $id_consumible]);
                    } else {
                        // Solo actualizar descripción
                        $update_query = "UPDATE archivos_consumibles SET descripcion = ?, fecha_actualizacion = NOW() WHERE id = ?";
                        $update_stmt = $conexion->prepare($update_query);
                        $update_stmt->execute([$descripcion, $id_consumible]);
                    }
                } else {
                    // Crear nuevo registro
                    $insert_query = "INSERT INTO archivos_consumibles (descripcion, nombre_archivo, fecha_actualizacion) VALUES (?, ?, NOW())";
                    $insert_stmt = $conexion->prepare($insert_query);
                    $insert_stmt->execute([$descripcion, $nombre_archivo]);
                    
                    // Obtener el ID del nuevo registro
                    $nuevo_id = $conexion->lastInsertId();
                    
                    // Renombrar el archivo con el ID real
                    if ($archivo_proporcionado) {
                        $nuevo_nombre_archivo = $nuevo_id . '_' . time() . '.' . $archivo_extension;
                        $nueva_ruta = $upload_path . $nuevo_nombre_archivo;
                        
                        if (rename($ruta_destino, $nueva_ruta)) {
                            // Actualizar el nombre del archivo en la base de datos
                            $update_nombre = "UPDATE archivos_consumibles SET nombre_archivo = ? WHERE id = ?";
                            $update_stmt = $conexion->prepare($update_nombre);
                            $update_stmt->execute([$nuevo_nombre_archivo, $nuevo_id]);
                        }
                    }
                }
                
                $conexion->commit();
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Registro guardado exitosamente'
                ]);

            } catch (Exception $e) {
                $conexion->rollback();
                echo json_encode(['error' => true, 'message' => 'Error al guardar registro: ' . $e->getMessage()]);
            }
            break;

        case 'eliminar_archivo_consumible':

            if(!has_access_with_permissions($name_page_principal, 'eliminar')) {
                echo json_encode(['error' => true, 'message' => 'No tienes permiso para modificar estos datos.']);
                exit;
            }

            $id_consumible = isset($_POST['id_consumible']) ? $_POST['id_consumible'] : '';

            if (empty($id_consumible)) {
                echo json_encode(['error' => true, 'message' => 'Datos requeridos no proporcionados']);
                exit;
            }

            $conexion->beginTransaction();

            try {
                // Obtener información del archivo antes de eliminar
                $get_archivo = "SELECT nombre_archivo FROM archivos_consumibles WHERE id = ?";
                $archivo_stmt = $conexion->prepare($get_archivo);
                $archivo_stmt->execute([$id_consumible]);
                $archivo_info = $archivo_stmt->fetch(PDO::FETCH_ASSOC);

                // Eliminar las asignaciones de usuarios primero
                $delete_asignaciones = "DELETE FROM usuario_consumibles WHERE consumible_id = ?";
                $delete_stmt = $conexion->prepare($delete_asignaciones);
                $delete_stmt->execute([$id_consumible]);

                // Eliminar el archivo físico si existe
                if ($archivo_info && !empty($archivo_info['nombre_archivo'])) {
                    $carpeta = find_table_field_only('constantes', 'nombre_constante', 'carpeta_consumibles');
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
                $delete_registro = "DELETE FROM archivos_consumibles WHERE id = ?";
                $delete_stmt = $conexion->prepare($delete_registro);
                $delete_stmt->execute([$id_consumible]);

                $conexion->commit();

                echo json_encode([
                    'success' => true,
                    'message' => 'Consumible eliminado exitosamente'
                ]);

            } catch (Exception $e) {
                $conexion->rollback();
                echo json_encode(['error' => true, 'message' => 'Error al eliminar consumible: ' . $e->getMessage()]);
            }
            break;

        case 'obtener_usuarios_asignados':
            // Obtener usuarios asignados a un consumible específico
            $id_consumible = isset($_POST['id_consumible']) ? $_POST['id_consumible'] : '';

            if (empty($id_consumible)) {
                echo json_encode(['error' => true, 'message' => 'ID de consumible requerido']);
                exit;
            }

            try {
                $query = "SELECT username FROM usuario_consumibles WHERE consumible_id = ?";
                $resultado = $conexion->prepare($query);
                $resultado->execute([$id_consumible]);
                $usuarios = $resultado->fetchAll(PDO::FETCH_COLUMN);
                
                echo json_encode([
                    'success' => true,
                    'usuarios' => $usuarios
                ]);
            } catch (Exception $e) {
                echo json_encode(['error' => true, 'message' => 'Error al obtener usuarios: ' . $e->getMessage()]);
            }
            break;

        case 'asignar_usuarios':
            // Asignar usuarios a un consumible
            if(!has_access_with_permissions($name_page_principal, 'editar')) {
                echo json_encode(['error' => true, 'message' => 'No tienes permiso para modificar estos datos.']);
                exit;
            }

            $id_consumible = isset($_POST['id_consumible']) ? $_POST['id_consumible'] : '';
            $usuarios_json = isset($_POST['usuarios']) ? $_POST['usuarios'] : '[]';
            
            if (empty($id_consumible)) {
                echo json_encode(['error' => true, 'message' => 'ID de consumible requerido']);
                exit;
            }

            $usuarios = json_decode($usuarios_json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo json_encode(['error' => true, 'message' => 'Formato de usuarios inválido']);
                exit;
            }

            $conexion->beginTransaction();

            try {
                // Eliminar asignaciones existentes
                $delete_query = "DELETE FROM usuario_consumibles WHERE consumible_id = ?";
                $delete_stmt = $conexion->prepare($delete_query);
                $delete_stmt->execute([$id_consumible]);

                // Insertar nuevas asignaciones
                if (!empty($usuarios)) {
                    $insert_query = "INSERT INTO usuario_consumibles (username, consumible_id) VALUES (?, ?)";
                    $insert_stmt = $conexion->prepare($insert_query);
                    
                    foreach ($usuarios as $username) {
                        $insert_stmt->execute([$username, $id_consumible]);
                    }
                }

                $conexion->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Asignación de usuarios actualizada exitosamente'
                ]);

            } catch (Exception $e) {
                $conexion->rollback();
                echo json_encode(['error' => true, 'message' => 'Error al asignar usuarios: ' . $e->getMessage()]);
            }
            break;

        case 'descargar_archivo':
            // Descargar archivo sin exponer la ruta
            $id_consumible = isset($_POST['id_consumible']) ? $_POST['id_consumible'] : '';

            if (empty($id_consumible)) {
                http_response_code(400);
                echo "Error: ID de consumible requerido";
                exit;
            }

            try {
                // Verificar que el usuario tenga acceso al consumible
                $user = current_user();
                $id_usuario = $user['username'];
                $user_level = (int)$user['user_level'];

                if ($user_level === 1) {
                    // Si es administrador, puede descargar cualquier archivo
                    $query = "SELECT nombre_archivo, descripcion FROM archivos_consumibles WHERE id = ?";
                    $stmt = $conexion->prepare($query);
                    $stmt->execute([$id_consumible]);
                } else {
                    // Si no es administrador, verificar que tenga acceso
                    $query = "SELECT ac.nombre_archivo, ac.descripcion 
                              FROM archivos_consumibles ac
                              INNER JOIN usuario_consumibles uc ON ac.id = uc.consumible_id
                              WHERE ac.id = ? AND uc.username = ?";
                    $stmt = $conexion->prepare($query);
                    $stmt->execute([$id_consumible, $id_usuario]);
                }

                $archivo_info = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$archivo_info || empty($archivo_info['nombre_archivo'])) {
                    http_response_code(404);
                    echo "Error: Archivo no encontrado o sin permisos";
                    exit;
                }

                // Obtener la ruta del archivo
                $carpeta = find_table_field_only('constantes', 'nombre_constante', 'carpeta_consumibles');
                if (empty($carpeta['valor_constante'])) {
                    http_response_code(500);
                    echo "Error: Carpeta de consumibles no configurada";
                    exit;
                }

                $upload_path = '../' . $carpeta['valor_constante'];
                if (substr($upload_path, -1) !== '/') {
                    $upload_path .= '/';
                }

                $archivo_completo = $upload_path . $archivo_info['nombre_archivo'];

                // Verificar que el archivo existe
                if (!file_exists($archivo_completo)) {
                    http_response_code(404);
                    echo '<!DOCTYPE html>
                        <html>
                        <head>
                            <meta charset="utf-8">
                            <title>Archivo no encontrado</title>
                            <script>
                                // Si está en un popup, cerrar. Si no, retroceder.
                                window.close();
                            </script>
                        </head>
                        <body>
                            <h3 style="color:red;text-align:center;margin-top:40px;">Error: Archivo físico no encontrado</h3>
                            <p style="text-align:center;">Esta ventana se cerrará automáticamente.</p>
                        </body>
                        </html>';
                    
                    exit;
                }

                // Determinar el tipo de archivo
                $extension = strtolower(pathinfo($archivo_info['nombre_archivo'], PATHINFO_EXTENSION));
                $mime_types = [
                    'pdf' => 'application/pdf',
                    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
                    'xls' => 'application/vnd.ms-excel',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'doc' => 'application/msword'
                ];

                $content_type = isset($mime_types[$extension]) ? $mime_types[$extension] : 'application/octet-stream';

                // Generar nombre de descarga basado en la descripción
                $nombre_descarga = preg_replace('/[^a-zA-Z0-9._-]/', '_', $archivo_info['descripcion']);
                $nombre_descarga = $nombre_descarga . '.' . $extension;

                // Configurar headers para descarga
                header('Content-Type: ' . $content_type);
                header('Content-Disposition: attachment; filename="' . $nombre_descarga . '"');
                header('Content-Length: ' . filesize($archivo_completo));
                header('Cache-Control: must-revalidate');
                header('Pragma: public');

                // Limpiar buffer de salida
                ob_clean();
                flush();

                // Enviar archivo
                readfile($archivo_completo);
                exit;

            } catch (Exception $e) {
                http_response_code(500);
                echo "Error del servidor: " . $e->getMessage();
                exit;
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
