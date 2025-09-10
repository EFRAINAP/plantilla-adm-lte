<?php
//$page_title = 'SQL';
require_once(LIB_PATH_INC . DS . "00_load.php");

function tableExists($table) {
    global $db;
    
    // Escapando el nombre de la tabla
    $escaped_table = $db->escape($table);
    
    $sql = "SHOW TABLES LIKE :table";
    $result = $db->query($sql, [':table' => $escaped_table]);
    return ($db->num_rows($result) > 0);
}

// Autenticación del usuario
function authenticate($username = '', $password = '') {
    global $db;
    
    $sql = "SELECT * FROM users WHERE username = :username AND estado_user = 1 LIMIT 1";
    $result = $db->query($sql, [':username' => $username]);
    
    if ($db->num_rows($result)) {
        $user = $db->fetch_assoc($result);
        // Comparar la contraseña proporcionada con la almacenada
        $hashDesdeBD = $user['password'];
        // Verificar si la contraseña proporcionada coincide con la almacenada
        if (password_verify($password, $hashDesdeBD)) {
            return $user['username'];
        } else {
            // Si la contraseña no coincide, retornar false
            return false;
        }
    }
    return false;
}

// Obtener usuario actual
function current_user() {
    global $db;
    static $current_user;
    if (!$current_user) {
        if (isset($_SESSION['user_name'])) {
            $user_name = $_SESSION['user_name'];
            $current_user = find_table_field_only('users', 'username', $user_name);
        }
    }
    return $current_user;
}

// verficar si el usuario tiene acceso a una pagina
function has_access($page) {
    global $db;
    $user = current_user();

    if (!$user) {
        return false;
    }

    // Los administradores (user_level = 1) tienen acceso a todo
    if (isset($user['user_level']) && $user['user_level'] == 1) {
        return true;
    }

    // Para usuarios normales, verificar permisos específicos
    $sql = "SELECT * FROM acceso_paginas WHERE username = :username AND pagina = :page LIMIT 1";
    $result = $db->query($sql, [':username' => $user['username'], ':page' => $page]);
    return ($db->num_rows($result) > 0);
}

// verificar si el usuario tiene acceso a una pagina con permisos
function has_access_with_permissions($page, $permission) {
    global $db;
    $user = current_user();
    if (!$user) {
        return false;
    }
    // Los administradores (user_level = 1) tienen acceso a todo
    if (isset($user['user_level']) && $user['user_level'] == 1) {
        return true;
    }
    // Para usuarios normales, verificar permisos específicos
    $sql = "SELECT $permission FROM acceso_paginas WHERE username = :username AND pagina = :page LIMIT 1";
    $result = $db->query($sql, [':username' => $user['username'], ':page' => $page]);
    if ($db->num_rows($result) > 0) {
        $permissions = $db->fetch_assoc($result);
        // Verificar si el permiso específico está habilitado
        if (isset($permissions[$permission]) && $permissions[$permission] === 'Si') {
            return true;
        }
    }
    return false;
}

// Actualizar último inicio de sesión
function updateLastLogIn($user_name) {
    global $db;
    
    $date = make_date(ini_set('date.timezone', 'America/Lima'));
    $sql = "UPDATE users SET last_login = :last_login WHERE username = :username LIMIT 1";
    $result = $db->query($sql, [':last_login' => $date, ':username' => $user_name]);
    return ($result && $db->affected_rows() === 1);
}

// Obtener todos los registros de una tabla en un array
function find_table_array($table) {
    global $db;
    
    if (tableExists($table)) {
        $escaped_table = $db->escape($table);
        $sql = "SELECT * FROM " . $escaped_table;
        $result = $db->query($sql);
        return $db->while_loop($result);
    }
    return false;
}

// Obtener registros con filtro
function find_table_field_array($table, $field, $value) {
    global $db;

    if (tableExists($table)) {
        $escaped_table = $db->escape($table);
        $escaped_field = $db->escape($field);
        $sql = "SELECT * FROM {$escaped_table} WHERE {$escaped_field} = :value";
        $result = $db->query($sql, [':value' => $value]);
        return $db->while_loop($result);
    }
    return false;
}

// Buscar un único registro por campo
function find_table_field_only($table, $field, $value) {
    global $db;
    
    if (tableExists($table)) {
        $escaped_table = $db->escape($table);
        $escaped_field = $db->escape($field);
        $sql = "SELECT * FROM {$escaped_table} WHERE {$escaped_field} = :value";
        $result = $db->query($sql, [':value' => $value]);
        
        if ($row = $db->fetch_assoc($result)) {
            return $row;
        }
    }
    return null;
}

