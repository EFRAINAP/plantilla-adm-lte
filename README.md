# 🚀 Plantilla AdminLTE + Tailwind CSS

Plantilla moderna PHP con AdminLTE para administración y Tailwind CSS para páginas públicas.

## ⚡ Setup Rápido

### 1. Clonar proyecto
```bash
git clone [url-repo] mi-proyecto
cd mi-proyecto
```

### 2. Instalar dependencias
```bash
# PHP
composer install

# Node.js
npm install
```

### 3. Configurar entorno
```bash
# Crear archivo de configuración
cp .env.example .env

# Ajustar rutas en .env:
ASSETS_URL="/mi-proyecto/public/assets"
```

### 4. Activar Tailwind Watch (Desarrollo)
```bash
npm run watch
# o
npx tailwindcss -i resources/css/app.css -o public/assets/tailwind.css --watch
```

### 5. Compilar para Producción
```bash
npm run build
# o  
npx tailwindcss -i resources/css/app.css -o public/assets/tailwind.css --minify
```

## 🎯 Estructura

- **Admin Panel:** `AdminLTE` → `/dashboard`, `/usuarios`, etc.
- **Páginas Públicas:** `Tailwind CSS` → `/landing/inicio`, `/landing/nosotros`
- **Layouts:** 
  - `resources/layouts/main.php` (Admin)
  - `resources/layouts/landing.php` (Público)

## 🔧 Desarrollo

**CSS en tiempo real:** El modo `--watch` detecta cambios automáticamente
**Agregar páginas:** Crear en `resources/modules/[carpeta]/`
**Nuevos estilos:** Solo escribe clases Tailwind y se compilan automáticamente

## 📦 Producción (cPanel)

1. Subir archivos (sin `node_modules`)
2. Configurar `.env` con rutas correctas
3. El CSS compilado ya está listo en `/public/assets/tailwind.css`

---

**🔥 Listo para usar:** Administración robusta + Landing moderno