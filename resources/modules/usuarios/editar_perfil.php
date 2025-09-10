<?php
// resources/modules/usuarios/editar_perfil.php
	$title = 'Editar perfil';

	$permiso = 'editar';
	// Definir la página principal para control de acceso
	$name_page_principal = '01_AdministrarPerfiles.php';
	require_once BASE_PATH . '/app/core/00_load.php';

	// Verificar si el usuario puede editar usuarios (administradores tienen acceso automático)
	if (!has_access_with_permissions($name_page_principal, $permiso)) {
		echo json_encode(['error' => true, 'message' => 'No tiene permisos para editar perfiles.']);
		redirect($name_page_principal, false); // Temporal
	}

    $all_proceso = find_table_array('proceso');
    $perfil = find_table_field_only('perfiles', 'perfil', (string)$_GET['perfil']);
	$result_documentos_perfil = documentos_perfil((string)$_GET['perfil']);
		
	function documentos_perfil($value) {
		global $db;

		// Ejecutamos la consulta para obtener los datos de ambas tablas
		$sql = $db->query(" SELECT t1.cod_documento, t1.descripcion_documento, t1.version, t2.perfil, t2.modo_almacenamiento, t2.tiempo_retencion, t2.recuperacion, t2.disposicion
							FROM documentos t1
							LEFT JOIN perfiles_detalle t2 
							ON t1.cod_documento = t2.cod_documento 
							AND t2.perfil = '{$value}'");
		// Inicializamos un array para almacenar los resultados
		$results = [];
		// Recorremos los resultados y los agregamos al array
		while ($result = $db->fetch_assoc($sql)) {
			$results[] = $result;
		}
		if (!empty($results)) {
			return $results; // Retornamos el array de resultados
		} else {
			return null; // Retornamos null si no hay resultados
		}
	}

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
						<span>Editar Perfil</span>
					</strong>			
				</div>
				<div class="table-border">
					<table border="1">
						<thead class="panel-default" >   
							<tr>
								<th class="text-left" style='width: 1%;'><small><img src="../Documentos/01_Imagenes/LogoTama.png" ></small> </th>
								<th class="text-center" style='width: 10%;'> <h2>Administrador de Perfiles</h2></th>
								<th class="text-center" style='width: 5%;'><small> Codigo: T-GI-F-01  <br> Versión: 01  <br> Fecha: 02/02/2024</small></th>
							</tr>    
						</thead>
					</table>
				</div>

				<div class="panel-body" style="background-color: #ECF0F1">			
					<form id="FormEditarPerfil" class="clearfix" >
						<div class="row">
							<div class="form-group col-md-4">
								<div class="input-group align-items-center" style="height: 40px;">
									<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
										<i class="bi bi-grid-fill" style="margin-right: 5px;"></i></i>Perfil
									</span>					
									<input type="text" class="form-control" style="height: 40px;" id="perfil" name="perfil" value="<?php echo htmlspecialchars($perfil['perfil']); ?>" required readonly>								
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
											<option value="<?php echo htmlspecialchars($proceso['proceso']); ?>" 
												<?php if ($perfil['proceso'] === $proceso['proceso']): echo "selected"; endif; ?>>
												<?php echo htmlspecialchars($proceso['proceso']); ?>
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
										<option value="1" <?php echo $perfil['estado_perfil'] ==  '1' ? 'selected' : ''; ?>>Habilitado</option>
										<option value="0" <?php echo $perfil['estado_perfil'] ==  '0' ? 'selected' : ''; ?>>Deshabilitado</option>
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
											if (!empty($result_documentos_perfil)) {
												foreach ($result_documentos_perfil as $documentos_perfil) {
													// Utiliza un valor predeterminado si 'perfil' está vacío
													$isChecked = !empty($documentos_perfil['perfil']) ? 'checked' : '';
													echo "<tr>
															<td><input type='checkbox' class='documentos_perfil-checkbox' data-cod_documento='{$documentos_perfil['cod_documento']}' $isChecked ></td>
															<td>{$documentos_perfil['cod_documento']}</td>
															<td>{$documentos_perfil['descripcion_documento']}</td>
															<td>{$documentos_perfil['version']}</td>
															<td><input type='text' class='modo_almacenamiento' value='{$documentos_perfil['modo_almacenamiento']}'></td>
															<td><input type='text' class='tiempo_retencion' value='{$documentos_perfil['tiempo_retencion']}'></td>
															<td><input type='text' class='recuperacion' value='{$documentos_perfil['recuperacion']}'></td>
															<td><input type='text' class='disposicion' value='{$documentos_perfil['disposicion']}'></td>
														</tr>";
												}
											} else {
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
							<button type="button" class="btn btn-danger" style="margin-top: 10px; width: 10%; background-color: #EC7063;" onclick="window.location.href='01_AdministrarPerfil.php';">
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
<script type="text/javascript" src="' . BASE_URL . '/public/assets/js/usuarios/editar_perfil.js"></script>
';

include __DIR__ . '/../../layouts/main.php';
?>