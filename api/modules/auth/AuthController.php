<?php
/**
 * Controller de Autenticación
 * Maneja login, logout y renovación de tokens
 */

namespace Api\Modules\Auth;

use Api\Core\BaseController;
use Api\Modules\Usuarios\Usuario;
use Api\Config\Config;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Api\Middleware\AuthenticationMiddleware;

class AuthController extends BaseController {
    
    public function login() {
        $data = $this->getJsonInput();
        
        // Validación básica
        if (empty($data['username']) || empty($data['password'])) {
            return $this->errorResponse('Username y password son requeridos', 400);
        }
        
        try {
            // Buscar usuario
            $usuario = new Usuario();
            $user = $usuario->findByUsername($data['username']);
            
            if (!$user) {
                return $this->errorResponse('Credenciales inválidas', 401);
            }
            
            // Verificar password
            if (!password_verify($data['password'], $user['password'])) {
                return $this->errorResponse('Credenciales inválidas', 401);
            }
            
            // Verificar si el usuario está activo
            if (isset($user['estado_user']) && $user['estado_user'] != 1) {
                return $this->errorResponse('Usuario desactivado', 401);
            }
            
            // Generar token JWT
            $authMiddleware = new AuthenticationMiddleware();
            $token = $authMiddleware->generateToken($user);
            
            // Actualizar último acceso
            $usuario->updateLastAccess($user['id']);
            
            // Remover password del response
            unset($user['password']);
            
            return $this->successResponse([
                'user' => $user,
                'token' => $token,
                'expires_in' => 7200 // 2 horas
            ], 'Inicio de sesión exitoso');
            
        } catch (Exception $e) {
            $this->logError('Error en login', [
                'username' => $data['username'],
                'error' => $e->getMessage()
            ]);
            
            return $this->errorResponse('Error interno del servidor', 500);
        }
    }
    
    public function logout() {
        // Para JWT no hay mucho que hacer server-side
        // El cliente debe eliminar el token
        
        return $this->successResponse([], 'Sesión cerrada exitosamente');
    }
    
    public function refresh() {
        // El middleware ya maneja la renovación automática
        // Este endpoint puede forzar una renovación
        
        $user = $this->getAuthUser();
        
        if (!$user) {
            return $this->errorResponse('No autenticado', 401);
        }
        
        try {
            $authMiddleware = new AuthenticationMiddleware();
            $newToken = $authMiddleware->generateToken($user);
            
            return $this->successResponse([
                'token' => $newToken,
                'expires_in' => 7200
            ], 'Token renovado exitosamente');
            
        } catch (Exception $e) {
            return $this->errorResponse('Error renovando token', 500);
        }
    }
    
    public function profile() {
        $user = $this->getAuthUser();
        
        if (!$user) {
            return $this->errorResponse('No autenticado', 401);
        }
        
        // Remover información sensible
        unset($user['password']);
        
        return $this->successResponse($user, 'Perfil del usuario');
    }
}