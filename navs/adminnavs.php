<?php
  $currentPage = basename($_SERVER['PHP_SELF']);
  function isActive($page, $current) {
    return $page === $current ? 'active' : '';
  }
?>
 <aside class="sidebar" id="sidebar">
    <a class="sidebar-logo" href="#">
      <div class="logo-hex">
        <svg width="16" height="12" viewBox="0 0 16 12" fill="none">
          <path d="M1 9L4 3h8l3 6" stroke="white" stroke-width="1.8" stroke-linecap="round"/>
          <circle cx="4.5" cy="9.5" r="1.8" fill="white"/>
          <circle cx="11.5" cy="9.5" r="1.8" fill="white"/>
        </svg>
      </div>
      <span class="logo-wordmark">REVV</span>
    </a>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Main</div>

      <a class="nav-item <?php echo isActive('admindashboard.php', $currentPage); ?>" href="http://localhost/rent/layouts/admindashboard.php">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <rect x="2" y="2" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
          <rect x="9" y="2" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
          <rect x="2" y="9" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
          <rect x="9" y="9" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
        </svg>
        Dashboard
      </a>
      <a class="nav-item <?php echo isActive('adminbooking.php', $currentPage); ?>" href="http://localhost/rent/layouts/adminbooking.php">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <rect x="2" y="4" width="13" height="10" rx="2" stroke="currentColor" stroke-width="1.4"/>
          <path d="M5 4V3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          <path d="M12 4V3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          <path d="M2 7.5h13" stroke="currentColor" stroke-width="1.4"/>
        </svg>
        Bookings
        <span class="nav-badge">3</span>
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Fleet</div>
      <a class="nav-item <?php echo isActive('adminvehicles.php', $currentPage); ?>" href="http://localhost/rent/layouts/adminvehicles.php">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <path d="M2 10L4.5 5h8L15 10" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          <rect x="1.5" y="9.5" width="14" height="4" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
          <circle cx="4.5" cy="13.5" r="1.3" stroke="currentColor" stroke-width="1.2"/>
          <circle cx="12.5" cy="13.5" r="1.3" stroke="currentColor" stroke-width="1.2"/>
        </svg>
        Vehicles
      </a>
      <a class="nav-item <?php echo isActive('admindrivers.php', $currentPage); ?>" href="http://localhost/rent/layouts/admindrivers.php">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <circle cx="8.5" cy="6" r="3" stroke="currentColor" stroke-width="1.4"/>
          <path d="M2.5 15c0-3.31 2.69-6 6-6s6 2.69 6 6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
        Drivers
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Business</div>
      <a class="nav-item <?php echo isActive('admincustomers.php', $currentPage); ?>" href="http://localhost/rent/layouts/admincustomers.php">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <circle cx="7" cy="6.5" r="3" stroke="currentColor" stroke-width="1.4"/>
          <path d="M2 15c0-2.76 2.24-5 5-5s5 2.24 5 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          <path d="M12 8.5c1.1.35 2 1.35 2 3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          <path d="M14 4.5c.83.28 1.5 1.03 1.5 2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
        </svg>
        Customers
      </a>
      <a class="nav-item <?php echo isActive('adminrentals.php', $currentPage); ?>" href="http://localhost/rent/layouts/adminrentals.php">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <circle cx="8.5" cy="8.5" r="6.5" stroke="currentColor" stroke-width="1.4"/>
          <path d="M8.5 5.5v3.5l2.5 2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
        Rentals
      </a>
      <a class="nav-item <?php echo isActive('adminpayments.php', $currentPage); ?>" href="http://localhost/rent/layouts/adminpayments.php">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <rect x="3" y="2" width="11" height="13" rx="2" stroke="currentColor" stroke-width="1.4"/>
          <path d="M6 6h5M6 9h5M6 12h3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
        </svg>
        Payments
      </a>
      <a class="nav-item <?php echo isActive('adminreports.php', $currentPage); ?>" href="http://localhost/rent/layouts/adminreports.php">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <path d="M3 13l4-5 3 3 4-6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M2 15h13" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
        </svg>
        Reports
      </a>
    </div>

    <div class="sidebar-section">
      <div class="sidebar-section-label">Admin</div>
      <a class="nav-item <?php echo isActive('adminsettings.php', $currentPage); ?>" href="http://localhost/rent/layouts/adminsettings.php">
        <svg class="nav-icon" width="17" height="17" viewBox="0 0 17 17" fill="none" color="white">
          <circle cx="8.5" cy="8.5" r="2" stroke="currentColor" stroke-width="1.4"/>
          <path d="M8.5 2v1.5M8.5 13.5V15M15 8.5h-1.5M3.5 8.5H2M12.7 4.3l-1.06 1.06M5.36 11.64L4.3 12.7M12.7 12.7l-1.06-1.06M5.36 5.36L4.3 4.3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
        Settings
      </a>
    </div>

    <div class="sidebar-bottom">
      <div class="user-card">
        <div class="user-avatar">JG</div>
        <div class="user-info">
          <div class="user-name">Jayne Gonzales</div>
          <div class="user-role">Administrator</div>
        </div>
        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="#6A6E75" style="flex-shrink:0">
          <path d="M4 6l3.5 3.5L11 6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
      </div>
    </div>
  </aside>