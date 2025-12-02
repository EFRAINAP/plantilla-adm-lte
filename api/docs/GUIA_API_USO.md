# 📚 Guía de Uso del API - Sistema T&M

## 🚀 **CONFIGURACIÓN INICIAL**

### **1. Requisitos**
- XAMPP/WAMP con PHP 8.0+
- MySQL/MariaDB
- mod_rewrite habilitado
- Extensiones: PDO, JSON, OpenSSL

### **2. Configuración .env**
```bash
# Copiar y configurar .env en la raíz del proyecto
APP_NAME="Sistema T&M"
APP_ENV=development  
APP_DEBUG=true
APP_URL=http://localhost/sistema-new

# Base de datos
DB_HOST=localhost
DB_DATABASE=tu_database
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password

# Timezone
APP_TIMEZONE=America/Lima
```

### **3. URL Base**
```
http://localhost/sistema-new/api
```

---

## 🎯 **ENDPOINTS DISPONIBLES**

### **🏥 Health Check**
```http
GET /api/health
```
**Respuesta:**
```json
{
  "success": true,
  "message": "API funcionando correctamente",
  "data": {
    "api_version": "1.0",
    "timestamp": "2025-11-18 13:24:41",
    "database": "OK",
    "php_version": "8.2.12",
    "memory_usage": "580KB"
  }
}
```

---

## 👥 **MÓDULO USUARIOS**

### **Listar Usuarios**
```http
GET /api/usuarios
```
**Parámetros de consulta:**
- `page` (int): Página (default: 1)
- `limit` (int): Registros por página (default: 10)

**Respuesta:**
```json
{
  "success": true,
  "message": "Operación exitosa",
  "data": [
    {
      "id": 34,
      "username": "GerenteIT",
      "name": "Admin Updated",
      "user_level": 0,
      "cargo": "Developer",
      "area": "IT",
      "estado_user": 1
    }
  ]
}
```

### **Ver Usuario Específico**
```http
GET /api/usuarios/{id}
```
**Ejemplo:**
```bash
curl -X GET http://localhost/sistema-new/api/api/usuarios/34
```

### **Crear Usuario**
```http
POST /api/usuarios
Content-Type: application/json

{
  "name": "Juan Pérez",
  "username": "jperez", 
  "password": "mi_password_seguro",
  "user_level": 0,
  "cargo": "Desarrollador",
  "area": "IT",
  "proceso": "TI"
}
```

**Campos requeridos:** `name`, `username`, `password`

### **Actualizar Usuario**
```http
PUT /api/usuarios/{id}
Content-Type: application/json

{
  "name": "Juan Pérez Actualizado",
  "cargo": "Senior Developer",
  "area": "IT"
}
```

### **Eliminar Usuario (Soft Delete)**
```http
DELETE /api/usuarios/{id}
```

### **Buscar Usuarios**
```http
GET /api/usuarios/search?q=admin&limit=5
```

### **Usuarios Activos**
```http
GET /api/usuarios/activos
```

---

## 📋 **MÓDULO TAREAS**

### **Listar Tareas**
```http
GET /api/tareas
```
**Parámetros:**
- `page`, `limit`: Paginación
- `estado`: Filtrar por estado
- `usuario_id`: Filtrar por usuario asignado

### **Ver Tarea Específica**
```http
GET /api/tareas/{id}
```

### **Crear Tarea**
```http
POST /api/tareas
Content-Type: application/json

{
  "titulo": "Revisar documentación",
  "descripcion": "Actualizar docs del sistema",
  "estado": "pendiente",
  "prioridad": "alta",
  "fecha_vencimiento": "2025-12-31",
  "usuario_asignado": 34
}
```

### **Actualizar Tarea**
```http
PUT /api/tareas/{id}
Content-Type: application/json

{
  "estado": "en_progreso",
  "descripcion": "Descripción actualizada"
}
```

### **Eliminar Tarea**
```http
DELETE /api/tareas/{id}
```

### **Tareas por Usuario**
```http
GET /api/tareas/usuario/{userId}
```

### **Tareas Vencidas**
```http
GET /api/tareas/vencidas
```

### **Tareas Pendientes**
```http
GET /api/tareas/pendientes
```

### **Estadísticas de Tareas**
```http
GET /api/tareas/estadisticas
```
**Respuesta:**
```json
{
  "success": true,
  "data": {
    "total": 150,
    "por_estado": {
      "pendiente": 45,
      "en_progreso": 30,
      "completada": 70,
      "cancelada": 5
    },
    "por_prioridad": {
      "baja": 20,
      "media": 80,
      "alta": 40,
      "urgente": 10
    },
    "vencidas": 8,
    "proximas_vencer": 15
  }
}
```

### **Completar Tarea**
```http
PUT /api/tareas/{id}/completar
```

---

## 📄 **MÓDULO ISO DOCUMENTOS**

### **Listar Documentos**
```http
GET /api/iso/documentos
```

### **Ver Documento Específico**
```http
GET /api/iso/documentos/{id}
```

### **Crear Documento**
```http
POST /api/iso/documentos
Content-Type: application/json

{
  "codigo": "PRC-002",
  "titulo": "Procedimiento de Control",
  "descripcion": "Descripción del procedimiento",
  "tipo_documento": "procedimiento",
  "estado": "borrador",
  "version": "1.0",
  "fecha_emision": "2025-11-18",
  "responsable": "Juan Pérez"
}
```

