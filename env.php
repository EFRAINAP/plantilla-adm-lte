# Configuración del Sistema
APP_NAME="Sistema Administrativo"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost/sistema-new

# Base de Datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_new
DB_USERNAME=root
DB_PASSWORD=

# Configuración de Sesiones
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Configuración de Logging
LOG_CHANNEL=single
LOG_LEVEL=debug

# Configuración de Correo
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@sistema.com
MAIL_FROM_NAME="${APP_NAME}"

# Configuración de Seguridad
APP_KEY=base64:your-32-character-secret-key-here
HASH_DRIVER=bcrypt

# Configuración de Timezone
APP_TIMEZONE=America/Peru

# Configuración de Uploads
MAX_UPLOAD_SIZE=2048
ALLOWED_EXTENSIONS=jpg,jpeg,png,gif,pdf,doc,docx

# Configuración de Cache
CACHE_DRIVER=file

# URLs y Rutas
ASSETS_URL=/assets
UPLOADS_URL=/uploads
