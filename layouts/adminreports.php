<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>REVV — Reports</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/adminreports.css">
</head>
<body>
<div class="app">

  <!-- ══ SIDEBAR ══ -->
 <?php include("../navs/adminnavs.php"); ?>

  <!-- ══ MAIN ══ -->
  <div class="main">
    <header class="topbar">
      <div class="topbar-title">Reports</div>
      <div class="topbar-divider"></div>
      <div style="font-size:13px;color:var(--muted2)">Business performance overview &amp; analytics</div>
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

    <div class="content">

      <div class="page-header">
        <div>
          <div class="page-eyebrow">Admin</div>
          <div class="page-title">Business Reports</div>
          <div class="page-sub">Fleet performance, revenue trends, and rental analytics</div>
        </div>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
          <button class="btn-ghost" onclick="exportReport()">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M2 10v2h10v-2M7 2v7M4 6l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Export PDF
          </button>
          <button class="btn-primary" onclick="printReport()">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="white"><rect x="3" y="1" width="8" height="5" rx="1" stroke="currentColor" stroke-width="1.4"/><rect x="1" y="5" width="12" height="6" rx="1.5" stroke="currentColor" stroke-width="1.4"/><rect x="3" y="9" width="8" height="4" rx="1" stroke="currentColor" stroke-width="1.4"/><circle cx="11" cy="8" r="0.9" fill="currentColor"/></svg>
            <span>Print Report</span>
          </button>
        </div>
      </div>

      <!-- Period tabs -->
      <div class="period-bar">
        <div class="period-tabs">
          <button class="ptab" onclick="setPeriod('week',this)">This Week</button>
          <button class="ptab" onclick="setPeriod('month',this)">This Month</button>
          <button class="ptab active" onclick="setPeriod('quarter',this)">This Quarter</button>
          <button class="ptab" onclick="setPeriod('year',this)">This Year</button>
        </div>
        <div class="period-spacer"></div>
        <div class="period-range" id="periodRange">Jan 1 – Apr 12, 2026</div>
      </div>

      <!-- KPI Strip -->
      <div class="kpi-strip">
        <div class="kpi-card k-green">
          <div class="kpi-label">Total Revenue</div>
          <div class="kpi-value k-green" id="kpi-revenue">₱273,600</div>
          <div class="kpi-delta up">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M6 9V3M3 6l3-3 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            +18.4% vs last quarter
          </div>
          <div class="kpi-bg-icon"><svg width="72" height="72" viewBox="0 0 72 72" fill="none" color="white"><path d="M12 54l18-22 14 14 20-30" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </div>
        <div class="kpi-card k-blue">
          <div class="kpi-label">Total Rentals</div>
          <div class="kpi-value k-blue" id="kpi-rentals">16</div>
          <div class="kpi-delta up">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M6 9V3M3 6l3-3 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            +5 vs last quarter
          </div>
          <div class="kpi-bg-icon"><svg width="72" height="72" viewBox="0 0 72 72" fill="none" color="white"><path d="M8 44L22 20h28l14 24" stroke="currentColor" stroke-width="3" stroke-linecap="round"/><rect x="6" y="43" width="60" height="14" rx="5" stroke="currentColor" stroke-width="3"/><circle cx="18" cy="57" r="5" stroke="currentColor" stroke-width="3"/><circle cx="54" cy="57" r="5" stroke="currentColor" stroke-width="3"/></svg></div>
        </div>
        <div class="kpi-card k-gold">
          <div class="kpi-label">Avg Rental Value</div>
          <div class="kpi-value k-gold" id="kpi-avg">₱17,100</div>
          <div class="kpi-delta up">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M6 9V3M3 6l3-3 3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            +₱2,340 vs last quarter
          </div>
          <div class="kpi-bg-icon"><svg width="72" height="72" viewBox="0 0 72 72" fill="none" color="white"><circle cx="36" cy="36" r="28" stroke="currentColor" stroke-width="3"/><path d="M36 22v28M28 30l8-8 8 8" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </div>
        <div class="kpi-card k-red">
          <div class="kpi-label">Overdue / Unpaid</div>
          <div class="kpi-value k-red" id="kpi-overdue">₱36,800</div>
          <div class="kpi-delta down">
            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M6 3v6M3 6l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            2 overdue rentals pending
          </div>
          <div class="kpi-bg-icon"><svg width="72" height="72" viewBox="0 0 72 72" fill="none" color="white"><circle cx="36" cy="36" r="28" stroke="currentColor" stroke-width="3"/><path d="M36 24v16M36 46v4" stroke="currentColor" stroke-width="3.5" stroke-linecap="round"/></svg></div>
        </div>
      </div>

      <!-- Charts Row 1 -->
      <div class="charts-row">
        <!-- Revenue Bar Chart -->
        <div class="chart-card">
          <div class="chart-head">
            <div>
              <div class="chart-title">Revenue by Month</div>
              <div class="chart-sub">Gross rental revenue · Jan – Apr 2026</div>
            </div>
            <span class="badge completed"><span class="badge-dot" style="background:var(--green)"></span>Q1 2026</span>
          </div>
          <div class="chart-body">
            <div class="bar-chart" id="revenueChart"></div>
          </div>
        </div>

        <!-- Fleet mix donut -->
        <div class="chart-card">
          <div class="chart-head">
            <div>
              <div class="chart-title">Fleet Mix</div>
              <div class="chart-sub">Rentals by vehicle type</div>
            </div>
          </div>
          <div class="chart-body">
            <div class="donut-wrap">
              <svg class="donut-svg" viewBox="0 0 160 160" id="donutSVG"></svg>
              <div class="donut-legend" id="donutLegend"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Row 2 — metric cards -->
      <div class="charts-row2">
        <div class="metric-card">
          <div class="metric-label">Avg Days per Rental</div>
          <div class="metric-value" style="color:var(--blue)">3.7</div>
          <div class="metric-sub">Days · Q1 2026</div>
          <svg class="sparkline" viewBox="0 0 200 50" preserveAspectRatio="none">
            <polyline points="0,40 40,30 80,35 120,20 160,22 200,10" fill="none" stroke="var(--blue)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.7"/>
            <polyline points="0,50 0,40 40,30 80,35 120,20 160,22 200,10 200,50" fill="var(--blue)" opacity="0.08"/>
          </svg>
        </div>
        <div class="metric-card">
          <div class="metric-label">Fleet Utilization</div>
          <div class="metric-value" style="color:var(--gold)">68%</div>
          <div class="metric-sub">Avg active vehicles per day</div>
          <svg class="sparkline" viewBox="0 0 200 50" preserveAspectRatio="none">
            <polyline points="0,35 40,28 80,32 120,18 160,25 200,15" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.7"/>
            <polyline points="0,50 0,35 40,28 80,32 120,18 160,25 200,15 200,50" fill="var(--gold)" opacity="0.08"/>
          </svg>
        </div>
        <div class="metric-card">
          <div class="metric-label">Repeat Customers</div>
          <div class="metric-value" style="color:var(--green)">44%</div>
          <div class="metric-sub">Returning renters this quarter</div>
          <svg class="sparkline" viewBox="0 0 200 50" preserveAspectRatio="none">
            <polyline points="0,42 40,38 80,30 120,26 160,20 200,14" fill="none" stroke="var(--green)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.7"/>
            <polyline points="0,50 0,42 40,38 80,30 120,26 160,20 200,14 200,50" fill="var(--green)" opacity="0.08"/>
          </svg>
        </div>
      </div>

      <!-- Bottom Row -->
      <div class="bottom-row">

        <!-- Top Vehicles -->
        <div class="chart-card">
          <div class="chart-head">
            <div>
              <div class="chart-title">Top Vehicles</div>
              <div class="chart-sub">By total revenue generated</div>
            </div>
          </div>
          <div style="padding:0 6px">
            <table class="rank-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Vehicle</th>
                  <th style="text-align:right">Revenue</th>
                </tr>
              </thead>
              <tbody id="topVehiclesBody"></tbody>
            </table>
          </div>
        </div>

        <!-- Rental Status + Insights -->
        <div style="display:flex;flex-direction:column;gap:16px">

          <!-- Status breakdown -->
          <div class="chart-card">
            <div class="chart-head">
              <div>
                <div class="chart-title">Rental Status</div>
                <div class="chart-sub">Current distribution</div>
              </div>
            </div>
            <div class="status-grid" id="statusGrid"></div>
          </div>

          <!-- Insights -->
          <div class="chart-card">
            <div class="chart-head">
              <div>
                <div class="chart-title">Key Insights</div>
                <div class="chart-sub">Auto-generated from current data</div>
              </div>
            </div>
            <div class="insight-list" id="insightList"></div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">
  <svg width="15" height="15" viewBox="0 0 15 15" fill="none"><circle cx="7.5" cy="7.5" r="6" stroke="var(--green)" stroke-width="1.3"/><path d="M5 7.5l2 2 3.5-3.5" stroke="var(--green)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
  <span id="toastMsg"></span>
</div>

<script src="../javascript/adminreports.js"></script>
</body>
</html>