$(document).ready(function() {
	// Inicializar el primer DataTable
	let perfil_Global = null; // Variable global para almacenar el perfil seleccionado

	var table1 = $('#Tabla_Perfiles').DataTable({
		lengthMenu: [12],
		responsive: false,
		destroy: true,
		processing: true,			
		ajax: {
			url: BASE_URL + "/app/ajax/perfiles/05_ajax_perfiles.php",
			method: "POST",
			dataSrc: "",
			data: function(d) {
				d.operacion = 'listar_perfiles';
			},
		},
		columns: [
			{data: 'perfil' },
			{data: 'proceso' },
			{data: "estado_perfil", render: function(data) {const estados = {
				0: "<span class='badge bg-danger' style='height: 24px; display: inline-flex; align-items: center;'>Deshabilitado</span>",
				1: "<span class='badge bg-success' style='height: 24px; display: inline-flex; align-items: center;'>Habilitado</span>"
				};return estados[data] || "";}},
			{data: null, render: function(data, type, row) {  
				return 	'<a href="05_CopiarPerfil.php?perfil=' + row.perfil + '" class="btn btn-success"> <i class="bi bi-clipboard"></i></a>' +
						'<a href="' + BASE_URL + '/usuarios/perfiles/agregar/editar_perfil?perfil=' + row.perfil + '" class="btn btn-warning"> <i class="bi bi-pencil"></i></a>' +
						'<button class="btn btn-danger eliminar-perfil"> <i class="bi bi-trash"></i></button>';}}				
		],
		deferRender: true,
		dom: "Brtip",
		buttons: [
			{ extend: "excel", text: '<i class="bi bi-file-earmark-excel"></i> EXCEL', className: "btn btn-success btn-sm", exportOptions: {columns: ":not(:last-child)" }},
			{ extend: "pdf", text: '<i class="bi bi-file-earmark-pdf"></i> PDF', className: "btn btn-danger btn-sm", exportOptions: {columns: ":not(:last-child)" }},
			{ extend: "copy", text: '<i class="bi bi-clipboard"></i> Copy', className: "btn btn-info btn-sm", exportOptions: {columns: ":not(:last-child)" }}
		],
		language: {
			"zeroRecords": "Lo sentimos. No se encontraron registros.",
			"info": "Mostrando página _PAGE_ de _PAGES_",
			"infoEmpty": "No hay registros aún.",
			"infoFiltered": "(filtrados de un total de _MAX_ registros)",
			"LoadingRecords": "Cargando ...",
			"Processing": "Procesando...",
			"paginate": {
				"previous": "Anterior",
				"next": "Siguiente", 
				}
		},
		autoWidth: false, // Desactiva el ajuste automático del ancho de columna
		columnDefs: [
			{ width: "20%", targets: 0, className: "text-start" },   
			{ width: "20%", targets: 1, className: "text-start" },  
			{ width: "30%", targets: 2, className: "text-start" },   
			{ width: "30%", targets: 3, className: "text-start" }  
		],
	});

	// Inicializar el segundo DataTable (sin perfil al principio)
	var table2 = $('#Tabla_Perfiles_Detalle').DataTable({
		lengthMenu: [15], 
		responsive: false,
		destroy: true,				
		ajax: {
			url: BASE_URL + "/app/ajax/perfiles/05_ajax_perfiles.php",
			dataSrc: "",
			method: "POST",
			data: function(d) {
				d.operacion = 'obtener_detalle_perfil';
				d.perfil = perfil_Global; // Usar la variable global
			}
		},
		columns: [
			{ data: 'cod_documento' },
			{ data: 'descripcion_documento' },
			{ data: 'modo_almacenamiento' },
		],
		deferRender: true,
		dom: "Brtip",
		buttons: [
			{ extend: "excel", text: '<i class="bi bi-file-earmark-excel"></i> EXCEL', className: "btn btn-success btn-sm", exportOptions: {columns: ":not(:last-child)" }},
			{ extend: "pdf", text: '<i class="bi bi-file-earmark-pdf"></i> PDF', className: "btn btn-danger btn-sm", exportOptions: {columns: ":not(:last-child)" }},
			{ extend: "copy", text: '<i class="bi bi-clipboard"></i> Copy', className: "btn btn-info btn-sm", exportOptions: {columns: ":not(:last-child)" }}
		],
		language: {
			"zeroRecords": "Lo sentimos. No se encontraron registros.",
			"info": "Mostrando página _PAGE_ de _PAGES_",
			"infoEmpty": "No hay registros aún.",
			"infoFiltered": "(filtrados de un total de _MAX_ registros)",
			"LoadingRecords": "Cargando ...",
			"Processing": "Procesando...",
			"paginate": {
				"previous": "Anterior",
				"next": "Siguiente", 
				}
		},
		autoWidth: false, // Desactiva el ajuste automático del ancho de columna
		columnDefs: [
			{ width: "20%", targets: 0, className: "text-start" },   
			{ width: "35%", targets: 1, className: "text-start" },  
			{ width: "45%", targets: 2, className: "text-start" },
		],
	});

	// Mueve los botones de DataTables al panel-heading / tabla buttons
	table1.buttons().container().appendTo('#buttons1'); 
	// Aplica búsqueda personalizada
	$('#customSearch1').on('keyup', function() {
		table1.search(this.value).draw();
	});
	
	// Mueve los botones de DataTables al panel-heading / tabla buttons
	table2.buttons().container().appendTo('#buttons2'); 
	// Aplica búsqueda personalizada
	$('#customSearch2').on('keyup', function() {
		table2.search(this.value).draw();
	});
	
	// Función para cargar el detalle basado en el primer registro visible en la página actual, incluyendo búsqueda y ordenamiento
	function actualizarDetalleConPrimerRegistroVisible() {
		var firstVisibleRowData = table1.rows({ filter: 'applied', order: 'applied', page: 'current' }).data()[0];
		if (firstVisibleRowData) {
			var perfil = firstVisibleRowData.perfil;
			perfil_Global = perfil; // Actualizar la variable global

			console.log("Primer registro visible en la tabla padre:", perfil_Global);

			// Actualizar la URL y cargar los datos en Tabla Hijo
			table2.ajax.reload(null, false); // false para mantener la paginación actual
		} else {
			perfil_Global = null; // Restablecer la variable global si no hay registro visible
			table2.clear().draw(); // Limpiar la tabla hijo si no hay registro visible
			console.log("No hay registros visibles en la tabla padre.");
		}
	}
	// Llamar a la función cuando se dibuja la tabla, para soportar paginación, búsqueda y ordenamiento
	table1.on('draw', function() {
		actualizarDetalleConPrimerRegistroVisible();
	});
	
	// Actualizar Tabla Hijo al hacer clic en una fila de Tabla Padre
	$('#Tabla_Perfiles tbody').on('click', 'tr', function() {
		var data = table1.row(this).data();
		if (data) {
			perfil_Global = data.perfil; // Actualizar la variable global
			table2.ajax.reload(null, false); // false para mantener la paginación actual
		}
	});

	// eliminar perfil
	$('#Tabla_Perfiles tbody').on('click', '.eliminar-perfil', function(e) {
		e.preventDefault();
		var data = table1.row($(this).parents('tr')).data();
		if (data) {
			var perfil = data.perfil;
			if (confirm("¿Estás seguro de que deseas eliminar el perfil: " + perfil + "?")) {
				$.post( BASE_URL + '/app/ajax/perfiles/05_ajax_perfiles.php', {
					operacion: 'eliminar_perfil',
					perfil: perfil
				}, function(response) {
					if (response.success) {
						table1.ajax.reload(null, false); // Recargar la tabla sin reiniciar la paginación
					} else {
						alert("Error al eliminar el perfil.");
					}
				}, 'json');
			}
		}
	});
});