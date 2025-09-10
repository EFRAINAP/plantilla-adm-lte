# Ejemplos de Implementación - Sistema Unificado PDF Viewer

## 1. Enlaces desde Listados

### Para PQR (con documentos y esquemas):
```php
// En el listado 02_AdmPQR.php
foreach($pqrs as $pqr) {
    echo '<td>';
    
    // Enlace para ver documento
    if (!empty($pqr['nombre_archivo'])) {
        echo '<a href="../Components/PDFViewer/UnifiedDocumentViewer.php?categoria=pqr&id=' . $pqr['id'] . '&tipo=documento" 
                 class="btn btn-primary btn-sm" target="_blank">
                 <i class="bi bi-file-earmark-pdf"></i> Documento
              </a> ';
    }
    
    // Enlace para ver esquema
    if (!empty($pqr['esquema'])) {
        echo '<a href="../Components/PDFViewer/UnifiedDocumentViewer.php?categoria=pqr&id=' . $pqr['id'] . '&tipo=esquema" 
                 class="btn btn-info btn-sm" target="_blank">
                 <i class="bi bi-diagram-3"></i> Esquema
              </a>';
    }
    
    echo '</td>';
}
```

### Para WPS (con documentos y esquemas):
```php
// En el listado 02_AdmWPS.php
foreach($wps_list as $wps) {
    echo '<td>';
    
    // Enlace para ver documento
    if (!empty($wps['nombre_archivo'])) {
        echo '<a href="../Components/PDFViewer/UnifiedDocumentViewer.php?categoria=wps&id=' . $wps['id'] . '&tipo=documento" 
                 class="btn btn-success btn-sm" target="_blank">
                 <i class="bi bi-file-earmark-pdf"></i> Documento
              </a> ';
    }
    
    // Enlace para ver esquema
    if (!empty($wps['esquema'])) {
        echo '<a href="../Components/PDFViewer/UnifiedDocumentViewer.php?categoria=wps&id=' . $wps['id'] . '&tipo=esquema" 
                 class="btn btn-info btn-sm" target="_blank">
                 <i class="bi bi-diagram-3"></i> Esquema
              </a>';
    }
    
    echo '</td>';
}
```

### Para Documentos ISO (solo documentos):
```php
// En el listado 01_ListadoDocumentos.php
foreach($documentos as $doc) {
    echo '<td>';
    
    // Enlace para ver documento
    if (!empty($doc['nombre_archivo'])) {
        echo '<a href="../Components/PDFViewer/UnifiedDocumentViewer.php?categoria=iso&id=' . $doc['id'] . '&tipo=documento" 
                 class="btn btn-info btn-sm" target="_blank">
                 <i class="bi bi-file-earmark-text"></i> Ver Documento
              </a>';
    }
    
    echo '</td>';
}
```

### Para Biblioteca (solo documentos):
```php
// En el listado 01_ListadoBiblioteca.php
foreach($biblioteca as $libro) {
    echo '<td>';
    
    // Enlace para ver documento
    if (!empty($libro['nombre_archivo'])) {
        echo '<a href="../Components/PDFViewer/UnifiedDocumentViewer.php?categoria=biblioteca&id=' . $libro['id'] . '&tipo=documento" 
                 class="btn btn-warning btn-sm" target="_blank">
                 <i class="bi bi-book"></i> Ver Documento
              </a>';
    }
    
    echo '</td>';
}
```

## 2. Funciones Helper Recomendadas

### Función para generar enlaces automáticamente:
```php
// En 06_functions.php
function generar_enlace_documento($categoria, $id, $tipo, $nombre_archivo = '', $texto_boton = 'Ver', $clase_btn = 'btn-primary') {
    if (empty($nombre_archivo)) {
        return '<span class="text-muted">No disponible</span>';
    }
    
    $iconos = [
        'documento' => 'bi-file-earmark-pdf',
        'esquema' => 'bi-diagram-3'
    ];
    
    $icono = isset($iconos[$tipo]) ? $iconos[$tipo] : 'bi-file-earmark';
    
    return '<a href="../Components/PDFViewer/UnifiedDocumentViewer.php?categoria=' . $categoria . '&id=' . $id . '&tipo=' . $tipo . '" 
               class="btn ' . $clase_btn . ' btn-sm" target="_blank">
               <i class="' . $icono . '"></i> ' . $texto_boton . '
            </a>';
}

// Uso:
echo generar_enlace_documento('pqr', $pqr['id'], 'documento', $pqr['nombre_archivo'], 'Documento', 'btn-primary');
echo generar_enlace_documento('pqr', $pqr['id'], 'esquema', $pqr['esquema'], 'Esquema', 'btn-info');
```

## 3. Configuración para JavaScript/AJAX

### Para cargar documentos dinámicamente:
```javascript
function abrirDocumento(categoria, id, tipo) {
    const url = `../Components/PDFViewer/UnifiedDocumentViewer.php?categoria=${categoria}&id=${id}&tipo=${tipo}`;
    window.open(url, '_blank');
}

// Para usar el servicio de archivos directamente:
function obtenerUrlArchivo(categoria, id, tipo) {
    return `../Components/servir_archivo.php?categoria=${categoria}&id=${id}&tipo=${tipo}`;
}

// Uso en eventos:
document.getElementById('btnVerDoc').addEventListener('click', function() {
    abrirDocumento('pqr', 123, 'documento');
});
```

## 4. Migración de Archivos Existentes

### Paso 1: Backup de archivos actuales
```bash
# Crear backup de archivos actuales
cp 03_MostrarPQR.php 03_MostrarPQR_backup.php
cp 03_MostrarWPS.php 03_MostrarWPS_backup.php
cp 02_MostrarDocumentos.php 02_MostrarDocumentos_backup.php
cp 02_MostrarBiblioteca.php 02_MostrarBiblioteca_backup.php
```

### Paso 2: Reemplazar con redirects
```php
// Nuevo contenido para 03_MostrarPQR.php
<?php
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

### Paso 3: Actualizar enlaces en listados
Cambiar todos los enlaces que apunten a los archivos antiguos para que usen la nueva sintaxis directamente.

## 5. Ventajas de la Migración

1. **Código Unificado**: Un solo archivo para mantener
2. **Consistencia**: Misma experiencia en todos los módulos
3. **Escalabilidad**: Fácil agregar nuevos tipos de documentos
4. **Seguridad**: Medidas de seguridad centralizadas
5. **Mantenimiento**: Updates en un solo lugar afectan todo el sistema

## 6. Consideraciones de Implementación

- Los archivos `*_nuevo.php` son ejemplos de implementación
- Se pueden reemplazar los archivos originales una vez probado el sistema
- Los permisos se validan automáticamente según la configuración
- El sistema es retrocompatible con URLs existentes mediante redirects
