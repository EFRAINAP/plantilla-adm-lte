# PDF Viewer Component - Sistema Unificado

Este componente proporciona una vista unificada para mostrar documentos PDF de diferentes categorías en el sistema TAMA.

## Archivos del Componente

- **UnifiedDocumentViewer.php**: Vista principal unificada que maneja todos los tipos de documentos
- **pdf-viewer.html**: Template HTML del visor
- **pdf-viewer.js**: Lógica JavaScript del visor
- **pdf-viewer.css**: Estilos del visor
- **../servir_archivo.php**: Endpoint seguro para servir archivos (ubicado en Components/)

## Uso del Sistema Unificado

### 1. Vista Unificada (Recomendado)

```php
// URL del visor unificado
$url = "../Components/PDFViewer/UnifiedDocumentViewer.php?categoria={categoria}&id={id}&tipo={tipo}";
```

**Parámetros:**
- `categoria`: Tipo de documento (iso, biblioteca, wps, pqr, of)
- `id`: ID del documento en la base de datos
- `tipo`: Tipo de archivo (documento, esquema)

**Categorías Soportadas:**

| Categoría | Tabla DB | Esquemas | Permiso Requerido | Color Header |
|-----------|----------|----------|-------------------|--------------|
| `iso` | documentos | No | 01_ListadoDocumentos.php | bg-info |
| `biblioteca` | biblioteca | No | 01_ListadoBiblioteca.php | bg-warning |
| `wps` | wps | Sí | 02_AdmWPS.php | bg-success |
| `pqr` | pqr | Sí | 02_AdmPQR.php | bg-primary |
| `of` | ordenes_fabricacion | Sí | 02_AdmOF.php | bg-danger |

### 2. Ejemplos de Uso

#### Para documentos WPS:
```php
$url = "../Components/PDFViewer/UnifiedDocumentViewer.php?categoria=wps&id=123&tipo=documento";
$url_esquema = "../Components/PDFViewer/UnifiedDocumentViewer.php?categoria=wps&id=123&tipo=esquema";
```

#### Para documentos ISO:
```php
$url = "../Components/PDFViewer/UnifiedDocumentViewer.php?categoria=iso&id=456&tipo=documento";
// Nota: ISO no maneja esquemas
```

#### Para biblioteca:
```php
$url = "../Components/PDFViewer/UnifiedDocumentViewer.php?categoria=biblioteca&id=789&tipo=documento";
// Nota: Biblioteca no maneja esquemas
```

### 3. Migración desde Archivos Separados

Los archivos de vista individuales pueden ser reemplazados por redirects simples:

```php
<?php
// Ejemplo: 03_MostrarPQR.php
if (isset($_GET['id']) && isset($_GET['tipo'])) {
    $id = (int)$_GET['id'];
    $tipo = $_GET['tipo'];
    
    $redirect_url = "../Components/PDFViewer/UnifiedDocumentViewer.php?categoria=pqr&id={$id}&tipo={$tipo}";
    header("Location: {$redirect_url}");
    exit;
} else {
    header("Location: 02_AdmPQR.php");
    exit;
}
?>
```

### 4. Agregar Nueva Categoría

Para agregar una nueva categoría de documentos:

1. **Actualizar UnifiedDocumentViewer.php:**
```php
$categorias_config['nueva_categoria'] = [
    'titulo' => 'Nuevo Tipo Documento',
    'permiso_page' => 'pagina_permisos.php',
    'redirect_page' => '../../ruta/listado.php',
    'tabla' => 'tabla_db',
    'campo_archivo' => 'nombre_archivo',
    'campo_esquema' => 'esquema', // o '' si no aplica
    'carpeta_constante' => 'carpeta_constante_db',
    'carpeta_esquema_constante' => 'carpeta_esquema_db', // o '' si no aplica
    'color_header' => 'bg-secondary',
    'icono' => 'bi-file-earmark'
];
```

2. **Actualizar servir_archivo.php:**
```php
// Agregar a $categorias_permitidas
$categorias_permitidas = ['wps', 'pqr', 'of', 'iso', 'biblioteca', 'nueva_categoria'];

// Agregar a $pages_permisos
$pages_permisos['nueva_categoria'] = 'pagina_permisos.php';

// Agregar case en el switch
case 'nueva_categoria':
    $carpeta_principal = find_table_field_only('constantes', 'nombre_constante', 'carpeta_nueva');
    $registro = find_table_field_only('tabla_nueva', 'id', (string)$id);
    // ... resto de la lógica
    break;
```

## Características de Seguridad

- **Autenticación**: Verificación de usuario logueado
- **Autorización**: Verificación de permisos por categoría
- **Path Security**: Uso de rutas absolutas del sistema de archivos
- **File Validation**: Verificación de existencia y tipo de archivo
- **CORS Headers**: Control de acceso cross-origin
- **Client-side Protection**: Bloqueo de herramientas de inspección y teclas de acceso

