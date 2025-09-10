<?php
// resources/modules/usuarios/index.php

    $title = 'Administrador de Usuarios';
	$name_page_principal = '01_AdministrarUsuarios.php';
    require_once BASE_PATH . '/app/core/00_load.php';

    $all_areas = find_table_array('area');
    $all_procesos = find_table_array('proceso');
    $all_puestos = find_table_array('puestos');

ob_start();
?>

<div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php //echo display_msg($msg); ?>
            </div>

        <!-- Panel principal de usuarios con mejoras visuales -->
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading d-flex align-items-center justify-content-between flex-wrap">
                    <div class="d-flex align-items-center me-3 mb-2 mb-md-0">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-people-fill me-2"></i><strong>Gestión de Usuarios</strong>
                            </span>
                            <button id="AbrirModalAgregarUsuario" class="btn btn-primary" title="Agregar nuevo usuario" data-bs-toggle="modal" data-bs-target="#Modal-Usuario">
                                <i class="bi bi-plus-lg"></i> Nuevo
                            </button>
                        </div>
                    </div>
                    <div id="buttons1" class="btn-group me-3 mb-2 mb-md-0"></div>
                    <div class="search-box">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search me-1"></i><strong>Buscar</strong>
                            </span>
                            <input type="text" id="customSearch1" class="form-control" placeholder="Buscar usuarios..." name="customSearch1">
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <table id="TablaUsuarios" class="display custom-header-font-size custom-data-font-size" style="width: 100%;">
                        <thead class="panel-default" style="color: #27ae60">
                            <tr>
                                <th class="text-left" style="width: 7%;"><small><i class="bi bi-person me-1"></i>Usuario</small></th>
                                <th class="text-center" style="width: 6%;"><small><i class="bi bi-shield me-1"></i>Nivel</small></th>
                                <th class="text-left" style="width: 13%;"><small><i class="bi bi-person-badge me-1"></i>Nombre</small></th>
                                <th class="text-left" style="width: 13%;"><small><i class="bi bi-building me-1"></i>Área</small></th>
                                <th class="text-left" style="width: 13%;"><small><i class="bi bi-building me-1"></i>Proceso</small></th>
                                <th class="text-left" style="width: 15%;"><small><i class="bi bi-briefcase me-1"></i>Cargo</small></th>
                                <th class="text-left" style="width: 10%;"><small><i class="bi bi-clock me-1"></i>Último Login</small></th>
                                <th class="text-left" style="width: 8%;"><small><i class="bi bi-toggle-on me-1"></i>Estado</small></th>
                                <th class="text-left" style="width: 8%;"><small><i class="bi bi-gear me-1"></i>Acciones</small></th>
                                <th class="text-left" style="width: 8%;"><small><i class="bi bi-shield-lock me-1"></i>Accesos</small></th>
                                <th class="text-left" style="width: 8%;"><small><i class="bi bi-person-lines-fill me-1"></i>Perfiles</small></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Contenido dinámico cargado por DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal único para Agregar/Editar usuarios -->
