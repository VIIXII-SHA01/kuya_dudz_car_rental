<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>REVV — Customers</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/admincustomers.css">
</head>
<body>
<div class="app">

  <!-- ══ SIDEBAR ══ -->
  <?php include("../navs/adminnavs.php"); ?>

  <!-- ══ MAIN ══ -->
  <div class="main">
    <header class="topbar">
      <div class="topbar-title">Customers</div>
      <div class="topbar-divider"></div>
      <div class="search-wrap">
        <svg class="search-icon" width="15" height="15" viewBox="0 0 15 15" fill="none" color="white"><circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.4"/><path d="M10.5 10.5l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <input class="search-input" id="searchInput" placeholder="Search by name, email, phone…" oninput="filterCustomers()">
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

    <div class="content">

      <div class="page-header">
        <div>
          <div class="page-eyebrow">Business Management</div>
          <div class="page-title">Customers</div>
          <div class="page-sub">Manage customer profiles, rental history, and account status</div>
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
            <span>Add Customer</span>
          </button>
        </div>
      </div>

      <!-- Summary strip -->
      <div class="summary-strip">
        <div class="sstrip-item"><div class="sstrip-val">14</div><div class="sstrip-lab">Total Customers</div></div>
        <div class="sstrip-item"><div class="sstrip-val" style="color:var(--green)">10</div><div class="sstrip-lab">Active</div></div>
        <div class="sstrip-item"><div class="sstrip-val" style="color:var(--gold)">3</div><div class="sstrip-lab">VIP</div></div>
        <div class="sstrip-item"><div class="sstrip-val" style="color:var(--muted2)">3</div><div class="sstrip-lab">Inactive</div></div>
        <div class="sstrip-item"><div class="sstrip-val" style="color:var(--red)">1</div><div class="sstrip-lab">Blacklisted</div></div>
      </div>

      <!-- Filter bar -->
      <div class="filter-bar">
        <div class="filter-tabs">
          <button class="ftab active" onclick="setFilter('all',this)">All</button>
          <button class="ftab" onclick="setFilter('active',this)">Active</button>
          <button class="ftab" onclick="setFilter('vip',this)">VIP</button>
          <button class="ftab" onclick="setFilter('inactive',this)">Inactive</button>
          <button class="ftab" onclick="setFilter('blacklisted',this)">Blacklisted</button>
        </div>
        <select class="filter-select" onchange="filterCustomers()" id="tierFilter">
          <option value="">All Tiers</option>
          <option value="Basic">Basic</option>
          <option value="Silver">Silver</option>
          <option value="Gold">Gold</option>
          <option value="Platinum">Platinum</option>
        </select>
        <div class="filter-spacer"></div>
        <div class="results-count" id="resultsCount"><strong>14</strong> customers</div>
      </div>

      <!-- GRID -->
      <div class="customers-grid" id="gridView"></div>

      <!-- Empty grid -->
      <div class="empty-state" id="emptyGrid">
        <div class="empty-icon"><svg width="32" height="32" viewBox="0 0 32 32" fill="none" color="#E8341A"><circle cx="13" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/><path d="M3 29c0-5.52 4.48-10 10-10s10 4.48 10 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M22 14c2.21 0 4 1.79 4 4M26 14v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></div>
        <div class="empty-title">No Customers Found</div>
        <div class="empty-sub">No customers match your current filters. Try adjusting the search or status filter.</div>
      </div>

      <!-- TABLE -->
      <div class="table-card" id="tableView">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th><div class="th-inner">Customer <svg width="10" height="10" viewBox="0 0 10 10" fill="none" color="#6A6E75"><path d="M5 2v6M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div></th>
                <th>Phone</th>
                <th>Joined</th>
                <th>Tier</th>
                <th>Rentals</th>
                <th>Total Spent</th>
                <th>Status</th>
                <th style="text-align:center">Actions</th>
              </tr>
            </thead>
            <tbody id="tableBody"></tbody>
          </table>
        </div>
        <div class="empty-state" id="emptyTable">
          <div class="empty-icon"><svg width="32" height="32" viewBox="0 0 32 32" fill="none" color="#E8341A"><circle cx="16" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/><path d="M5 29c0-6.08 4.92-11 11-11s11 4.92 11 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></div>
          <div class="empty-title">No Customers Found</div>
          <div class="empty-sub">No customers match your current filters.</div>
        </div>
        <div class="table-footer">
          <div class="tf-info" id="tfInfo">Showing <strong>1–10</strong> of <strong>14</strong></div>
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
        <div class="modal-title" id="modalTitle">Add Customer</div>
      </div>
      <div class="modal-close" onclick="closeModal('addModal')">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      </div>
    </div>
    <div class="modal-body">
      <div class="section-divider">Personal Info</div>
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px">
        <div id="modalAvatarPreview" style="width:60px;height:60px;border-radius:4px;background:linear-gradient(135deg,#3D8FBE,#3DBE7A);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:24px;color:white;flex-shrink:0;position:relative;overflow:hidden">
          <span id="avatarInitials">?</span>
          <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,0.12),transparent 60%)"></div>
        </div>
        <div class="avatar-picker" id="avatarPicker">
          <div class="av-opt" style="background:linear-gradient(135deg,#E8341A,#F5642A)" data-bg="linear-gradient(135deg,#E8341A,#F5642A)" onclick="pickAvatar(this)"></div>
          <div class="av-opt selected" style="background:linear-gradient(135deg,#3D8FBE,#3DBE7A)" data-bg="linear-gradient(135deg,#3D8FBE,#3DBE7A)" onclick="pickAvatar(this)"></div>
          <div class="av-opt" style="background:linear-gradient(135deg,#D4A843,#F5642A)" data-bg="linear-gradient(135deg,#D4A843,#F5642A)" onclick="pickAvatar(this)"></div>
          <div class="av-opt" style="background:linear-gradient(135deg,#3DBE7A,#3D8FBE)" data-bg="linear-gradient(135deg,#3DBE7A,#3D8FBE)" onclick="pickAvatar(this)"></div>
          <div class="av-opt" style="background:linear-gradient(135deg,#9A3DBE,#E8341A)" data-bg="linear-gradient(135deg,#9A3DBE,#E8341A)" onclick="pickAvatar(this)"></div>
          <div class="av-opt" style="background:linear-gradient(135deg,#6A6E75,#3D8FBE)" data-bg="linear-gradient(135deg,#6A6E75,#3D8FBE)" onclick="pickAvatar(this)"></div>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">First Name</label>
          <input class="form-input" id="f-fname" placeholder="Maria" oninput="updateInitials()">
        </div>
        <div class="form-group">
          <label class="form-label">Last Name</label>
          <input class="form-input" id="f-lname" placeholder="Santos" oninput="updateInitials()">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input class="form-input" id="f-email" type="email" placeholder="maria@email.com">
        </div>
        <div class="form-group">
          <label class="form-label">Phone Number</label>
          <input class="form-input" id="f-phone" placeholder="+63 9XX XXX XXXX">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Date of Birth</label>
          <input class="form-input" id="f-dob" type="date">
        </div>
        <div class="form-group">
          <label class="form-label">Address</label>
          <input class="form-input" id="f-address" placeholder="City, Province">
        </div>
      </div>

      <div class="section-divider">Account Details</div>
      <div class="form-row three">
        <div class="form-group">
          <label class="form-label">Membership Tier</label>
          <select class="form-select" id="f-tier">
            <option value="Basic">Basic</option>
            <option value="Silver">Silver</option>
            <option value="Gold">Gold</option>
            <option value="Platinum">Platinum</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">ID Type</label>
          <select class="form-select" id="f-idtype">
            <option>Driver's License</option>
            <option>Passport</option>
            <option>National ID</option>
            <option>Postal ID</option>
            <option>SSS ID</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Account Status</label>
          <select class="form-select" id="f-status">
            <option value="active">Active</option>
            <option value="vip">VIP</option>
            <option value="inactive">Inactive</option>
            <option value="blacklisted">Blacklisted</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Government ID Number</label>
          <input class="form-input" id="f-idnum" placeholder="ID Number">
        </div>
        <div class="form-group">
          <label class="form-label">Emergency Contact</label>
          <input class="form-input" id="f-emergency" placeholder="+63 9XX XXX XXXX">
        </div>
      </div>

      <div class="form-row full" style="margin-top:4px">
        <div class="form-group">
          <label class="form-label">Notes (optional)</label>
          <textarea class="form-textarea" id="f-notes" placeholder="Any additional notes about this customer…"></textarea>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-ghost" onclick="closeModal('addModal')">Cancel</button>
      <button class="btn-primary" onclick="saveCustomer()">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M2 6.5l3.5 3.5 5.5-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span id="saveBtnLabel">Save Customer</span>
      </button>
    </div>
  </div>
</div>

<!-- ══ DETAIL MODAL ══ -->
<div class="modal-overlay" id="detailModal" onclick="closeModalOutside(event,'detailModal')">
  <div class="modal" style="max-width:520px">
    <div class="modal-head">
      <div>
        <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--red);margin-bottom:4px">Customer Profile</div>
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
        <span>Edit Customer</span>
      </button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">
  <svg width="15" height="15" viewBox="0 0 15 15" fill="none" id="toastIcon"></svg>
  <span id="toastMsg"></span>
</div>

<script src="../javascript/admincustomers.js"></script>
</body>
</html>