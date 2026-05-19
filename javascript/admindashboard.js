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

  const DASHBOARD_API = '/rent/php/dashboard_action.php';
  const dashboardBase = document.getElementById('statTotalCars');

  if (dashboardBase) {
    loadDashboard();
    initRevenueToggle();
  }

  async function loadDashboard() {
    try {
      const response = await fetch(DASHBOARD_API);
      const data = await response.json();
      if (!response.ok || data.error) {
        throw new Error(data.error || 'Unable to load dashboard data.');
      }
      populateDashboard(data);
    } catch (err) {
      console.warn('Dashboard API failed:', err);
      setDashboardEmptyState();
    }
  }

  function formatCurrency(value) {
    return '₱' + Number(value || 0).toLocaleString();
  }

  function initRevenueToggle() {
    const buttons = document.querySelectorAll('.revenue-toggle');
    buttons.forEach(button => {
      button.addEventListener('click', () => {
        buttons.forEach(btn => {
          btn.classList.remove('active');
          btn.style.background = 'var(--card2)';
          btn.style.color = 'var(--muted2)';
          btn.style.borderColor = 'var(--border)';
        });
        button.classList.add('active');
        button.style.background = 'var(--red-dim)';
        button.style.color = 'var(--red)';
        button.style.borderColor = 'rgba(232,52,26,0.3)';
        setRevenuePeriod(button.dataset.period);
      });
    });
  }

  function setRevenuePeriod(period) {
    const monthlyBtn = document.getElementById('revenueMonthlyBtn');
    const weeklyBtn = document.getElementById('revenueWeeklyBtn');
    if (!monthlyBtn || !weeklyBtn) return;
    if (period === 'weekly') {
      monthlyBtn.style.background = 'var(--card2)';
      monthlyBtn.style.color = 'var(--muted2)';
      monthlyBtn.style.borderColor = 'var(--border)';
      weeklyBtn.style.background = 'var(--red-dim)';
      weeklyBtn.style.color = 'var(--red)';
      weeklyBtn.style.borderColor = 'rgba(232,52,26,0.3)';
    } else {
      monthlyBtn.style.background = 'var(--red-dim)';
      monthlyBtn.style.color = 'var(--red)';
      monthlyBtn.style.borderColor = 'rgba(232,52,26,0.3)';
      weeklyBtn.style.background = 'var(--card2)';
      weeklyBtn.style.color = 'var(--muted2)';
      weeklyBtn.style.borderColor = 'var(--border)';
    }
  }

  function setDashboardEmptyState() {
    const numericStats = ['statTotalCars', 'statAvailableCars', 'statActiveBookings', 'statTotalCustomers'];
    numericStats.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.textContent = '0';
    });
    const revenueTotal = document.getElementById('revenueTotal');
    const revenueChange = document.getElementById('revenueChange');
    const revenueMonth = document.getElementById('revenueMonth');
    if (revenueTotal) revenueTotal.textContent = '₱0';
    if (revenueChange) revenueChange.textContent = '0%';
    if (revenueMonth) revenueMonth.textContent = '₱0';
    renderRevenueBars([]);
    renderRecentBookings([]);
    renderActivityLog([]);
    renderFleetStatus([]);
  }

  function populateDashboard(data) {
    document.getElementById('statTotalCars').textContent = data.stats.totalCars || 0;
    document.getElementById('statAvailableCars').textContent = data.stats.availableCars || 0;
    document.getElementById('statActiveBookings').textContent = data.stats.activeBookings || 0;
    document.getElementById('statTotalCustomers').textContent = data.stats.totalCustomers || 0;

    document.getElementById('revenueTotal').textContent = formatCurrency(data.stats.totalRevenue || 0);
    document.getElementById('revenueChange').textContent = (data.stats.revenueChange >= 0 ? '+' : '') + (data.stats.revenueChange || 0) + '%';
    document.getElementById('revenueMonth').textContent = formatCurrency(data.stats.currentMonthRevenue || 0);

    renderRevenueBars(data.revenueChart || []);
    renderRecentBookings(data.recentBookings || []);
    renderActivityLog(data.activity || []);
    renderFleetStatus(data.fleetStatus || []);
  }

  function renderRevenueBars(series) {
    const container = document.getElementById('revenueBars');
    if (!container) return;
    if (!series.length) {
      container.innerHTML = '<div class="chart-empty">No revenue history available.</div>';
      return;
    }

    const maxRevenue = Math.max.apply(null, series.map(function(item) { return item.revenue; }).concat(1));
    const targetValue = series.length && series[0].target ? series[0].target : 0;
    const targetPercent = targetValue > 0 ? Math.min(100, Math.round(targetValue / maxRevenue * 100)) : 0;

    container.innerHTML = series.map(item => {
      const height = item.revenue > 0 ? Math.max(12, Math.round(item.revenue / maxRevenue * 100)) : 8;
      return `
        <div class="bar-group">
          <div class="bar-pair">
            <div class="bar revenue" style="height:${height}%" title="${formatCurrency(item.revenue)}"></div>
            ${targetValue > 0 ? `<div class="bar target" style="height:${targetPercent}%"></div>` : ''}
          </div>
          <div class="bar-label">${item.month}</div>
        </div>`;
    }).join('');
  }

  function renderRecentBookings(bookings) {
    const body = document.getElementById('recentBookingsBody');
    if (!body) return;
    if (!bookings.length) {
      body.innerHTML = '<tr><td colspan="5" class="table-empty">No recent bookings found.</td></tr>';
      return;
    }

    body.innerHTML = bookings.map(item => {
      const statusClass = ['active','pending','done','canceled','overdue'].includes(item.status) ? item.status : 'pending';
      const amountColor = item.amount > 0 ? 'var(--green)' : 'var(--muted)';
      return `
        <tr>
          <td><span class="booking-id">${item.booking_ref}</span></td>
          <td><div class="customer-cell"><div class="cust-avatar">${item.customer.slice(0,2).toUpperCase()}</div><div><div class="cust-name">${item.customer}</div></div></div></td>
          <td><div class="car-model">${item.vehicle}</div><div class="car-plate">${item.plate}</div></td>
          <td><span class="badge ${statusClass}"><span class="badge-dot"></span>${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span></td>
          <td><span class="amount" style="color:${amountColor}">${formatCurrency(item.amount)}</span></td>
        </tr>`;
    }).join('');
  }

  function renderActivityLog(events) {
    const list = document.getElementById('dashboardActivityList');
    if (!list) return;
    if (!events.length) {
      list.innerHTML = '<div class="activity-item"><div class="act-dot-wrap"><div class="act-dot" style="background:var(--muted)"></div><div class="act-line"></div></div><div class="act-body"><div class="act-text">No recent activity available.</div><div class="act-time">—</div></div></div>';
      return;
    }

    list.innerHTML = events.map(event => {
      const color = event.status === 'paid' || event.status === 'active' ? 'var(--green)' : event.status === 'pending' ? 'var(--gold)' : event.status === 'overdue' ? 'var(--red)' : 'var(--blue)';
      return `
        <div class="activity-item">
          <div class="act-dot-wrap"><div class="act-dot" style="background:${color}"></div><div class="act-line"></div></div>
          <div class="act-body">
            <div class="act-text">${event.message}</div>
            <div class="act-time"><svg width="10" height="10" viewBox="0 0 10 10" fill="none" color="currentColor"><circle cx="5" cy="5" r="4" stroke="currentColor" stroke-width="1.1"/><path d="M5 3v2l1.5 1" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/></svg>${new Date(event.time).toLocaleString()}</div>
          </div>
        </div>`;
    }).join('');
  }

  function renderFleetStatus(fleet) {
    const grid = document.getElementById('fleetStatusGrid');
    if (!grid) return;
    if (!fleet.length) {
      grid.innerHTML = '<div class="fleet-item"><div class="fleet-item-info"><div class="fleet-item-model">No fleet status available.</div></div></div>';
      return;
    }

    grid.innerHTML = fleet.map(item => `
      <div class="fleet-item">
        <div class="fleet-item-icon" style="background:var(--muted);border:1px solid var(--border)">
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none" color="#6A6E75"><path d="M2 11L4.5 6h9L16 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><rect x="1.5" y="10.5" width="15" height="4.5" rx="2" stroke="currentColor" stroke-width="1.4"/><circle cx="5" cy="15" r="1.5" stroke="currentColor" stroke-width="1.2"/><circle cx="13" cy="15" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
        </div>
        <div class="fleet-item-info"><div class="fleet-item-model">${item.model}</div><div class="fleet-item-plate">${item.plate}</div></div>
        <span class="badge ${item.badge}"><span class="badge-dot"></span>${item.label}</span>
      </div>`).join('');
  }
