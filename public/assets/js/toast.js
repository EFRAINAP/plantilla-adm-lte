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

export function showSwalToast(message, type = 'info') {
  const iconMap = {
    success: '✅',
    info: 'ℹ️',
    warning: '⚠️',
    danger: '❌'
  };

  const icon = iconMap[type] || iconMap['info'];

  const titleHtml = `
    
    <div class="w-100">
      <div class="alert alert-${type} alert-dismissible d-flex align-items-start gap-2 px-3 py-4" role="alert">
        <div class="fs-5 lh-1">${icon}</div>
        <div>${message}</div>
      </div>
    </div>
  `;

  Swal.fire({
    toast: true,
    position: 'top-end',
    html: titleHtml,
    showConfirmButton: false,
    timer: 6000,
    timerProgressBar: true,
    background: 'transparent',
    icon: undefined,
    customClass: {
      popup: `p-0 m-0 border-0 shadow-sm bg-transparent alert alert-${type}`,
      htmlContainer: 'p-0 m-0',
      timerProgressBar: `bg-${type} rounded-0`
    },
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
  });
}

export function showSwalToast2(message, type = 'info') { // versión simplificada o pasada
  // Mapeo de tipo → ícono Font Awesome
  const iconMap = {
    success: 'bi-check-circle-fill',
    info: 'bi-info-circle-fill',
    warning: 'bi-exclamation-triangle-fill',
    danger: 'bi-x-circle-fill'
  };

  const iconClass = iconMap[type] || iconMap['info'];

  const titleHtml = `
    <i class="fa-solid ${iconClass} me-2"></i> ${message}
  `;

  Swal.fire({
    toast: true,
    position: 'top-end',
    title: titleHtml,
    showConfirmButton: false,
    timer: 5000,
    timerProgressBar: true,
    icon: undefined, // Oculta el ícono nativo de SweetAlert2
    customClass: {
      popup: `text-bg-${type} border-0 shadow d-flex align-items-center`,
      title: 'fs-6 d-flex align-items-center',
      timerProgressBar: 'bg-white'
    },
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer);
      toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
  });
}