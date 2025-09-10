<?php
	$page_title = 'Eliminar Documentos';
	$name_page_principal = '03_AdministrarDocumentos.php';
	$permiso = 'eliminar';
	require_once('../01_General/00_load.php');
	
	$user = current_user();
    if (!$session->isUserLoggedIn(true)) { 
        redirect('../index.php', false);
    }

    // Verificar permisos (los administradores tienen acceso automático)
    if (!has_access($name_page_principal)) {
        $session->msg("d", "No tienes permisos para acceder a esta página. Contacta al administrador.");
        redirect('../03_home.php', false);
    }

	// Verificar si el usuario puede eliminar documentos (administradores tienen acceso automático)
	if (!has_access_with_permissions($name_page_principal, $permiso)) {
		echo json_encode(['error' => true, 'message' => 'No tiene permisos para eliminar documentos.']);
		redirect($name_page_principal, false); // Temporal
	}
?>

<?php
	$documentos = find_table_field_only('documentos', 'cod_documento', (string)$_GET['cod_documento']);
	$all_area = find_table_array('area');
	$all_tipodocumento = find_table_array('tipo_documento');
?>

<?php

try {
    // Crear conexión utilizando PDO
    $con = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Configurar el modo de error de PDO para que lance excepciones
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Conexión fallida: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leer los datos enviados en la solicitud
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar si la acción es 'load_procesos'
    if (isset($data['action']) && $data['action'] === 'load_procesos') {
        $area = htmlspecialchars($data['area'], ENT_QUOTES, 'UTF-8'); // Sanear el área recibida

        // Consulta para obtener los procesos relacionados con el área
		$query = $con->prepare("SELECT proceso, descripcion_proceso FROM proceso WHERE area = :area");
        $query->bindParam(':area', $area, PDO::PARAM_STR);
        $query->execute();
        $procesos = $query->fetchAll(PDO::FETCH_ASSOC);

        // Devolver los datos como JSON
        header('Content-Type: application/json');
        echo json_encode($procesos);
        exit;
    }
}
?>

<?php

	$carpetaiso = find_table_field_only('constantes', 'nombre_constante', 'carpeta_iso');
	$upload_path = '../'.$carpetaiso['valor_constante'];
	
	if(isset($_POST['eliminar_documento'])){

		$req_fields = array('area','proceso','tipodocumento','cod_documento','descripciondocumento','responsable', 'version', 'fecha', 'transversal', 'visualizacion', 'estado_documento');
		validate_fields($req_fields);

		if (isset($_GET['cod_documento'])) {
			// Obtener el registro del documento a eliminar
			$documento = find_table_field_only('documentos', 'cod_documento', (string)$_GET['cod_documento']);
			// Comprobar si el documento existe
			if ($documento) {
				// Validar si el documento no está asociado en la tabla distribución
				$perfil_iso = find_table_field_only('perfil_iso_detalle', 'cod_documento', (string)$_GET['cod_documento']);

				if ($perfil_iso) {
				// Si el documento está en la tabla distribución, no permitir eliminar
				$session->msg('d', 'No se puede eliminar el documento porque está distribuido.');
				redirect('03_AdministrarDocumentos.php', false);
				exit;
				}
				// Obtener el nombre del archivo desde la base de datos
				$file_path = $upload_path . $documento['nombre_archivo'];
				// Eliminar el archivo si existe
				if (file_exists($file_path)) {
				unlink($file_path); // Eliminar el archivo del servidor
				}
				// Eliminar el registro de la base de datos
				$query = "DELETE FROM documentos WHERE cod_documento = '{$documento['cod_documento']}'";
				if ($db->query($query)) {
				$session->msg('s', "Registro eliminado correctamente.");
				redirect('03_AdministrarDocumentos.php', false);
				} else {
				$session->msg('d', 'Error al eliminar el registro de la base de datos.');
				redirect('06_EliminarDocumentos.php', false);
				}
			} else {
					$session->msg('d', 'Documento no encontrado.');
					redirect('06_EliminarDocumentos.php', false);
					}
		} else {
				$session->msg('d', 'ID de documento no proporcionado.');
				redirect('06_EliminarDocumentos.php', false);
		}

	}
?>

<?php require_once('../04_header.php'); ?>

