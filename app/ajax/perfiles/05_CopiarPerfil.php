<?php
    $page_title = 'Copiar perfil';
    $name_page_principal = '01_AdministrarPerfiles.php';
	$permiso = 'adicionar';
	require_once('../01_General/00_load.php');
		
	$user = current_user();
    if (!$session->isUserLoggedIn(true)) { 
        redirect('', false);
    }

    // Verificar permisos (los administradores tienen acceso automático)
    if (!has_access($name_page_principal)) {
        $session->msg("d", "No tienes permisos para acceder a esta página. Contacta al administrador.");
        redirect('dashboard', false);
    }
	
	// Verificar si el usuario puede adicionar usuarios (administradores tienen acceso automático)
	if (!has_access_with_permissions($name_page_principal, $permiso)) {
		echo json_encode(['error' => true, 'message' => 'No tiene permisos para crear perfiles.']);
		redirect($name_page_principal, false); // Temporal
	}
?>

<?php
    $all_proceso = find_table_array('proceso');
    $perfil = find_table_field_only('perfiles', 'perfil', (string)$_GET['perfil']);
	$result_documentos_perfil = documentos_perfil((string)$_GET['perfil']);
?>	
	
<?php	
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
?>

<?php require_once('../04_header.php'); ?>

<script>
	$(document).ready(function() {
		// Inicializar Select2 en el elemento select
		$('#proceso, #estado_perfil').select2({
			minimumResultsForSearch: Infinity, // Oculta el cuadro de búsqueda en select2 si no es necesario
			tags: true,
			placeholder: "Seleccione una opción",
			allowClear: true
		});
	});
</script>

<div class="row">
	<div class="col-md-12">
		<?php echo display_msg($msg); ?>
	</div>
</div>

<div class="row" >
	<div class="col-md-12">
		<div class="panel panel-default">	
			<div class="panel-heading" style="background-color: #ABEBC6 ">
				<strong>
					<span class="bi bi-grid-fill"></span>
					<span>Copiar Perfil</span>
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
				<form id="FormCopiarPerfil" class="clearfix" >
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
						<button type="button" class="btn btn-danger" style="margin-top: 10px; width: 10%; background-color: #EC7063;" onclick="window.location.href='01_AdministrarPerfiles.php';">
							Salir
						</button>	
					</div>			
				</form>
			</div>
		</div>
	</div>	
</div>

<?php require_once('../05_footer.php'); ?>

<script>
$(document).ready(function() {
    // Inicializar el DataTable
    var table = $('#DocumentosTable').DataTable();
    // Evento de envío del formulario
    $('#FormCopiarPerfil').on('submit', function(e) {
        e.preventDefault(); // Prevenir el comportamiento predeterminado del formulario

        // Obtener valores de los campos del formulario
        let var_operacion = 'A';
        let perfil = $('#perfil').val();
        let proceso = $('#proceso').val();
        let estado_perfil = $('#estado_perfil').val();

        // Array para almacenar los documentos con sus detalles
        let documentosConDetalles = [];

        // Usar la API de DataTable para iterar sobre todas las filas (incluso las no visibles)
        table.rows().every(function() {
            var row = $(this.node());  // Obtener la fila completa
            var checkbox = row.find('.documentos_perfil-checkbox');

            // Comprobar si el checkbox está seleccionado
            if (checkbox.is(':checked')) {
                // Obtener los valores de la fila seleccionada
                var cod_documento = checkbox.data('cod_documento');
                var modo_almacenamiento = row.find('.modo_almacenamiento').val();
                var tiempo_retencion = row.find('.tiempo_retencion').val();
                var recuperacion = row.find('.recuperacion').val();
                var disposicion = row.find('.disposicion').val();

                // Agregar los detalles del documento al array
                documentosConDetalles.push({
                    cod_documento,
                    modo_almacenamiento,
                    tiempo_retencion,
                    recuperacion,
                    disposicion
                });
            }
        });

        // Enviar los datos al servidor con AJAX
        $.post('06_GrabarPerfil.php', {
            var_operacion,
            perfil,
            proceso,
            estado_perfil,
            documentos: documentosConDetalles

        }, function(response) {
            // Si la respuesta es exitosa, redirigir
            if (response.success) {
                window.location.href = '01_AdministrarPerfiles.php'; // Redirigir a la página correcta
            } else {
                alert(response.message); // Mostrar mensaje de error si existe
            }
        }, 'json'); // Esperar un JSON como respuesta
    });
});
</script>