<div class="modal fade" id="Modal-Usuario" tabindex="-1" role="dialog" aria-labelledby="modalUsuarioLabel" aria-hidden="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-gear fs-4 me-3"></i>
                    <div>
                        <h5 class="modal-title mb-1" id="modalUsuarioLabel">Agregar Usuario</h5>
                        <small class="text-muted" id="modalUsuarioSubtitle">Ingrese la información del nuevo usuario</small>
                    </div>
                </div>
                <div class="text-center" style='width: 40%;'>
                    <small>
                        <i class="bi bi-file-text me-1"></i>Código: T-GI-F-01<br>
                        <i class="bi bi-arrow-clockwise me-1"></i>Versión: 01<br>
                        <i class="bi bi-calendar me-1"></i>Fecha: 02/02/2024
                    </small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <!-- Información básica del usuario -->
                <div class="panel panel-default mb-4">
                    <div class="panel-body border-start p-3 border-info-subtle border-4 border-radius-3 rounded-3" id="panel-info-basica">
                        <h6 class="mb-3"><i class="bi bi-person-fill me-2"></i>Información Básica</h6>
                            <div class="row">
                                <div class="form-group col-md-6 mb-3">
                                    <label class="form-label"><i class="bi bi-person me-1"></i>Usuario</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <label class="form-label"><i class="bi bi-shield me-1"></i>Nivel de Usuario</label>
                                    <select class="form-control" id="user_level" name="user_level" required>
                                        <option value="">Seleccionar nivel...</option>
                                        <option value="1">1 - Administrador</option>
                                        <option value="2">2 - Jefe de Área</option>
                                        <option value="3">3 - Usuario Normal</option>
                                    </select>
                                </div>	
                                <div class="form-group col-md-6 mb-3">
                                    <label class="form-label"><i class="bi bi-person-badge me-1"></i>Nombre Completo</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <label class="form-label"><i class="bi bi-building me-1"></i>Área</label>
                                    <select class="form-control" id="area" name="area" required>
                                        <option value="">-- Seleccione área --</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <label class="form-label"><i class="bi bi-building me-1"></i>Proceso</label>
                                    <select class="form-control" id="proceso" name="proceso" required disabled>
                                        <option value="">-- Seleccione proceso --</option>
                                        <!-- Las opciones se cargarán dinámicamente -->
                                    </select>
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <label class="form-label"><i class="bi bi-briefcase me-1"></i>Cargo</label>
                                    <select class="form-control" id="cargo" name="cargo" required disabled>
                                        <option value="">-- Seleccione cargo --</option>
                                        <!-- Las opciones se cargarán dinámicamente -->
                                    </select>
                                </div>
                                <div class="form-group col-md-6 mb-3">
                                    <label class="form-label"><i class="bi bi-toggle-on me-1"></i>Estado</label>
                                    <select class="form-control" id="estado_user" name="estado_user" required>
                                        <option value="">Seleccionar estado...</option>
                                        <option value="1">Habilitado</option>
                                        <option value="0">Deshabilitado</option>
                                    </select>
                                </div>
                            </div>
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="panel panel-default" id="panel-password">
                    <div class="panel-body border-start p-3 border-warning border-4 border-radius-3 rounded-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0" id="titulo-password"><i class="bi bi-key-fill me-2"></i>Establecer Contraseña</h6>
                            <button type="button" class="btn btn-outline-warning btn-sm" id="btnMostrarCambiarPassword" style="display: none;">
                                <i class="bi bi-key me-1"></i>Cambiar Contraseña
                            </button>
                        </div>
                        <div id="campos-password">
                            <div class="row">
                                <div class="form-group col-md-12 mb-3">
                                    <label class="form-label" id="label-password"><i class="bi bi-lock me-1"></i>Nueva Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <small class="form-text text-muted">Mínimo 6 caracteres</small>
                                </div>
                                <div class="form-group col-md-12 mb-3">
                                    <label class="form-label"><i class="bi bi-lock-fill me-1"></i>Confirmar Contraseña</label>
                                    <input type="password" class="form-control" id="reenter_password" name="reenter_password" required>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-danger btn-sm" id="btnCambiarPassword" style="display: none;">
                                    <i class="bi bi-key me-1"></i>Cambiar Contraseña
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btnGuardarUsuario">
                    <i class="bi bi-check-circle me-2"></i><span id="btnGuardarText">Agregar Usuario</span>
                </button>
                <button type="button" class="btn btn-success" id="btnEditarUsuario">
                    <i class="bi bi-check-circle me-2"></i><span id="btnGuardarText">Editar Usuario</span>
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>	
            </div>
        </div>
    </div>
</div>

