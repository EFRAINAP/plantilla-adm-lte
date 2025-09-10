<?php
// resources/modules/usuarios/roles.php
$title = 'Administrador de Perfiles';
// Definir la página principal para control de acceso
$name_page_principal = '01_AdministrarPerfiles.php';
require_once BASE_PATH . '/app/core/00_load.php';

ob_start();
?>
<div class="container-fluid">
    <div class="row mt-3 p-3">

        <div class="col-md-12">
            
        </div>
        <div class="col-md-5">
            <div class="panel panel-default">
                <div class="panel-heading d-flex align-items-center justify-content-between flex-nowrap" style="flex-wrap: nowrap;">
                    <!-- Botón de agregar -->
                    <div class="d-flex align-items-center me-2">
                        <div class="input-group">
                            <a href="<?= url('usuarios/perfiles/agregar') ?>" class="btn btn-primary" onclick="redirectToAddTask()">
                                <i class="bi bi-plus-lg"></i>
                            </a>
                        </div>
                    </div>
                    <!-- Botones de Exportación de DataTables -->
                    <div id="buttons1" class="btn-group me-2" style="white-space: nowrap;">
                        <!-- Botones de DataTables se insertarán aquí -->
                    </div>
                    <!-- Campo de Búsqueda Personalizado -->
                    <div class="search-box" style="flex-shrink: 1;">
                        <div class="input-group" style="width: auto;">
                            <input type="text" id="customSearch1" class="form-control" placeholder="Buscar en la tabla..." style="min-width: 100px;">
                        </div>
                    </div>
                </div>

                <div class="panel-body table-responsive col-md-12">				
                    <table id="Tabla_Perfiles" class="display custom-header-font-size custom-data-font-size" style="width:100%">
                        <thead class="panel-default" style="color: #27ae60">
                            <tr>
                                <th class="text-left" style='width: 20%;'><small>Perfil</small></th> 
                                <th class="text-left" style='width: 20%;'><small>Proceso</small></th> 
                                <th class="text-left" style='width: 30%;'><small>Estado</small></th>
                                <th class="text-left" style='width: 30%;'><small>Acciones</small></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>	
            </div>	
        </div>
            
        <div class="col-md-7">
            <div class="panel panel-default">
                <div class="panel-heading clearfix" style="display: flex; align-items: center; justify-content: space-between;">
                    <!-- Botones de Exportación de DataTables -->
                    <div id="buttons2" class="btn-group me-2" style="white-space: nowrap;">
                        <!-- Botones de DataTables se insertarán aquí -->
                    </div>
                    <div id="Perfil_Elegido">
                        <!-- Botones de DataTables se insertarán aquí -->
                    </div>
                    <!-- Campo de Búsqueda Personalizado -->
                    <div class="search-box ms-auto" style="flex-shrink: 1;">
                        <div class="input-group" style="width: auto;">
                            <span class="input-group-text"><i class=""></i><strong></strong></span>
                            <input type="text" id="customSearch2" class="form-control" placeholder="Buscar en la tabla..." style="min-width: 150px;">
                        </div>
                    </div>
                </div>

                <div class="panel-body table-responsive col-md-12">				
                    <table id="Tabla_Perfiles_Detalle" class="display custom-header-font-size custom-data-font-size" style="width:100%">
                        <thead class="panel-default" style="color: #27ae60">
                            <tr>
                                <th class="text-left" style='width: 10%;'><small>Código</small></th>
                                <th class="text-left" style='width: 40%;'><small>Descripción Documento</small></th> 
                                <th class="text-left" style='width: 13%;'><small>Almacenamiento</small></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<?php
$content = ob_get_clean();
// Agregar scripts específicos para esta página
$pageScripts = '
<script>
    // Definir variables globales para JavaScript
    const BASE_URL = "' . BASE_URL . '";
</script>
<script type="text/javascript" src="' . BASE_URL . '/public/assets/js/usuarios/GestionPerfil.js"></script>
';

include __DIR__ . '/../../layouts/main.php';
?>
