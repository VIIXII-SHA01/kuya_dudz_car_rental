const sidebar  = document.getElementById('sidebar');
  const overlay  = document.getElementById('sidebarOverlay');
  const toggle   = document.getElementById('menuToggle');

  function openSidebar() {
    sidebar.classList.add('open');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  toggle.addEventListener('click', () => {
    sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
  });

  overlay.addEventListener('click', closeSidebar);

  // Close sidebar on nav click (mobile UX)
  sidebar.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', () => {
      if (window.innerWidth <= 960) closeSidebar();
    });
  });

  // Close on resize if window goes back to desktop
  window.addEventListener('resize', () => {
    if (window.innerWidth > 960) closeSidebar();
  });

  // Escape key
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeSidebar();
  });

  // Bar animations
  document.querySelectorAll('.bar, .spark-bar').forEach((bar, i) => {
    bar.style.animationDelay = (i * 0.04) + 's';
  });