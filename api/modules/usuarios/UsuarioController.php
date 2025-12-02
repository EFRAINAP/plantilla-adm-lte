<?php
/**
 * UsuarioController - Gestión de usuarios API
 */

namespace Api\Modules\Usuarios;

use Api\Core\BaseController;
use Api\Modules\Usuarios\Usuario;
use Exception;
use PDOException;

class UsuarioController extends BaseController {
    
    private $usuarioModel;
    
    public function __construct() {
        parent::__construct();
        $this->usuarioModel = new Usuario();
    }

    public function index() {
        try {            
            $resultado = $this->usuarioModel->findAllUser();
            $this->response->success($resultado);
        } catch (Exception $e) {
            $this->logError('Error en usuarios/index', ['error' => $e->getMessage()]);
            $this->response->error('Error al obtener usuarios', 500);
        }
    }
    
    public function show() {
        try {
            $id = $this->request->getParam('id');
            
            if (!$id) {
                $this->response->error('ID de usuario requerido', 400);
                return;
            }
            
            $resultado = $this->usuarioModel->show($id);
            
            if (!$resultado) {
                $this->response->notFound('Usuario no encontrado');
                return;
            }
            
            $this->response->success($resultado);
            
        } catch (Exception $e) {
            $this->logError('Error en usuarios/show', ['id' => $id, 'error' => $e->getMessage()]);
            $this->response->error('Error al obtener usuario', 500);
        }
    }
    
    public function store() {
        try {
            // Los datos ya vienen validados por ValidationMiddleware
            $data = $this->request->getValidatedData() ?: $this->request->getBody();
            
            $resultado = $this->usuarioModel->createUsuario($data);
            
            $this->response->success($resultado, 'Usuario creado exitosamente', 201);
            
        } catch (Exception $e) {
            $this->logError('Error en usuarios/store', ['data' => $data ?? [], 'error' => $e->getMessage()]);
            $this->response->error('Error al crear usuario', 500);
        }
    }
    
    public function update() {
        $id = $this->request->getParam('id');
        try {
            // Los datos ya vienen validados por ValidationMiddleware
            $data = $this->request->getValidatedData() ?: $this->request->getBody();
            
            // Verificar que el usuario existe
            $usuario = $this->usuarioModel->findById($id);
            if (!$usuario) {
                $this->response->notFound('Usuario no encontrado');
            }
            
            $resultado = $this->usuarioModel->update($id, $data);
            
            $this->response->success($resultado, 'Usuario actualizado exitosamente');
            
        } catch (Exception $e) {
            $this->logError('Error en usuarios/update', ['id' => $id, 'data' => $data ?? [], 'error' => $e->getMessage()]);
            $this->response->error('Error al actualizar usuario', 500);
        }
    }
    
    public function delete() {
        $id = $this->request->getParam('id');
        try {
            // Verificar que el usuario existe
            $usuario = $this->usuarioModel->findById($id);
            if (!$usuario) {
                $this->response->notFound('Usuario no encontrado');
            }
            
            // Soft delete - cambiar estado en lugar de eliminar
            $resultado = $this->usuarioModel->update($id, ['estado_user' => 0]);
            
            $this->response->success($resultado, 'Usuario desactivado exitosamente');
            
        } catch (Exception $e) {
            $this->logError('Error en usuarios/delete', ['id' => $id, 'error' => $e->getMessage()]);
            $this->response->error('Error al desactivar usuario', 500);
        }
    }
    
    public function search() {
        try {
            $query = $this->request->getQuery('q', '');
            $limit = $this->request->getQuery('limit', 10);
            
            if (empty($query)) {
                $this->response->error('Parámetro de búsqueda requerido', 400);
            }
            
            $usuarios = $this->usuarioModel->search($query, $limit);
            
            $this->response->success($usuarios);
            
        } catch (Exception $e) {
            $this->logError('Error en usuarios/search', ['query' => $query ?? '', 'error' => $e->getMessage()]);
            $this->response->error('Error en la búsqueda', 500);
        }
    }
    
    public function activos() {
        try {
            $usuarios = $this->usuarioModel->findActivos();
            $this->response->success($usuarios);
            
        } catch (Exception $e) {
            $this->logError('Error en usuarios/activos', ['error' => $e->getMessage()]);
            $this->response->error('Error al obtener usuarios activos', 500);
        }
    }
    
    public function cambiarPassword() {
        $id = $this->request->getParam('id');
        
        try {
            $data = $this->request->getBody();
            
            if (!$id) {
                $this->response->error('ID de usuario requerido', 400);
                return;
            }
            
            if (empty($data['password'])) {
                $this->response->error('Nueva contraseña requerida', 400);
                return;
            }
            
            // Verificar que el usuario existe
            $usuario = $this->usuarioModel->findById($id);
            if (!$usuario) {
                $this->response->notFound('Usuario no encontrado');
                return;
            }
            
            $resultado = $this->usuarioModel->cambiarPassword($id, $data['password']);
            
            $this->response->success($resultado, 'Contraseña actualizada correctamente');
            
        } catch (Exception $e) {
            $this->logError('Error en usuarios/cambiarPassword', ['id' => $id, 'error' => $e->getMessage()]);
            $this->response->error('Error al cambiar contraseña', 500);
        }
    }
    
