<?php

require_once(__DIR__ . '/../01_ajax_connection.php');

// Definir la cabecera para devolver datos en formato JSON
header('Content-Type: application/json; charset=UTF-8');

try {
    // Crear la conexión
    $objeto = new connection_ajax();
    $conexion = $objeto->conectar();

    // Obtener la operación
    $operacion = isset($_POST['operacion']) ? $_POST['operacion'] : 'obtener_todos_los_usuarios';

    switch ($operacion) {
        case 'create':
            // Validar campos requeridos
            $required = ['name', 'username', 'password', 'cargo', 'user_level', 'area', 'proceso', 'estado_user'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['error' => true, 'message' => "El campo {$field} es requerido."]);
                    exit;
                }
            }

            $name = trim($_POST['name']);
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $user_level = (int)$_POST['user_level'];
            $area = trim($_POST['area']);
            $proceso = trim($_POST['proceso']);
            $cargo = trim($_POST['cargo']);
            $estado_user = (int)$_POST['estado_user'];

            // Verificar si el usuario ya existe
            $check_sql = "SELECT username FROM users WHERE username = :username";
            $check_stmt = $conexion->prepare($check_sql);
            $check_stmt->bindParam(':username', $username);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                echo json_encode(['error' => true, 'message' => 'El nombre de usuario ya existe.']);
                exit;
            }

            // Hash de la contraseña
            $hash = password_hash($password, PASSWORD_BCRYPT);

            // Insertar usuario
            $sql = "INSERT INTO users (name, username, password, user_level, area, cargo, estado_user, proceso) 
                    VALUES (:name, :username, :password, :user_level, :area, :cargo, :estado_user, :proceso)";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hash);
            $stmt->bindParam(':user_level', $user_level);
            $stmt->bindParam(':area', $area);
            $stmt->bindParam(':cargo', $cargo);
            $stmt->bindParam(':estado_user', $estado_user);
            $stmt->bindParam(':proceso', $proceso);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Usuario agregado correctamente.']);
            } else {
                echo json_encode(['error' => true, 'message' => 'Error al agregar el usuario.']);
            }
            break;

        case 'update':
            // Validar campos requeridos
            $required = ['name', 'username', 'cargo', 'user_level', 'area', 'proceso', 'estado_user'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    echo json_encode(['error' => true, 'message' => "El campo {$field} es requerido."]);
                    exit;
                }
            }

            $name = trim($_POST['name']);
            $username = trim($_POST['username']);
            $user_level = (int)$_POST['user_level'];
            $area = trim($_POST['area']);
            $proceso = trim($_POST['proceso']);
            $cargo = trim($_POST['cargo']);
            $estado_user = (int)$_POST['estado_user'];

            $sql = "UPDATE users SET 
                        name = :name,
                        user_level = :user_level, 
                        area = :area, 
                        cargo = :cargo,
                        estado_user = :estado_user,
                        proceso = :proceso
                    WHERE username = :username";

            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':user_level', $user_level);
            $stmt->bindParam(':area', $area);
            $stmt->bindParam(':cargo', $cargo);
            $stmt->bindParam(':estado_user', $estado_user);
            $stmt->bindParam(':proceso', $proceso);
            $stmt->bindParam(':username', $username);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente.']);
            } else {
                echo json_encode(['error' => true, 'message' => 'Error al actualizar el usuario.']);
            }
            break;

        case 'delete':
            if (empty($_POST['username'])) {
                echo json_encode(['error' => true, 'message' => 'Username es requerido.']);
                exit;
            }

            $username = trim($_POST['username']);

            // Iniciar transacción
            $conexion->beginTransaction();

            try {
                // Eliminar accesos y perfiles del usuario
                $delete_accesos = "DELETE FROM acceso_paginas WHERE username = :username";
                $stmt1 = $conexion->prepare($delete_accesos);
                $stmt1->bindParam(':username', $username);
                $stmt1->execute();

                $delete_perfiles = "DELETE FROM acceso_perfiles WHERE username = :username";
                $stmt2 = $conexion->prepare($delete_perfiles);
                $stmt2->bindParam(':username', $username);
                $stmt2->execute();

                // Eliminar usuario
                $delete_user = "DELETE FROM users WHERE username = :username";
                $stmt3 = $conexion->prepare($delete_user);
                $stmt3->bindParam(':username', $username);
                $stmt3->execute();

                if ($stmt3->rowCount() > 0) {
                    $conexion->commit();
                    echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
                } else {
                    $conexion->rollback();
                    echo json_encode(['error' => true, 'message' => 'Usuario no encontrado.']);
                }
            } catch (Exception $e) {
                $conexion->rollback();
                echo json_encode(['error' => true, 'message' => 'Error al eliminar el usuario: ' . $e->getMessage()]);
            }
            break;

        case 'cambiar_password':
            if (empty($_POST['username']) || empty($_POST['password'])) {
                echo json_encode(['error' => true, 'message' => 'Username y password son requeridos.']);
                exit;
            }

            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $hash = password_hash($password, PASSWORD_BCRYPT);

            $sql = "UPDATE users SET password = :password WHERE username = :username";
            $stmt = $conexion->prepare($sql);
            $stmt->bindParam(':password', $hash);
            $stmt->bindParam(':username', $username);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente.']);
            } else {
                echo json_encode(['error' => true, 'message' => 'Error al actualizar la contraseña.']);
            }
            break;

        case 'obtener_todos_los_usuarios':
            // Preparar la consulta para evitar inyecciones SQL
            $query = "SELECT * FROM users ORDER BY name ASC";
            $stmt = $conexion->prepare($query);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Error en la consulta: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

?>