// Buscar un unico distinct registro por campo
function find_table_distinct_field_two_only($table, $field, $field1, $field2, $value1, $value2) {
    global $db;
    
    if (tableExists($table)) {
        $escaped_table = $db->escape($table);
		$escaped_field = $db->escape($field);
        $escaped_field1 = $db->escape($field1);
        $escaped_field2 = $db->escape($field2);
        $sql = "SELECT DISTINCT  {$escaped_field} FROM {$escaped_table} WHERE {$escaped_field1} = :value1 AND {$escaped_field2} = :value2";
        $result = $db->query($sql, [':value1' => $value1, ':value2' => $value2]);
        
        if ($row = $db->fetch_assoc($result)) {
            return $row;
        }
    }
    return null;
}

// Obtener registros con dos campos de filtro
function find_table_field_two_only($table, $field1, $field2, $value1, $value2) {
    global $db;

    if (tableExists($table)) {
        $escaped_table = $db->escape($table);
        $escaped_field1 = $db->escape($field1);
        $escaped_field2 = $db->escape($field2);
        $sql = "SELECT * FROM {$escaped_table} WHERE {$escaped_field1} = :value1 AND {$escaped_field2} = :value2";
        $result = $db->query($sql, [':value1' => $value1, ':value2' => $value2]);

        return $db->while_loop($result);
    }
    return null;
}



// Obtener registros con dos campos de filtro
function find_table_field_two_only2($table, $field1, $field2, $value1, $value2) {
    global $db;

    if (tableExists($table)) {
        $escaped_table = $db->escape($table);
        $escaped_field1 = $db->escape($field1);
        $escaped_field2 = $db->escape($field2);
        $sql = "SELECT * FROM {$escaped_table} WHERE {$escaped_field1} = :value1 AND {$escaped_field2} = :value2";
        $result = $db->query($sql, [':value1' => $value1, ':value2' => $value2]);

        if ($row = $db->fetch_assoc($result)) {
            return $row;
        }
    }
    return null;
}

// Obtener registros con 1 campos de filtro distrintos para 2 tablas
function find_table_2_field_1_array($table1, $table2, $field1, $field2, $filter, $value) {
    global $db;

    // Verificar si las tablas existen
    if (tableExists($table1) && tableExists($table2)) {
        // Escapar los nombres de las tablas y columnas
        $escaped_table1 = $db->escape($table1);
        $escaped_table2 = $db->escape($table2);
        $escaped_field1 = $db->escape($field1);
        $escaped_field2 = $db->escape($field2);
        $escaped_filter = $db->escape($filter);

        // Construcción de la consulta SQL
        $sql = "SELECT * FROM {$escaped_table1} T0
                JOIN {$escaped_table2} T1
                ON T0.{$escaped_field1} = T1.{$escaped_field2}
                WHERE T0.{$escaped_filter} = :value";

        // Ejecutar la consulta
        $result = $db->query($sql, [':value' => $value]);

        // Devolver los resultados procesados
        return $db->while_loop($result);
    }

    // Si alguna de las tablas no existe, devolver false
    return false;
}

// Obtener registros con 2 campos de filtro distrintos para 2 tablas
function find_table_2_field_2_array($table1, $table2, $field1, $field2, $filter, $value, $filter2, $value2) {
    global $db;

    // Verificar si las tablas existen
    if (tableExists($table1) && tableExists($table2)) {
        // Escapar los nombres de las tablas y columnas
        $escaped_table1 = $db->escape($table1);
        $escaped_table2 = $db->escape($table2);
        $escaped_field1 = $db->escape($field1);
        $escaped_field2 = $db->escape($field2);
        $escaped_filter = $db->escape($filter);
        $escaped_filter2 = $db->escape($filter2);
        $escaped_value2 = $db->escape($value2);

        // Construcción de la consulta SQL
        $sql = "SELECT * FROM {$escaped_table1} T0
                JOIN {$escaped_table2} T1
                ON T0.{$escaped_field1} = T1.{$escaped_field2}
                WHERE T0.{$escaped_filter} = :value1
                AND T1.{$escaped_filter2} = :value2";

        // Ejecutar la consulta
        $result = $db->query($sql, [':value1' => $value, ':value2' => $value2]);

        // Devolver los resultados procesados
        return $db->while_loop($result);
    }

    // Si alguna de las tablas no existe, devolver false
    return false;
}