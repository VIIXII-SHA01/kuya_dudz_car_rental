 // Bar animations with stagger
  document.querySelectorAll('.bar').forEach((bar, i) => {
    bar.style.animationDelay = (i * 0.04) + 's';
  });

  // Sidebar nav active state
  document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
      this.classList.add('active');
    });
  });