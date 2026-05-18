<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>REVV — Vehicles</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/adminvehicles.css">
</head>
<body>
<div class="app">
  <!-- ══ SIDEBAR ══ -->
 <?php include("../navs/adminnavs.php"); ?>

  <!-- ══ MAIN ══ -->
  <div class="main">
    <header class="topbar">
      <div class="topbar-title">Vehicles</div>
      <div class="topbar-divider"></div>
      <div class="search-wrap">
        <svg class="search-icon" width="15" height="15" viewBox="0 0 15 15" fill="none" color="white"><circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.4"/><path d="M10.5 10.5l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <input class="search-input" id="searchInput" placeholder="Search by model, plate, type…" oninput="filterVehicles()">
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

      <!-- Page header -->
      <div class="page-header">
        <div>
          <div class="page-eyebrow">Fleet Management</div>
          <div class="page-title">Vehicles</div>
          <div class="page-sub">Manage your entire rental fleet — add, edit, and track vehicle status</div>
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
            <span>Add Vehicle</span>
          </button>
        </div>
      </div>

      <!-- Summary strip -->
      <div class="summary-strip">
        <div class="sstrip-item"><div class="sstrip-val">20</div><div class="sstrip-lab">Total Fleet</div></div>
        <div class="sstrip-item"><div class="sstrip-val" style="color:var(--green)">9</div><div class="sstrip-lab">Available</div></div>
        <div class="sstrip-item"><div class="sstrip-val" style="color:var(--gold)">7</div><div class="sstrip-lab">On Rent</div></div>
        <div class="sstrip-item"><div class="sstrip-val" style="color:var(--blue)">2</div><div class="sstrip-lab">Reserved</div></div>
        <div class="sstrip-item"><div class="sstrip-val" style="color:var(--red)">2</div><div class="sstrip-lab">Maintenance</div></div>
      </div>

      <!-- Filter bar -->
      <div class="filter-bar">
        <div class="filter-tabs">
          <button class="ftab active" onclick="setFilter('all',this)">All</button>
          <button class="ftab" onclick="setFilter('available',this)">Available</button>
          <button class="ftab" onclick="setFilter('rented',this)">On Rent</button>
          <button class="ftab" onclick="setFilter('reserved',this)">Reserved</button>
          <button class="ftab" onclick="setFilter('maintenance',this)">Maintenance</button>
        </div>
        <select class="filter-select" onchange="filterVehicles()" id="typeFilter">
          <option value="">All Types</option>
          <option>Sedan</option>
          <option>SUV</option>
          <option>Hatchback</option>
          <option>Van</option>
          <option>Pickup</option>
        </select>
        <select class="filter-select" onchange="filterVehicles()" id="brandFilter">
          <option value="">All Brands</option>
          <option>Toyota</option>
          <option>Honda</option>
          <option>Mitsubishi</option>
          <option>Ford</option>
          <option>Hyundai</option>
          <option>Suzuki</option>
          <option>Nissan</option>
        </select>
        <div class="filter-spacer"></div>
        <div class="results-count" id="resultsCount"><strong>20</strong> vehicles</div>
      </div>

      <!-- GRID VIEW -->
      <div class="vehicles-grid" id="gridView"></div>

      <!-- TABLE VIEW -->
      <div class="table-card" id="tableView">
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th style="width:70px">Photo</th>
                <th><div class="th-inner">Vehicle <svg width="10" height="10" viewBox="0 0 10 10" fill="none" color="#6A6E75"><path d="M5 2v6M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div></th>
                <th>Plate No.</th>
                <th>Type</th>
                <th>Year</th>
                <th>Fuel</th>
                <th>Seats</th>
                <th><div class="th-inner">Daily Rate</div></th>
                <th>Status</th>
                <th style="text-align:center">Actions</th>
              </tr>
            </thead>
            <tbody id="tableBody"></tbody>
          </table>
        </div>
        <div class="empty-state" id="emptyStateTable">
          <div class="empty-icon"><svg width="32" height="32" viewBox="0 0 32 32" fill="none" color="#E8341A"><path d="M4 19L8 9h16l4 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><rect x="3" y="18" width="26" height="8" rx="3" stroke="currentColor" stroke-width="1.8"/><circle cx="9" cy="26" r="2.5" stroke="currentColor" stroke-width="1.5"/><circle cx="23" cy="26" r="2.5" stroke="currentColor" stroke-width="1.5"/></svg></div>
          <div class="empty-title">No Vehicles Found</div>
          <div class="empty-sub">No vehicles match your current filters.</div>
        </div>
        <div class="table-footer">
          <div class="tf-info" id="tfInfo">Showing <strong>1–10</strong> of <strong>20</strong></div>
          <div class="pagination">
            <button class="pg-btn" disabled><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M8 2L4 6l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>
            <button class="pg-btn active">1</button>
            <button class="pg-btn">2</button>
            <button class="pg-btn"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M4 2l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>
          </div>
        </div>
      </div>

      <!-- GRID empty state -->
      <div class="empty-state" id="emptyStateGrid">
        <div class="empty-icon"><svg width="32" height="32" viewBox="0 0 32 32" fill="none" color="#E8341A"><path d="M4 19L8 9h16l4 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><rect x="3" y="18" width="26" height="8" rx="3" stroke="currentColor" stroke-width="1.8"/><circle cx="9" cy="26" r="2.5" stroke="currentColor" stroke-width="1.5"/><circle cx="23" cy="26" r="2.5" stroke="currentColor" stroke-width="1.5"/></svg></div>
        <div class="empty-title">No Vehicles Found</div>
        <div class="empty-sub">No vehicles match your current filters. Try adjusting the search or status filter.</div>
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
        <div class="modal-title" id="modalTitle">Add Vehicle</div>
      </div>
      <div class="modal-close" onclick="closeModal('addModal')">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      </div>
    </div>
    <div class="modal-body">
      <div class="section-divider">Vehicle Info</div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Brand / Make</label>
          <select class="form-select" id="f-brand">
            <option value="">Select brand…</option>
            <option>Toyota</option><option>Honda</option><option>Mitsubishi</option>
            <option>Ford</option><option>Hyundai</option><option>Suzuki</option><option>Nissan</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Model</label>
          <input class="form-input" id="f-model" placeholder="e.g. Vios 1.3L" />
        </div>
      </div>
      <div class="form-row three">
        <div class="form-group">
          <label class="form-label">Year</label>
          <input class="form-input" id="f-year" type="number" placeholder="2024" min="2000" max="2030" />
        </div>
        <div class="form-group">
          <label class="form-label">Color</label>
          <input class="form-input" id="f-color" placeholder="e.g. Pearl White" />
        </div>
        <div class="form-group">
          <label class="form-label">Type</label>
          <select class="form-select" id="f-type">
            <option value="">Select…</option>
            <option>Sedan</option><option>SUV</option><option>Hatchback</option><option>Van</option><option>Pickup</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Plate Number</label>
          <input class="form-input" id="f-plate" placeholder="e.g. ABC-1234" />
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select class="form-select" id="f-status">
            <option value="available">Available</option>
            <option value="rented">On Rent</option>
            <option value="reserved">Reserved</option>
            <option value="maintenance">Maintenance</option>
          </select>
        </div>
      </div>
      <div class="section-divider">Specs & Pricing</div>
      <div class="form-row three">
        <div class="form-group">
          <label class="form-label">Seats</label>
          <input class="form-input" id="f-seats" type="number" placeholder="5" min="2" max="15" />
        </div>
        <div class="form-group">
          <label class="form-label">Fuel Type</label>
          <select class="form-select" id="f-fuel">
            <option>Gasoline</option><option>Diesel</option><option>Electric</option><option>Hybrid</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Transmission</label>
          <select class="form-select" id="f-trans">
            <option>Automatic</option><option>Manual</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Daily Rate (₱)</label>
          <input class="form-input" id="f-rate" type="number" placeholder="800" />
        </div>
        <div class="form-group">
          <label class="form-label">Mileage (km)</label>
          <input class="form-input" id="f-mileage" type="number" placeholder="12000" />
        </div>
      </div>
      <div class="section-divider">Photo</div>
      <div class="form-row full">
        <div class="upload-zone" onclick="showToast('Photo upload — coming soon!','success')">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none" color="#E8341A"><rect x="2" y="6" width="24" height="17" rx="3" stroke="currentColor" stroke-width="1.6"/><circle cx="9" cy="13" r="2.5" stroke="currentColor" stroke-width="1.5"/><path d="M2 19l7-6 5 5 4-4 8 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 3l3 3-3 3M21 6H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
          <div class="upload-label"><strong>Click to upload</strong> or drag & drop</div>
          <div class="upload-sub">PNG, JPG up to 5MB</div>
        </div>
      </div>
      <div class="form-row full" style="margin-top:4px">
        <div class="form-group">
          <label class="form-label">Notes (optional)</label>
          <textarea class="form-textarea" id="f-notes" placeholder="Any additional info about this vehicle…"></textarea>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-ghost" onclick="closeModal('addModal')">Cancel</button>
      <button class="btn-primary" onclick="saveVehicle()">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M2 6.5l3.5 3.5 5.5-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span id="modalSaveLabel">Save Vehicle</span>
      </button>
    </div>
  </div>
</div>

<!-- ══ DETAIL MODAL ══ -->
<div class="modal-overlay" id="detailModal" onclick="closeModalOutside(event,'detailModal')">
  <div class="modal" style="max-width:520px">
    <div class="modal-head">
      <div>
        <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--red);margin-bottom:4px">Vehicle Details</div>
        <div class="modal-title" id="detailTitle">—</div>
      </div>
      <div class="modal-close" onclick="closeModal('detailModal')">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      </div>
    </div>
    <div id="detailContent"></div>
    <div class="modal-footer">
      <button class="btn-ghost" onclick="closeModal('detailModal')">Close</button>
      <button class="btn-primary" id="detailEditBtn" onclick="">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M2 9.5L9.5 2l1.5 1.5-7.5 7.5H2V9.5z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span>Edit Vehicle</span>
      </button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">
  <svg width="15" height="15" viewBox="0 0 15 15" fill="none" id="toastIcon"></svg>
  <span id="toastMsg"></span>
</div>

<script src="../javascript/adminvehicles.js"></script>
</body>
</html>