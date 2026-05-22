<!DOCTYPE html>
<html lang="en">
<head>
<?php include __DIR__ . '/../includes/favicon.php'; ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KDCR — Drivers</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/rent/css/admindrivers.css">
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

      <div class="topbar-title">Drivers</div>
      <div class="topbar-divider"></div>
      <div class="search-wrap">
        <svg class="search-icon" width="15" height="15" viewBox="0 0 15 15" fill="none" color="white"><circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.4"/><path d="M10.5 10.5l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <input class="search-input" id="searchInput" placeholder="Search by name, license, phone…" oninput="filterDrivers()">
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
          <div class="page-eyebrow">Fleet Management</div>
          <div class="page-title">Drivers</div>
          <div class="page-sub">Manage driver profiles, licenses, and availability</div>
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
          <button class="btn-ghost" id="exportBtn" onclick="exportCSV()">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M2 10v2h10v-2M7 2v7M4 6l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Export
          </button>
          <button class="btn-primary" id="addDriverBtn" onclick="openAddModal()">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="white"><path d="M7 2v10M2 7h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <span>Add Driver</span>
          </button>
        </div>
      </div>

      <!-- Summary strip -->
      <div class="summary-strip">
        <div class="sstrip-item"><div class="sstrip-val" id="totalDriversCount">0</div><div class="sstrip-lab">Total Drivers</div></div>
        <div class="sstrip-item"><div class="sstrip-val" id="availableDriversCount" style="color:var(--green)">0</div><div class="sstrip-lab">Available</div></div>
        <div class="sstrip-item"><div class="sstrip-val" id="rentedDriversCount" style="color:var(--gold)">0</div><div class="sstrip-lab">Rented</div></div>
        <div class="sstrip-item"><div class="sstrip-val" id="dayoffDriversCount" style="color:var(--muted2)">0</div><div class="sstrip-lab">Day Off</div></div>
        <div class="sstrip-item"><div class="sstrip-val" id="suspendedDriversCount" style="color:var(--red)">0</div><div class="sstrip-lab">Suspended</div></div>
      </div>

      <!-- Filter bar -->
      <div class="filter-bar">
        <div class="filter-tabs">
          <button class="ftab active" onclick="setFilter('all',this)">All</button>
          <button class="ftab" onclick="setFilter('available',this)">Available</button>
          <button class="ftab" onclick="setFilter('rented',this)">Rented</button>
          <button class="ftab" onclick="setFilter('dayoff',this)">Day Off</button>
          <button class="ftab" onclick="setFilter('suspended',this)">Suspended</button>
        </div>
        <select class="filter-select" onchange="filterDrivers()" id="expFilter">
          <option value="">All Experience</option>
          <option value="junior">Junior (&lt;3 yrs)</option>
          <option value="mid">Mid (3–7 yrs)</option>
          <option value="senior">Senior (7+ yrs)</option>
        </select>
        <div class="filter-spacer"></div>
        <div class="results-count" id="resultsCount"><strong>0</strong> drivers</div>
      </div>

      <!-- GRID -->
      <div class="drivers-grid" id="gridView"></div>

      <!-- Empty grid -->
      <div class="empty-state" id="emptyGrid">
        <div class="empty-icon"><svg width="32" height="32" viewBox="0 0 32 32" fill="none" color="#E8341A"><circle cx="16" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/><path d="M5 29c0-6.08 4.92-11 11-11s11 4.92 11 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></div>
        <div class="empty-title">No Drivers Found</div>
        <div class="empty-sub">No drivers match your current filters. Try adjusting the search or status filter.</div>
      </div>

      <!-- TABLE -->
      <div class="table-card" id="tableView">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th><div class="th-inner">Driver <svg width="10" height="10" viewBox="0 0 10 10" fill="none" color="#6A6E75"><path d="M5 2v6M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div></th>
                <th>License No.</th>
                <th>Phone</th>
                <th>Experience</th>
                <th>Rating</th>
                <th>Trips</th>
                <th>Status</th>
                <th style="text-align:center">Actions</th>
              </tr>
            </thead>
            <tbody id="tableBody"></tbody>
          </table>
        </div>
        <div class="empty-state" id="emptyTable">
          <div class="empty-icon"><svg width="32" height="32" viewBox="0 0 32 32" fill="none" color="#E8341A"><circle cx="16" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/><path d="M5 29c0-6.08 4.92-11 11-11s11 4.92 11 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></div>
          <div class="empty-title">No Drivers Found</div>
          <div class="empty-sub">No drivers match your current filters.</div>
        </div>
        <div class="table-footer">
          <div class="tf-info" id="tfInfo">Showing <strong>0</strong> of <strong>0</strong></div>
          <div class="pagination" id="pagination"></div>
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
        <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--red);margin-bottom:4px">Fleet Management</div>
        <div class="modal-title" id="modalTitle">Add Driver</div>
      </div>
      <div class="modal-close" onclick="closeModal('addModal')">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      </div>
    </div>
    <div class="modal-body">
      <div class="section-divider">Personal Info</div>
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px">
        <div id="modalAvatarPreview" style="width:60px;height:60px;border-radius:4px;background:linear-gradient(135deg,#E8341A,#F5642A);display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:24px;color:white;flex-shrink:0;position:relative;overflow:hidden">
          <span id="avatarInitials">?</span>
          <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,0.12),transparent 60%)"></div>
        </div>
        <div class="avatar-picker" id="avatarPicker">
          <div class="av-opt selected" style="background:linear-gradient(135deg,#E8341A,#F5642A)" data-bg="linear-gradient(135deg,#E8341A,#F5642A)" onclick="pickAvatar(this)"></div>
          <div class="av-opt" style="background:linear-gradient(135deg,#3D8FBE,#3DBE7A)" data-bg="linear-gradient(135deg,#3D8FBE,#3DBE7A)" onclick="pickAvatar(this)"></div>
          <div class="av-opt" style="background:linear-gradient(135deg,#D4A843,#F5642A)" data-bg="linear-gradient(135deg,#D4A843,#F5642A)" onclick="pickAvatar(this)"></div>
          <div class="av-opt" style="background:linear-gradient(135deg,#3DBE7A,#3D8FBE)" data-bg="linear-gradient(135deg,#3DBE7A,#3D8FBE)" onclick="pickAvatar(this)"></div>
          <div class="av-opt" style="background:linear-gradient(135deg,#9A3DBE,#E8341A)" data-bg="linear-gradient(135deg,#9A3DBE,#E8341A)" onclick="pickAvatar(this)"></div>
          <div class="av-opt" style="background:linear-gradient(135deg,#6A6E75,#3D8FBE)" data-bg="linear-gradient(135deg,#6A6E75,#3D8FBE)" onclick="pickAvatar(this)"></div>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">First Name</label>
          <input class="form-input" id="f-fname" placeholder="Juan" oninput="updateInitials()">
        </div>
        <div class="form-group">
          <label class="form-label">Last Name</label>
          <input class="form-input" id="f-lname" placeholder="Dela Cruz" oninput="updateInitials()">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <input class="form-input" id="f-email" type="email" placeholder="juan@email.com">
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

      <div class="section-divider">License & Experience</div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">License Number</label>
          <input class="form-input" id="f-license" placeholder="N01-23-456789">
        </div>
        <div class="form-group">
          <label class="form-label">License Expiry</label>
          <input class="form-input" id="f-expiry" type="date">
        </div>
      </div>
      <div class="form-row three">
        <div class="form-group">
          <label class="form-label">Experience (yrs)</label>
          <input class="form-input" id="f-exp" type="number" placeholder="5" min="0" max="50">
        </div>
        <div class="form-group">
          <label class="form-label">License Type</label>
          <select class="form-select" id="f-lictype">
            <option>Professional</option>
            <option>Non-Professional</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select class="form-select" id="f-status">
            <option value="available">Available</option>
            <option value="dayoff">Day Off</option>
            <option value="suspended">Suspended</option>
          </select>
        </div>
      </div>

      <div class="section-divider">Documents</div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Driver Photo</label>
          <input class="form-input" type="file" id="f-photo" accept="image/jpeg,image/png">
          <div class="form-help">Upload a profile picture (JPG/PNG).</div>
        </div>
        <div class="form-group">
          <label class="form-label">License / Document</label>
          <input class="form-input" type="file" id="f-document" accept="application/pdf,image/jpeg,image/png">
          <div class="form-help">Upload one document at a time.</div>
        </div>
      </div>

      <div class="form-row full" style="margin-top:16px">
        <div class="form-group">
          <label class="form-label">Notes (optional)</label>
          <textarea class="form-textarea" id="f-notes" placeholder="Any additional notes about this driver…"></textarea>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-ghost" onclick="closeModal('addModal')">Cancel</button>
      <button class="btn-primary" onclick="saveDriver()">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M2 6.5l3.5 3.5 5.5-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span id="saveBtnLabel">Save Driver</span>
      </button>
    </div>
  </div>
