🛠️ Instructivo de Inicio para Crear un Sistema con PHP + AdminLTE + Bootstrap 5
🔧 Tecnologías a Utilizar

PHP 8.2

Composer (para autoloading y dependencias)

AdminLTE (incluye Bootstrap 5 y SweetAlert2)

Bootstrap 5

SweetAlert2

JavaScript ES6

jQuery (viene con AdminLTE)

Layout modular y reutilizable

Sidebar dinámico y persistente

Sistema de navegación jerárquico (menú multinivel)

📁 Estructura de Carpetas Recomendadas
/project-root
│
├── app/                  # Lógica principal del sistema (Controladores, Modelos, etc)
│
├── public/               # Carpeta pública accesible (index.php, assets, etc.)
│   ├── assets/           # Archivos estáticos personalizados
│   └── index.php
│
├── resources/            # Vistas, layouts, componentes
│   ├── layouts/
│   ├── modules/
│   └── partials/
│
├── routes/               # Archivos con rutas
│
├── vendor/               # Autoload de Composer
│
├── .env                  # Variables de entorno
├── composer.json
└── README.md


✅ Pasos para Iniciar el Proyecto
1. Inicializar con Composer 
    crear un composer.json

2. Instalar AdminLTE

3. Crear el Layout Reutilizable
    Este layout debe tener:

    Header

    Footer

    Sidebar (incluye script JS para mantener estado)

    Contenedor de contenido (<?= $content ?>)

4. Sidebar Dinámico con Módulos Jerárquicos

$menu = [
  ['title' => 'Dashboard', 'icon' => 'fas fa-home', 'route' => '/dashboard'],
  ['separator' => 'Módulos'],
  [
    'title' => 'Usuarios',
    'icon' => 'fas fa-users',
    'children' => [
      ['title' => 'Listado', 'route' => '/usuarios'],
      ['title' => 'Roles', 'route' => '/usuarios/roles'],
    ]
  ],
];

Usa una función recursiva para renderizar menús con niveles 1, 2, 3...

Recomendación: Guarda el estado del sidebar con localStorage:
Archivo: public/assets/js/sidebar-state.js

document.addEventListener("DOMContentLoaded", function() {
  const body = document.body;
  const sidebarKey = "sidebar-collapsed";

  // Aplicar estado al cargar
  if (localStorage.getItem(sidebarKey) === "true") {
    body.classList.add("sidebar-collapse");
  }

  // Guardar estado cuando se hace toggle
  document.querySelector('[data-widget="pushmenu"]').addEventListener("click", () => {
    const isCollapsed = body.classList.contains("sidebar-collapse");
    localStorage.setItem(sidebarKey, isCollapsed);
  });
});

5. Modularización del Sistema

Cada módulo tiene su carpeta dentro de /resources/modules/

Cada archivo PHP representa una "vista" o funcionalidad.

Puedes usar un mini-router o incluir desde una clase View::render('nombre', $data).


6. Recomendaciones Adicionales
🔐 Seguridad

Sanitiza y valida todo input del usuario.

Usa htmlspecialchars() para evitar XSS.

Maneja sesiones de forma segura.
📦 Librerías útiles

vlucas/phpdotenv – para manejar archivos .env
monolog/monolog – para logging profesional


7. Ejemplo para Copilot: Creación de un Módulo

Crea archivo: resources/modules/usuarios/index.php

<?php
$title = "Usuarios";
ob_start();
?>
<div class="container-fluid">
  <h1>Listado de Usuarios</h1>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/main.php';