<!-- Modal para Control de accesos -->
<div class="modal fade" id="Modal-Control-Accesos" tabindex="-1" role="dialog" aria-labelledby="modalAgregarUsuarioLabel" aria-hidden="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-yellow">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-gear fs-4 me-3"></i>
                    <div>
                        <h5 class="modal-title mb-1" id="modalAgregarUsuarioLabel">Agregar accesos</h5>
                        <small class="text-muted">Seleccione los accesos a asignar</small>
                    </div>
                </div>
                <div class="text-center" style='width: 40%;'>
                    <small>
                        <i class="bi bi-file-text me-1"></i>Código: T-GI-F-01<br>
                        <i class="bi bi-arrow-clockwise me-1"></i>Versión: 01<br>
                        <i class="bi bi-calendar me-1"></i>Fecha: 02/02/2024
                    </small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
				 <!-- Filtros y búsqueda -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="buscar-paginas" class="form-control" placeholder="Buscar páginas...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-success btn-sm" id="seleccionar-todas">
                                <i class="bi bi-check-all"></i> Seleccionar Todas
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" id="limpiar-seleccion">
                                <i class="bi bi-x-square"></i> Limpiar Selección
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" id="permisos-completos">
                                <i class="bi bi-shield-check"></i> Permisos Completos
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de páginas con permisos -->
                <div class="panel panel-default">
                    <div class="panel-body" style="background-color: #f8f9fa; border-radius: 8px; max-height: 500px; overflow-y: auto;">
                        <table class="table table-striped table-hover" id="tabla-permisos">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th style="width: 5%;">
                                        <input type="checkbox" id="check-all-pages" class="form-check-input">
                                    </th>
                                    <th style="width: 30%;">
                                        <i class="bi bi-info-circle me-1"></i>Descripción
                                    </th>
                                    <th style="width: 10%;" class="text-center">
                                        <i class="bi bi-pencil me-1"></i>Editar
                                    </th>
                                    <th style="width: 10%;" class="text-center">
                                        <i class="bi bi-trash me-1"></i>Eliminar
                                    </th>
                                    <th style="width: 10%;" class="text-center">
                                        <i class="bi bi-plus-circle me-1"></i>Adicionar
                                    </th>
                                    <th style="width: 10%;" class="text-center">
                                        <i class="bi bi-eye me-1"></i>Seguimiento
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
								<!-- Contenido dinámico cargado por DataTables 2 -->
                            </tbody>
                        </table>
                    </div>
                </div>

				<!-- Resumen de permisos seleccionados -->
                <div class="mt-3">
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Resumen:</strong> 
                        <span id="resumen-permisos">No hay páginas seleccionadas</span>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-yellow">
                <div class="me-auto">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb"></i> 
                        Tip: Selecciona las páginas primero, luego configura los permisos específicos
                    </small>
                </div>
                <button type="button" class="btn btn-success" id="btnGuardarAccesos">
                    <i class="bi bi-check-circle me-2"></i>Guardar Accesos
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>	
            </div>
        </div>
    </div>
</div>

<!-- Modal para Control de Perfiles -->
<div class="modal fade" id="Modal-Control-Perfiles" tabindex="-1" role="dialog" aria-labelledby="modalPerfilesUsuarioLabel" aria-hidden="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-red">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-lines-fill fs-4 me-3"></i>
                    <div>
                        <h5 class="modal-title mb-1" id="modalPerfilesUsuarioLabel">Control de Perfiles</h5>
                        <small class="text-muted">Seleccione los perfiles a asignar</small>
                    </div>
                </div>
                <div class="text-center" style='width: 40%;'>
                    <small>
                        <i class="bi bi-file-text me-1"></i>Código: T-GI-F-01<br>
                        <i class="bi bi-arrow-clockwise me-1"></i>Versión: 01<br>
                        <i class="bi bi-calendar me-1"></i>Fecha: 02/02/2024
                    </small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
				<!-- Filtros y búsqueda -->
				<div class="row mb-3">
                    <div class="col-md-3">
                        <div class="input-group">
							<span class="input-group-text">
								<i class="bi bi-search me-1"></i><strong>Buscar</strong>
							</span>
                            <input type="text" id="customSearch3" class="form-control" placeholder="Buscar perfiles...">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="btn-group" role="group">
                            <!--<button type="button" class="btn btn-outline-success btn-sm" id="seleccionar-todos-perfiles">
                                <i class="bi bi-check-all"></i> Seleccionar Todos
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" id="limpiar-seleccion-perfiles">
                                <i class="bi bi-x-square"></i> Limpiar Selección
                            </button>-->
							<button type="button" id="btnAgregarUserPerfiles" class="btn btn-outline-info btn-sm" title="Agregar perfil" data-bs-toggle="modal" data-bs-target="#Modal-Agregar-Perfil">
                                <i class="bi bi-plus-lg"></i> Agregar
                            </button>
                        </div>
                    </div>
					<div class="col-md-4 text-end">
						<div id="buttons3" class="btn-group me-2 mb-2 mb-md-0"></div>
					</div>
                </div>

				<div class="panel-body" style="background-color: #f8f9fa; border-radius: 8px; max-height: 500px; overflow-y: auto;">
					<table class="table table-striped table-hover" id="TablaUsuariosPerfiles">
						<thead class="table-dark sticky-top">
							<tr>
								<th style="width: 30%;">
									<i class="bi bi-person me-1"></i>Usuario
								</th>
								<th style="width: 30%;">
									<i class="bi bi-person-badge me-1"></i>Perfil
								</th>
								<th style="width: 35%;">
									<i class="bi bi-diagram-3 me-1"></i>Proceso
								</th>
								<th style="width: 10%;" class="text-center">
									<i class="bi bi-gear me-1"></i>Acciones
								</th>
							</tr>
						</thead>

						<tbody>
							<!-- Contenido dinámico -->
						</tbody>
					</table>
                </div>
            </div>

            <div class="modal-footer bg-red">
                <div class="me-auto">
                    <small class="text-muted">
                        <i class="bi bi-lightbulb"></i> 
                        Tip: Selecciona las páginas primero, luego configura los permisos específicos
                    </small>
                </div>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>	
            </div>
        </div>
    </div>
