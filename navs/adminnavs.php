<?php
  $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $segments = explode('/', trim($uri, '/'));
  $currentRoute = '';
  if (isset($segments[0]) && $segments[0] === 'rent') {
    $currentRoute = $segments[1] ?? '';
  } else {
    $currentRoute = $segments[0] ?? '';
  }
  if ($currentRoute === '') {
    $currentRoute = 'login';
  }
  function isActive($route, $current) {
    return $route === $current ? 'active' : '';
  }
  $navUserRole = strtolower((string) ($_SESSION['user']['role'] ?? ''));
  $isNavAdmin = $navUserRole === 'admin';
  $navSectionLabel = $isNavAdmin ? 'Admin' : 'Staff';
?>
 <aside class="sidebar" id="sidebar">
    <a class="sidebar-logo" href="/rent/dashboard">
      <div class="logo-hex">
        <svg width="16" height="12" viewBox="0 0 16 12" fill="none">
          <path d="M1 9L4 3h8l3 6" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
          <circle cx="4.5" cy="9.5" r="1.8" fill="white"/>
          <circle cx="11.5" cy="9.5" r="1.8" fill="white"/>
        </svg>
      </div>
      <span class="logo-wordmark">KDCR</span>
    </a>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Main</div>

      <a class="nav-item <?php echo isActive('dashboard', $currentRoute); ?>" href="/rent/dashboard">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <rect x="2" y="2" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
          <rect x="9" y="2" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
          <rect x="2" y="9" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
          <rect x="9" y="9" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
        </svg>
        Dashboard
      </a>
      <a class="nav-item <?php echo isActive('bookings', $currentRoute); ?>" href="/rent/bookings">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <rect x="2" y="4" width="13" height="10" rx="2" stroke="currentColor" stroke-width="1.4"/>
          <path d="M5 4V3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          <path d="M12 4V3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          <path d="M2 7.5h13" stroke="currentColor" stroke-width="1.4"/>
        </svg>
        Bookings
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Fleet</div>
      <a class="nav-item <?php echo isActive('vehicles', $currentRoute); ?>" href="/rent/vehicles">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <path d="M2 10L4.5 5h8L15 10" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          <rect x="1.5" y="9.5" width="14" height="4" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
          <circle cx="4.5" cy="13.5" r="1.3" stroke="currentColor" stroke-width="1.2"/>
          <circle cx="12.5" cy="13.5" r="1.3" stroke="currentColor" stroke-width="1.2"/>
        </svg>
        Vehicles
      </a>
      <a class="nav-item <?php echo isActive('drivers', $currentRoute); ?>" href="/rent/drivers">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <circle cx="8.5" cy="6" r="3" stroke="currentColor" stroke-width="1.4"/>
          <path d="M2.5 15c0-3.31 2.69-6 6-6s6 2.69 6 6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
        Drivers
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Business</div>
      <a class="nav-item <?php echo isActive('customers', $currentRoute); ?>" href="/rent/customers">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <circle cx="7" cy="6.5" r="3" stroke="currentColor" stroke-width="1.4"/>
          <path d="M2 15c0-2.76 2.24-5 5-5s5 2.24 5 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          <path d="M12 8.5c1.1.35 2 1.35 2 3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          <path d="M14 4.5c.83.28 1.5 1.03 1.5 2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
        </svg>
        Customers
      </a>
      <a class="nav-item <?php echo isActive('payments', $currentRoute); ?>" href="/rent/payments">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <rect x="3" y="2" width="11" height="13" rx="2" stroke="currentColor" stroke-width="1.4"/>
          <path d="M6 6h5M6 9h5M6 12h3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
        </svg>
        Payments
      </a>
      <a class="nav-item <?php echo isActive('reports', $currentRoute); ?>" href="/rent/reports">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <path d="M3 13l4-5 3 3 4-6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M2 15h13" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
        </svg>
        Reports
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label"><?php echo htmlspecialchars($navSectionLabel, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php if ($isNavAdmin): ?>
      <a class="nav-item <?php echo isActive('users', $currentRoute); ?>" href="/rent/users">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <path d="M5 5.5a2.5 2.5 0 1 1 5 0 2.5 2.5 0 0 1-5 0Z" stroke="currentColor" stroke-width="1.4"/>
          <path d="M2 15c0-2.5 2-4.5 4.5-4.5h4c2.5 0 4.5 2 4.5 4.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
        Users
      </a>
      <?php endif; ?>
      <a class="nav-item <?php echo isActive('profile', $currentRoute); ?>" href="/rent/profile">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <circle cx="8.5" cy="5.5" r="3" stroke="currentColor" stroke-width="1.4"/>
          <path d="M4.5 14c0-2.21 1.79-4 4-4s4 1.79 4 4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
        Profile
      </a>
      <a class="nav-item" href="/rent/logout">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <path d="M6.5 3.5h6a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2h-6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          <path d="M10.5 8.5H2.5M8 6l2.5 2.5L8 11" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Logout
      </a>
    </div>

    <div class="sidebar-bottom">
      <div class="user-card">
        <div class="user-avatar" id="sidebarUserInitials">JG</div>
        <div class="user-info">
          <div class="user-name" id="sidebarUserName">Jayne Gonzales</div>
          <div class="user-role" id="sidebarUserRole">Administrator</div>
        </div>
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="#6A6E75" style="flex-shrink:0">
          <path d="M4 6l3.5 3.5L11 6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
      </div>
    </div>
  </aside>