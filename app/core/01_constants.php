<?php

// Definir constantes de configuración en base a las variables de entorno
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USERNAME'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASSWORD'] ?? '');
define('DB_NAME', $_ENV['DB_DATABASE'] ?? 'iso');

// Configuración de la aplicación
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Sistema Administrativo');
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_DEBUG', ($_ENV['APP_DEBUG'] ?? 'false') === 'true');
define('APP_TIMEZONE', $_ENV['APP_TIMEZONE'] ?? 'America/Lima');

// Configuración de archivos
define('MAX_UPLOAD_SIZE', (int)($_ENV['MAX_UPLOAD_SIZE'] ?? 2048));
define('ALLOWED_EXTENSIONS', $_ENV['ALLOWED_EXTENSIONS'] ?? 'jpg,jpeg,png,gif,pdf,doc,docx');

?>
