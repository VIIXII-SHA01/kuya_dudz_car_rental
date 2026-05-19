<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>REVV — Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/rent/css/admindashboard.css">
</head>
<body>
<div class="app">

  <!-- ══ SIDEBAR ══ -->

  <?php include("../navs/adminnavs.php"); ?>

  <!-- Overlay -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- ══ MAIN ══ -->
  <div class="main" id="mainContent">

    <!-- TOPBAR -->
    <header class="topbar">
      <!-- Hamburger — shown only when sidebar is collapsed (via CSS) -->
      <button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" color="white">
          <path d="M3 5h12M3 9h12M3 13h12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
      </button>

      <div class="topbar-title">Dashboard</div>
      <div class="topbar-divider"></div>

      <div class="search-wrap">
        <svg class="search-icon" width="15" height="15" viewBox="0 0 15 15" fill="none" color="white"><circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.4"/><path d="M10.5 10.5l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <input class="search-input" placeholder="Search bookings, cars, customers…">
      </div>

      <div class="topbar-right">
        <div class="topbar-date">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><rect x="1.5" y="2.5" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M4 2.5V1M9 2.5V1M1.5 5.5h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          Sat, 12 April 2026
        </div>
        <div class="icon-btn">
          <svg width="17" height="17" viewBox="0 0 17 17" fill="none" color="#9A9DA4"><path d="M8.5 2a5 5 0 0 1 5 5v3l1.5 2H2L3.5 10V7a5 5 0 0 1 5-5z" stroke="currentColor" stroke-width="1.4"/><path d="M7 13.5a1.5 1.5 0 0 0 3 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
          <div class="notif-dot"></div>
        </div>
        <div class="icon-btn">
          <div style="width:22px;height:22px;background:linear-gradient(135deg,var(--red),var(--orange));border-radius:2px;display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:11px;color:white">JG</div>
        </div>
      </div>
    </header>

    <!-- CONTENT -->
    <div class="content">

      <!-- Greeting -->
      <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px">
        <div>
          <div style="font-size:11.5px;color:var(--muted2);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:5px">Good morning</div>
          <div style="font-family:'Bebas Neue',sans-serif;font-size:clamp(26px,3vw,38px);letter-spacing:2px;line-height:1">Welcome back, <span style="color:var(--red)">Jayne</span></div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
          <a href="/rent/vehicles" style="display:flex;align-items:center;gap:8px;padding:10px 18px;background:var(--card);border:1px solid var(--border2);border-radius:3px;color:var(--white);font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;cursor:pointer;transition:all 0.18s;text-decoration:none" onmouseover="this.style.borderColor='var(--red)'" onmouseout="this.style.borderColor='var(--border2)'">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M7 2v10M2 7h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
            Add Vehicle
          </a>
          <a href="/rent/bookings" style="display:flex;align-items:center;gap:8px;padding:10px 18px;background:var(--red);border:none;border-radius:3px;color:white;font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;cursor:pointer;transition:all 0.18s;text-decoration:none" onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 20px var(--red-glow)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="white"><rect x="2" y="3" width="10" height="8" rx="1.5" stroke="currentColor" stroke-width="1.4"/><path d="M5 3V2M9 3V2M2 6h10" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
            New Booking
          </a>
        </div>
      </div>

      <!-- STAT CARDS -->
      <div class="stats-grid">
        <div class="stat-card red">
          <div class="stat-card-bg"></div>
          <div class="stat-top">
            <div class="stat-icon-wrap">
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" color="#E8341A"><path d="M2 12L5 6h10l3 6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><rect x="1.5" y="11.5" width="17" height="5" rx="2" stroke="currentColor" stroke-width="1.5"/><circle cx="5.5" cy="16.5" r="1.8" stroke="currentColor" stroke-width="1.3"/><circle cx="14.5" cy="16.5" r="1.8" stroke="currentColor" stroke-width="1.3"/></svg>
            </div>
            <div class="stat-trend trend-up" id="statTotalCarsTrend"><svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 7l3-4 3 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>+2 new</div>
          </div>
          <div class="stat-body">
            <div class="stat-value" id="statTotalCars">0</div>
            <div class="stat-label">Total Cars</div>
          </div>
          <div class="stat-spark" id="statTotalCarsSpark"><div class="spark-bar-row"><div class="spark-bar" style="height:35%"></div><div class="spark-bar" style="height:55%"></div><div class="spark-bar" style="height:40%"></div><div class="spark-bar" style="height:70%"></div><div class="spark-bar" style="height:50%"></div><div class="spark-bar" style="height:80%"></div><div class="spark-bar accent" style="height:100%"></div></div></div>
        </div>

        <div class="stat-card green">
          <div class="stat-card-bg"></div>
          <div class="stat-top">
            <div class="stat-icon-wrap">
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" color="#3DBE7A"><path d="M3 9l4 4 10-8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div class="stat-trend trend-up" id="statAvailableCarsTrend"><svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 7l3-4 3 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>Ready</div>
          </div>
          <div class="stat-body">
            <div class="stat-value" id="statAvailableCars" style="color:var(--green)">0</div>
            <div class="stat-label">Available Cars</div>
          </div>
          <div class="stat-spark" id="statAvailableCarsSpark"><div class="spark-bar-row"><div class="spark-bar" style="height:60%"></div><div class="spark-bar" style="height:45%"></div><div class="spark-bar" style="height:70%"></div><div class="spark-bar" style="height:30%"></div><div class="spark-bar" style="height:55%"></div><div class="spark-bar" style="height:75%"></div><div class="spark-bar accent" style="height:25%"></div></div></div>
        </div>

        <div class="stat-card gold">
          <div class="stat-card-bg"></div>
          <div class="stat-top">
            <div class="stat-icon-wrap">
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" color="#D4A843"><rect x="3" y="5" width="14" height="11" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M3 8h14M7 5V3M13 5V3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><circle cx="7" cy="12" r="1" fill="currentColor"/><circle cx="10" cy="12" r="1" fill="currentColor"/><circle cx="13" cy="12" r="1" fill="currentColor"/></svg>
            </div>
            <div class="stat-trend trend-neu" id="statActiveBookingsTrend"><svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 5h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>Active</div>
          </div>
          <div class="stat-body">
            <div class="stat-value" id="statActiveBookings" style="color:var(--gold)">0</div>
            <div class="stat-label">Active Bookings</div>
          </div>
          <div class="stat-spark" id="statActiveBookingsSpark"><div class="spark-bar-row"><div class="spark-bar" style="height:40%"></div><div class="spark-bar" style="height:65%"></div><div class="spark-bar" style="height:50%"></div><div class="spark-bar" style="height:80%"></div><div class="spark-bar" style="height:60%"></div><div class="spark-bar" style="height:90%"></div><div class="spark-bar accent" style="height:75%"></div></div></div>
        </div>

        <div class="stat-card blue">
          <div class="stat-card-bg"></div>
          <div class="stat-top">
            <div class="stat-icon-wrap">
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" color="#3D8FBE"><circle cx="10" cy="8" r="3.5" stroke="currentColor" stroke-width="1.5"/><path d="M3 17c0-3.87 3.13-7 7-7s7 3.13 7 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            </div>
            <div class="stat-trend trend-up" id="statTotalCustomersTrend"><svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 7l3-4 3 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>+8 this mo.</div>
          </div>
          <div class="stat-body">
            <div class="stat-value" id="statTotalCustomers" style="color:var(--blue)">0</div>
            <div class="stat-label">Total Customers</div>
          </div>
          <div class="stat-spark" id="statTotalCustomersSpark"><div class="spark-bar-row"><div class="spark-bar" style="height:30%"></div><div class="spark-bar" style="height:50%"></div><div class="spark-bar" style="height:45%"></div><div class="spark-bar" style="height:60%"></div><div class="spark-bar" style="height:70%"></div><div class="spark-bar" style="height:85%"></div><div class="spark-bar accent" style="height:100%"></div></div></div>
        </div>
      </div>

      <!-- REVENUE + QUICK ACTIONS -->
      <div class="three-col">
        <div class="panel-card">
          <div class="panel-head">
            <div class="panel-head-title">
              <div class="panel-icon"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="#E8341A"><path d="M2 11l3-4 3 3 4-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
              Revenue Overview
            </div>
            <div style="display:flex;gap:6px">
              <button id="revenueMonthlyBtn" data-period="monthly" class="revenue-toggle active" style="padding:5px 10px;background:var(--red-dim);border:1px solid rgba(232,52,26,0.3);border-radius:2px;color:var(--red);font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer">Monthly</button>
              <button id="revenueWeeklyBtn" data-period="weekly" class="revenue-toggle" style="padding:5px 10px;background:var(--card2);border:1px solid var(--border);border-radius:2px;color:var(--muted2);font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer">Weekly</button>
            </div>
          </div>
          <div class="chart-area">
            <div class="chart-legend">
              <div class="legend-item"><div class="legend-dot" style="background:var(--red)"></div>Revenue</div>
              <div class="legend-item"><div class="legend-dot" style="background:rgba(232,52,26,0.25)"></div>Target</div>
            </div>
            <div class="chart-bars" id="revenueBars"></div>
            <div class="chart-summary" id="revenueSummary">
              <div class="chart-stat"><div class="chart-stat-val" style="color:var(--red)" id="revenueTotal">₱0</div><div class="chart-stat-lab">Total Revenue</div></div>
              <div class="chart-stat"><div class="chart-stat-val" style="color:var(--green)" id="revenueChange">+0%</div><div class="chart-stat-lab">vs Last Year</div></div>
              <div class="chart-stat"><div class="chart-stat-val" style="color:var(--gold)" id="revenueMonth">₱0</div><div class="chart-stat-lab">This Month</div></div>
            </div>
          </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px">
          <div class="panel-card">
            <div class="panel-head">
              <div class="panel-head-title">
                <div class="panel-icon"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="#E8341A"><path d="M6.5 1.5v10M1.5 6.5h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg></div>
                Quick Actions
              </div>
            </div>
            <div class="actions-grid">
              <a class="action-btn" href="/rent/bookings"><div class="action-icon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" color="#E8341A"><rect x="2" y="4" width="12" height="9" rx="1.5" stroke="currentColor" stroke-width="1.4"/><path d="M5 4V3M11 4V3M2 7h12M8 8v3M6.5 9.5H9.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div><div><div class="action-label">New Booking</div><div class="action-sub">Reserve a vehicle</div></div></a>
              <a class="action-btn" href="/rent/customers"><div class="action-icon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" color="#E8341A"><circle cx="8" cy="7" r="3" stroke="currentColor" stroke-width="1.4"/><path d="M3 14c0-2.76 2.24-5 5-5s5 2.24 5 5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg></div><div><div class="action-label">Add Customer</div><div class="action-sub">Register new</div></div></a>
              <a class="action-btn" href="/rent/vehicles"><div class="action-icon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" color="#E8341A"><path d="M2 12L4.5 7h7L14 12" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/><rect x="1.5" y="11.5" width="13" height="3" rx="1.5" stroke="currentColor" stroke-width="1.3"/><circle cx="4.5" cy="14.5" r="1.2" stroke="currentColor" stroke-width="1.1"/><circle cx="11.5" cy="14.5" r="1.2" stroke="currentColor" stroke-width="1.1"/></svg></div><div><div class="action-label">Add Vehicle</div><div class="action-sub">Fleet management</div></div></a>
              <a class="action-btn" href="/rent/reports"><div class="action-icon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none" color="#E8341A"><path d="M3 13l4-5.5 3 3 4-6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 15h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div><div><div class="action-label">View Reports</div><div class="action-sub">Analytics & stats</div></div></a>
            </div>
          </div>
        </div>
      </div>

      <!-- BOOKINGS TABLE + ACTIVITY -->
      <div class="two-col" style="margin-bottom:28px">
        <div class="panel-card">
          <div class="panel-head">
            <div class="panel-head-title">
              <div class="panel-icon"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="#E8341A"><rect x="1.5" y="3" width="10" height="8.5" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M4.5 3V2M8.5 3V2M1.5 5.5h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg></div>
              Recent Bookings
            </div>
            <a class="section-action" href="/rent/bookings">View All →</a>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>ID</th><th>Customer</th><th>Vehicle</th><th>Status</th><th>Amount</th></tr></thead>
              <tbody id="recentBookingsBody">
                <tr><td colspan="5" class="table-empty">Loading recent bookings…</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="panel-card">
          <div class="panel-head">
            <div class="panel-head-title">
              <div class="panel-icon"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="#E8341A"><circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.2"/><path d="M6.5 4v3l2 1.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg></div>
              Recent Activity
            </div>
            <a class="section-action" href="/rent/reports">See All →</a>
          </div>
          <div class="activity-list" id="dashboardActivityList">
            <div class="activity-item"><div class="act-dot-wrap"><div class="act-dot" style="background:var(--muted)"></div><div class="act-line"></div></div><div class="act-body"><div class="act-text">Loading recent activity…</div><div class="act-time"><svg width="10" height="10" viewBox="0 0 10 10" fill="none" color="currentColor"><circle cx="5" cy="5" r="4" stroke="currentColor" stroke-width="1.1"/><path d="M5 3v2l1.5 1" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/></svg>Just now</div></div></div>
          </div>
        </div>
      </div>

      <!-- FLEET STATUS -->
      <div class="section-header">
        <div class="section-title">Fleet Status</div>
        <a class="section-action" href="/rent/vehicles">Manage Fleet →</a>
      </div>
      <div class="panel-card" style="margin-bottom:32px">
        <div class="fleet-grid" id="fleetStatusGrid">
          <div class="fleet-item"><div class="fleet-item-icon" style="background:var(--muted);border:1px solid var(--border)"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" color="#6A6E75"><path d="M2 11L4.5 6h9L16 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><rect x="1.5" y="10.5" width="15" height="4.5" rx="2" stroke="currentColor" stroke-width="1.4"/><circle cx="5" cy="15" r="1.5" stroke="currentColor" stroke-width="1.2"/><circle cx="13" cy="15" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg></div><div class="fleet-item-info"><div class="fleet-item-model">Loading top fleet…</div><div class="fleet-item-plate">—</div></div><span class="badge pending"><span class="badge-dot" style="background:var(--muted)"></span>Loading</span></div>
        </div>
      </div>

    </div><!-- /content -->
  </div><!-- /main -->
</div><!-- /app -->

<script src="/rent/javascript/admindashboard.js"></script>
</body>
</html>