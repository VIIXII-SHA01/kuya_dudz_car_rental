<!DOCTYPE html>
<html lang="en">
<head>
<?php include __DIR__ . '/../includes/favicon.php'; ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KDCR — Payments</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/rent/css/adminpayments.css">
<link rel="stylesheet" href="/rent/css/theme.css">
</head>
<body>
<div class="app">

  <!-- ══ SIDEBAR ══ -->
  <?php include __DIR__ . '/../navs/adminnavs.php'; ?>

  <!-- Sidebar Overlay -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- ══ MAIN ══ -->
  <div class="main">
    <header class="topbar">
      <!-- Hamburger — shown only when sidebar is collapsed (via CSS) -->
      <button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" color="white">
          <path d="M3 5h12M3 9h12M3 13h12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
      </button>

      <div class="topbar-title">Payments</div>
      <div class="topbar-divider"></div>
      <div class="search-wrap">
        <svg class="search-icon" width="15" height="15" viewBox="0 0 15 15" fill="none" color="white"><circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.4"/><path d="M10.5 10.5l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <input class="search-input" id="searchInput" placeholder="Search by payment ID, customer, rental…" oninput="filterPayments()">
      </div>
      <div class="topbar-right">
        <div class="topbar-date">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><rect x="1.5" y="2.5" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M4 2.5V1M9 2.5V1M1.5 5.5h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          <span id="topbarDateText">Sat, 12 April 2026</span>
        </div>
        <button id="themeToggle" class="icon-btn theme-toggle" type="button" aria-label="Toggle theme" title="Toggle theme">
          <span class="theme-toggle-icon">☀️</span>
        </button>
        <div class="icon-btn">
          <svg width="17" height="17" viewBox="0 0 17 17" fill="none" color="#9A9DA4"><path d="M8.5 2a5 5 0 0 1 5 5v3l1.5 2H2L3.5 10V7a5 5 0 0 1 5-5z" stroke="currentColor" stroke-width="1.4"/><path d="M7 13.5a1.5 1.5 0 0 0 3 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
          <div class="notif-dot"></div>
        </div>
        <div class="icon-btn">
          <div id="topbarUserInitials" style="width:22px;height:22px;background:linear-gradient(135deg,var(--red),var(--orange));border-radius:2px;display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:11px;color:white">JG</div>
        </div>
      </div>
    </header>

    <div class="content">

      <div class="page-header">
        <div>
          <div class="page-eyebrow">Business Management</div>
          <div class="page-title">Payments</div>
          <div class="page-sub">Monitor transactions, balances, and payment statuses across all rentals</div>
        </div>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
          <div class="view-toggle">
            <button class="vtog active" id="gridToggle" title="Grid view" onclick="setView('grid')">
              <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="currentColor"><rect x="1.5" y="1.5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="8.5" y="1.5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="1.5" y="8.5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="8.5" y="8.5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/></svg>
            </button>
            <button class="vtog" id="listToggle" title="List view" onclick="setView('list')">
              <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="currentColor"><path d="M5 4h8M5 7.5h8M5 11h8M2 4h.5M2 7.5h.5M2 11h.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
            </button>
          </div>
          <button class="btn-ghost" onclick="exportCSV()">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M2 10v2h10v-2M7 2v7M4 6l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Export
          </button>
          <button class="btn-primary" onclick="openAddModal()">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="white"><path d="M7 2v10M2 7h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <span>Record Payment</span>
          </button>
        </div>
      </div>

      <!-- Summary strip -->
      <div class="summary-strip">
        <div class="sstrip-item"><div class="sstrip-val" id="strip-total">₱0</div><div class="sstrip-lab">Total Collected</div></div>
        <div class="sstrip-item"><div class="sstrip-val" style="color:var(--green)" id="strip-paid">0</div><div class="sstrip-lab">Paid</div></div>
        <div class="sstrip-item"><div class="sstrip-val" style="color:var(--gold)" id="strip-pending">0</div><div class="sstrip-lab">Pending</div></div>
        <div class="sstrip-item"><div class="sstrip-val" style="color:var(--red)" id="strip-overdue">0</div><div class="sstrip-lab">Overdue</div></div>
        <div class="sstrip-item"><div class="sstrip-val" style="color:var(--purple)" id="strip-partial">0</div><div class="sstrip-lab">Partial</div></div>
      </div>

      <!-- Filter bar -->
      <div class="filter-bar">
        <div class="filter-tabs">
          <button class="ftab active" onclick="setFilter('all',this)">All</button>
          <button class="ftab" onclick="setFilter('paid',this)">Paid</button>
          <button class="ftab" onclick="setFilter('pending',this)">Pending</button>
          <button class="ftab" onclick="setFilter('overdue',this)">Overdue</button>
          <button class="ftab" onclick="setFilter('partial',this)">Partial</button>
          <button class="ftab" onclick="setFilter('refunded',this)">Refunded</button>
        </div>
        <select class="filter-select" onchange="filterPayments()" id="methodFilter">
          <option value="">All Methods</option>
          <option value="Cash">Cash</option>
          <option value="Card">Card</option>
          <option value="GCash">GCash</option>
          <option value="Maya">Maya</option>
          <option value="Bank Transfer">Bank Transfer</option>
        </select>
        <div class="filter-spacer"></div>
        <div class="results-count" id="resultsCount"><strong>0</strong> payments</div>
      </div>

      <!-- GRID -->
      <div class="payments-grid" id="gridView"></div>
      <div class="empty-state" id="emptyGrid">
        <div class="empty-icon"><svg width="32" height="32" viewBox="0 0 32 32" fill="none" color="#E8341A"><rect x="4" y="6" width="24" height="20" rx="3" stroke="currentColor" stroke-width="1.8"/><path d="M4 12h24M10 18h4M10 22h8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></div>
        <div class="empty-title">No Payments Found</div>
        <div class="empty-sub">No payments match your current filters. Try adjusting the search or status filter.</div>
      </div>

      <!-- TABLE -->
      <div class="table-card" id="tableView">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th><div class="th-inner">Payment ID <svg width="10" height="10" viewBox="0 0 10 10" fill="none" color="#6A6E75"><path d="M5 2v6M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div></th>
                <th>Customer</th>
                <th>Rental</th>
                <th>Method</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Balance</th>
                <th>Status</th>
                <th style="text-align:center">Actions</th>
              </tr>
            </thead>
            <tbody id="tableBody"></tbody>
          </table>
        </div>
        <div class="empty-state" id="emptyTable">
          <div class="empty-icon"><svg width="32" height="32" viewBox="0 0 32 32" fill="none" color="#E8341A"><rect x="4" y="6" width="24" height="20" rx="3" stroke="currentColor" stroke-width="1.8"/><path d="M4 12h24M10 18h4M10 22h8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></div>
          <div class="empty-title">No Payments Found</div>
          <div class="empty-sub">No payments match your current filters.</div>
        </div>
        <div class="table-footer">
          <div class="tf-info" id="tfInfo">Showing <strong>—</strong></div>
          <div class="pagination">
            <button class="pg-btn" disabled><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M8 2L4 6l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>
            <button class="pg-btn active">1</button>
            <button class="pg-btn">2</button>
            <button class="pg-btn"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M4 2l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- ══ ADD/EDIT MODAL ══ -->
