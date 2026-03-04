const setupAdminSidebar = () => {
    const shell = document.querySelector('[data-admin-shell]');
    if (!shell) return;

    const sidebar = shell.querySelector('[data-admin-sidebar]');
    const overlay = shell.querySelector('[data-admin-sidebar-overlay]');
    const openButton = shell.querySelector('[data-admin-sidebar-open]');
    const closeTargets = shell.querySelectorAll('[data-admin-sidebar-close]');

    if (!sidebar || !overlay || !openButton) return;

    const openSidebar = () => {
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    };

    const closeSidebar = () => {
        sidebar.classList.add('-translate-x-full');
        sidebar.classList.remove('translate-x-0');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    openButton.addEventListener('click', openSidebar);
    closeTargets.forEach((element) => element.addEventListener('click', closeSidebar));

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            document.body.classList.remove('overflow-hidden');
            overlay.classList.add('hidden');
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
        } else {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeSidebar();
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupAdminSidebar);
} else {
    setupAdminSidebar();
}