</div>

<!-- Modal para Agregar Perfil a Usuario -->
<div class="modal fade" id="Modal-Agregar-Perfil" tabindex="-1" role="dialog" aria-labelledby="modalAgregarPerfilLabel" aria-hidden="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-plus fs-4 me-3"></i>
                    <div>
                        <h5 class="modal-title mb-1" id="modalAgregarPerfilLabel">Agregar Perfil</h5>
                        <small class="text-muted">Asignar un perfil al usuario seleccionado</small>
                    </div>
                </div>
                <div class="text-center" style='width: 40%;'>
                    <small>
                        <i class="bi bi-file-text me-1"></i>Código: T-GI-F-01<br>
                        <i class="bi bi-arrow-clockwise me-1"></i>Versión: 01<br>
                        <i class="bi bi-calendar me-1"></i>Fecha: 02/02/2024
                    </small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <!-- Información del usuario -->
                <div class="panel panel-default mb-4">
                    <div class="panel-body" style="background-color: #e3f2fd; border-radius: 8px; border-left: 4px solid #2196f3;">
                        <h6 class="mb-3"><i class="bi bi-person-fill me-2"></i>Usuario Seleccionado</h6>
                        <div class="row">
                            <div class="form-group col-md-12 mb-3">
                                <label class="form-label"><i class="bi bi-person me-1"></i>Usuario</label>
                                <input type="text" class="form-control" id="username-perfil" name="username" readonly>
                                <small class="form-text text-muted">Usuario al que se asignará el perfil</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selección de perfil -->
                <div class="panel panel-default">
                    <div class="panel-body" style="background-color: #f3e5f5; border-radius: 8px; border-left: 4px solid #9c27b0;">
                        <h6 class="mb-3"><i class="bi bi-person-badge me-2"></i>Seleccionar Perfil</h6>
                        <div class="row">
                            <div class="form-group col-md-12 mb-3">
                                <label class="form-label"><i class="bi bi-diagram-3 me-1"></i>Perfil</label>
                                <select class="form-control" id="perfil-usuario" name="perfil" required>
                                    <option value="">Seleccionar perfil...</option>
                                    <!-- Las opciones se cargarán dinámicamente -->
                                </select>
                                <small class="form-text text-muted">Seleccione el perfil que desea asignar al usuario</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" name="agregar_perfil" class="btn btn-success" id="btnAgregarPerfil">
                    <i class="bi bi-check-circle me-2"></i>Agregar Perfil
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
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
    
    // Pasar datos PHP a JavaScript
    window.datosUsuarios = {
        areas: ' . json_encode($all_areas) . ',
        procesos: ' . json_encode($all_procesos) . ',
        puestos: ' . json_encode($all_puestos) . '
    };
</script>
<script type="text/javascript" src="' . BASE_URL . '/public/assets/js/usuarios/GestionUsuarios.js"></script>
';

include __DIR__ . '/../../layouts/main.php';
?>