<div class="modal-overlay" id="addModal" onclick="closeModalOutside(event,'addModal')">
  <div class="modal">
    <div class="modal-head">
      <div>
        <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--red);margin-bottom:4px">Business Management</div>
        <div class="modal-title" id="modalTitle">Record Payment</div>
      </div>
      <div class="modal-close" onclick="closeModal('addModal')">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      </div>
    </div>
    <div class="modal-body">
      <div class="section-divider">Transaction Info</div>
      <div class="form-row">
        <div class="form-group autocomplete-group">
          <label class="form-label">Customer Name</label>
          <input class="form-input" id="f-customer" placeholder="e.g. Maria Santos" autocomplete="off">
          <div class="autocomplete-dropdown" id="customerSuggestions" style="display:none"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Customer ID</label>
          <input class="form-input" id="f-cusid" placeholder="CUS-001">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Rental ID</label>
          <input class="form-input" id="f-rentalid" placeholder="RNT-001">
        </div>
        <div class="form-group">
          <label class="form-label">Payment Date</label>
          <input class="form-input" id="f-date" type="date">
        </div>
      </div>

      <div class="section-divider">Amount & Method</div>
      <div class="form-row three">
        <div class="form-group">
          <label class="form-label">Total Due (₱)</label>
          <input class="form-input" id="f-due" type="number" placeholder="5000" oninput="calcBalance()">
        </div>
        <div class="form-group">
          <label class="form-label">Amount Paid (₱)</label>
          <input class="form-input" id="f-paid" type="number" placeholder="5000" oninput="calcBalance()">
        </div>
        <div class="form-group">
          <label class="form-label">Balance (₱)</label>
          <input class="form-input" id="f-balance" placeholder="Auto-calculated" readonly style="color:var(--red);font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:15px;">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Payment Method</label>
          <select class="form-select" id="f-method">
            <option value="Cash">Cash</option>
            <option value="Card">Card</option>
            <option value="GCash">GCash</option>
            <option value="Maya">Maya</option>
            <option value="Bank Transfer">Bank Transfer</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Reference No.</label>
          <input class="form-input" id="f-ref" placeholder="e.g. GC-2026-00123">
        </div>
      </div>
      <div class="form-row full">
        <div class="form-group">
          <label class="form-label">Status</label>
          <select class="form-select" id="f-status">
            <option value="paid">Paid</option>
            <option value="pending">Pending</option>
            <option value="overdue">Overdue</option>
            <option value="partial">Partial</option>
            <option value="refunded">Refunded</option>
          </select>
        </div>
      </div>
      <div class="form-row full" style="margin-top:4px">
        <div class="form-group">
          <label class="form-label">Notes (optional)</label>
          <textarea class="form-textarea" id="f-notes" placeholder="Any additional notes about this payment…"></textarea>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-ghost" onclick="closeModal('addModal')">Cancel</button>
      <button class="btn-primary" onclick="savePayment()">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M2 6.5l3.5 3.5 5.5-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span id="saveBtnLabel">Save Payment</span>
      </button>
    </div>
  </div>
</div>

<!-- ══ DETAIL MODAL ══ -->
<div class="modal-overlay" id="detailModal" onclick="closeModalOutside(event,'detailModal')">
  <div class="modal" style="max-width:520px">
    <div class="modal-head">
      <div>
        <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--red);margin-bottom:4px">Payment Record</div>
        <div class="modal-title" id="detailTitle">—</div>
      </div>
      <div class="modal-close" onclick="closeModal('detailModal')">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      </div>
    </div>
    <div id="detailContent"></div>
    <div class="modal-footer">
      <button class="btn-ghost" onclick="closeModal('detailModal')">Close</button>
      <button class="btn-primary" id="detailEditBtn">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M2 9.5L9.5 2l1.5 1.5-7.5 7.5H2V9.5z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Edit Payment</span>
      </button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">
  <svg width="15" height="15" viewBox="0 0 15 15" fill="none" id="toastIcon"></svg>
  <span id="toastMsg"></span>
</div>
<script src="/rent/javascript/theme.js"></script><script src="/rent/javascript/adminpayments.js"></script>
<script src="/rent/javascript/admindashboard.js"></script>
</body>
</html>