## Ventajas del Sistema Unificado

1. **Escalabilidad**: Fácil agregar nuevas categorías
2. **Mantenibilidad**: Un solo punto de código para el visor
3. **Consistencia**: Experiencia uniforme para todos los documentos
4. **Seguridad**: Centralización de medidas de seguridad
5. **Reutilización**: Código compartido entre módulos

---

## Características del Componente Original

- ✅ Visualización por páginas o vista completa
- ✅ Búsqueda de texto con resaltado
- ✅ Navegación entre coincidencias de búsqueda
- ✅ Zoom in/out con límites configurables
- ✅ Navegación entre páginas
- ✅ Diseño responsive
- ✅ Prevención de menú contextual
- ✅ Indicadores de carga y estado

## Uso Básico

### 1. Incluir los archivos necesarios

```html
<!DOCTYPE html>
<html>
<head>
    <!-- PDF Viewer CSS -->
    <link href="../../Components/PDFViewer/pdf-viewer.css" rel="stylesheet">
</head>
<body>
    <!-- Contenedor del PDF Viewer -->
    <div id="pdfViewer">
        <?php include("../../Components/PDFViewer/pdf-viewer.html"); ?>
    </div>
    
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
    
    <!-- PDF Viewer Component -->
    <script src="../../Components/PDFViewer/pdf-viewer.js"></script>
</body>
</html>
```

### 2. Inicializar el visor

```javascript
document.addEventListener('DOMContentLoaded', function() {
    // Crear instancia del visor
    const viewer = new PDFViewer('ruta/al/archivo.pdf', 'pdfViewer');
});
```

## Parámetros del Constructor

```javascript
new PDFViewer(pdfUrl, containerId)
```

- `pdfUrl` (string): URL del archivo PDF a cargar
- `containerId` (string, opcional): ID del contenedor HTML. Por defecto: 'pdfViewer'

## Métodos Públicos

### loadNewPDF(pdfUrl)
Carga un nuevo archivo PDF en el visor existente.

```javascript
viewer.loadNewPDF('nuevo-archivo.pdf');
```

## Controles Disponibles

- **Ver Todo / Ver Paginado**: Alterna entre vista de todas las páginas y vista página por página
- **Búsqueda**: Campo de texto para buscar contenido en el PDF
- **Navegación de páginas**: Botones anterior/siguiente
- **Zoom**: Botones para aumentar/reducir el zoom
- **Navegación de coincidencias**: Ir a coincidencia anterior/siguiente en búsquedas

## Configuración Avanzada

### Límites de Zoom
```javascript
this.MIN_SCALE = 0.2;  // Zoom mínimo
this.MAX_SCALE = 5.0;  // Zoom máximo
this.scale = 2.5;      // Zoom inicial
```

### Personalización de Estilos
Modifica `pdf-viewer.css` para personalizar la apariencia:

```css
.pdf-viewer-container {
    /* Personalizar contenedor principal */
}

.pdf-controls {
    /* Personalizar barra de controles */
}

.pdf-canvas-container {
    /* Personalizar área de visualización */
}
```

## Ejemplos de Implementación

### En Sistema PQR
```php
// 03_MostrarPQR.php
<div id="pdfViewer">
    <?php include("../../Components/PDFViewer/pdf-viewer.html"); ?>
</div>

<script>
const viewer = new PDFViewer("<?php echo $archivo; ?>", 'pdfViewer');
</script>
```

### En Sistema WPS
```php
// 03_MostrarWPS.php
<div id="pdfViewer">
    <?php include("../../Components/PDFViewer/pdf-viewer.html"); ?>
</div>

<script>
const viewer = new PDFViewer("<?php echo $archivo; ?>", 'pdfViewer');
</script>
```

## Dependencias

- **PDF.js**: Biblioteca principal para renderizado de PDF
- **Bootstrap 5**: Para estilos de botones e iconos
- **Bootstrap Icons**: Para iconografía

## Compatibilidad

- Navegadores modernos que soporten ES6+ 
- PDF.js versión 3.11.174 o superior
- Bootstrap 5.x

## Notas de Implementación

1. El componente previene el menú contextual en el canvas para evitar descargas no autorizadas
2. Los archivos PDF deben estar en rutas accesibles desde el servidor web
3. La búsqueda es case-insensitive y resalta todas las coincidencias
4. El zoom se aplica tanto en vista paginada como en vista completa
5. El visor maneja automáticamente la carga y los errores de PDF

## Troubleshooting

### PDF no se carga
- Verificar que la ruta del archivo sea correcta
- Verificar permisos de acceso al archivo
- Revisar la consola del navegador para errores

### Búsqueda no funciona
- Verificar que el PDF contenga texto seleccionable
- PDFs escaneados sin OCR no soportan búsqueda de texto

### Problemas de renderizado
- Verificar que PDF.js worker esté correctamente configurado
- Revisar compatibilidad del navegador con PDF.js
