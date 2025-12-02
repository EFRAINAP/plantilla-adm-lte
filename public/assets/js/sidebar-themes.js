// Gestor de Temas del Sidebar
class SidebarThemeManager {
    constructor() {
        this.currentTheme = localStorage.getItem('sidebar-theme') || 'dark';
        // Aplicar tema inmediatamente para evitar parpadeo
        this.applyThemeImmediate();
        this.init();
    }

    init() {
        // Ya aplicado en constructor, solo verificar
        this.applyTheme(this.currentTheme);
    }

    applyThemeImmediate() {
        // Aplicación inmediata sin transiciones para evitar parpadeo
        const sidebar = document.querySelector('.app-sidebar');
        if (sidebar) {
            if (this.currentTheme === 'dark') {
                sidebar.setAttribute('data-bs-theme', 'dark');
            } else {
                sidebar.classList.add(`theme-${this.currentTheme}`);
            }
        }
    }



    changeTheme(themeName) {
        // Aplicar tema directamente (ya limpia internamente)
        this.applyTheme(themeName);
        
        // Guardar en localStorage
        localStorage.setItem('sidebar-theme', themeName);
        this.currentTheme = themeName;
        
        // Actualizar selector visual
        this.updateThemeSelector(themeName);
        
        // Mostrar notificación
        this.showThemeChangeNotification(themeName);
    }

    applyTheme(themeName) {
        const sidebar = document.querySelector('.app-sidebar');
        if (sidebar) {
            // Primero limpiar TODOS los temas y atributos
            const themeClasses = ['theme-blue', 'theme-red', 'theme-pink', 'theme-rainbow', 'theme-cyber'];
            sidebar.classList.remove(...themeClasses);
            sidebar.removeAttribute('data-bs-theme');
            
            // Luego aplicar el tema correspondiente SIN PARPADEO
            if (themeName === 'dark') {
                sidebar.setAttribute('data-bs-theme', 'dark');
            } else {
                sidebar.classList.add(`theme-${themeName}`);
            }
        }
    }

    removeCurrentTheme() {
        // Ya no es necesario porque applyTheme limpia todo primero
        return;
    }

    updateThemeSelector(themeName) {
        // Remover clase active de todos
        document.querySelectorAll('.theme-color').forEach(el => {
            el.classList.remove('active');
        });
        
        // Agregar clase active al seleccionado
        const activeTheme = document.querySelector(`[data-theme="${themeName}"]`);
        if (activeTheme) {
            activeTheme.classList.add('active');
        }
    }

    showThemeChangeNotification(themeName) {
        const themeNames = {
            'dark': 'Corporativo',
            'blue': 'Azul Corporativo',
            'red': 'Rojo Corporativo',
            'pink': 'Rosa Elegante',
            'rainbow': 'Sofisticado',
            'cyber': 'Tecnológico'
        };

        // Solo mostrar si existe Swal (SweetAlert2)
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `Tema ${themeNames[themeName]} aplicado`,
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
            });
        }
    }

    // Método público para cambiar tema desde código
    setTheme(themeName) {
        this.changeTheme(themeName);
    }

    // Método público para obtener tema actual
    getCurrentTheme() {
        return this.currentTheme;
    }
}

// Aplicar tema inmediatamente al cargar el script (antes del DOM)
(function() {
    const currentTheme = localStorage.getItem('sidebar-theme') || 'dark';
    const applyEarlyTheme = function() {
        const sidebar = document.querySelector('.app-sidebar');
        if (sidebar) {
            // Limpiar cualquier tema previo
            const themeClasses = ['theme-blue', 'theme-red', 'theme-pink', 'theme-rainbow', 'theme-cyber'];
            sidebar.classList.remove(...themeClasses);
            sidebar.removeAttribute('data-bs-theme');
            
            // Aplicar tema actual
            if (currentTheme === 'dark') {
                sidebar.setAttribute('data-bs-theme', 'dark');
            } else {
                sidebar.classList.add(`theme-${currentTheme}`);
            }
        }
    };
    
    // Intentar aplicar inmediatamente si el sidebar ya existe
    if (document.querySelector('.app-sidebar')) {
        applyEarlyTheme();
    }
    
    // También aplicar cuando el DOM esté listo por si acaso
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', applyEarlyTheme);
    } else {
        applyEarlyTheme();
    }
})();

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Crear instancia global del gestor de temas
    window.sidebarThemeManager = new SidebarThemeManager();
});

// Función helper global para cambiar tema
function changeSidebarTheme(themeName) {
    if (window.sidebarThemeManager) {
        window.sidebarThemeManager.setTheme(themeName);
    }
}

// Función para crear selector de temas en mi perfil
function createProfileThemeSelector(containerId) {
    const themes = [
        { name: 'dark', label: 'Corporativo' },
        { name: 'blue', label: 'Azul Corporativo' },
        { name: 'red', label: 'Rojo Corporativo' },
        { name: 'pink', label: 'Rosa Elegante' },
        { name: 'rainbow', label: 'Sofisticado' },
        { name: 'cyber', label: 'Tecnológico' }
    ];

    const currentTheme = localStorage.getItem('sidebar-theme') || 'dark';
    
    const selectorHTML = `
        <div class="profile-theme-selector">
            <h6><i class="bi bi-palette"></i> Personalizar Tema del Sidebar</h6>
            <p class="text-muted small">Elige tu tema favorito para personalizar la apariencia del sidebar</p>
            <div class="theme-grid">
                ${themes.map(theme => `
                    <div class="theme-option ${theme.name === currentTheme ? 'active' : ''}" 
                         data-theme="${theme.name}">
                        <div class="theme-preview" data-theme="${theme.name}"></div>
                        <div class="theme-name">${theme.label}</div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;

    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = selectorHTML;
        
        // Agregar eventos
        container.addEventListener('click', (e) => {
            const themeOption = e.target.closest('.theme-option');
            if (themeOption) {
                const themeName = themeOption.getAttribute('data-theme');
                
                // Remover active de todos
                container.querySelectorAll('.theme-option').forEach(el => {
                    el.classList.remove('active');
                });
                
                // Agregar active al seleccionado
                themeOption.classList.add('active');
                
                // Cambiar tema
                changeSidebarTheme(themeName);
            }
        });
    }
}