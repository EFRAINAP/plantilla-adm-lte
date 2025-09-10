$(document).ready(function() {

	var table = $("#TablaDocumentos").DataTable({
		lengthMenu: [15], 
		responsive: true,
		destroy: true,
        processing: true,
        autoWidth: false,
		order: [[4, 'asc']], // Columna
		ajax: {
			url: BASE_URL + "/app/ajax/iso/06_ajax_documentostransversales.php", 
			method: "POST",
			dataSrc: "",
			data: function(d) {
			}
		},
		deferRender: true,
		columns: [
			{data: "area", title: 'Área' , className: 'text-start text-bold'},
			{data: "proceso", title: 'Proceso', className: 'text-start text-bold'},
			{data: "tipo_documento", title: 'Tipo Documento', className: 'text-start text-bold'},
			{data: "cod_documento", title: 'Código', className: 'text-start text-bold'},
			{data: "descripcion_documento", title: 'Descripción Documento', className: 'text-start text-bold'},
			{data: "version", title: 'Versión', className: 'text-start text-bold'},
			{data: "fecha", title: 'Fecha', className: 'text-start text-bold'},
			{data: "frecuencia", title: 'Frecuencia', className: 'text-start text-bold'},
			{data: "transversal", title: 'Transversal', className: 'text-start text-bold'},
			{data: "visualizacion", title: 'Visualización', className: 'text-start text-bold'},
			{data: "responsable", title: 'Responsable', className: 'text-start text-bold'},
			{data: "estado_documento", title: 'Estado', className: 'text-start text-bold', render: function(data) {const estados = {
				0: "<span class='badge bg-danger' style='height: 24px; display: inline-flex; align-items: center;'>Deshabilitado</span>",
				1: "<span class='badge bg-success' style='height: 24px; display: inline-flex; align-items: center;'>Habilitado</span>"
				};return estados[data] || "";}},
            {data: "nombre_archivo", title: 'Nombre Archivo', className: 'text-start text-bold', render: function(data, type, row) {
				var d = {};
				d.var_url = "<?php echo $urlraiz['valor_constante'] . $carpetaiso['valor_constante']; ?>";
				if (row.visualizacion === 'Lectura') {
					return '<a href="../Components/PDFViewer/UnifiedDocumentViewer.php?id=' + row.cod_documento + '&tipo=documento&categoria=iso" class="btn btn-danger btn-sm" type="button" style="margin: 1px" title="Mostrar" data-bs-toggle="tooltip">Lectura <i class="bi bi-file-pdf"></i></a>';
				} 
				else if (row.visualizacion === 'Descarga') {
					return '<a href="' + d.var_url + encodeURIComponent(row.nombre_archivo) + '" ' + 'class="btn btn-warning btn-sm" type="button" style="margin: 1px" title="Formato" data-bs-toggle="tooltip">Descarga <i class="bi bi-filetype-doc"></i></a>';
				} 
				else if (row.visualizacion === 'Impreso') {
					return '<span class="btn btn-secondary btn-sm" type="button" style="margin: 1px" title="Impreso" data-bs-toggle="tooltip">Impreso <i class="bi bi-file-ppt"></i></span>';
				} 
				else {
					return '<span class="text-muted">No disponible</span>';
				}
			}},
		],
		columnDefs: [
            {targets: -1, data: "nombre_archivo", responsivePriority: 1}					
		],
		dom: "Brtip",
		buttons: [
			{ extend: "excel", text: '<i class="bi bi-file-earmark-excel"></i> EXCEL', className: "btn btn-success btn-sm", exportOptions: {columns: ":not(:last-child)" }},
			{ extend: "pdf", text: '<i class="bi bi-file-earmark-pdf"></i> PDF', className: "btn btn-danger btn-sm", exportOptions: {columns: ":not(:last-child)" }},
			{ extend: "copy", text: '<i class="bi bi-clipboard"></i> Copy', className: "btn btn-info btn-sm", exportOptions: {columns: ":not(:last-child)" }}
		],
		language: {
			zeroRecords: "Lo sentimos. No se encontraron registros.",
			info: "Mostrando página _PAGE_ de _PAGES_",
			infoEmpty: "No hay registros aún.",
			infoFiltered: "(filtrados de un total de _MAX_ registros)",
			LoadingRecords: "Cargando ...",
			Processing: "Procesando...",
			paginate: {
				"previous": "Anterior",
				"next": "Siguiente", 
			}
		}	
	});			
					
	// Inicializar Select2 en el elemento select con id #table_length
	$('#table_length').select2({
	minimumResultsForSearch: Infinity // Oculta el cuadro de búsqueda en select2 si no es necesario
	});
	// Actualizar la longitud de la página en DataTables cuando cambie el select
	$('#table_length').on('change', function() {
		var length = $(this).val();
		table.page.len(length).draw();  // Cambia la longitud de página de DataTables
	});
	// Mueve los botones de DataTables al panel-heading / tabla buttons
	table.buttons().container().appendTo('#buttons'); 
	// Aplica búsqueda personalizada
	$('#customSearch').on('keyup', function() {
		table.search(this.value).draw();
	});				
});