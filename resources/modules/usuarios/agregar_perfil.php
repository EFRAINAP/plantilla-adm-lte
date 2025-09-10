<?php
	$title = 'Agregar perfil';

	$permiso = 'adicionar';
	// Definir la página principal para control de acceso
	$name_page_principal = '01_AdministrarPerfiles.php';
	require_once BASE_PATH . '/app/core/00_load.php';
	
	// Verificar si el usuario puede adicionar usuarios (administradores tienen acceso automático)
	if (!has_access_with_permissions($name_page_principal, $permiso)) {
		echo json_encode(['error' => true, 'message' => 'No tiene permisos para crear perfiles.']);
		redirect($name_page_principal, false); // Temporal
	}
	// Obtener todos los procesos y documentos para los select y la tabla
	$all_proceso = find_table_array('proceso');
	$all_documentos = find_table_array('documentos');
	ob_start();
?>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<?php //echo display_msg($msg); ?>
		</div>
	</div>

	<div class="row" >
		<div class="col-md-12">
			<div class="panel panel-default">	
				<div class="panel-heading" style="background-color: #ABEBC6 ">
					<strong>
						<span class="bi bi-grid-fill"></span>
						<span>Agregar Perfil</span>
					</strong>			
				</div>
				<div class="table-border">
					<table border="1">
						<thead class="panel-default" >   
							<tr>
								<th class="text-left" style='width: 1%;'><small><img src="../Documentos/01_Imagenes/LogoTama.png" ></small> </th>
								<th class="text-center" style='width: 10%;'> <h2>Administrador de Perfil </h2></th>
								<th class="text-center" style='width: 5%;'><small> Codigo: T-GI-F-01  <br> Versión: 01  <br> Fecha: 02/02/2024</small></th>
							</tr>    
						</thead>
					</table>
				</div>

				<div class="panel-body" style="background-color: #ECF0F1">			
					<form id="FormAgregarPerfil" class="clearfix" >
						<div class="row">
							<div class="form-group col-md-4">
								<div class="input-group align-items-center" style="height: 40px;">
									<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
										<i class="bi bi-grid-fill" style="margin-right: 5px;"></i></i>Perfil
									</span>
									<input type="text" class="form-control" style="height: 40px;" id="perfil" name="perfil" value="" required>
								</div>
							</div>
							<div class="form-group col-md-4">
								<div class="input-group align-items-center" style="height: 40px;">
									<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
										<i class="bi bi-grid-fill" style="margin-right: 5px;"></i>Proceso
									</span>
									<select class="form-control" style="height: 40px; margin-left: 5px;" id="proceso" name="proceso" required>
										<option value=""></option>
										<?php foreach ($all_proceso as $proceso): ?>
											<option value="<?php echo htmlspecialchars($proceso['proceso'], ENT_QUOTES, 'UTF-8'); ?>">
												<?php echo htmlspecialchars($proceso['proceso'], ENT_QUOTES, 'UTF-8'); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>	
							<div class="form-group col-md-4">
								<div class="input-group align-items-center" style="height: 40px;">
									<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
										<i class="bi bi-grid-fill" style="margin-right: 5px;"></i>Estado
									</span>
									<select class="form-control" style="height: 40px; margin-left: 5px;" id="estado_perfil" name="estado_perfil" required>
										<option value=""></option>
										<option value="1">Habilitado</option>
										<option value="0">Deshabilitado</option>
									</select>
								</div>
							</div>					
							<!-- Tabla de documentos -->
							<div class="form-group col-md-12">						
								<table id="DocumentosTable" class="table table-bordered table-striped">
									<thead class="panel-default" style="color: #27ae60">
										<tr>
											<th class="text-left" style='width: 10%;'><small>Check</small></th>
											<th class="text-left" style='width: 15%;'><small>Código</small></th> 
											<th class="text-left" style='width: 100%;'><small>Descripción Documento</small></th> 
											<th class="text-left" style='width: 10%;'><small>Versión</small></th> 
											<th class="text-left" style='width: 40%;'><small>Almacenamiento</small></th> 
											<th class="text-left" style='width: 40%;'><small>Retención</small></th> 
											<th class="text-left" style='width: 40%;'><small>Recuperación</small></th> 
											<th class="text-left" style='width: 40%;'><small>Disposición</small></th>
										</tr>
									</thead>
									<tbody>
										<?php
											// Verificar si hay documentos
											if (count($all_documentos) > 0) {
												foreach ($all_documentos as $documento) {
													// Verifica si el campo 'Transversal' es 'SI'
													$checked = ($documento['transversal'] === 'Si') ? "checked" : "";
													
													echo "<tr>
															<td><input type='checkbox' class='documento-checkbox' data-cod_documento='{$documento['cod_documento']}' $checked></td>
															<td>{$documento['cod_documento']}</td>
															<td>{$documento['descripcion_documento']}</td>
															<td>{$documento['version']}</td>
															<td><input type='text' class='modo_almacenamiento' value='ServidorArchivos'></td>
															<td><input type='text' class='tiempo_retencion' value='Hasta nueva actualización'></td>
															<td><input type='text' class='recuperacion' value='Backup'></td>
															<td><input type='text' class='disposicion' value='Carpeta Superado'></td>
														</tr>";  
												}
											} else {
												// No hay documentos
												echo "<tr><td colspan='8' class='text-center'>No se encontraron documentos.</td></tr>";
											}
										?>
									</tbody>
								</table>
							</div>
						</div>					
						<!-- Botón de envío -->
						<div class="form-group col-md-6">
							<button type="submit" class="btn btn-success" style="margin-top: 10px; margin-right: 40px; width: 10%; background-color: #28a745;">
								Grabar
							</button>
							<button type="button" class="btn btn-danger" style="margin-top: 10px; width: 10%; background-color: #EC7063;" onclick="window.location.href='01_AdministrarPerfiles.php';">
								Salir
							</button>	
						</div>							
					</form>
				</div>
			</div>
		</div>	
	</div>
</div>

<?php
$content = ob_get_clean();
$pageScripts = '
<script>
    // Definir variables globales para JavaScript
    const BASE_URL = "' . BASE_URL . '";
</script>
<script type="text/javascript" src="' . BASE_URL . '/public/assets/js/usuarios/agregar_perfil.js"></script>
';

include __DIR__ . '/../../layouts/main.php';
?>
