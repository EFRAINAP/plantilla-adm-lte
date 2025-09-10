# Sistema Administrativo - PHP + AdminLTE + Bootstrap 5

Un sistema administrativo moderno y completamente funcional construido con PHP 8.2, AdminLTE 3, Bootstrap 5 y SweetAlert2.

## 🚀 Características

- ✅ **Layout Responsivo**: AdminLTE 3 con Bootstrap 5
- ✅ **Sidebar Dinámico**: Menús jerárquicos con persistencia en localStorage
- ✅ **Modularización**: Estructura modular y reutilizable
- ✅ **Router Simple**: Sistema de rutas personalizado
- ✅ **SweetAlert2**: Notificaciones y alertas elegantes
- ✅ **Gestión de Estado**: Sidebar colapsible persistente
- ✅ **Variables de Entorno**: Configuración con archivos .env
- ✅ **Autoloading**: PSR-4 con Composer

## 📁 Estructura del Proyecto

```
sistema-new/
├── app/                    # Lógica principal (Controladores, Modelos)
├── public/                 # Carpeta pública
│   ├── assets/            # CSS y JS personalizados
│   │   ├── css/
│   │   └── js/
│   └── index.php          # Punto de entrada
├── resources/             # Vistas y layouts
│   ├── layouts/           # Layouts principales
│   ├── modules/           # Módulos de la aplicación
│   │   └── usuarios/      # Módulo de usuarios
│   └── partials/          # Componentes parciales
├── routes/                # Definición de rutas
├── vendor/                # Dependencias de Composer
├── .env                   # Variables de entorno
├── composer.json          # Configuración de Composer
└── README.md
```

## 🛠️ Tecnologías Utilizadas

- **PHP 8.2+**
- **AdminLTE 3.2** (incluye Bootstrap 5)
- **SweetAlert2** (notificaciones)
- **jQuery** (viene con AdminLTE)
- **Font Awesome** (iconos)
- **Composer** (autoloading y dependencias)

## 📋 Instalación

1. **Clonar o descargar el proyecto**
   ```bash
   cd c:/xampp/htdocs/
   ```

2. **Instalar dependencias**
   ```bash
   composer install
   ```

3. **Configurar variables de entorno**
   - Editar el archivo `.env` con tu configuración
   - Configurar base de datos, correo, etc.

4. **Configurar servidor web**
   - Asegúrate de que Apache esté ejecutándose
   - Accede a: `http://localhost/sistema-new/public/`

## 🎯 Uso

### Dashboard Principal
- Accede a `/dashboard` para ver el panel principal
- Estadísticas y métricas del sistema

### Gestión de Usuarios
- **Listado**: `/usuarios` - Lista todos los usuarios
- **Roles**: `/usuarios/roles` - Gestión de roles y permisos

### Características del Sidebar
- **Persistencia**: El estado se guarda en localStorage
- **Jerárquico**: Soporte para múltiples niveles
- **Dinámico**: Fácil configuración desde `sidebar-menu.php`

## 🔧 Personalización

### Agregar Nuevos Módulos

1. **Crear carpeta del módulo**
   ```
   resources/modules/nuevo-modulo/
   ```

2. **Crear vistas del módulo**
   ```php
   // resources/modules/nuevo-modulo/index.php
   <?php
   $title = "Nuevo Módulo";
   ob_start();
   ?>
   <div class="container-fluid">
       <!-- Tu contenido aquí -->
   </div>
   <?php
   $content = ob_get_clean();
   include __DIR__ . '/../../layouts/main.php';
   ?>
   ```

3. **Agregar ruta en index.php**
   ```php
   $router->get('/nuevo-modulo', function() {
       renderView('nuevo-modulo/index');
   });
   ```

4. **Agregar al menú sidebar**
   ```php
   // En resources/partials/sidebar-menu.php
   $menu[] = [
       'title' => 'Nuevo Módulo',
       'icon' => 'fas fa-star',
       'route' => '/nuevo-modulo'
   ];
   ```

### Personalizar Estilos

- **CSS personalizado**: `public/assets/css/custom.css`
- **JavaScript personalizado**: `public/assets/js/custom.js`

## 🔒 Seguridad

El sistema incluye:
- Validación y sanitización de datos
- Protección XSS con `htmlspecialchars()`
- Gestión segura de sesiones
- Variables de entorno para configuración sensible

## 📚 Funciones JavaScript Disponibles

```javascript
// Notificaciones
showToast('success', 'Mensaje de éxito');
showToast('error', 'Mensaje de error');

// Diálogos de confirmación
showConfirmDialog('Título', 'Mensaje');

// Loading overlay
showLoading('Procesando...');
hideLoading();

// Envío de formularios AJAX
submitFormAjax(formElement, options);

// Confirmación de eliminación
confirmDelete(url, itemName, onSuccess);
```

## 🚦 Rutas Disponibles

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/dashboard` | Dashboard principal |
| GET | `/usuarios` | Lista de usuarios |
| GET | `/usuarios/roles` | Gestión de roles |
| POST | `/usuarios/create` | Crear usuario |
| POST | `/usuarios/roles/create` | Crear rol |
| DELETE | `/usuarios/{id}/delete` | Eliminar usuario |

## 🎨 Componentes Incluidos

- **Sidebar dinámico** con múltiples niveles
- **Tablas responsivas** con DataTables
- **Modales** para formularios
- **Alertas** con SweetAlert2
- **Tarjetas** de estadísticas
- **Formularios** con validación
- **Botones** con confirmación

## 🔄 Estado del Proyecto

✅ **Completado:**
- Estructura base del proyecto
- Layout principal con AdminLTE
- Sidebar dinámico y persistente
- Sistema de rutas básico
- Módulo de usuarios de ejemplo
- CSS y JavaScript personalizados
- Configuración de entorno

## 🤝 Contribuir

1. Fork el proyecto
2. Crea una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Abre un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT.

---

¡Sistema listo para usar! 🎉
