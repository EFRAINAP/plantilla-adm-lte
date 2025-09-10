
	$(document).ready(function() {
		// Configuración inicial
		let selectedUsername = null;
		
		// Función para mostrar loading
		function showLoading(tableId) {
			$(`#${tableId} tbody`).html('<tr><td colspan="100%" class="text-center"><div class="loading-spinner"></div><br>Cargando datos...</td></tr>');
		}

		// Función para mostrar notificaciones toast
		function showToast(message, type = 'info') {
			const toastHtml = `
				<div class="toast align-items-center text-bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
					<div class="d-flex">
						<div class="toast-body">${message}</div>
						<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
					</div>
				</div>
			`;
			
			if (!$('#toast-container').length) {
				$('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>');
			}
			
			const $toast = $(toastHtml);
			$('#toast-container').append($toast);
			const toast = new bootstrap.Toast($toast[0]);
			toast.show();
			
			setTimeout(() => $toast.remove(), 5000);
		}

		// Configuración mejorada de DataTable principal
		const table1 = $("#TablaUsuarios").DataTable({
			lengthMenu: [10, 25, 50], 
			responsive: true,
			destroy: true,
			processing: true,
			ajax: {
				url: BASE_URL + '/app/ajax/usuarios/03_ajax_usuarios.php',
				method: "POST",
				dataSrc: "",
				data: function(d) {
					d.operacion = 'obtener_todos_los_usuarios';
					return $.extend({}, d, {
					});
				},
				beforeSend: function() {
					showLoading('TablaUsuarios');
				},
				error: function(xhr, error, thrown) {
					showToast('Error al cargar los usuarios: ' + thrown, 'danger');
				}
			},
			deferRender: true,
			columns: [
				{data: "username", className: "fw-semibold"},
				{data: "user_level", render: function(data) {
					const levels = {
						1: "<span class='badge bg-blue text-dark'>Admin</span>",
						2: "<span class='badge bg-red text-dark'>Jefe</span>",
						3: "<span class='badge bg-yellow text-dark'>Usuario</span>"
					};
					return levels[data] || "<span class='badge bg-warning'>N/A</span>";
				}},
				{data: "name"},
				{data: "area",
					render: function(data) {
					const area = window.datosUsuarios.areas.find(a => a.area == data);
					return area ? area.descripcion_area : data;
					}
				},
				{data: "proceso", render: function(data) {
					const proceso = window.datosUsuarios.procesos.find(p => p.proceso == data);
					return proceso ? proceso.descripcion_proceso : data;
					}
				},
				{data: "cargo",
					render: function(data) {
						const cargo = window.datosUsuarios.puestos.find(c => c.cod_puesto == data);
						return cargo ? cargo.descripcion_puesto : data;
					}
				},
				{data: "last_login", render: function(data) {
					return data ? moment(data).format('DD/MM/YYYY HH:mm') : 'Nunca';
				}},
				{data: "estado_user", render: function(data) {
					const estados = {
						0: "<span class='badge bg-danger'>Deshabilitado</span>",
						1: "<span class='badge bg-success'>Habilitado</span>"
					};
					return estados[data] || "<span class='badge bg-warning'>N/A</span>";
				}},
				{data: null, render: function(data, type, row) {  
					return 	`<div class="btn-group" role="group">
								<button class="btn btn-sm btn-warning btnEditarUser" title="Editar usuario" data-bs-toggle="modal" data-bs-target="#Modal-Usuario">
									<i class="bi bi-pencil"></i>
								</button>
								<button data-username="${row.username}" class="btn btn-sm btn-danger btnEliminar" title="Eliminar usuario">
									<i class="bi bi-trash"></i>
								</button>
							</div>`;
				}},
				{data: null, render: function(data, type, row) {
					return `<div class="btn-group btn-group-sm" role="group">
								<button class="btn btn-warning btnAccesos" title="Control de accesos" data-bs-toggle="modal" data-bs-target="#Modal-Control-Accesos">
									<i class="bi bi-shield-lock"></i>
								</button>
							</div>`;
				}},
				{data: null, render: function(data, type, row) {
					return `<div class="btn-group btn-group-sm" role="group">
								<button class="btn bg-red btnAccesosPerfiles" title="Perfiles de usuario" data-bs-toggle="modal" data-bs-target="#Modal-Control-Perfiles">
									<i class="bi bi-person-lines-fill"></i>
								</button>
							</div>`;
				}}
			],
			order: [[1, 'asc'], [0, 'asc']],
			columnDefs: [
				{ width: "12%", targets: 0, className: "text-start" },   
				{ width: "8%", targets: 1, className: "text-center" },  
				{ width: "20%", targets: 2, className: "text-start" },   
				{ width: "15%", targets: 3, className: "text-start" },   
				{ width: "18%", targets: 4, className: "text-start" },   
				{ width: "12%", targets: 5, className: "text-start" },   
				{ width: "8%", targets: 6, className: "text-center" },  
				{ width: "7%", targets: 7, className: "text-center" },
				{ width: "7%", targets: 8, className: "text-center" }, 
				{ width: "7%", targets: 9, className: "text-center" }, 
			],
			dom: "Brtip",
			buttons: [
				{ 
					extend: "excel", 
					text: '<i class="bi bi-file-earmark-excel"></i> Excel', 
					className: "btn btn-success btn-sm",
					exportOptions: { columns: ":not(:last-child)" },
					title: 'Usuarios_' + moment().format('YYYY-MM-DD')
				},
				{ 
					extend: "pdf", 
					text: '<i class="bi bi-file-earmark-pdf"></i> PDF', 
					className: "btn btn-danger btn-sm",
					exportOptions: { columns: ":not(:last-child)" },
					title: 'Usuarios_' + moment().format('YYYY-MM-DD')
				},
				{ 
					extend: "copy", 
					text: '<i class="bi bi-clipboard"></i> Copiar', 
					className: "btn btn-info btn-sm",
					exportOptions: { columns: ":not(:last-child)" }
				}
			],
			language: {
				"zeroRecords": "No se encontraron usuarios registrados.",
				"info": "Mostrando _START_ a _END_ de _TOTAL_ usuarios",
				"infoEmpty": "No hay usuarios registrados.",
				"infoFiltered": "(filtrados de _MAX_ usuarios totales)",
				"loadingRecords": "Cargando usuarios...",
				"processing": "Procesando...",
				"search": "Buscar:",
				"paginate": {
					"previous": "Anterior",
					"next": "Siguiente"
				}
			}
		});
		// Configurar botones y búsqueda personalizada
		table1.buttons().container().appendTo('#buttons1'); 
		$('#customSearch1').on('keyup', function() {
			table1.search(this.value).draw();
		});

		// Configuración de DataTable de Perfiles (mejorada)
        const table3 = $('#TablaUsuariosPerfiles').DataTable({
            lengthMenu: [8, 15, 25], 
            responsive: true,
            destroy: true,
            processing: true,		
            ajax: {
                url: BASE_URL + "/app/ajax/usuarios/03_ajax_usuarios_perfiles.php",
				method: "POST",
				data: function(d) {
					d.operacion = 'obtener_perfiles_usuario';
					d.username = selectedUsername || '';
					return $.extend({}, d, {});
				},
                dataSrc: function(json) {
					if (json.success) {
						return json.data || [];
					} else {
						showToast(json.message || 'Error al cargar perfiles', 'danger');
						return [];
					}
				},
                beforeSend: function() {
					showLoading('TablaUsuariosPerfiles');
				}
            },
			deferRender: true,
            columns: [
                { data: 'username', className: "fw-semibold" },
                { data: 'perfil', render: function(data) {
                	return `<span class="badge bg-info">${data}</span>`;
                }},
				{ data: 'proceso' },
				{data: null, render: function(data, type, row) {  
					return 	`<div class="btn-group btn-group-sm" role="group">
								<button  class="btn btn-danger btnEliminarUsuariosPerfiles" title="Eliminar perfil">
									<i class="bi bi-trash"></i>
								</button>
							</div>`;	
				}}
			],
			order: [[0, 'asc'], [1, 'asc']],				
			columnDefs: [
				{ width: "15%", targets: 0, className: "text-start" },   
				{ width: "25%", targets: 1, className: "text-start" },
				{ width: "45%", targets: 2, className: "text-start" },				
				{ width: "15%", targets: 3, className: "text-center" }
			],
            dom: "Brtip",
			buttons: [
				{ extend: "excel", text: '<i class="bi bi-file-earmark-excel"></i> Excel', className: "btn btn-success btn-sm", exportOptions: {columns: ":not(:last-child)" }},
				{ extend: "pdf", text: '<i class="bi bi-file-earmark-pdf"></i> PDF', className: "btn btn-danger btn-sm", exportOptions: {columns: ":not(:last-child)" }},
				{ extend: "copy", text: '<i class="bi bi-clipboard"></i> Copiar', className: "btn btn-info btn-sm", exportOptions: {columns: ":not(:last-child)" }}
			],
			language: {
				"zeroRecords": "No hay perfiles asignados para este usuario.",
				"info": "Mostrando _START_ a _END_ de _TOTAL_ perfiles",
				"infoEmpty": "No hay perfiles registrados.",
				"infoFiltered": "(filtrados de _MAX_ perfiles totales)",
				"loadingRecords": "Cargando perfiles...",
				"processing": "Procesando...",
				"paginate": {
					"previous": "Anterior",
					"next": "Siguiente"
				}
			}
        });

		// Configurar botones y búsqueda para tabla de perfiles
		table3.buttons().container().appendTo('#buttons3'); 
		$('#customSearch3').on('keyup', function() {
			table3.search(this.value).draw();
		});

		// ==================== FUNCIONALIDADES DE INTERACCIÓN MEJORADAS ====================

		// Función mejorada para actualizar tablas hijas
		function actualizarRegistroTablasHijos() {
			const firstVisibleRowData = table1.rows({ filter: 'applied', order: 'applied', page: 'current' }).data()[0];			
			if (firstVisibleRowData && firstVisibleRowData.username) {
				const username = firstVisibleRowData.username;
				selectedUsername = username;
				// Actualizar tabla de perfiles
				table3.ajax.reload(null, false); // false = no resetear paginación
			} else {
				selectedUsername = null;
				// Limpiar tabla de perfiles
				table3.clear().draw();
			}
		}
	
		// Evento cuando se redibuja la tabla principal
		table1.on('draw', function() {
			actualizarRegistroTablasHijos();
		});	

		// Evento mejorado al hacer clic en una fila de usuarios
		$('#TablaUsuarios tbody').on('click', 'tr', function() {
			// Remover selección anterior y agregar selección actual
			$('#TablaUsuarios tbody tr').removeClass('table-warning');
			$(this).addClass('table-warning');
			
			const data = table1.row(this).data();
			if (data) {
				selectedUsername = data.username;
				// Actualizar tabla de perfiles inmediatamente
				table3.ajax.reload(null, false);
				// Mostrar información del usuario seleccionado
				showToast(`Usuario seleccionado: ${data.name} (${data.username})`, 'info');
			}
		});	

		// ==================== EVENTOS DE MODAL Y EDICIÓN ====================
		
		// Evento para abrir modal de edición con datos pre-cargados
		$(document).on('click', '.btnEditarUser', function() {
			const row = $(this).closest('tr');
			const data = table1.row(row).data();
			selectedUsername = data.username;
			configurarModalParaEditar(data);
			cargarDatosUsuarioEnSecuencia(data);
		});

		// Evento para abrir modal de agregar usuario
		$(document).on('click', '#AbrirModalAgregarUsuario', function() {
			configurarModalParaAgregar();
		});

		// Función para configurar modal para editar usuario
		function configurarModalParaEditar(userData) {
			$('#modalUsuarioLabel').text('Editar Usuario');
			$('#modalUsuarioSubtitle').text('Modificar información del usuario seleccionado');
			$('#titulo-password').html('<i class="bi bi-key-fill me-2"></i>Cambiar Contraseña (Opcional)');
			$('#label-password').html('<i class="bi bi-lock me-1"></i>Nueva Contraseña');
			$('#panel-info-basica').removeClass('border-info-subtle').addClass('border-warning-subtle');
			$('#btnEditarUsuario').show();
			$('#btnGuardarUsuario').hide();

			// Mostrar el botón "Cambiar Contraseña" y ocultar los campos inicialmente
			$('#btnMostrarCambiarPassword').show();
			$('#campos-password').hide();
			$('#btnCambiarPassword').show(); // Ocultar botón de cambiar contraseña
			
			// Llenar datos del usuario
			$('#username').val(userData.username).prop('readonly', true);
			$('#user_level').val(userData.user_level);
			$('#name').val(userData.name);
			$('#estado_user').val(userData.estado_user);
			
			// Los campos de contraseña no son requeridos en edición
			$('#password').prop('required', false).val('');
			$('#reenter_password').prop('required', false).val('');
		}

		function configurarModalParaAgregar() {
			$('#modalUsuarioLabel').text('Agregar Usuario');
			$('#modalUsuarioSubtitle').text('Ingrese la información del nuevo usuario');
			$('#titulo-password').html('<i class="bi bi-key-fill me-2"></i>Establecer Contraseña');
			$('#label-password').html('<i class="bi bi-lock me-1"></i>Contraseña');
			$('#panel-info-basica').removeClass('border-warning-subtle').addClass('border-info-subtle');

			// Ocultar el botón "Cambiar Contraseña" y mostrar los campos de contraseña
			$('#btnMostrarCambiarPassword').hide();
			$('#campos-password').show();
			$('#btnCambiarPassword').hide();
			$('#btnGuardarUsuario').show();
			$('#btnEditarUsuario').hide();

			// Limpiar formulario
			$('#username').val('').prop('readonly', false);
			$('#user_level').val('');
			$('#name').val('');
			$('#area').val('');
			$('#proceso').val('');
			$('#cargo').val('');
			$('#estado_user').val('');
			$('#password').val('').prop('required', true);
			$('#reenter_password').val('').prop('required', true);
			// Resetear selects dependientes
			$('#proceso').prop('disabled', true).html('<option value="">-- Seleccione proceso --</option>');
			$('#cargo').prop('disabled', true).html('<option value="">-- Seleccione cargo --</option>');
		}

		// Evento para mostrar/ocultar campos de cambio de contraseña en modo editar
		$(document).on('click', '#btnMostrarCambiarPassword', function() {
			const $campos = $('#campos-password');
			const $boton = $(this);
			
			if ($campos.is(':hidden')) {
				$campos.slideDown();
				$boton.html('<i class="bi bi-eye-slash me-1"></i>Ocultar Contraseña');
				$('#password').prop('required', true);
				$('#reenter_password').prop('required', true);
			} else {
				$campos.slideUp();
				$boton.html('<i class="bi bi-key me-1"></i>Cambiar Contraseña');
				$('#password').prop('required', false).val('');
				$('#reenter_password').prop('required', false).val('');
			}
		});

		// Evento para abrir modal de agregar perfil
		$(document).on('click', '#btnAgregarUserPerfiles', function() {
			if (!selectedUsername) {
				showToast('Por favor, selecciona un usuario primero', 'warning');
				return false;
			}
			
			// Cargar usuario en el modal
			$('#username-perfil').val(selectedUsername);
			
			// Cargar perfiles disponibles
			cargarPerfilesDisponibles();
			
			// Actualizar título del modal
			$('#modalAgregarPerfilLabel').text(`Agregar Perfil - ${selectedUsername}`);
		});
		
		// Función para cargar perfiles disponibles
		function cargarPerfilesDisponibles() {
			$.ajax({
				url: BASE_URL + '/app/ajax/usuarios/03_ajax_usuarios_perfiles.php',
				method: 'POST',
				data: { operacion: 'obtener_perfiles_disponibles' },
				dataType: 'json',
				beforeSend: function() {
					$('#perfil-usuario').html('<option value="">Cargando perfiles...</option>').prop('disabled', true);
				},
					success: function(response) {
					let options = '<option value="">Seleccionar perfil...</option>';
					
					if (response.success && response.data && response.data.length > 0) {
						response.data.forEach(function(perfil) {
							options += `<option value="${perfil.perfil}">${perfil.perfil} - ${perfil.proceso}</option>`;
						});
					} else {
						options += '<option value="">No hay perfiles disponibles</option>';
					}
					$('#perfil-usuario').html(options).prop('disabled', false);
				},
				error: function() {
					$('#perfil-usuario').html('<option value="">Error al cargar perfiles</option>').prop('disabled', false);
					showToast('Error al cargar los perfiles disponibles', 'danger');
				}
			});
		}

		// ==================== CONFIRMACIONES DE ELIMINACIÓN ====================
		
		// Confirmación para eliminar usuarios
		$(document).on('click', '.btnEliminar', function(e) {
			e.preventDefault();
			const username = $(this).data('username');
			
			if (confirm(`¿Está seguro de eliminar el usuario "${username}"?\n\nEsta acción no se puede deshacer.`)) {
				$.ajax({
					url: BASE_URL + '/app/ajax/usuarios/03_ajax_usuarios.php',
					method: 'POST',
					data: { 
						operacion: 'delete',
						username: username 
					},
					dataType: 'json',
					success: function(response) {
						showToast(response.message, response.error ? 'danger' : 'success');
						// Recargar la tabla principal
						table1.ajax.reload();
					},
					error: function(xhr, status, error) {
						showToast('Error al eliminar el usuario: ' + error, 'danger');
					}
				});
			}
		});
		
		// Confirmación para eliminar perfiles
		$(document).on('click', '.btnEliminarUsuariosPerfiles', function(e) {
			e.preventDefault();
			var data = table3.row($(this).parents('tr')).data();
			
			if (data) {
				if (confirm(`¿Eliminar perfil "${data.perfil}" para el usuario "${data.username}"?`)) {
					// Redirigir a la página de eliminación de perfiles
					$.ajax({
						url: BASE_URL + '/app/ajax/usuarios/03_ajax_usuarios_perfiles.php',
						method: 'POST',
						data: {
							username: data.username,
							perfil: data.perfil,
							operacion: 'eliminar_perfil'
						},
						success: function(response) {
							showToast('Perfil eliminado correctamente', 'success');
							table3.ajax.reload();
						},
						error: function(xhr, status, error) {
							showToast('Error al eliminar el perfil: ' + error, 'danger');
						}
					});
				}
			}
		});

		// ==================== VALIDACIÓN DE FORMULARIOS ====================
		
		// Validación de contraseñas en el modal editar usuario
		$('#reenter_password').on('blur', function() {
			const password = $('#password').val();
			const reenterPassword = $(this).val();
			
			if (password && reenterPassword && password !== reenterPassword) {
				$(this).addClass('is-invalid');
				showToast('Las contraseñas no coinciden', 'warning');
			} else {
				$(this).removeClass('is-invalid');
			}
		});

		// Validación de contraseñas en el modal agregar usuario
		$('#reenter_password-agregar').on('blur', function() {
			const password = $('#password-agregar').val();
			const reenterPassword = $(this).val();
			
			if (password && reenterPassword && password !== reenterPassword) {
				$(this).addClass('is-invalid');
				showToast('Las contraseñas no coinciden', 'warning');
			} else {
				$(this).removeClass('is-invalid');
			}
		});

		// ==================== FUNCIONALIDADES ENVIO DE FORMULARIOS ====================

		// Enviar formulario de agregar usuario
		$('#btnGuardarUsuario').on('click', function(e) {
			e.preventDefault();
			const username = $('#username').val();
			const userLevel = $('#user_level').val();
			const name = $('#name').val();
			const area = $('#area').val();
			const proceso = $('#proceso').val();
			const cargo = $('#cargo').val();
			const estadoUser = $('#estado_user').val();
			const password = $('#password').val();
			const reenterPassword = $('#reenter_password').val();
			if (username && userLevel && name && area && proceso && cargo && estadoUser && password && reenterPassword) {
				if (password === reenterPassword) {
					$.ajax({
						url: BASE_URL + '/app/ajax/usuarios/03_ajax_usuarios.php',
						method: 'POST',
						data: {
							username: username,
							user_level: userLevel,
							name: name,
							proceso: proceso,
							area: area,
							cargo: cargo,
							estado_user: estadoUser,
							password: password,
							operacion: 'create'
						},
						dataType: 'json',
						success: function(response) {
							$('#Modal-Usuario').modal('hide');
							showToast(response.message, response.error ? 'danger' : 'success');
							table1.ajax.reload();
						},
					});
				} else {
					showToast('Las contraseñas no coinciden', 'warning');
				}
			} else {
				showToast('Por favor, completa todos los campos requeridos', 'warning');	
			}
		});

		// Enviar formulario de edición
		$('#btnEditarUsuario').on('click', function(e) {
			e.preventDefault();
			const username = $('#username').val();
			const userLevel = $('#user_level').val();
			const name = $('#name').val();
			const area = $('#area').val();
			const proceso = $('#proceso').val();
			const cargo = $('#cargo').val();
			const estadoUser = $('#estado_user').val();

			if (username && userLevel && name && area && proceso && cargo && estadoUser) {
				$.ajax({
					url: BASE_URL + '/app/ajax/usuarios/03_ajax_usuarios.php',
					method: 'POST',
					data: {
						operacion: 'update',
						username: username,
						user_level: userLevel,
						name: name,
						area: area,
						proceso: proceso,
						cargo: cargo,
						estado_user: estadoUser
					},
					dataType: 'json',
					success: function(response) {
						$('#Modal-Usuario').modal('hide');
						showToast(response.message, response.error ? 'danger' : 'success');
						table1.ajax.reload();
					},
					error: function(xhr, status, error) {
						showToast('Error al actualizar usuario: ' + error, 'danger');
					}
				});
			} else {
				showToast('Por favor, completa todos los campos requeridos', 'warning');	
			}
		});

		// Enviar formulario de cambio de contraseña
		$('#btnCambiarPassword').on('click', function(e) {
			e.preventDefault();

			const password = $('#password').val();
			const reenterPassword = $('#reenter_password').val();
			const username = $('#username').val();
			if (password && reenterPassword && password === reenterPassword) {
				$.ajax({
					url: BASE_URL + '/app/ajax/usuarios/03_ajax_usuarios.php',
					method: 'POST',
					data: {
						operacion: 'cambiar_password',
						password: password,
						username: username
					},
					dataType: 'json',
					success: function(response) {
						$('#Modal-Usuario').modal('hide');
						showToast(response.message, response.error ? 'danger' : 'success');
						table1.ajax.reload();
					},
					error: function(xhr, status, error) {
						showToast('Error al cambiar contraseña: ' + error, 'danger');
					}
				});
			} else {
				showToast('Por favor, completa todos los campos requeridos', 'warning');
			}
		});

		// Enviar formulario de agregar perfil
		$('#btnAgregarPerfil').on('click', function(e) {
			e.preventDefault();
			
			const username = $('#username-perfil').val();
			const perfil = $('#perfil-usuario').val();
			
			// Validaciones mejoradas
			if (!username) {
				showToast('No se puede identificar el usuario', 'danger');
				return;
			}
			
			if (!perfil) {
				showToast('Por favor seleccione un perfil', 'warning');
				$('#perfil-usuario').focus();
				return;
			}
			
			// Enviar datos al servidor
			const $btn = $(this);
			$.ajax({
				url: BASE_URL + '/app/ajax/usuarios/03_ajax_usuarios_perfiles.php',
				method: 'POST',
				data: {
					username: username,
					perfil: perfil,
					operacion: 'asignar_perfil'
				},
				dataType: 'json',
				beforeSend: function() {
					$btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Agregando...');
				},
				success: function(response) {
					$('#Modal-Agregar-Perfil').modal('hide');
					// Mostrar mensaje de éxito
					showToast(response.message, response.error ? 'danger' : 'success');
					table3.ajax.reload();
					
					// Limpiar formulario
					$('#perfil-usuario').val('');
				},
				error: function(xhr, status, error) {
					let errorMsg = 'Error al agregar el perfil';
					if (xhr.responseText && xhr.responseText.includes('ya ha sido registrado')) {
						errorMsg = 'Este perfil ya está asignado al usuario';
					}
					showToast(errorMsg, 'danger');
				},
				complete: function() {
					$btn.prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>Agregar Perfil');
				}
			});
		});
		
		// Limpiar modal al cerrarlo
		$('#Modal-Agregar-Perfil').on('hidden.bs.modal', function() {
			$('#perfil-usuario').val('');
		});

		// ===================== CONFIGURACIÓN DE PERMISOS =====================
		
		// Función para cargar permisos desde todas las páginas disponibles
		function cargarPermisosDesdeTabla2() {
			$('#tabla-permisos tbody').empty();
			$('#resumen-permisos').text('Cargando páginas...');
			
			if (!selectedUsername) {
				$('#tabla-permisos tbody').html('<tr><td colspan="7" class="text-center text-muted">No hay usuario seleccionado</td></tr>');
				$('#resumen-permisos').text('No hay usuario seleccionado');
				return;
			}
			
			$.ajax({
				url: BASE_URL + '/app/ajax/usuarios/03_ajax_todas_paginas.php',
				method: 'POST',
				data: { username: selectedUsername, operacion: 'obtener_todas_paginas' },
				dataType: 'json',
				success: function(paginas) {
					if (!Array.isArray(paginas) || paginas.length === 0) {
						$('#tabla-permisos tbody').html('<tr><td colspan="7" class="text-center text-muted">No hay páginas disponibles</td></tr>');
						$('#resumen-permisos').text('No hay páginas disponibles');
						return;
					}
					
					let html = '';
					paginas.forEach(function(p) {
						html += `
							<tr data-pagina="${p.pagina}" data-username="${selectedUsername}">
								<td><input type="checkbox" class="form-check-input page-checkbox" ${p.tiene_acceso ? 'checked' : ''}></td>
								<td><small class="text-muted">${p.descripcion_pagina || 'Sin descripción'}</small></td>
								<td class="text-center"><div class="form-check form-switch"><input class="form-check-input permiso-switch" type="checkbox" data-permiso="editar" ${p.editar ? 'checked' : ''}></div></td>
								<td class="text-center"><div class="form-check form-switch"><input class="form-check-input permiso-switch" type="checkbox" data-permiso="eliminar" ${p.eliminar ? 'checked' : ''}></div></td>
								<td class="text-center"><div class="form-check form-switch"><input class="form-check-input permiso-switch" type="checkbox" data-permiso="adicionar" ${p.adicionar ? 'checked' : ''}></div></td>
								<td class="text-center"><div class="form-check form-switch"><input class="form-check-input permiso-switch" type="checkbox" data-permiso="seguimiento" ${p.seguimiento ? 'checked' : ''}></div></td>
							</tr>`;
					});
					
					$('#tabla-permisos tbody').html(html);
					actualizarResumenPermisos();
					configurarEventosModalPermisos();
				},
				error: function() {
					$('#tabla-permisos tbody').html('<tr><td colspan="7" class="text-center text-danger">Error al cargar páginas</td></tr>');
					$('#resumen-permisos').text('Error al cargar páginas');
					showToast('Error al cargar las páginas disponibles', 'danger');
				}
			});
		}
		
		// Función para actualizar el resumen de permisos
		function actualizarResumenPermisos() {
			const checkboxes = $('#tabla-permisos .page-checkbox:checked');
			const total = checkboxes.length;
			
			if (total === 0) {
				$('#resumen-permisos').text('No hay páginas seleccionadas');
				return;
			}
			
			let permisos = { editar: 0, eliminar: 0, adicionar: 0, seguimiento: 0 };
			
			checkboxes.each(function() {
				const row = $(this).closest('tr');
				['editar', 'eliminar', 'adicionar', 'seguimiento'].forEach(p => {
					if (row.find(`[data-permiso="${p}"]`).prop('checked')) permisos[p]++;
				});
			});
			
			$('#resumen-permisos').html(
				`<strong>${total}</strong> páginas seleccionadas | 
				 Editar: <span class="badge bg-warning">${permisos.editar}</span> | 
				 Eliminar: <span class="badge bg-danger">${permisos.eliminar}</span> | 
				 Adicionar: <span class="badge bg-success">${permisos.adicionar}</span> | 
				 Seguimiento: <span class="badge bg-info">${permisos.seguimiento}</span>`
			);
		}
		
		// Configurar eventos del modal de permisos (versión optimizada)
		function configurarEventosModalPermisos() {
			// Eventos delegados para mejor rendimiento
			$('#tabla-permisos')
				.off('change', '.page-checkbox').on('change', '.page-checkbox', function() {
					const row = $(this).closest('tr');
					if (!$(this).prop('checked')) {
						row.find('.permiso-switch').prop('checked', false);
					}
					actualizarResumenPermisos();
				})
				.off('change', '.permiso-switch').on('change', '.permiso-switch', function() {
					const row = $(this).closest('tr');
					const pageCheckbox = row.find('.page-checkbox');
					if ($(this).prop('checked') && !pageCheckbox.prop('checked')) {
						pageCheckbox.prop('checked', true);
					}
					actualizarResumenPermisos();
				});
			
			// Checkbox maestro
			$('#check-all-pages').off('change').on('change', function() {
				const isChecked = $(this).prop('checked');
				$('#tabla-permisos .page-checkbox').prop('checked', isChecked);
				if (!isChecked) $('#tabla-permisos .permiso-switch').prop('checked', false);
				actualizarResumenPermisos();
			});
			
			// Botones de control masivo
			$('#seleccionar-todas').off('click').on('click', () => {
				$('#tabla-permisos .page-checkbox, #check-all-pages').prop('checked', true);
				actualizarResumenPermisos();
			});
			
			$('#limpiar-seleccion').off('click').on('click', () => {
				$('#tabla-permisos .page-checkbox, #tabla-permisos .permiso-switch, #check-all-pages').prop('checked', false);
				actualizarResumenPermisos();
			});
			
			$('#permisos-completos').off('click').on('click', () => {
				$('#tabla-permisos .page-checkbox, #tabla-permisos .permiso-switch, #check-all-pages').prop('checked', true);
				actualizarResumenPermisos();
			});
			
			// Filtro de búsqueda optimizado
			$('#buscar-paginas').off('input').on('input', function() {
				const filtro = $(this).val().toLowerCase();
				$('#tabla-permisos tbody tr').each(function() {
					const texto = $(this).find('td:eq(1), td:eq(2)').text().toLowerCase();
					$(this).toggle(texto.includes(filtro));
				});
			});
		}
		
		// Evento para abrir modal de control de accesos
		$(document).on('click', '.btnAccesos', function() {
			if (!selectedUsername) {
				showToast('Por favor, selecciona un usuario primero', 'warning');
				return false;
			}
			
			// Actualizar título del modal con el usuario seleccionado
			$('#Modal-Control-Accesos .modal-title').text(`Control de Accesos - ${selectedUsername}`);
			
			// Esperar un momento para que el modal se abra completamente
			setTimeout(function() {
				cargarPermisosDesdeTabla2();
			}, 300);
		});
		
		// Función para guardar los accesos modificados
		$('#btnGuardarAccesos').off('click').on('click', function() {
			if (!selectedUsername) {
				showToast('No hay usuario seleccionado', 'danger');
				return;
			}
			
			const permisos = [];
			$('#tabla-permisos tbody tr').each(function() {
				const $row = $(this);
				const pagina = $row.data('pagina');
				const isSelected = $row.find('.page-checkbox').prop('checked');
				
				if (isSelected && pagina) {
					permisos.push({
						username: selectedUsername,
						pagina: pagina,
						editar: $row.find('[data-permiso="editar"]').prop('checked') ? 1 : 0,
						eliminar: $row.find('[data-permiso="eliminar"]').prop('checked') ? 1 : 0,
						adicionar: $row.find('[data-permiso="adicionar"]').prop('checked') ? 1 : 0,
						seguimiento: $row.find('[data-permiso="seguimiento"]').prop('checked') ? 1 : 0
					});
				}
			});
			
			// Enviar al servidor
			const $btn = $(this);
			$.ajax({
				url: BASE_URL + '/app/ajax/usuarios/03_ajax_todas_paginas.php',
				method: 'POST',
				dataType: 'json',
				data: {
					username: selectedUsername,
					permisos: JSON.stringify(permisos),
					operacion: 'asignar_acceso'
				},
				beforeSend: () => $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Guardando...'),
				success: function(response) {
					$('#Modal-Control-Accesos').modal('hide');
					showToast(response.message, response.error ? 'danger' : 'success');
				},
				error: (xhr, status, error) => showToast('Error al actualizar accesos: ' + error, 'danger'),
				complete: () => $btn.prop('disabled', false).html('<i class="bi bi-check-circle me-2"></i>Guardar Accesos')
			});
		});

		// === LÓGICA DE SELECTS DEPENDIENTES ===
		function inicializarSelectsDependientes() {
			const areas = window.datosUsuarios.areas;
			const selectArea = document.getElementById('area');

			// Cargar áreas iniciales
			areas.forEach(area => {
				let opt = document.createElement('option');
				opt.value = area.area;
				opt.textContent = area.descripcion_area;
				selectArea.appendChild(opt);
			});
			
			// Evento cuando cambia el área
			selectArea.addEventListener('change', function() {
				cargarProcesos(this.value);
			});

			// Evento cuando cambia el proceso
			document.getElementById('proceso').addEventListener('change', function() {
				cargarCargos(this.value);
			});
		}

		// Función auxiliar para cargar datos en secuencia
		function cargarDatosUsuarioEnSecuencia(userData) {
			// 1. Primero cargar área
			$('#area').val(userData.area);
			
			// 2. Cargar procesos del área seleccionada
			cargarProcesos(userData.area, function() {
				// 3. Una vez cargados los procesos, seleccionar el proceso del usuario
				$('#proceso').val(userData.proceso);
				
				// 4. Cargar cargos del proceso seleccionado
				cargarCargos(userData.proceso, function() {
					// 5. Una vez cargados los cargos, seleccionar el cargo del usuario
					$('#cargo').val(userData.cargo);
				});
			});
		}

		// Función para cargar procesos por área
		function cargarProcesos(areaSeleccionada, callback) {
			const procesos = window.datosUsuarios.procesos;
			const selectProceso = document.getElementById('proceso');
			
			// Limpiar y deshabilitar proceso y cargo
			selectProceso.innerHTML = '<option value="">-- Seleccione proceso --</option>';
			$('#cargo').prop('disabled', true).html('<option value="">-- Seleccione cargo --</option>');
			
			if (areaSeleccionada) {
				// Filtrar procesos por área
				const filteredProcesos = procesos.filter(p => p.area == areaSeleccionada);
				
				// Agregar procesos al select
				filteredProcesos.forEach(proc => {
					let opt = document.createElement('option');
					opt.value = proc.proceso;
					opt.textContent = proc.descripcion_proceso;
					selectProceso.appendChild(opt);
				});
				
				selectProceso.disabled = false;
				
				// Ejecutar callback
				if (callback) callback();
			} else {
				selectProceso.disabled = true;
			}
		}

		// Función para cargar cargos por proceso
		function cargarCargos(procesoSeleccionado, callback) {
			const puestos = window.datosUsuarios.puestos;
			const selectCargo = document.getElementById('cargo');
			
			// Limpiar cargo
			selectCargo.innerHTML = '<option value="">-- Seleccione cargo --</option>';
			
			if (procesoSeleccionado) {
				// Filtrar cargos por proceso
				const filteredCargos = puestos.filter(p => p.proceso == procesoSeleccionado);
				
				// Agregar cargos al select
				filteredCargos.forEach(cargo => {
					let opt = document.createElement('option');
					opt.value = cargo.cod_puesto;
					opt.textContent = cargo.descripcion_puesto;
					selectCargo.appendChild(opt);
				});
				
				selectCargo.disabled = false;
				
				// Ejecutar callback
				if (callback) callback();
			} else {
				selectCargo.disabled = true;
			}
		}

		// Inicializar cuando el DOM esté listo
		inicializarSelectsDependientes();

	});
