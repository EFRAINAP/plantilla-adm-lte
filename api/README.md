# Backend API - Sistema T&M

Api MVC modular para el sistema T&M con APIs REST. Arquitectura escalable y mantenible.

## Estructura Modular del Proyecto

```
api/
├── index.php                 # Punto de entrada principal
├── .htaccess                 # Configuración Apache/Nginx
├── config/
│   └── Config.php            # Configuración desde .env
├── core/                     # Núcleo del framework
│   ├── Router.php            # Manejo de rutas
│   ├── Request.php           # Manejo de peticiones HTTP
│   ├── Response.php          # Manejo de respuestas HTTP
│   ├── Database.php          # Conexión y operaciones DB
│   └── BaseController.php    # Controlador base
├── modules/                  # Módulos organizados por funcionalidad
│   ├── iso/
│   │   ├── routes.php        # Rutas del módulo ISO
│   │   ├── ISOController.php # Controlador ISO
│   │   ├── ISOModel.php      # Modelo de documentos ISO
│   │   └── ISOValidator.php  # Validaciones ISO
│   ├── usuarios/             # Módulo de usuarios
│   ├── tareas/               # Módulo de tareas
│   ├── dosier/               # Módulo dosier
│   └── ...                   # Otros módulos
├── controllers/              # Controladores legacy (compatibilidad)
├── models/                   # Modelos base
├── utils/                    # Utilidades globales
│   ├── helpers.php           # Funciones helper
│   └── Sanitizer.php         # Sanitización y validación
└── README.md                 # Este archivo
```

## Configuración

1. **Variables de entorno**: El sistema lee la configuración del archivo `.env` en la raíz del proyecto
2. **Base de datos**: Configurada automáticamente desde las variables `DB_*` del .env
3. **Permisos**: El sistema crea automáticamente los directorios `/logs` y `/uploads` si no existen
4. **Servidor web**: Incluye `.htaccess` para Apache con reglas de rewrite configuradas

## Rutas de la API

### Health Check
- `GET /api/health` - Estado del API

### Módulo ISO
- `GET /api/iso/documentos` - Listar documentos ISO (con filtros y paginación)
- `GET /api/iso/documentos/{id}` - Obtener documento específico
- `POST /api/iso/documentos` - Crear nuevo documento
- `PUT /api/iso/documentos/{id}` - Actualizar documento
- `DELETE /api/iso/documentos/{id}` - Eliminar documento (soft delete)
- `GET /api/iso/tipos` - Obtener tipos de documento
- `GET /api/iso/estados` - Obtener estados disponibles
- `GET /api/iso/reportes/vencimientos` - Documentos próximos a vencer
- `GET /api/iso/reportes/estadisticas` - Estadísticas del módulo

### Usuarios (Legacy - Compatibilidad)
- `GET /api/usuarios` - Listar usuarios (con paginación)
- `GET /api/usuarios/{id}` - Obtener usuario específico
- `POST /api/usuarios` - Crear nuevo usuario
- `PUT /api/usuarios/{id}` - Actualizar usuario
- `DELETE /api/usuarios/{id}` - Desactivar usuario

### Tareas (Legacy - Compatibilidad)
- `GET /api/tareas` - Listar tareas (con filtros)
- `GET /api/tareas/{id}` - Obtener tarea específica
- `POST /api/tareas` - Crear nueva tarea
- `PUT /api/tareas/{id}` - Actualizar tarea
- `DELETE /api/tareas/{id}` - Eliminar tarea

## Uso

### Crear Documento ISO
```bash
POST /api/iso/documentos
Content-Type: application/json

{
    "codigo": "ISO-001",
    "titulo": "Procedimiento de Calidad",
    "descripcion": "Procedimiento para control de calidad",
    "tipo_documento": "procedimiento",
    "version": "1.0",
    "fecha_emision": "2025-11-17",
    "fecha_vencimiento": "2026-11-17",
    "responsable": "Juan Pérez"
}
```

### Listar Documentos ISO con Filtros
```bash
GET /api/iso/documentos?tipo=procedimiento&estado=activo&page=1&limit=10&search=calidad
```

### Reporte de Vencimientos
```bash
GET /api/iso/reportes/vencimientos?dias=30
```

### Crear Usuario (Legacy)
```bash
POST /api/usuarios
Content-Type: application/json

{
    "nombre": "Juan Pérez",
    "email": "juan@ejemplo.com",
    "password": "mi_password",
    "perfil": "admin"
}
```

## Respuestas

Todas las respuestas son en formato JSON:

### Éxito
```json
{
    "success": true,
    "message": "Operación exitosa",
    "data": {...}
}
```

### Error
```json
{
    "error": true,
    "message": "Descripción del error",
    "details": "Detalles adicionales (opcional)"
}
```

## Características

### Arquitectura
- ✅ **Estructura Modular** - Cada módulo es independiente y escalable
- ✅ **MVC Pattern** - Separación clara de responsabilidades
- ✅ **REST API** - Endpoints estándar REST
- ✅ **Autoloader Inteligente** - Carga automática de clases desde módulos

### Funcionalidades
- ✅ **CORS Ready** - Configurado para frontend separado
- ✅ **Paginación** - Listados con paginación automática
- ✅ **Validación Robusta** - Validadores específicos por módulo
- ✅ **Sanitización** - Limpieza automática de datos de entrada
- ✅ **Logging** - Registro detallado de errores
- ✅ **Response Standardized** - Respuestas JSON estandarizadas

### Seguridad
- ✅ **SQL Injection Protection** - Prepared statements
- ✅ **XSS Prevention** - Sanitización de datos
- ✅ **Input Validation** - Validación estricta de entrada
- ✅ **Error Handling** - Manejo seguro de errores

### Configuración
- ✅ **.env Support** - Configuración desde variables de entorno
- ✅ **Database Abstraction** - Capa de abstracción de BD
- ✅ **Health Check** - Monitoreo del estado del API
- ✅ **Debug Mode** - Información detallada en desarrollo

## Próximos Pasos

1. **Completar módulos**: Dosier, Biblioteca, Capacitaciones, Consumibles
2. **Autenticación JWT**: Implementar sistema de tokens
3. **Middleware de autorización**: Control de permisos por módulo
4. **Cache**: Implementar cache para consultas frecuentes
5. **Tests**: Agregar tests unitarios y de integración
6. **Documentación API**: Swagger/OpenAPI documentation