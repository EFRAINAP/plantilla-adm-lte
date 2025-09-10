/**
 * Sidebar State Management
 * Mantiene el estado del sidebar usando localStorage
 */
document.addEventListener("DOMContentLoaded", function() {
    const body = document.body;
    const sidebarKey = "sidebar-collapsed";
    const menuStateKey = "sidebar-menu-state";

    // Aplicar estado del sidebar al cargar
    if (localStorage.getItem(sidebarKey) === "true") {
        body.classList.add("sidebar-collapse");
    }

    // Guardar estado cuando se hace toggle del sidebar
    const pushMenuButton = document.querySelector('[data-widget="pushmenu"]');
    if (pushMenuButton) {
        pushMenuButton.addEventListener("click", function() {
            // Usar setTimeout para capturar el estado después del toggle
            setTimeout(function() {
                const isCollapsed = body.classList.contains("sidebar-collapse");
                localStorage.setItem(sidebarKey, isCollapsed);
            }, 100);
        });
    }

    // Restaurar estado de los menús expandidos
    restoreMenuState();

    // Guardar estado de los menús cuando se expanden/contraen
    const treeviewItems = document.querySelectorAll('.nav-item.has-treeview > .nav-link');
    treeviewItems.forEach(function(item) {
        item.addEventListener('click', function(e) {
            const parentLi = this.closest('.nav-item');
            const menuId = getMenuId(this);
            
            setTimeout(function() {
                const isOpen = parentLi.classList.contains('menu-open');
                saveMenuItemState(menuId, isOpen);
            }, 100);
        });
    });

    /**
     * Restaura el estado de los menús desde localStorage
     */
    function restoreMenuState() {
        const menuState = JSON.parse(localStorage.getItem(menuStateKey) || '{}');
        
        Object.keys(menuState).forEach(function(menuId) {
            if (menuState[menuId]) {
                const menuLink = document.querySelector(`[data-menu-id="${menuId}"]`);
                if (menuLink) {
                    const parentLi = menuLink.closest('.nav-item');
                    if (parentLi) {
                        parentLi.classList.add('menu-open');
                        
                        // Encontrar el submenu y mostrarlo
                        const submenu = parentLi.querySelector('.nav-treeview');
                        if (submenu) {
                            submenu.style.display = 'block';
                        }
                    }
                }
            }
        });
    }

    /**
     * Guarda el estado de un item del menú
     */
    function saveMenuItemState(menuId, isOpen) {
        const menuState = JSON.parse(localStorage.getItem(menuStateKey) || '{}');
        menuState[menuId] = isOpen;
        localStorage.setItem(menuStateKey, JSON.stringify(menuState));
    }

    /**
     * Genera un ID único para un item del menú
     */
    function getMenuId(linkElement) {
        const text = linkElement.querySelector('p') ? linkElement.querySelector('p').textContent.trim() : '';
        return text.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
    }

    // Agregar data-menu-id a todos los enlaces del menú para facilitar la identificación
    treeviewItems.forEach(function(item) {
        const menuId = getMenuId(item);
        item.setAttribute('data-menu-id', menuId);
    });
});
