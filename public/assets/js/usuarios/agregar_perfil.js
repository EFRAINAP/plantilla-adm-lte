$(document).ready(function() {
    console.log('=== INICIANDO agregar_perfil.js ===');
    console.log('BASE_URL:', typeof BASE_URL !== 'undefined' ? BASE_URL : 'NO DEFINIDO');
    
    // Definir BASE_URL si no está definido
    if (typeof BASE_URL === 'undefined') {
        window.BASE_URL = window.location.protocol + '//' + window.location.host + '/sistema-new';
        console.log('BASE_URL definido como fallback:', BASE_URL);
    }
    
    $('#proceso, #estado_perfil').select2({
        minimumResultsForSearch: Infinity, // Oculta el cuadro de búsqueda en select2 si no es necesario
        tags: true,
        placeholder: "Seleccione una opción",
        allowClear: true
    });
    // Inicializar el DataTable
    var table = $('#DocumentosTable').DataTable();

    // Evento de envío del formulario
    $('#FormAgregarPerfil').on('submit', function(e) {
        console.log('=== FORMULARIO ENVIADO ===');
        e.preventDefault(); // Prevenir el comportamiento predeterminado del formulario

        // Obtener valores de los campos del formulario
        let operacion = 'crear_perfil';		
        let perfil = $('#perfil').val();
        let proceso = $('#proceso').val();
        let estado_perfil = $('#estado_perfil').val();

        // Validaciones básicas
        if (!perfil || perfil.trim() === '') {
            alert('El campo Perfil es obligatorio');
            $('#perfil').focus();
            return;
        }

        if (!proceso || proceso.trim() === '') {
            alert('El campo Proceso es obligatorio');
            $('#proceso').focus();
            return;
        }

        // Array para almacenar los documentos con sus detalles
        let documentosConDetalles = [];

        // Usar la API de DataTable para iterar sobre todas las filas (incluso las no visibles)
        table.rows().every(function() {
            var row = $(this.node());  // Obtener la fila completa
            var checkbox = row.find('.documento-checkbox');

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
		// Enviar datos al servidor con AJAX
		console.log('Enviando datos:', {
			operacion,
			perfil,
			proceso,
			estado_perfil,
			documentos: documentosConDetalles
		});
		
		$.ajax({
			url: BASE_URL + '/app/ajax/perfiles/05_ajax_perfiles.php',
			type: 'POST',
			data: {
				operacion,
				perfil,
				proceso,
				estado_perfil,
				documentos: documentosConDetalles
			},
			dataType: 'json',
			success: function(response) {
				console.log('Respuesta del servidor:', response);
				if (response.success) { // Suponiendo que la respuesta tiene un campo 'success'
					console.log("Perfil creado con éxito. Redirigiendo...");
					window.location.href = BASE_URL + '/usuarios/perfiles';
				} else {
					console.error('Error en la respuesta:', response.message);
					alert(response.message); // Mostrar mensaje de error si existe
				}   
			},
			error: function(xhr, status, error) {
				console.error('Error AJAX:', {xhr, status, error});
				console.error('Respuesta del servidor:', xhr.responseText);
				alert("Ocurrió un error al procesar la solicitud: " + error);
			}
		});
	});
});