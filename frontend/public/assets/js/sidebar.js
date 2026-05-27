const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const topbar = document.getElementById('topbar');
    const toggleBtn = document.getElementById('toggleBtn');
    const mobileBtn = document.getElementById('mobileBtn');

    // Desktop collapse
    if (toggleBtn) {
      toggleBtn.addEventListener('click', () => {
        if (sidebar) sidebar.classList.toggle('collapsed');
        if (content) content.classList.toggle('full');
        if (topbar) topbar.classList.toggle('full');
      });
    }

    // Mobile sidebar open
    if (mobileBtn) {
      mobileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (sidebar) sidebar.classList.add('mobile-show');
      });
    }

    // 🔥 Click outside to close sidebar on mobile
    document.addEventListener('click', (event) => {
      if (sidebar && sidebar.classList.contains('mobile-show')) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickOnMobileBtn = mobileBtn && mobileBtn.contains(event.target);
        if (!isClickInsideSidebar && !isClickOnMobileBtn) {
          sidebar.classList.remove('mobile-show');
        }
      }
    });

    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.sidebar .nav-link');

    if (navLinks.length > 0) {
      navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
          link.classList.add('active');
        } else {
          link.classList.remove('active');
        }
      });
    }