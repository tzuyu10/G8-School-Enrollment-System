document.addEventListener('DOMContentLoaded', function () {
    // ---------- Top navbar toggle ----------
    const navToggler = document.getElementById('navbarTogglerBtn');
    const navbarNav  = document.getElementById('navbarNav');

    const navbarCollapse = navbarNav && window.bootstrap
        ? bootstrap.Collapse.getOrCreateInstance(navbarNav, { toggle: false })
        : null;

    if (navToggler && navbarCollapse && !navToggler.dataset.bsBound) {
        navToggler.dataset.bsBound = 'true';

        navToggler.addEventListener('click', () => navbarCollapse.toggle());

        navbarNav.addEventListener('shown.bs.collapse', () => {
            navToggler.setAttribute('aria-expanded', 'true');
        });
        navbarNav.addEventListener('hidden.bs.collapse', () => {
            navToggler.setAttribute('aria-expanded', 'false');
        });
    }

    // ---------- Sidebar ----------
    const sidebar      = document.getElementById('mainSidebar');
    const arrowToggle   = document.getElementById('sidebarArrowToggle'); // mobile only now
    const backdrop      = document.getElementById('sidebarBackdrop');
    const mainContent   = document.getElementById('mainContent');

    const COLLAPSED_KEY = 'sidebar_collapsed';
    const MOBILE_BP = 991.98;

    function isMobile() {
        return window.innerWidth <= MOBILE_BP;
    }

    function closeNavbarMenu() {
        navbarCollapse?.hide();
    }

    function setArrowState(expanded) {
        arrowToggle?.classList.toggle('active', expanded);
        arrowToggle?.setAttribute('aria-expanded', String(expanded));
    }

    function applyDesktopState(collapsed) {
        sidebar.classList.toggle('collapsed', collapsed);
        sidebar.classList.remove('mobile-open');
        document.body.classList.toggle('sidebar-collapsed', collapsed);

        if (mainContent) mainContent.style.marginLeft = collapsed ? '56px' : '200px';

        backdrop?.classList.remove('active');
    }

    function applyMobileState(open) {
        sidebar.classList.toggle('mobile-open', open);
        backdrop?.classList.toggle('active', open);
        setArrowState(open);

        if (open) closeNavbarMenu();
    }

    function closeMobile() {
        applyMobileState(false);
    }

    function suppressTransition(run) {
        sidebar?.classList.add('no-transition');
        mainContent?.classList.add('no-transition');

        run();

        requestAnimationFrame(() => {
            sidebar?.classList.remove('no-transition');
            mainContent?.classList.remove('no-transition');
        });
    }

    function init() {
        if (!sidebar) return;

        suppressTransition(() => {
            if (isMobile()) {
                sidebar.classList.remove('collapsed');
                document.body.classList.remove('sidebar-collapsed');
                if (mainContent) mainContent.style.marginLeft = '0';
                applyMobileState(false);
            } else {
                const collapsed = localStorage.getItem(COLLAPSED_KEY) === 'true';
                applyDesktopState(collapsed);
                closeNavbarMenu();
            }
        });
    }

    // Mobile: arrow handle opens/closes the sidebar
    arrowToggle?.addEventListener('click', () => {
        if (!isMobile()) return;
        applyMobileState(!sidebar.classList.contains('mobile-open'));
    });

    // Desktop: clicking the sidebar itself toggles collapsed/expanded.
    // Real nav links are excluded so navigation still works normally.
    sidebar?.addEventListener('click', (e) => {
        if (isMobile()) return;
        if (e.target.closest('a')) return;

        const next = !sidebar.classList.contains('collapsed');
        localStorage.setItem(COLLAPSED_KEY, String(next));
        applyDesktopState(next);
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
});