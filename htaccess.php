RewriteEngine On

# Redirigir todo el tráfico a public/index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ public/index.php [QSA,L]

# Opcional: Redirigir desde el directorio raíz al public si se accede directamente
RewriteCond %{REQUEST_URI} !^/public/
RewriteRule ^index\.php$ public/index.php [R=301,L]
