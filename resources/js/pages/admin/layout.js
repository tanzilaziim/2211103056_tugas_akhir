const setupAdminSidebar = () => {
    const shell = document.querySelector('[data-admin-shell]');
    if (!shell) return;

    const sidebar = shell.querySelector('[data-admin-sidebar]');
    const overlay = shell.querySelector('[data-admin-sidebar-overlay]');
    const mobileOpenButton = shell.querySelector('[data-admin-sidebar-open]');
    const desktopToggleButton = shell.querySelector('[data-admin-sidebar-desktop-toggle]');
    const desktopToggleIcon = shell.querySelector('[data-admin-sidebar-desktop-icon]');
    const closeTargets = shell.querySelectorAll('[data-admin-sidebar-close]');

    if (!sidebar || !overlay || !desktopToggleButton || !desktopToggleIcon) return;

    const STORAGE_KEY = 'admin-sidebar-collapsed';
    let desktopCollapsed = localStorage.getItem(STORAGE_KEY) === '1';

    const isDesktop = () => window.innerWidth >= 1024;

    const openMobileSidebar = () => {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    const closeMobileSidebar = () => {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    const applyDesktopState = () => {
        if (!isDesktop()) {
            sidebar.classList.remove('lg:!w-0', 'lg:!overflow-hidden');
            desktopToggleButton.classList.add('left-[327px]', '-translate-x-1/2');
            desktopToggleButton.classList.remove('left-0', 'translate-x-0');
            desktopToggleIcon.classList.add('ph-caret-left');
            desktopToggleIcon.classList.remove('ph-caret-right');
            desktopToggleButton.setAttribute('aria-label', 'Collapse sidebar');
            return;
        }

        closeMobileSidebar();

        if (desktopCollapsed) {
            sidebar.classList.add('lg:!w-0', 'lg:!overflow-hidden');
            desktopToggleButton.classList.remove('left-[327px]', '-translate-x-1/2');
            desktopToggleButton.classList.add('left-0', 'translate-x-0');
            desktopToggleIcon.classList.remove('ph-caret-left');
            desktopToggleIcon.classList.add('ph-caret-right');
            desktopToggleButton.setAttribute('aria-label', 'Expand sidebar');
        } else {
            sidebar.classList.remove('lg:!w-0', 'lg:!overflow-hidden');
            desktopToggleButton.classList.add('left-[327px]', '-translate-x-1/2');
            desktopToggleButton.classList.remove('left-0', 'translate-x-0');
            desktopToggleIcon.classList.add('ph-caret-left');
            desktopToggleIcon.classList.remove('ph-caret-right');
            desktopToggleButton.setAttribute('aria-label', 'Collapse sidebar');
        }
    };

    desktopToggleButton.addEventListener('click', () => {
        if (!isDesktop()) return;
        desktopCollapsed = !desktopCollapsed;
        localStorage.setItem(STORAGE_KEY, desktopCollapsed ? '1' : '0');
        applyDesktopState();
    });

    mobileOpenButton?.addEventListener('click', () => {
        if (!isDesktop()) openMobileSidebar();
    });

    closeTargets.forEach((element) => {
        element.addEventListener('click', () => {
            if (!isDesktop()) closeMobileSidebar();
        });
    });

    window.addEventListener('resize', applyDesktopState);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !isDesktop()) closeMobileSidebar();
    });

    applyDesktopState();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupAdminSidebar);
} else {
    setupAdminSidebar();
}
