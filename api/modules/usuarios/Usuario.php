<?php
/**
 * Usuario Model - Gestión de datos de usuarios
 */
namespace Api\Modules\Usuarios;
use Api\Core\BaseModel;
use Exception;

class Usuario extends BaseModel {
    
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'username',
        'name', 
        'password',
        'user_level',
        'last_login',
        'image',
        'cargo',
        'area',
        'estado_user',
        'proceso'
    ];
    
    protected $hidden = [
        'password'
    ];
    
    public function __construct() {
        parent::__construct();
    }

    public function findAllUser() {
        $respuesta = $this->findAll();
        // Remove passwords from the result set
        foreach ($respuesta as &$user) {
            unset($user['password']);
        }
        return $respuesta;
    }

    public function show($id) {
        $usuario = $this->findById($id);
        
        if (!$usuario) {
            return null;
        }
        
        // Remover password de la respuesta usando método inherited
        return $this->toArray($usuario);
    }
    
    public function createUsuario($data) {
        // Hash de la contraseña antes de guardar
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        
        $data['estado_user'] = 1;
        $respuesta = parent::create($data);
        
        return $this->toArray($respuesta);
    }
    
    public function update($id, $data) {
        // Hash de la contraseña si se está actualizando
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            // Remover password si está vacío para no actualizarlo
            unset($data['password']);
        }
        
        $respuesta = parent::update($id, $data);
        return $this->toArray($respuesta);
    }
    
    public function findByUsername($user) {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        return $this->db->fetch($sql, ['username' => $user]);
    }
    
    public function authenticate($email, $password) {
        $user = $this->findByUsername($email);
        
        if ($user && password_verify($password, $user['password'])) {
            return $this->toArray($user);
        }
        
        return false;
    }
    
    public function findActivos() {
        $sql = "SELECT * FROM {$this->table} 
                WHERE estado_user = 1 
                ORDER BY name ASC";
        
        $usuarios = $this->db->fetchAll($sql);
        
        // Remove passwords
        foreach ($usuarios as &$user) {
            unset($user['password']);
        }
        
        return $usuarios;
    }
    
    public function search($query, $limit = 50) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (name LIKE :query OR username LIKE :query) 
                AND estado_user = 1
                ORDER BY name ASC 
                LIMIT {$limit}";
        
        $usuarios = $this->db->fetchAll($sql, ['query' => "%{$query}%"]);
        
        // Remove passwords
        foreach ($usuarios as &$user) {
            unset($user['password']);
        }
        
        return $usuarios;
    }
    
    // Métodos adicionales para autenticación
    public function updateLastAccess($userId) {
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE id = :id";
        return $this->db->query($sql, ['id' => $userId]);
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    public function cambiarPassword($id, $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE {$this->table} SET password = :password WHERE id = :id";
        $result = $this->db->query($sql, ['password' => $hash, 'id' => $id]);
        
        return ['success' => true, 'id' => $id];
    }
    
    // Gestión de perfiles
    public function getPerfilesUsuario($username) {
        $sql = "SELECT ap.*, p.* 
                FROM acceso_perfiles ap
                INNER JOIN perfiles p ON ap.perfil = p.perfil 
                WHERE ap.username = :username 
                ORDER BY ap.perfil ASC";
        
        return $this->db->fetchAll($sql, ['username' => $username]);
    }
    
    public function asignarPerfil($username, $perfil) {
        try {
            // Verificar si el perfil existe
            $checkPerfil = "SELECT perfil FROM perfiles WHERE perfil = :perfil";
            $perfilExists = $this->db->fetch($checkPerfil, ['perfil' => $perfil]);
            
            if (!$perfilExists) {
                return ['success' => false, 'message' => 'El perfil seleccionado no existe'];
            }
            
            // Verificar si ya existe la asignación
            $checkExist = "SELECT username FROM acceso_perfiles WHERE username = :username AND perfil = :perfil";
            $exists = $this->db->fetch($checkExist, ['username' => $username, 'perfil' => $perfil]);
            
            if ($exists) {
                return ['success' => false, 'message' => 'El perfil ya está asignado a este usuario'];
            }
            
            // Insertar nueva asignación
            $sql = "INSERT INTO acceso_perfiles (username, perfil) VALUES (:username, :perfil)";
            $this->db->query($sql, ['username' => $username, 'perfil' => $perfil]);
            
            return ['success' => true, 'message' => 'Perfil asignado correctamente'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al asignar perfil: ' . $e->getMessage()];
        }
    }
    
    public function eliminarPerfil($username, $perfil) {
        try {
            // Verificar si existe la asignación
            $checkExist = "SELECT username FROM acceso_perfiles WHERE username = :username AND perfil = :perfil";
            $exists = $this->db->fetch($checkExist, ['username' => $username, 'perfil' => $perfil]);
            
            if (!$exists) {
                return ['success' => false, 'message' => 'La asignación de perfil no existe'];
            }
            
            // Eliminar la asignación
            $sql = "DELETE FROM acceso_perfiles WHERE username = :username AND perfil = :perfil";
            $this->db->query($sql, ['username' => $username, 'perfil' => $perfil]);
            
            return ['success' => true, 'message' => 'Perfil eliminado correctamente'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error al eliminar perfil: ' . $e->getMessage()];
        }
    }
    
    public function getPerfilesDisponibles() {
        $sql = "SELECT * FROM perfiles ORDER BY perfil ASC";
        return $this->db->fetchAll($sql);
    }
    
    // Gestión de accesos a páginas
    public function getAccesosUsuario($username) {
        // Obtener todas las páginas disponibles
        $sqlPaginas = "SELECT pagina, descripcion_pagina FROM paginas ORDER BY descripcion_pagina ASC";
        $todasPaginas = $this->db->fetchAll($sqlPaginas);
        
        // Obtener accesos actuales del usuario
        $sqlAccesos = "SELECT pagina, editar, eliminar, adicionar, seguimiento 
                      FROM acceso_paginas 
                      WHERE username = :username";
        $accesosActuales = $this->db->fetchAll($sqlAccesos, ['username' => $username]);
        
        // Crear mapa de accesos
        $mapaAccesos = [];
        foreach ($accesosActuales as $acceso) {
            $mapaAccesos[$acceso['pagina']] = [
                'editar' => ($acceso['editar'] === 'Si'),
                'eliminar' => ($acceso['eliminar'] === 'Si'),
                'adicionar' => ($acceso['adicionar'] === 'Si'),
                'seguimiento' => ($acceso['seguimiento'] === 'Si')
            ];
        }
        
        // Combinar todas las páginas con accesos actuales
        $result = [];
        foreach ($todasPaginas as $pagina) {
            $paginaNombre = $pagina['pagina'];
            $tieneAcceso = isset($mapaAccesos[$paginaNombre]);
            
            $result[] = [
                'username' => $username,
                'pagina' => $paginaNombre,
                'descripcion_pagina' => $pagina['descripcion_pagina'],
                'tiene_acceso' => $tieneAcceso,
                'editar' => $tieneAcceso ? $mapaAccesos[$paginaNombre]['editar'] : false,
                'eliminar' => $tieneAcceso ? $mapaAccesos[$paginaNombre]['eliminar'] : false,
                'adicionar' => $tieneAcceso ? $mapaAccesos[$paginaNombre]['adicionar'] : false,
                'seguimiento' => $tieneAcceso ? $mapaAccesos[$paginaNombre]['seguimiento'] : false
            ];
        }
        
        return $result;
    }
    
    public function asignarAccesos($username, $permisos) {
        try {
            // Iniciar transacción
            $this->db->getConnection()->beginTransaction();
            
            // Eliminar accesos existentes
            $deleteSql = "DELETE FROM acceso_paginas WHERE username = :username";
            $this->db->query($deleteSql, ['username' => $username]);
            
            // Insertar nuevos accesos
            $insertSql = "INSERT INTO acceso_paginas (username, pagina, editar, eliminar, adicionar, seguimiento) 
                         VALUES (:username, :pagina, :editar, :eliminar, :adicionar, :seguimiento)";
            
            foreach ($permisos as $permiso) {
                $this->db->query($insertSql, [
                    'username' => $username,
                    'pagina' => $permiso['pagina'],
                    'editar' => $permiso['editar'] ? 'Si' : 'No',
                    'eliminar' => $permiso['eliminar'] ? 'Si' : 'No',
                    'adicionar' => $permiso['adicionar'] ? 'Si' : 'No',
                    'seguimiento' => $permiso['seguimiento'] ? 'Si' : 'No'
                ]);
            }
            
            // Confirmar transacción
            $this->db->getConnection()->commit();
            
            return ['success' => true, 'message' => 'Accesos actualizados correctamente'];
            
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            return ['success' => false, 'message' => 'Error al asignar accesos: ' . $e->getMessage()];
        }
    }
}