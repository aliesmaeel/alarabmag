/**
 * Section sidebar: collapse, mobile toggle, scroll spy on homepage.
 */
(function () {
    const sidebar = document.getElementById('pageSidebar');
    const toggle = document.getElementById('sidebarToggle');
    const collapseBtn = document.getElementById('sidebarCollapse');
    const expandBtn = document.getElementById('sidebarExpand');
    const backdrop = document.getElementById('sidebarBackdrop');
    const STORAGE_KEY = 'alarab_sidebar_collapsed';

    if (!sidebar) return;

    const isDesktop = () => window.matchMedia('(min-width: 1101px)').matches;

    const closeMobileDrawer = () => {
        sidebar.classList.remove('open');
        backdrop?.classList.remove('open');
    };

    const setCollapsed = (collapsed) => {
        if (!isDesktop()) {
            sidebar.classList.remove('collapsed');
            expandBtn?.classList.remove('visible');
            expandBtn?.setAttribute('hidden', '');
            return;
        }

        sidebar.classList.toggle('collapsed', collapsed);
        expandBtn?.classList.toggle('visible', collapsed);

        if (collapsed) {
            expandBtn?.removeAttribute('hidden');
        } else {
            expandBtn?.setAttribute('hidden', '');
        }

        collapseBtn?.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
    };

    if (localStorage.getItem(STORAGE_KEY) === '1') {
        setCollapsed(true);
    }

    collapseBtn?.addEventListener('click', () => {
        if (isDesktop()) {
            setCollapsed(true);
        } else {
            closeMobileDrawer();
        }
    });

    expandBtn?.addEventListener('click', () => {
        setCollapsed(false);
    });

    toggle?.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        backdrop?.classList.toggle('open');
    });

    backdrop?.addEventListener('click', closeMobileDrawer);

    document.querySelectorAll('.page-sidebar-link').forEach((a) => {
        a.addEventListener('click', () => {
            if (!isDesktop()) {
                closeMobileDrawer();
            }
        });
    });

    window.addEventListener('resize', () => {
        if (!isDesktop()) {
            sidebar.classList.remove('collapsed');
            expandBtn?.classList.remove('visible');
            expandBtn?.setAttribute('hidden', '');
        } else if (localStorage.getItem(STORAGE_KEY) === '1') {
            setCollapsed(true);
        }
    });

    const anchorLinks = document.querySelectorAll('.page-sidebar-link[data-section]');
    if (!anchorLinks.length) return;

    const sectionIds = ['top', 'influencers', 'artists', 'business', 'doctors', 'fashion', 'news', 'blogs'];

    const setActive = (id) => {
        document.querySelectorAll('.page-sidebar-link').forEach((a) => {
            a.classList.toggle('active', a.dataset.section === id);
        });
        document.querySelectorAll('.nav-bar a[data-section]').forEach((a) => {
            a.classList.toggle('active', a.dataset.section === id);
        });
    };

    window.addEventListener(
        'scroll',
        () => {
            let current = 'top';
            const offset = window.innerWidth <= 1100 ? 120 : 100;

            sectionIds.forEach((id) => {
                const el = document.getElementById(id);
                if (el && window.scrollY >= el.offsetTop - offset) {
                    current = id;
                }
            });

            setActive(current);
        },
        { passive: true }
    );

    window.dispatchEvent(new Event('scroll'));
})();