<style>
	#img{display:none}
	label{padding: 5px; border: 1px solid #ddd; border-radius: 5px}
</style>

<script>
    function showFileName(event) {
        const input = event.target;
        const fileName = input.files.length > 0 ? input.files[0].name : 'Ningún archivo seleccionado';
        document.getElementById('imgName').textContent = fileName;
    }
</script>	

<script>
	function loadProceso() {
		const area = document.getElementById("area").value; // Obtiene el área seleccionada
		const procesoSelect = document.getElementById("proceso"); // Elemento del combo proceso
		const procesoSeleccionado = "<?php echo htmlspecialchars($documentos['proceso']); ?>"; // Proceso a seleccionar

		// Limpia el combo de proceso
		procesoSelect.innerHTML = '<option value="">Cargando...</option>';

		// Realiza una solicitud AJAX a la misma página
		fetch('', { 
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ action: 'load_procesos', area: area }) 
		})
		.then(response => response.json())
		.then(data => {
			procesoSelect.innerHTML = '<option value="">Seleccione un proceso</option>'; // Resetea las opciones
			data.forEach(proceso => {
				const option = document.createElement('option');
				option.value = proceso.proceso; // Ajusta según tus datos
				option.textContent = proceso.descripcion_proceso; // Ajusta según tus datos

				// Selecciona el proceso si coincide
				if (proceso.proceso === procesoSeleccionado) {
					option.selected = true;
				}

				procesoSelect.appendChild(option);
			});
		})
		.catch(error => {
			console.error('Error al cargar los procesos:', error);
			procesoSelect.innerHTML = '<option value="">Error al cargar</option>';
		});
	}

	// Cargar procesos al cargar la página si el área está seleccionada
	document.addEventListener('DOMContentLoaded', () => {
    const area = document.getElementById("area").value;
    if (area) {
        loadProceso();
    }
});

</script>