</div>

<!-- ══ DETAIL MODAL ══ -->
<div class="modal-overlay" id="detailModal" onclick="closeModalOutside(event,'detailModal')">
  <div class="modal" style="max-width:520px">
    <div class="modal-head">
      <div>
        <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--red);margin-bottom:4px">Driver Profile</div>
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
        <span>Edit Driver</span>
      </button>
    </div>
  </div>
</div>

<!-- DOCUMENT PREVIEW MODAL -->
<div class="modal-overlay" id="docPreviewModal" onclick="closeModalOutside(event,'docPreviewModal')">
  <div class="modal" style="max-width:760px;min-height:460px;">
    <div class="modal-head">
      <div>
        <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--red);margin-bottom:4px">License Preview</div>
        <div class="modal-title" id="docPreviewTitle">Document</div>
      </div>
      <div class="modal-close" onclick="closeModal('docPreviewModal')">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      </div>
    </div>
    <div class="modal-body" style="padding:0;">
      <iframe id="docPreviewFrame" src="" style="width:100%;height:500px;border:none;" title="Document Preview"></iframe>
    </div>
    <div class="modal-footer" style="justify-content:flex-end;">
      <button class="btn-primary" onclick="closeModal('docPreviewModal')">Close</button>
    </div>
  </div>
</div><!-- TOAST -->
<div class="toast" id="toast">
  <svg width="15" height="15" viewBox="0 0 15 15" fill="none" id="toastIcon"></svg>
  <span id="toastMsg"></span>
</div>

<script src="/rent/javascript/theme.js"></script>
<script src="/rent/javascript/admindrivers.js?v=3"></script>
<script src="/rent/javascript/admindashboard.js"></script>
</body>
</html>
