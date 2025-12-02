<?php
/**
 * Interface para todos los middleware del sistema
 */

namespace Api\Middleware;

use Api\Core\Request;
use Api\Core\Response;

interface MiddlewareInterface {
    /**
     * Manejar la petición HTTP a través del middleware
     * 
     * @param Request $request Objeto de petición HTTP
     * @param callable $next Siguiente middleware/controlador en la cadena
     * @return Response Respuesta HTTP
     */
    public function handle(Request $request, callable $next): Response;
}