<script>
	$(document).ready(function() {
		// Inicializar Select2 en el elemento select
		$('#area, #proceso, #tipodocumento, #transversal, #visualizacion, #estado_documento').select2({
			minimumResultsForSearch: Infinity,
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
			<div class="panel-heading"  style="background-color: #ABEBC6 ">
				<strong>
					<span class="bi bi-grid-fill"></span>
					<span>Eliminar Documento</span>
				</strong>			
			</div>
			<div class="table-border">
				<table border="1">
					<thead class="panel-default" >
						<tr>
							<th class="text-left" style='width: 1%;'><small><img src="../Documentos/01_Imagenes/LogoTama.png" ></small> </th>
							<th class="text-center" style='width: 10%;'> <h2>Lista maestra de información documentada</h2></th>
							<th class="text-center" style='width: 5%;'><small> Codigo: T-GI-F-01  <br> Versión: 01  <br> Fecha: 02/02/2024</small></th>
						</tr>    
					</thead>
				</table>
			</div>
			<div class="panel-body" style="background-color: #ECF0F1">
				 <form method="post" action="06_EliminarDocumentos.php?cod_documento=<?php echo (string)$documentos['cod_documento']; ?>" enctype="multipart/form-data">             
					<div class="row">
						<div class="form-group col-md-3">
							<div class="input-group align-items-center" style="height: 40px;">
								<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
									<i class="bi bi-grid-fill" style="margin-right: 5px;"></i>Área
								</span>
								<select class="form-control" style="height: 40px; margin-left: 5px;" id="area" name="area" onchange="loadProceso()" disabled>
									<option value=""></option>
									<?php foreach ($all_area as $area): ?>
										<option value="<?php echo htmlspecialchars($area['area']); ?>"
											<?php if ($documentos['area'] === $area['area']): echo "selected"; endif; ?>>
											<?php echo htmlspecialchars($area['descripcion_area']); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group col-md-3">
							<div class="input-group align-items-center" style="height: 40px;">
								<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
									<i class="bi bi-grid-fill" style="margin-right: 5px;"></i>Proceso
								</span>
								<select class="form-control" style="height: 40px; margin-left: 5px;" id="proceso" name="proceso" disabled>
									<option value="">Seleccione un proceso</option>
								</select>
							</div>
						</div>
						<div class="form-group col-md-6">
							<div class="input-group align-items-center" style="height: 40px;">
								<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
									<i class="bi bi-grid-fill" style="margin-right: 5px;"></i></i>Responsable
								</span>
								<input type="text" class="form-control" style="height: 40px;" id="responsable" name="responsable" value="<?php echo $documentos['responsable']; ?>" disabled>
							</div>
						</div>	
						<div class="form-group col-md-3">
							<div class="input-group align-items-center" style="height: 40px;">
								<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
									<i class="bi bi-grid-fill" style="margin-right: 5px;"></i>Tipo
								</span>
								<select class="form-control" style="height: 40px; margin-left: 5px;" id="tipodocumento" name="tipodocumento" disabled>
									<option value=""></option>
									<?php foreach ($all_tipodocumento as $tipodocumento): ?>
										<option value="<?php echo htmlspecialchars($tipodocumento['tipo_documento']); ?>" 
											<?php if ($documentos['tipo_documento'] === $tipodocumento['tipo_documento']): echo "selected"; endif; ?>>
											<?php echo htmlspecialchars($tipodocumento['tipo_documento']); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>				
						<div class="form-group col-md-3">
							<div class="input-group align-items-center" style="height: 40px;">
								<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
									<i class="bi bi-grid-fill" style="margin-right: 5px;"></i></i>Código
								</span>
								<input type="text" class="form-control" style="height: 40px;" id="cod_documento" name="cod_documento" value="<?php echo $documentos['cod_documento']; ?>" required readonly>
							</div>
						</div>				
						<div class="form-group col-md-6">
							<div class="input-group align-items-center" style="height: 40px;">
								<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
									<i class="bi bi-grid-fill" style="margin-right: 5px;"></i></i>Descripción
								</span>
								<input type="text" class="form-control" style="height: 40px;" id="descripciondocumento" name="descripciondocumento" value="<?php echo $documentos['descripcion_documento']; ?>" disabled>
							</div>
						</div>				
						<div class="form-group col-md-3">
							<div class="input-group align-items-center" style="height: 40px;">
								<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
									<i class="bi bi-grid-fill" style="margin-right: 5px;"></i></i>Versión
								</span>
								<input type="text" class="form-control" style="height: 40px;" id="version" name="version" value="<?php echo $documentos['version']; ?>" disabled>
							</div>
						</div>
						<div class="form-group col-md-3">
							<div class="input-group align-items-center" style="height: 40px;">
								<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
									<i class="bi bi-grid-fill" style="margin-right: 5px;"></i></i>Fecha
								</span>
								<input type="date" class="form-control" style="height: 40px;" id="fecha" name="fecha" value="<?php echo $documentos['fecha']; ?>" disabled>
							</div>
						</div>
						<div class="form-group col-md-6">
							<div class="input-group align-items-center" style="height: 40px;">
								<label for="img" class="bi bi-filetype-doc input-group-addon" style="height: 40px; width: 120px;"> Documento</label>
								<input type="file" id="img" name="doc_adjunto" class="input-group-addon col-md-3" onchange="showFileName(event)">
								<span id="imgName" name="doc_adjunto" class="form-control" style="height: 40px;"><?php echo $documentos['nombre_archivo']; ?></span>
							</div>
						</div>
						<div class="form-group col-md-3">
							<div class="input-group align-items-center" style="height: 40px;">
								<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
									<i class="bi bi-grid-fill" style="margin-right: 5px;"></i></i>Frecuencia
								</span>
								<select class="form-control" style="height: 40px; margin-left: 5px;" id="frecuencia" name="frecuencia" disabled>
									<option value=""></option>
									<?php foreach ($all_frecuencia as $frecuencia): ?>
										<option value="<?php echo htmlspecialchars($frecuencia['frecuencia']); ?>" 
											<?php if ($documentos['frecuencia'] === $frecuencia['frecuencia']): echo "selected"; endif; ?>>
											<?php echo htmlspecialchars($frecuencia['frecuencia']); ?>
										</option>
									<?php endforeach; ?>						
								</select>
							</div>
						</div>							
						<div class="form-group col-md-3">
							<div class="input-group align-items-center" style="height: 40px;">
								<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
									<i class="bi bi-grid-fill" style="margin-right: 5px;"></i>Transversal
								</span>
								<select class="form-control" style="height: 40px; margin-left: 5px;" id="transversal" name="transversal" disabled>
									<option value="No" <?php echo $documentos['transversal'] == 'No' ? 'selected' : ''; ?>>No</option>
									<option value="Si" <?php echo $documentos['transversal'] == 'Si' ? 'selected' : ''; ?>>Sí</option>
								</select>
							</div>
						</div>	
						<div class="form-group col-md-3">
							<div class="input-group align-items-center" style="height: 40px;">
								<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
									<i class="bi bi-grid-fill" style="margin-right: 5px;"></i>Visualizacion
								</span>
								<select class="form-control" style="height: 40px; margin-left: 5px;" id="visualizacion" name="visualizacion" disabled>
									<option value=""></option>
									<option value="Lectura" <?php echo $documentos['visualizacion'] == 'Lectura' ? 'selected' : ''; ?>>Lectura</option>
									<option value="Descarga" <?php echo $documentos['visualizacion'] == 'Descarga' ? 'selected' : ''; ?>>Descarga</option>
									<option value="Impreso" <?php echo $documentos['visualizacion'] == 'Impreso' ? 'selected' : ''; ?>>Impreso</option>
								</select>
							</div>
						</div>	
						<div class="form-group col-md-3">
							<div class="input-group align-items-center" style="height: 40px;">
								<span class="input-group-addon d-flex align-items-center" style="height: 40px; width: 120px;">
									<i class="bi bi-grid-fill" style="margin-right: 5px;"></i>Estado
								</span>
								<select class="form-control" style="height: 40px; margin-left: 5px;" id="estado_documento" name="estado_documento" disabled>
									<option value="1" <?php echo $documentos['estado_documento'] ==  '1' ? 'selected' : ''; ?>>Habilitado</option>
									<option value="0" <?php echo $documentos['estado_documento'] ==  '0' ? 'selected' : ''; ?>>Deshabilitado</option>
								</select>
							</div>
						</div>						
						<!-- Botón de envío -->		
						<div class="form-group col-md-6">
							<button type="submit" name="eliminar_documento" class="btn btn-success" style="margin-top: 10px; margin-right: 40px; width: 10%; background-color: #28a745;">
								Grabar
							</button>
							<button type="button" class="btn btn-danger" style="margin-top: 10px; width: 10%; background-color: #EC7063;" onclick="window.location.href='03_AdministrarDocumentos.php';">
								Salir
							</button>	
						</div>					
									
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php require_once('../05_footer.php'); ?>