    // Gestión de perfiles
    public function perfiles() {
        $id = $this->request->getParam('id');
        
        try {
            if (!$id) {
                $this->response->error('ID de usuario requerido', 400);
                return;
            }
            
            // Obtener usuario por ID y luego sus perfiles por username
            $usuario = $this->usuarioModel->findById($id);
            if (!$usuario) {
                $this->response->notFound('Usuario no encontrado');
                return;
            }
            
            $perfiles = $this->usuarioModel->getPerfilesUsuario($usuario['username']);
            
            $this->response->success([
                'usuario' => $usuario['username'],
                'perfiles' => $perfiles,
                'total' => count($perfiles)
            ]);
            
        } catch (Exception $e) {
            $this->logError('Error en usuarios/perfiles', ['id' => $id, 'error' => $e->getMessage()]);
            $this->response->error('Error al obtener perfiles', 500);
        }
    }
    
    public function asignarPerfil() {
        $id = $this->request->getParam('id');
        
        try {
            $data = $this->request->getBody();
            
            if (!$id || empty($data['perfil'])) {
                $this->response->error('ID de usuario y perfil son requeridos', 400);
                return;
            }
            
            // Obtener usuario por ID
            $usuario = $this->usuarioModel->findById($id);
            if (!$usuario) {
                $this->response->notFound('Usuario no encontrado');
                return;
            }
            
            $resultado = $this->usuarioModel->asignarPerfil($usuario['username'], $data['perfil']);
            
            if ($resultado['success']) {
                $this->response->success($resultado, 'Perfil asignado correctamente');
            } else {
                $this->response->error($resultado['message'], 400);
            }
            
        } catch (Exception $e) {
            $this->logError('Error en usuarios/asignarPerfil', ['id' => $id, 'error' => $e->getMessage()]);
            $this->response->error('Error al asignar perfil', 500);
        }
    }
    
    public function eliminarPerfil() {
        $id = $this->request->getParam('id');
        
        try {
            $data = $this->request->getBody();
            
            if (!$id || empty($data['perfil'])) {
                $this->response->error('ID de usuario y perfil son requeridos', 400);
                return;
            }
            
            // Obtener usuario por ID
            $usuario = $this->usuarioModel->findById($id);
            if (!$usuario) {
                $this->response->notFound('Usuario no encontrado');
                return;
            }
            
            $resultado = $this->usuarioModel->eliminarPerfil($usuario['username'], $data['perfil']);
            
            if ($resultado['success']) {
                $this->response->success($resultado, 'Perfil eliminado correctamente');
            } else {
                $this->response->error($resultado['message'], 400);
            }
            
        } catch (Exception $e) {
            $this->logError('Error en usuarios/eliminarPerfil', ['id' => $id, 'error' => $e->getMessage()]);
            $this->response->error('Error al eliminar perfil', 500);
        }
    }
    
    // Gestión de accesos a páginas
    public function accesos() {
        $id = $this->request->getParam('id');
        
        try {
            if (!$id) {
                $this->response->error('ID de usuario requerido', 400);
                return;
            }
            
            // Obtener usuario por ID
            $usuario = $this->usuarioModel->findById($id);
            if (!$usuario) {
                $this->response->notFound('Usuario no encontrado');
                return;
            }
            
            $accesos = $this->usuarioModel->getAccesosUsuario($usuario['username']);
            
            $this->response->success([
                'usuario' => $usuario['username'],
                'accesos' => $accesos,
                'total' => count($accesos)
            ]);
            
        } catch (Exception $e) {
            $this->logError('Error en usuarios/accesos', ['id' => $id, 'error' => $e->getMessage()]);
            $this->response->error('Error al obtener accesos', 500);
        }
    }
    
    public function asignarAccesos() {
        $id = $this->request->getParam('id');
        
        try {
            $data = $this->request->getBody();
            
            if (!$id || empty($data['permisos'])) {
                $this->response->error('ID de usuario y permisos son requeridos', 400);
                return;
            }
            
            // Obtener usuario por ID
            $usuario = $this->usuarioModel->findById($id);
            if (!$usuario) {
                $this->response->notFound('Usuario no encontrado');
                return;
            }
            
            $resultado = $this->usuarioModel->asignarAccesos($usuario['username'], $data['permisos']);
            
            if ($resultado['success']) {
                $this->response->success($resultado, 'Accesos actualizados correctamente');
            } else {
                $this->response->error($resultado['message'], 400);
            }
            
        } catch (Exception $e) {
            $this->logError('Error en usuarios/asignarAccesos', ['id' => $id, 'error' => $e->getMessage()]);
            $this->response->error('Error al asignar accesos', 500);
        }
    }
    
    public function perfilesDisponibles() {
        try {
            $perfiles = $this->usuarioModel->getPerfilesDisponibles();
            
            $this->response->success([
                'perfiles' => $perfiles,
                'total' => count($perfiles)
            ]);
            
        } catch (Exception $e) {
            $this->logError('Error en usuarios/perfilesDisponibles', ['error' => $e->getMessage()]);
            $this->response->error('Error al obtener perfiles disponibles', 500);
        }
    }
}