document.addEventListener('DOMContentLoaded', () => {
    const appShell = document.querySelector('.app-shell');
    const desktopSidebarToggle = document.querySelector('[data-sidebar-desktop-toggle]');
    const desktopSidebarToggleIcon = desktopSidebarToggle?.querySelector('[data-sidebar-desktop-toggle-icon]');
    const SIDEBAR_STATE_KEY = 'crud.sidebar.collapsed';

    const syncDesktopToggleState = (collapsed) => {
        if (!desktopSidebarToggle) {
            return;
        }

        desktopSidebarToggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        desktopSidebarToggle.setAttribute('aria-label', collapsed ? 'Mostrar sidebar' : 'Ocultar sidebar');
        desktopSidebarToggle.setAttribute('title', collapsed ? 'Mostrar sidebar' : 'Ocultar sidebar');

        if (desktopSidebarToggleIcon) {
            desktopSidebarToggleIcon.classList.toggle('bi-layout-sidebar-inset-reverse', collapsed);
            desktopSidebarToggleIcon.classList.toggle('bi-layout-sidebar-inset', !collapsed);
        }
    };

    if (appShell && desktopSidebarToggle) {
        const savedCollapsedState = localStorage.getItem(SIDEBAR_STATE_KEY) === 'true';

        if (savedCollapsedState) {
            appShell.classList.add('sidebar-collapsed');
        }

        syncDesktopToggleState(savedCollapsedState);

        desktopSidebarToggle.addEventListener('click', () => {
            const collapsed = appShell.classList.toggle('sidebar-collapsed');
            localStorage.setItem(SIDEBAR_STATE_KEY, String(collapsed));
            syncDesktopToggleState(collapsed);
        });
    }

    const toggles = document.querySelectorAll('[data-sidebar-toggle]');

    toggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const targetId = toggle.getAttribute('data-target');
            const content = document.getElementById(targetId);

            if (!content) {
                return;
            }

            const isOpen = content.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

            const chevron = toggle.querySelector('.sidebar-chevron');
            if (chevron) {
                chevron.classList.toggle('is-open', isOpen);
            }
        });
    });
});
