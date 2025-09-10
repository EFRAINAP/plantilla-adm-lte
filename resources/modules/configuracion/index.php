<?php 
$title = "Configuración del Sistema";
ob_start();
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Configuración General</h3>
                </div>
                <form id="configForm">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nombre del Sistema</label>
                            <input type="text" class="form-control" name="app_name" value="Sistema AdminLTE" required>
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea class="form-control" name="app_description" rows="3">Sistema de administración moderno y eficiente</textarea>
                        </div>
                        <div class="form-group">
                            <label>Email de Administrador</label>
                            <input type="email" class="form-control" name="admin_email" value="admin@sistema.com" required>
                        </div>
                        <div class="form-group">
                            <label>Zona Horaria</label>
                            <select class="form-control" name="timezone">
                                <option value="America/Mexico_City" selected>México (GMT-6)</option>
                                <option value="America/New_York">Nueva York (GMT-5)</option>
                                <option value="Europe/Madrid">Madrid (GMT+1)</option>
                                <option value="Asia/Tokyo">Tokio (GMT+9)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Idioma por Defecto</label>
                            <select class="form-control" name="default_language">
                                <option value="es" selected>Español</option>
                                <option value="en">English</option>
                                <option value="fr">Français</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="maintenance_mode" name="maintenance_mode">
                                <label class="custom-control-label" for="maintenance_mode">Modo Mantenimiento</label>
                            </div>
                            <small class="text-muted">Activar para bloquear el acceso al sistema</small>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="user_registration" name="user_registration" checked>
                                <label class="custom-control-label" for="user_registration">Permitir Registro de Usuarios</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                        <button type="button" class="btn btn-secondary ml-2" onclick="resetForm()">Restablecer</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información del Sistema</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Versión PHP:</strong></td>
                            <td><?= phpversion() ?></td>
                        </tr>
                        <tr>
                            <td><strong>Servidor:</strong></td>
                            <td><?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Sistema:</strong></td>
                            <td><?= php_uname('s') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Memoria Límite:</strong></td>
                            <td><?= ini_get('memory_limit') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Upload Max:</strong></td>
                            <td><?= ini_get('upload_max_filesize') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Acciones del Sistema</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning btn-block" onclick="clearCache()">
                            <i class="fas fa-broom"></i> Limpiar Cache
                        </button>
                        <button class="btn btn-info btn-block" onclick="checkUpdates()">
                            <i class="fas fa-sync"></i> Verificar Actualizaciones
                        </button>
                        <button class="btn btn-success btn-block" onclick="backupSystem()">
                            <i class="fas fa-download"></i> Respaldar Sistema
                        </button>
                        <button class="btn btn-danger btn-block" onclick="confirmRestart()">
                            <i class="fas fa-power-off"></i> Reiniciar Sistema
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#configForm').on('submit', function(e) {
        e.preventDefault();
        
        // Simular guardado
        toastr.success('Configuración guardada exitosamente');
    });
});

function resetForm() {
    if (confirm('¿Está seguro de restablecer la configuración?')) {
        document.getElementById('configForm').reset();
        toastr.info('Formulario restablecido');
    }
}

function clearCache() {
    toastr.info('Limpiando cache...');
    setTimeout(() => {
        toastr.success('Cache limpiado exitosamente');
    }, 2000);
}

function checkUpdates() {
    toastr.info('Verificando actualizaciones...');
    setTimeout(() => {
        toastr.success('Sistema actualizado');
    }, 3000);
}

function backupSystem() {
    toastr.info('Generando respaldo...');
    setTimeout(() => {
        toastr.success('Respaldo generado exitosamente');
    }, 4000);
}

function confirmRestart() {
    if (confirm('¿Está seguro de reiniciar el sistema? Los usuarios conectados serán desconectados.')) {
        toastr.warning('Reiniciando sistema...');
        setTimeout(() => {
            toastr.success('Sistema reiniciado');
        }, 5000);
    }
}
</script>

<?php
$content = ob_get_clean();
include RESOURCES_PATH . '/layouts/main.php';