### **Actualizar Documento**
```http
PUT /api/iso/documentos/{id}
Content-Type: application/json

{
  "estado": "aprobado",
  "version": "2.0"
}
```

### **Eliminar Documento**
```http
DELETE /api/iso/documentos/{id}
```

### **Tipos de Documento**
```http
GET /api/iso/tipos
```

### **Estados Disponibles**
```http
GET /api/iso/estados
```

### **Reportes - Próximos a Vencer**
```http
GET /api/iso/reportes/vencimientos?dias=30
```

### **Estadísticas ISO**
```http
GET /api/iso/reportes/estadisticas
```

---

## 📝 **EJEMPLOS PRÁCTICOS**

### **Usando cURL**

#### Crear Usuario:
```bash
curl -X POST http://localhost/sistema-new/api/api/usuarios \\
  -H "Content-Type: application/json" \\
  -d '{
    "name": "María González",
    "username": "mgonzalez",
    "password": "password123",
    "cargo": "Analista",
    "area": "Calidad"
  }'
```

#### Listar Usuarios con Paginación:
```bash
curl -X GET "http://localhost/sistema-new/api/api/usuarios?page=2&limit=5"
```

#### Buscar Usuarios:
```bash
curl -X GET "http://localhost/sistema-new/api/api/usuarios/search?q=admin"
```

### **Usando PowerShell**

#### Health Check:
```powershell
Invoke-RestMethod -Uri "http://localhost/sistema-new/api/api/health" -Method GET
```

#### Crear Tarea:
```powershell
$tarea = @{
    titulo = "Nueva tarea desde PowerShell"
    descripcion = "Descripción de la tarea"
    usuario_asignado = 34
    prioridad = "media"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost/sistema-new/api/api/tareas" \\
  -Method POST \\
  -Body $tarea \\
  -ContentType "application/json"
```

### **Usando JavaScript/Fetch**

#### Listar Usuarios:
```javascript
fetch('http://localhost/sistema-new/api/api/usuarios')
  .then(response => response.json())
  .then(data => {
    console.log('Usuarios:', data.data);
  })
  .catch(error => {
    console.error('Error:', error);
  });
```

#### Crear Usuario:
```javascript
const nuevoUsuario = {
  name: "Carlos López",
  username: "clopez",
  password: "mi_password",
  cargo: "Supervisor",
  area: "Producción"
};

fetch('http://localhost/sistema-new/api/api/usuarios', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify(nuevoUsuario)
})
.then(response => response.json())
.then(data => {
  if (data.success) {
    console.log('Usuario creado:', data.data);
  } else {
    console.error('Error:', data.message);
  }
});
```

---

## 🔧 **FORMATO DE RESPUESTAS**

### **Respuesta Exitosa**
```json
{
  "success": true,
  "message": "Operación exitosa",
  "data": {
    // Datos solicitados
  },
  "timestamp": "2025-11-18 13:24:41"
}
```

### **Respuesta de Error**
```json
{
  "error": true,
  "message": "Descripción del error",
  "details": "Detalles adicionales (solo en modo debug)",
  "timestamp": "2025-11-18 13:24:41"
}
```

### **Códigos de Estado HTTP**
- `200` - OK (operación exitosa)
- `201` - Created (recurso creado)
- `400` - Bad Request (datos inválidos)
- `404` - Not Found (recurso no encontrado)
- `500` - Internal Server Error (error del servidor)

---

## ⚠️ **CONSIDERACIONES IMPORTANTES**

### **Seguridad**
- ⚠️ **Actualmente NO hay autenticación**
- ⚠️ **Todos los endpoints son públicos**
- ⚠️ **No hay rate limiting**
- 🔒 **Las contraseñas se almacenan hasheadas**

### **Validación**
- ✅ **Campos requeridos validados**
- ⚠️ **Validación de tipos básica**
- ⚠️ **Sin sanitización automática avanzada**

### **Performance**
- ✅ **Paginación implementada**
- ⚠️ **Sin caching implementado**
- ⚠️ **Sin compresión de respuestas**

### **CORS**
- ✅ **Headers CORS habilitados**
- ✅ **Preflight requests manejados**
- ⚠️ **Origen '*' (muy permisivo)**

---

## 🐛 **DEBUGGING**

### **Habilitar Debug Mode**
En `.env`:
```
APP_DEBUG=true
```

### **Logs de Errores**
Los errores se guardan en:
```
/logs/api_errors_YYYY-MM-DD.log
```

### **Debug de Router**
Con debug habilitado, el router logea:
- Método y URI procesados
- Rutas disponibles
- Controladores encontrados

### **Test de Conectividad**
```bash
# Verificar que Apache esté corriendo
curl -I http://localhost/sistema-new/api/api/health

# Verificar .htaccess y mod_rewrite
curl http://localhost/sistema-new/api/test.php
```

---

## 🎯 **PRÓXIMOS PASOS RECOMENDADOS**

1. **Implementar autenticación JWT**
2. **Añadir middleware de seguridad**
3. **Crear validación robusta de datos**
4. **Implementar rate limiting**
5. **Añadir caching para performance**
6. **Crear documentación automática (Swagger)**

**📋 Esta API está lista para desarrollo y testing, pero necesita seguridad antes de producción.**