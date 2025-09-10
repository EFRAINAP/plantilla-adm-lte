<?php
	$title = 'ISO Asignados';
	$name_page_principal = '01_ListadoDocumentos.php';
	require_once BASE_PATH . '/app/core/00_load.php';
	$user = current_user();

    $all_perfil = find_table_field_array('acceso_perfiles', 'username', $user['username']);
	$primerPerfil = $all_perfil[0]['perfil'] ?? ''; // Primer perfil como valor predeterminado

	$urlraiz = find_table_field_only('constantes', 'nombre_constante', 'url_raiz');
	$carpetaiso = find_table_field_only('constantes', 'nombre_constante', 'carpeta_iso');

	ob_start();
?>
<div class="container-fluid">
	<div class="row mt-3 p-3">
		<div class="col-md-12">
			<?php echo display_msg($msg); ?>
		</div>
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading d-flex align-items-center justify-content-between flex-nowrap">
					<!-- Menú de longitud de menu -->
					<div class="lengthMenu">
						<div class="input-group">
							<span class="input-group-text"><i class="bi bi-grid-fill"></i><strong>Registros</strong></span>
							<select id="table_length" aria-controls="TablaDocumentos" class="form-control" style="width: 60px;">
								<option value="15">15</option>
								<option value="20">20</option>
								<option value="50">50</option>
								<option value="100">100</option>
							</select>
						</div>
					</div>
					<!-- Select de Perfil -->
					<div class="d-flex align-items-center me-2">
						<div class="input-group" style="width: auto;">
							<span class="input-group-text">
								<i class="bi bi-grid-fill"></i><strong> Perfil </strong>
							</span>                        	
							<select id="perfil" name="perfil" aria-controls="TablaTareas" class="form-control" style="width: 150px;">
								<?php 			
								$isFirstSelected = true; // Variable para verificar si ya se ha seleccionado el primer registro
								foreach ($all_perfil as $perfil): ?>
									<option value="<?php echo htmlspecialchars($perfil['perfil'], ENT_QUOTES, 'UTF-8'); ?>"
										<?php 
										// Selecciona el primer perfil automáticamente si no se ha seleccionado otro
										if ($isFirstSelected || (isset($selectedperfil) && $selectedperfil == $perfil['perfil'])) {
											echo 'selected';
											$isFirstSelected = false; // Desactiva la selección automática después del primer registro
										}
										?>>
										<?php echo htmlspecialchars($perfil['perfil'], ENT_QUOTES, 'UTF-8'); ?>
									</option>
								<?php endforeach; ?>
							</select>				
						</div>
					</div>							
					<!-- Botones de exportación de DataTables -->
					<div id="buttons" class="btn-group me-2">
						<!-- Botones de DataTables se insertarán aquí -->
					</div>
					<!-- Campo de búsqueda personalizado -->
					<div class="search-box">
						<div class="input-group">
							<span class="input-group-text"><i class="bi bi-grid-fill"></i><strong>Busqueda</strong></span>
							<input type="text" id="customSearch" class="form-control" placeholder="Buscar en la tabla...">
						</div>
					</div>
				</div>
			</div>				
			<div class="col-md-12 panel-body table-responsive mb-5">
				<table id="TablaDocumentos" class="nowrap custom-header-font-size custom-data-font-size">
					<thead>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Incluye el archivo JavaScript para DataTables -->

<?php
$content = ob_get_clean();

// Agregar scripts específicos para esta página
$pageScripts = '
<script>
    // Definir variables globales para JavaScript
    const BASE_URL = "' . BASE_URL . '";
</script>
<script type="text/javascript" src="' . BASE_URL . '/public/assets/js/iso/asignados.js"></script>
';

include __DIR__ . '/../../layouts/main.php';
?>
