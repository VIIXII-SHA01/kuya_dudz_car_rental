(function() {
  const menuToggle = document.getElementById('menuToggle');
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.getElementById('sidebarOverlay');

  if (!menuToggle || !sidebar || !overlay) return;

  function toggleSidebar() {
    const isOpen = sidebar.classList.toggle('open');
    overlay.classList.toggle('active', isOpen);
    menuToggle.classList.toggle('active', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
    menuToggle.classList.remove('active');
    document.body.style.overflow = '';
  }

  menuToggle.addEventListener('click', toggleSidebar);
  overlay.addEventListener('click', closeSidebar);

  // Close sidebar on escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeSidebar();
  });

  // Close sidebar when clicking a nav item on mobile
  const navItems = document.querySelectorAll('.nav-item');
  navItems.forEach(item => {
    item.addEventListener('click', () => {
      if (window.innerWidth <= 860) closeSidebar();
    });
  });
})();