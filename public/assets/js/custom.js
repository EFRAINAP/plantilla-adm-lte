/**
 * Funciones JavaScript personalizadas para el sistema
 */

// Configuración global de SweetAlert2
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

/**
 * Muestra una notificación toast
 */
function showToast(type, message) {
    Toast.fire({
        icon: type,
        title: message
    });
}

/**
 * Muestra un diálogo de confirmación
 */
function showConfirmDialog(title, text, confirmButtonText = 'Sí', cancelButtonText = 'Cancelar') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText
    });
}

/**
 * Muestra un loading overlay
 */
function showLoading(message = 'Cargando...') {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

/**
 * Oculta el loading overlay
 */
function hideLoading() {
    Swal.close();
}

/**
 * Manejo de formularios con AJAX
 */
function submitFormAjax(formElement, options = {}) {
    const defaults = {
        showLoading: true,
        loadingMessage: 'Procesando...',
        successMessage: 'Operación exitosa',
        errorMessage: 'Error al procesar la solicitud',
        onSuccess: null,
        onError: null
    };
    
    const config = Object.assign(defaults, options);
    
    if (config.showLoading) {
        showLoading(config.loadingMessage);
    }
    
    const formData = new FormData(formElement);
    
    fetch(formElement.action, {
        method: formElement.method || 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (config.showLoading) {
            hideLoading();
        }
        
        if (data.success) {
            showToast('success', data.message || config.successMessage);
            if (config.onSuccess) {
                config.onSuccess(data);
            }
        } else {
            showToast('error', data.message || config.errorMessage);
            if (config.onError) {
                config.onError(data);
            }
        }
    })
    .catch(error => {
        if (config.showLoading) {
            hideLoading();
        }
        console.error('Error:', error);
        showToast('error', config.errorMessage);
        if (config.onError) {
            config.onError(error);
        }
    });
}

/**
 * Confirmar eliminación
 */
function confirmDelete(url, itemName = 'este elemento', onSuccess = null) {
    showConfirmDialog(
        '¿Estás seguro?',
        `No podrás revertir la eliminación de ${itemName}`,
        'Sí, eliminar',
        'Cancelar'
    ).then((result) => {
        if (result.isConfirmed) {
            showLoading('Eliminando...');
            
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showToast('success', data.message || 'Elemento eliminado correctamente');
                    if (onSuccess) {
                        onSuccess(data);
                    }
                } else {
                    showToast('error', data.message || 'Error al eliminar');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showToast('error', 'Error al eliminar el elemento');
            });
        }
    });
}

/**
 * Inicialización cuando el DOM está listo
 */
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit para formularios con clase 'auto-submit'
    document.querySelectorAll('form.auto-submit').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitFormAjax(this);
        });
    });
    
    // Botones de confirmación para eliminación
    document.querySelectorAll('[data-confirm-delete]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href') || this.dataset.url;
            const itemName = this.dataset.itemName || 'este elemento';
            const onSuccess = this.dataset.onSuccess ? 
                new Function('data', this.dataset.onSuccess) : 
                () => window.location.reload();
            
            confirmDelete(url, itemName, onSuccess);
        });
    });
    
    // Tooltips de Bootstrap
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

// Funciones de utilidad
const Utils = {
    /**
     * Formatea un número como moneda
     */
    formatCurrency: function(amount, currency = 'USD') {
        return new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: currency
        }).format(amount);
    },
    
    /**
     * Formatea una fecha
     */
    formatDate: function(date, options = {}) {
        const defaults = {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        const config = Object.assign(defaults, options);
        return new Date(date).toLocaleDateString('es-ES', config);
    },
    
    /**
     * Debounce function
     */
    debounce: function(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }
};
