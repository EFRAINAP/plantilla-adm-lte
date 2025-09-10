# Components Directory - Servicios Compartidos

Esta carpeta contiene todos los componentes y servicios compartidos del sistema TAMA.

## Estructura

```
Components/
├── servir_archivo.php          # 🔧 Servicio seguro para servir archivos PDF
├── toast.js                    # 🍞 Sistema de notificaciones
└── PDFViewer/                  # 📄 Componente completo para visualizar PDFs
    ├── UnifiedDocumentViewer.php    # Vista unificada principal
    ├── pdf-viewer.html              # Template HTML
    ├── pdf-viewer.js                # Lógica JavaScript
    ├── pdf-viewer.css               # Estilos
    ├── README.md                    # Documentación del componente
    └── EJEMPLOS_IMPLEMENTACION.md   # Ejemplos de uso
```

## 🔧 servir_archivo.php

**Servicio centralizado** para servir archivos PDF de forma segura desde cualquier módulo del sistema.

### Características:
- ✅ **Autenticación**: Verifica usuario logueado
- ✅ **Autorización**: Verifica permisos por categoría
- ✅ **Seguridad**: Valida archivos y rutas
- ✅ **Escalabilidad**: Soporta múltiples categorías

### Categorías Soportadas:
- `iso` - Documentos ISO (tabla: documentos)
- `biblioteca` - Biblioteca Virtual (tabla: biblioteca)  
- `wps` - Documentos WPS (tabla: wps)
- `pqr` - Documentos PQR (tabla: pqr)
- `of` - Órdenes de Fabricación (tabla: ordenes_fabricacion)

### Uso:
```php
// URL del servicio
$url = "../Components/servir_archivo.php?categoria={categoria}&id={id}&tipo={tipo}";

// Ejemplos:
$url_pqr = "../Components/servir_archivo.php?categoria=pqr&id=123&tipo=documento";
$url_iso = "../Components/servir_archivo.php?categoria=iso&id=456&tipo=documento";
```

## 📄 PDFViewer Component

**Componente completo** para visualizar documentos PDF con funcionalidades avanzadas.

### Uso Básico:
```php
// Vista individual (antigua forma)
<div id="pdfViewer">
    <?php include("../Components/PDFViewer/pdf-viewer.html"); ?>
</div>
<script>
    const viewer = new PDFViewer(pdfUrl, 'pdfViewer');
</script>
```

### Uso Unificado (recomendado):
```php
// Redirigir a la vista unificada
$url = "../Components/PDFViewer/UnifiedDocumentViewer.php?categoria=pqr&id=123&tipo=documento";
header("Location: $url");
```

## 🎯 Ventajas de la Centralización

1. **Mantenimiento**: Un solo lugar para updates
2. **Consistencia**: Misma experiencia en todo el sistema
3. **Seguridad**: Medidas centralizadas
4. **Escalabilidad**: Fácil agregar nuevos módulos
5. **Reutilización**: Código compartido

## 🔄 Migración desde Módulos

Los servicios anteriormente específicos de módulos ahora están centralizados:

| Anterior | Nuevo |
|----------|-------|
| `14_GestionDosier/servir_archivo.php` | `Components/servir_archivo.php` |
| `06_DocumentosISO/02_MostrarDocumentos.php` | `Components/PDFViewer/UnifiedDocumentViewer.php` |
| `10_BibliotecaVirtual/02_MostrarBiblioteca.php` | `Components/PDFViewer/UnifiedDocumentViewer.php` |
| `14_GestionDosier/03_MostrarWPS.php` | `Components/PDFViewer/UnifiedDocumentViewer.php` |
| `14_GestionDosier/03_MostrarPQR.php` | `Components/PDFViewer/UnifiedDocumentViewer.php` |

## 📝 Agregar Nuevo Servicio

Para agregar un nuevo servicio compartido:

1. Crear el archivo en `Components/`
2. Documentar en este README
3. Actualizar referencias en módulos específicos
4. Crear ejemplos de uso

## 🔗 Referencias Rápidas

- [PDFViewer README](PDFViewer/README.md) - Documentación completa del visor
- [Ejemplos de Implementación](PDFViewer/EJEMPLOS_IMPLEMENTACION.md) - Cómo usar el sistema
