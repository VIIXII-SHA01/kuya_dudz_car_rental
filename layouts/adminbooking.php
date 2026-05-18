<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>REVV — Booking List</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/adminbooking.css">
</head>
<body>
<div class="app">

  <!-- ══ SIDEBAR ══ -->
  <?php include("../navs/adminnavs.php"); ?>

  <!-- ══ MAIN ══ -->
  <div class="main">

    <!-- TOPBAR -->
    <header class="topbar">
      <div class="topbar-title">Bookings</div>
      <div class="topbar-divider"></div>
      <div class="search-wrap">
        <svg class="search-icon" width="15" height="15" viewBox="0 0 15 15" fill="none" color="white"><circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.4"/><path d="M10.5 10.5l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <input class="search-input" id="searchInput" placeholder="Search by customer, vehicle, plate, ID…" oninput="filterTable()">
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

      <!-- Page header -->
      <div class="page-header">
        <div class="page-header-left">
          <div class="page-eyebrow">Fleet Management</div>
          <div class="page-title">Booking List</div>
          <div class="page-sub">Manage and track all vehicle reservations</div>
        </div>
        <div style="display:flex;gap:10px;align-items:center">
          <button class="btn-ghost" onclick="exportCSV()" style="padding:11px 16px;display:flex;align-items:center;gap:7px">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M2 10v2h10v-2M7 2v7M4 6l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Export
          </button>
          <button class="btn-primary" onclick="openModal()">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="white"><path d="M7 2v10M2 7h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <span>New Booking</span>
          </button>
        </div>
      </div>

      <!-- Summary strip -->
      <div class="summary-strip">
        <div class="sstrip-item">
          <div class="sstrip-val">15</div>
          <div class="sstrip-lab">Total Bookings</div>
        </div>
        <div class="sstrip-item">
          <div class="sstrip-val" style="color:var(--green)">7</div>
          <div class="sstrip-lab">Active Rentals</div>
        </div>
        <div class="sstrip-item">
          <div class="sstrip-val" style="color:var(--gold)">3</div>
          <div class="sstrip-lab">Pending</div>
        </div>
        <div class="sstrip-item">
          <div class="sstrip-val" style="color:var(--blue)">4</div>
          <div class="sstrip-lab">Completed</div>
        </div>
        <div class="sstrip-item">
          <div class="sstrip-val" style="color:var(--red)">1</div>
          <div class="sstrip-lab">Canceled</div>
        </div>
      </div>

      <!-- Filter bar -->
      <div class="filter-bar">
        <div class="filter-tabs">
          <button class="ftab active" data-filter="all" onclick="setFilter('all',this)">All</button>
          <button class="ftab" data-filter="active" onclick="setFilter('active',this)">Active</button>
          <button class="ftab" data-filter="pending" onclick="setFilter('pending',this)">Pending</button>
          <button class="ftab" data-filter="done" onclick="setFilter('done',this)">Completed</button>
          <button class="ftab" data-filter="canceled" onclick="setFilter('canceled',this)">Canceled</button>
        </div>

        <select class="filter-select" onchange="filterTable()">
          <option value="">All Vehicles</option>
          <option>Toyota Vios</option>
          <option>Honda City</option>
          <option>Mitsubishi Mirage</option>
          <option>Ford EcoSport</option>
          <option>Hyundai Accent</option>
          <option>Suzuki Swift</option>
        </select>

        <div class="filter-date-range">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><rect x="1.5" y="2.5" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M4 2.5V1M9 2.5V1M1.5 5.5h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          Apr 1 – Apr 30, 2026
        </div>

        <div class="filter-spacer"></div>
        <div class="results-count" id="resultsCount"><strong>15</strong> bookings found</div>
      </div>

      <!-- Table -->
      <div class="table-card">
        <div class="table-wrap">
          <table id="bookingTable">
            <thead>
              <tr>
                <th style="width:40px"><div class="cb-wrap"><input type="checkbox" class="cb" id="checkAll" onchange="toggleAll(this)"></div></th>
                <th onclick="sortTable('id')" class="sorted">
                  <div class="th-inner">Booking ID <svg class="sort-icon" width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M5 2v6M2 5l3-3 3 3" stroke="#E8341A" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                </th>
                <th onclick="sortTable('customer')"><div class="th-inner">Customer <svg class="sort-icon" width="10" height="10" viewBox="0 0 10 10" fill="none" color="#6A6E75"><path d="M5 2v6M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div></th>
                <th onclick="sortTable('vehicle')"><div class="th-inner">Vehicle <svg class="sort-icon" width="10" height="10" viewBox="0 0 10 10" fill="none" color="#6A6E75"><path d="M5 2v6M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div></th>
                <th>Plate No.</th>
                <th onclick="sortTable('pickup')"><div class="th-inner">Pickup Date <svg class="sort-icon" width="10" height="10" viewBox="0 0 10 10" fill="none" color="#6A6E75"><path d="M5 2v6M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div></th>
                <th>Return Date</th>
                <th>Duration</th>
                <th onclick="sortTable('amount')"><div class="th-inner">Amount <svg class="sort-icon" width="10" height="10" viewBox="0 0 10 10" fill="none" color="#6A6E75"><path d="M5 2v6M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div></th>
                <th>Status</th>
                <th style="text-align:center">Actions</th>
              </tr>
            </thead>
            <tbody id="tableBody"></tbody>
          </table>
        </div>

        <!-- Empty state -->
        <div class="empty-state" id="emptyState">
          <div class="empty-icon">
            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" color="#E8341A">
              <rect x="3" y="7" width="26" height="20" rx="3" stroke="currentColor" stroke-width="1.8"/>
              <path d="M10 7V5M22 7V5M3 13h26" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
              <path d="M12 20h8M12 24h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            </svg>
          </div>
          <div class="empty-title">No Bookings Found</div>
          <div class="empty-sub">No bookings match your current filters. Try adjusting the search or status filter.</div>
        </div>

        <!-- Footer -->
        <div class="table-footer">
          <div class="tf-info" id="tfInfo">Showing <strong>1–10</strong> of <strong>15</strong> bookings</div>
          <div class="pagination">
            <button class="pg-btn" disabled>
              <svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M8 2L4 6l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            </button>
            <button class="pg-btn active">1</button>
            <button class="pg-btn">2</button>
            <button class="pg-btn">
              <svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M4 2l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- ══ ADD BOOKING MODAL ══ -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModalOutside(event)">
  <div class="modal" id="modal">
    <div class="modal-head">
      <div>
        <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--red);margin-bottom:4px">Fleet Management</div>
        <div class="modal-title">New Booking</div>
      </div>
      <div class="modal-close" onclick="closeModal()">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      </div>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Customer Name</label>
          <input class="form-input" id="f-customer" placeholder="Full name" />
        </div>
        <div class="form-group">
          <label class="form-label">Contact / Email</label>
          <input class="form-input" id="f-email" placeholder="email@example.com" />
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Vehicle</label>
          <select class="form-select" id="f-vehicle">
            <option value="">Select vehicle…</option>
            <option>Toyota Vios 1.3L</option>
            <option>Honda City RS</option>
            <option>Mitsubishi Mirage</option>
            <option>Ford EcoSport</option>
            <option>Hyundai Accent</option>
            <option>Suzuki Swift</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Plate Number</label>
          <input class="form-input" id="f-plate" placeholder="e.g. ABC-1234" />
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Pickup Date</label>
          <input class="form-input" id="f-pickup" type="date" />
        </div>
        <div class="form-group">
          <label class="form-label">Return Date</label>
          <input class="form-input" id="f-return" type="date" />
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Amount (₱)</label>
          <input class="form-input" id="f-amount" type="number" placeholder="0.00" />
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select class="form-select" id="f-status">
            <option value="pending">Pending</option>
            <option value="active">Active</option>
            <option value="done">Completed</option>
            <option value="canceled">Canceled</option>
          </select>
        </div>
      </div>
      <div class="form-row full">
        <div class="form-group">
          <label class="form-label">Notes (optional)</label>
          <input class="form-input" id="f-notes" placeholder="Any special requests or notes…" />
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-ghost" onclick="closeModal()">Cancel</button>
      <button class="btn-primary" onclick="addBooking()">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M2 6.5l3.5 3.5 5.5-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Save Booking</span>
      </button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">
  <svg width="15" height="15" viewBox="0 0 15 15" fill="none" id="toastIcon"><circle cx="7.5" cy="7.5" r="6" stroke="#E8341A" stroke-width="1.3"/></svg>
  <span id="toastMsg"></span>
</div>

<script src="../javascript/adminbooking.js"></script>
</body>
</html>