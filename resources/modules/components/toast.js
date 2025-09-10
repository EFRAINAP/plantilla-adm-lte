// Función para mostrar notificaciones toast

export function showToast(message, type = 'info') {
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