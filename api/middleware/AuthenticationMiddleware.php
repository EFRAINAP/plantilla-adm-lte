<?php
/**
 * Middleware de Autenticación JWT
 * Valida tokens JWT y carga información del usuario
 */

namespace Api\Middleware;

use Api\Middleware\MiddlewareInterface;
use Api\Core\Request;
use Api\Core\Response;
use Api\Config\Config;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Api\Modules\Usuarios\Usuario;

class AuthenticationMiddleware implements MiddlewareInterface {
    private $config;
    private $excludePaths;
    
    public function __construct() {
        $middlewareConfig = require __DIR__ . '/../config/middleware.php';
        
        // Configuración completamente desde .env
        $this->config = [
            'jwt_secret' => Config::getJwtSecret(),
            'jwt_expire' => Config::getJwtExpire(),
            'refresh_threshold' => Config::getJwtRefreshThreshold(),
            'header_name' => $middlewareConfig['authentication']['header_name'],
            'cookie_name' => $middlewareConfig['authentication']['cookie_name']
        ];
        
        $this->excludePaths = $middlewareConfig['authentication']['exclude_paths'];
    }
    
    public function handle(Request $request, callable $next): Response {
        $path = $request->getPath();
        
        // Verificar si la ruta está excluida de autenticación
        if ($this->isExcludedPath($path)) {
            return $next($request);
        }
        
        // Extraer token
        $token = $this->extractToken($request);
        
        if (!$token) {
            return new Response([
                'error' => true,
                'message' => 'Token de autenticación requerido'
            ], 401);
        }
        
        // Validar token JWT
        $payload = $this->validateToken($token);
        
        if (!$payload) {
            return new Response([
                'error' => true,
                'message' => 'Token inválido o expirado'
            ], 401);
        }
        
        // Cargar usuario y añadirlo al request
        $user = $this->loadUser($payload['user_id']);
        
        if (!$user) {
            return new Response([
                'error' => true,
                'message' => 'Usuario no encontrado'
            ], 401);
        }
        
        $request->setUser($user);
        
        // Verificar si el token necesita renovación
        if ($this->shouldRefreshToken($payload)) {
            $newToken = $this->generateToken($user);
            $response = $next($request);
            $response->addHeader('X-New-Token', $newToken);
            return $response;
        }
        
        return $next($request);
    }
    
    private function isExcludedPath($path) {
        foreach ($this->excludePaths as $excludedPath) {
            if (strpos($path, $excludedPath) === 0) {
                return true;
            }
        }
        return false;
    }
    
    private function extractToken(Request $request) {
        // Intentar obtener de Authorization header
        $authHeader = $request->getHeader('Authorization');
        if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
            return substr($authHeader, 7);
        }
        
        // Intentar obtener de cookie
        if (isset($_COOKIE[$this->config['cookie_name']])) {
            return $_COOKIE[$this->config['cookie_name']];
        }
        
        return null;
    }
    
    private function validateToken($token) {
        try {
            $parts = explode('.', $token);
            
            if (count($parts) !== 3) {
                return false;
            }
            
            list($header, $payload, $signature) = $parts;
            
            // Verificar signature
            $expectedSignature = $this->generateSignature($header . '.' . $payload);
            
            if (!hash_equals($expectedSignature, $signature)) {
                return false;
            }
            
            // Decodificar payload
            $decodedPayload = json_decode(base64_decode($payload), true);
            
            // Verificar expiración
            if (isset($decodedPayload['exp']) && $decodedPayload['exp'] < time()) {
                return false;
            }
            
            return $decodedPayload;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function generateSignature($data) {
        return base64_encode(hash_hmac('sha256', $data, $this->config['jwt_secret'], true));
    }
    
    private function loadUser($userId) {
        try {
            $usuario = new Usuario();
            return $usuario->findById($userId);
        } catch (Exception $e) {
            return null;
        }
    }
    
    private function shouldRefreshToken($payload) {
        $timeUntilExpiry = $payload['exp'] - time();
        return $timeUntilExpiry < $this->config['refresh_threshold'];
    }
    
    public function generateToken($user) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        $payload = json_encode([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'user_level' => $user['user_level'],
            'iat' => time(),
            'exp' => time() + $this->config['jwt_expire']
        ]);
        
        $encodedHeader = base64_encode($header);
        $encodedPayload = base64_encode($payload);
        
        $signature = $this->generateSignature($encodedHeader . '.' . $encodedPayload);
        
        return $encodedHeader . '.' . $encodedPayload . '.' . $signature;
    }
}