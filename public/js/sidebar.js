(function () {
    const sidebar       = document.getElementById('mainSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const toggleIcon    = document.getElementById('sidebarToggleIcon');
    const backdrop      = document.getElementById('sidebarBackdrop');
    const mainContent   = document.getElementById('mainContent');
    const navbarNav     = document.getElementById('navbarNav');

    const COLLAPSED_KEY = 'sidebar_collapsed';

    // Match Bootstrap navbar-expand-lg breakpoint
    const MOBILE_BP = 991.98;

    const navbarCollapse = navbarNav && window.bootstrap
        ? bootstrap.Collapse.getOrCreateInstance(navbarNav, { toggle: false })
        : null;

    function isMobile() {
        return window.innerWidth <= MOBILE_BP;
    }

    function closeNavbar() {
        navbarCollapse?.hide();
    }

    function applyDesktopState(collapsed) {
        sidebar.classList.toggle('collapsed', collapsed);
        sidebar.classList.remove('mobile-open');

        document.body.classList.toggle('sidebar-collapsed', collapsed);

        if (mainContent) {
            mainContent.style.marginLeft = collapsed ? '56px' : '200px';
        }

        if (toggleIcon) {
            toggleIcon.className = 'bi ' + (collapsed ? 'bi-layout-sidebar' : 'bi-layout-sidebar-reverse');
        }

        backdrop?.classList.remove('active');
        sidebarToggle?.setAttribute('aria-expanded', String(!collapsed));
    }

    function applyMobileState(open) {
        sidebar.classList.toggle('mobile-open', open);
        backdrop?.classList.toggle('active', open);

        if (toggleIcon) {
            toggleIcon.className = 'bi ' + (open ? 'bi-x-lg' : 'bi-list');
        }

        sidebarToggle?.setAttribute('aria-expanded', String(open));

        if (open) {
            closeNavbar();
        }
    }

    function closeMobile() {
        applyMobileState(false);
    }

    function init() {
        if (!sidebar) return;

        if (isMobile()) {
            sidebar.classList.remove('collapsed');
            document.body.classList.remove('sidebar-collapsed');

            if (mainContent) {
                mainContent.style.marginLeft = '0';
            }

            applyMobileState(false);
        } else {
            const collapsed = localStorage.getItem(COLLAPSED_KEY) === 'true';
            applyDesktopState(collapsed);
            closeNavbar();
        }
    }

    sidebarToggle?.addEventListener('click', () => {
        if (isMobile()) {
            applyMobileState(!sidebar.classList.contains('mobile-open'));
        } else {
            const next = !sidebar.classList.contains('collapsed');
            localStorage.setItem(COLLAPSED_KEY, String(next));
            applyDesktopState(next);
        }
    });

    backdrop?.addEventListener('click', closeMobile);

    sidebar?.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (isMobile()) closeMobile();
        });
    });

    navbarNav?.addEventListener('show.bs.collapse', () => {
        if (isMobile()) closeMobile();
    });

    let lastMobile = isMobile();

    window.addEventListener('resize', () => {
        const nowMobile = isMobile();

        if (nowMobile !== lastMobile) {
            lastMobile = nowMobile;
            init();
        }
    });

    init();
})();