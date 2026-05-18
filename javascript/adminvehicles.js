/* ════ DATA ════ */
let vehicles = [
  { id:1, brand:'Toyota',     model:'Vios 1.3L',    year:2023, color:'Pearl White',    type:'Sedan',    plate:'ABC-1234', seats:5, fuel:'Gasoline', trans:'Automatic', rate:800,  mileage:12400, status:'available',   notes:'' },
  { id:2, brand:'Honda',      model:'City RS',       year:2022, color:'Lunar Silver',   type:'Sedan',    plate:'XYZ-5678', seats:5, fuel:'Gasoline', trans:'Automatic', rate:900,  mileage:23100, status:'rented',      notes:'' },
  { id:3, brand:'Mitsubishi', model:'Mirage G4',     year:2021, color:'Jet Black',      type:'Sedan',    plate:'DEF-9012', seats:5, fuel:'Gasoline', trans:'Automatic', rate:750,  mileage:31500, status:'available',   notes:'' },
  { id:4, brand:'Ford',       model:'EcoSport 1.5', year:2023, color:'Race Red',        type:'SUV',      plate:'JKL-7890', seats:5, fuel:'Gasoline', trans:'Automatic', rate:1200, mileage:8200,  status:'available',   notes:'' },
  { id:5, brand:'Hyundai',    model:'Accent GL',     year:2020, color:'Phantom Black',  type:'Sedan',    plate:'GHI-3456', seats:5, fuel:'Gasoline', trans:'Manual',    rate:700,  mileage:44200, status:'maintenance', notes:'Engine oil change scheduled' },
  { id:6, brand:'Suzuki',     model:'Swift GL+',     year:2022, color:'Champion Yellow',type:'Hatchback',plate:'PQR-3344', seats:5, fuel:'Gasoline', trans:'Automatic', rate:800,  mileage:18700, status:'rented',      notes:'' },
  { id:7, brand:'Toyota',     model:'Rush 1.5G',     year:2023, color:'Silver Metallic',type:'SUV',      plate:'MNO-5566', seats:7, fuel:'Gasoline', trans:'Automatic', rate:1400, mileage:6500,  status:'reserved',    notes:'' },
  { id:8, brand:'Honda',      model:'BR-V S',        year:2022, color:'Rallye Red',     type:'SUV',      plate:'STU-7788', seats:7, fuel:'Gasoline', trans:'Automatic', rate:1300, mileage:14300, status:'rented',      notes:'' },
  { id:9, brand:'Mitsubishi', model:'Xpander GLS',   year:2023, color:'Sterling Silver',type:'Van',      plate:'VWX-9900', seats:7, fuel:'Gasoline', trans:'Automatic', rate:1600, mileage:5100,  status:'available',   notes:'' },
  { id:10,brand:'Nissan',     model:'Almera VL',     year:2022, color:'Brilliant White', type:'Sedan',   plate:'YZA-1122', seats:5, fuel:'Gasoline', trans:'Automatic', rate:900,  mileage:19800, status:'rented',      notes:'' },
  { id:11,brand:'Toyota',     model:'Fortuner 4x2',  year:2023, color:'Super White',    type:'SUV',      plate:'BCD-3344', seats:7, fuel:'Diesel',   trans:'Automatic', rate:2200, mileage:9900,  status:'available',   notes:'' },
  { id:12,brand:'Ford',       model:'Everest Trend', year:2022, color:'Diffused Silver',type:'SUV',      plate:'EFG-5566', seats:7, fuel:'Diesel',   trans:'Automatic', rate:2000, mileage:15600, status:'reserved',    notes:'' },
  { id:13,brand:'Hyundai',    model:'Tucson GL',     year:2023, color:'Iron Gray',      type:'SUV',      plate:'HIJ-7788', seats:5, fuel:'Gasoline', trans:'Automatic', rate:1500, mileage:7200,  status:'rented',      notes:'' },
  { id:14,brand:'Suzuki',     model:'Ertiga GA',     year:2021, color:'Silky Silver',   type:'Van',      plate:'KLM-9900', seats:7, fuel:'Gasoline', trans:'Automatic', rate:1100, mileage:28400, status:'maintenance', notes:'Brake pads replacement' },
  { id:15,brand:'Honda',      model:'Jazz 1.5 CVT',  year:2022, color:'Passion Red',    type:'Hatchback',plate:'NOP-1122', seats:5, fuel:'Gasoline', trans:'Automatic', rate:950,  mileage:21300, status:'available',   notes:'' },
];

let nextId = 16;
let currentFilter = 'all';
let currentSearch = '';
let currentView   = 'grid';
let editingId     = null;

const STATUS_COLOR = { available:'#3DBE7A', rented:'#D4A843', reserved:'#3D8FBE', maintenance:'#ff6b54' };
const TYPE_ICON_PATH = `M4 13L7 7h10l3 6`;

/* ════ CAR SVG ════ */
function carSVG(color, w=110, h=54) {
  const c = STATUS_COLOR[color] || '#E8341A';
  return `<svg width="${w}" height="${h}" viewBox="0 0 110 54" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M8 36L18 16h74l10 20" stroke="${c}" stroke-width="2" stroke-linecap="round"/>
    <rect x="4" y="34" width="102" height="14" rx="5" fill="${c}" fill-opacity="0.12" stroke="${c}" stroke-width="1.5"/>
    <path d="M4 36h102" stroke="${c}" stroke-opacity="0.3" stroke-width="1"/>
    <path d="M20 16l-4 18M90 16l4 18" stroke="${c}" stroke-width="1.5" stroke-linecap="round"/>
    <rect x="24" y="18" width="24" height="12" rx="2" fill="${c}" fill-opacity="0.15" stroke="${c}" stroke-width="1"/>
    <rect x="62" y="18" width="24" height="12" rx="2" fill="${c}" fill-opacity="0.15" stroke="${c}" stroke-width="1"/>
    <circle cx="22" cy="48" r="5.5" fill="#0E1115" stroke="${c}" stroke-width="1.8"/>
    <circle cx="22" cy="48" r="2" fill="${c}" fill-opacity="0.6"/>
    <circle cx="88" cy="48" r="5.5" fill="#0E1115" stroke="${c}" stroke-width="1.8"/>
    <circle cx="88" cy="48" r="2" fill="${c}" fill-opacity="0.6"/>
    <path d="M8 38h94" stroke="${c}" stroke-opacity="0.15" stroke-width="5"/>
  </svg>`;
}

/* ════ STATUS BADGE ════ */
function badgeHTML(s) {
  const map = { available:['available','Available'], rented:['rented','On Rent'], reserved:['reserved','Reserved'], maintenance:['maintenance','Maintenance'] };
  const [cls, label] = map[s] || ['available','Available'];
  return `<span class="badge ${cls}"><span class="badge-dot"></span>${label}</span>`;
}

/* ════ RENDER GRID ════ */
function renderGrid(data) {
  const grid = document.getElementById('gridView');
  const empty = document.getElementById('emptyStateGrid');
  if (!data.length) { grid.innerHTML=''; empty.classList.add('show'); return; }
  empty.classList.remove('show');
  grid.innerHTML = data.map(v => `
    <div class="vehicle-card" onclick="viewVehicle(${v.id})">
      <div class="vc-image-wrap">
        <div class="vc-image-bg"></div>
        <div class="vc-image-grid"></div>
        <div class="vc-year-badge">${v.year}</div>
        <div class="vc-status-badge">${badgeHTML(v.status)}</div>
        <div class="vc-car-svg">${carSVG(v.status)}</div>
      </div>
      <div class="vc-body">
        <div class="vc-make">${v.brand}</div>
        <div class="vc-model">${v.model}</div>
        <div class="vc-specs">
          <span class="vc-spec">
            <svg width="11" height="11" viewBox="0 0 11 11" fill="none" color="#6A6E75"><circle cx="5.5" cy="5.5" r="4" stroke="currentColor" stroke-width="1.1"/><path d="M3 8.5l2-3 2 3" stroke="currentColor" stroke-width="1" stroke-linecap="round"/></svg>
            ${v.seats} Seats
          </span>
          <span class="vc-spec">
            <svg width="11" height="11" viewBox="0 0 11 11" fill="none" color="#6A6E75"><path d="M5.5 2v5l2.5 2" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/><circle cx="5.5" cy="5.5" r="4" stroke="currentColor" stroke-width="1.1"/></svg>
            ${v.trans}
          </span>
          <span class="vc-spec">
            <svg width="11" height="11" viewBox="0 0 11 11" fill="none" color="#6A6E75"><rect x="2" y="2" width="5" height="7" rx="1" stroke="currentColor" stroke-width="1"/><path d="M7 4.5h1.5a1 1 0 010 2H7" stroke="currentColor" stroke-width="1" stroke-linecap="round"/></svg>
            ${v.fuel}
          </span>
          <span class="vc-spec">
            <svg width="11" height="11" viewBox="0 0 11 11" fill="none" color="#6A6E75"><path d="M1.5 9L3.5 5h4l2 4" stroke="currentColor" stroke-width="1" stroke-linecap="round"/><rect x="1" y="8.5" width="9" height="2" rx="1" stroke="currentColor" stroke-width="1"/></svg>
            ${v.type}
          </span>
        </div>
        <div class="vc-footer">
          <div>
            <div class="vc-rate">₱${v.rate.toLocaleString()}</div>
            <div class="vc-rate-label">per day</div>
          </div>
          <span class="vc-plate">
            <svg width="11" height="11" viewBox="0 0 11 11" fill="none" color="#9A9DA4"><rect x="1" y="2.5" width="9" height="6" rx="1" stroke="currentColor" stroke-width="1.1"/></svg>
            ${v.plate}
          </span>
        </div>
        <div class="vc-actions">
          <button class="vc-btn view" onclick="viewVehicle(${v.id});event.stopPropagation()">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M1 6.5s2.5-4 5.5-4 5.5 4 5.5 4-2.5 4-5.5 4-5.5-4-5.5-4z" stroke="currentColor" stroke-width="1.2"/><circle cx="6.5" cy="6.5" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
            View
          </button>
          <button class="vc-btn edit" onclick="openEditModal(${v.id});event.stopPropagation()">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 9.5L9.5 2l1.5 1.5-7.5 7.5H2V9.5z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Edit
          </button>
          <button class="vc-btn del" onclick="deleteVehicle(${v.id});event.stopPropagation()">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 3.5h9M5 3.5V2h3v1.5M10 3.5l-.7 7.5H3.7L3 3.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Delete
          </button>
        </div>
      </div>
    </div>
  `).join('');
}

/* ════ RENDER TABLE ════ */
function renderTableView(data) {
  const tbody = document.getElementById('tableBody');
  const empty = document.getElementById('emptyStateTable');
  const tfi   = document.getElementById('tfInfo');
  if (!data.length) { tbody.innerHTML=''; empty.classList.add('show'); tfi.innerHTML='No results'; return; }
  empty.classList.remove('show');
  tfi.innerHTML = `Showing <strong>1–${Math.min(10,data.length)}</strong> of <strong>${data.length}</strong>`;
  tbody.innerHTML = data.map(v => `
    <tr onclick="viewVehicle(${v.id})">
      <td><div class="car-thumb">${carSVG(v.status,50,30)}</div></td>
      <td>
        <div class="car-name">${v.brand} ${v.model}</div>
        <div class="car-make">${v.type} · ${v.year}</div>
      </td>
      <td><span class="plate">${v.plate}</span></td>
      <td style="color:var(--muted2);font-size:13px">${v.type}</td>
      <td style="color:var(--muted2);font-family:'Barlow Condensed',sans-serif;font-size:14px;letter-spacing:1px">${v.year}</td>
      <td style="color:var(--muted2);font-size:13px">${v.fuel}</td>
      <td style="color:var(--muted2);font-size:13px">${v.seats}</td>
      <td><span class="amount">₱${v.rate.toLocaleString()}</span></td>
      <td>${badgeHTML(v.status)}</td>
      <td>
        <div style="display:flex;align-items:center;gap:6px;justify-content:center">
          <div class="act-btn view" title="View" onclick="viewVehicle(${v.id});event.stopPropagation()"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M1 6.5s2.5-4 5.5-4 5.5 4 5.5 4-2.5 4-5.5 4-5.5-4-5.5-4z" stroke="currentColor" stroke-width="1.2"/><circle cx="6.5" cy="6.5" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg></div>
          <div class="act-btn edit" title="Edit" onclick="openEditModal(${v.id});event.stopPropagation()"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 9.5L9.5 2l1.5 1.5-7.5 7.5H2V9.5z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
          <div class="act-btn del" title="Delete" onclick="deleteVehicle(${v.id});event.stopPropagation()"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 3.5h9M5 3.5V2h3v1.5M10 3.5l-.7 7.5H3.7L3 3.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </div>
      </td>
    </tr>
  `).join('');
}

/* ════ FILTER + SEARCH ════ */
function getFiltered() {
  const typeVal  = document.getElementById('typeFilter').value;
  const brandVal = document.getElementById('brandFilter').value;
  const q = currentSearch.toLowerCase();
  return vehicles.filter(v => {
    const matchStatus = currentFilter === 'all' || v.status === currentFilter;
    const matchType   = !typeVal  || v.type  === typeVal;
    const matchBrand  = !brandVal || v.brand === brandVal;
    const matchSearch = !q || v.model.toLowerCase().includes(q) || v.brand.toLowerCase().includes(q) || v.plate.toLowerCase().includes(q) || v.type.toLowerCase().includes(q);
    return matchStatus && matchType && matchBrand && matchSearch;
  });
}

function filterVehicles() {
  currentSearch = document.getElementById('searchInput').value;
  const data = getFiltered();
  document.getElementById('resultsCount').innerHTML = `<strong>${data.length}</strong> vehicle${data.length!==1?'s':''}`;
  if (currentView === 'grid') renderGrid(data);
  else renderTableView(data);
}

function setFilter(val, btn) {
  currentFilter = val;
  document.querySelectorAll('.ftab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  filterVehicles();
}

/* ════ VIEW TOGGLE ════ */
function setView(v) {
  currentView = v;
  document.getElementById('gridToggle').classList.toggle('active', v==='grid');
  document.getElementById('listToggle').classList.toggle('active', v==='list');
  document.getElementById('gridView').style.display   = v==='grid' ? '' : 'none';
  document.getElementById('emptyStateGrid').style.display = v==='grid' ? '' : 'none';
  document.getElementById('tableView').style.display  = v==='list' ? 'block' : 'none';
  filterVehicles();
}

/* ════ VIEW DETAIL ════ */
function viewVehicle(id) {
  const v = vehicles.find(x => x.id === id);
  if (!v) return;
  document.getElementById('detailTitle').textContent = `${v.brand} ${v.model}`;
  document.getElementById('detailEditBtn').onclick = () => { closeModal('detailModal'); openEditModal(id); };
  document.getElementById('detailContent').innerHTML = `
    <div class="detail-hero">
      <div class="detail-car-wrap">${carSVG(v.status, 110, 54)}</div>
      <div class="detail-info">
        <div class="detail-make">${v.brand} · ${v.year}</div>
        <div class="detail-model">${v.model}</div>
        <div class="detail-meta">
          ${badgeHTML(v.status)}
          <span style="display:inline-flex;align-items:center;gap:5px;background:var(--card);border:1px solid var(--border2);border-radius:2px;padding:4px 10px;font-family:'Barlow Condensed',sans-serif;font-size:12px;letter-spacing:2px;color:var(--white);font-weight:700">${v.plate}</span>
        </div>
      </div>
    </div>
    <div class="detail-stat-grid">
      <div class="detail-stat">
        <div class="detail-stat-val" style="color:var(--red)">₱${v.rate.toLocaleString()}</div>
        <div class="detail-stat-lab">Daily Rate</div>
      </div>
      <div class="detail-stat">
        <div class="detail-stat-val" style="color:var(--muted2)">${v.mileage.toLocaleString()}</div>
        <div class="detail-stat-lab">Mileage (km)</div>
      </div>
      <div class="detail-stat">
        <div class="detail-stat-val" style="color:var(--muted2)">${v.seats}</div>
        <div class="detail-stat-lab">Seats</div>
      </div>
    </div>
    <div class="detail-body">
      <div class="detail-row"><span class="detail-key">Type</span><span class="detail-val">${v.type}</span></div>
      <div class="detail-row"><span class="detail-key">Color</span><span class="detail-val">${v.color}</span></div>
      <div class="detail-row"><span class="detail-key">Fuel</span><span class="detail-val">${v.fuel}</span></div>
      <div class="detail-row"><span class="detail-key">Transmission</span><span class="detail-val">${v.trans}</span></div>
      <div class="detail-row"><span class="detail-key">Year</span><span class="detail-val">${v.year}</span></div>
      ${v.notes ? `<div class="detail-row"><span class="detail-key">Notes</span><span class="detail-val" style="max-width:260px;text-align:right;white-space:normal;line-height:1.5">${v.notes}</span></div>` : ''}
    </div>
  `;
  openModal('detailModal');
}

/* ════ ADD/EDIT MODAL ════ */
function openAddModal() {
  editingId = null;
  document.getElementById('modalTitle').textContent = 'Add Vehicle';
  document.getElementById('modalSaveLabel').textContent = 'Save Vehicle';
  ['f-brand','f-model','f-year','f-color','f-type','f-plate','f-seats','f-fuel','f-trans','f-rate','f-mileage','f-status','f-notes'].forEach(id => {
    const el = document.getElementById(id);
    if (el.tagName==='SELECT') el.selectedIndex=0; else el.value='';
  });
  openModal('addModal');
}

function openEditModal(id) {
  const v = vehicles.find(x => x.id===id);
  if (!v) return;
  editingId = id;
  document.getElementById('modalTitle').textContent = `Edit — ${v.brand} ${v.model}`;
  document.getElementById('modalSaveLabel').textContent = 'Save Changes';
  document.getElementById('f-brand').value   = v.brand;
  document.getElementById('f-model').value   = v.model;
  document.getElementById('f-year').value    = v.year;
  document.getElementById('f-color').value   = v.color;
  document.getElementById('f-type').value    = v.type;
  document.getElementById('f-plate').value   = v.plate;
  document.getElementById('f-seats').value   = v.seats;
  document.getElementById('f-fuel').value    = v.fuel;
  document.getElementById('f-trans').value   = v.trans;
  document.getElementById('f-rate').value    = v.rate;
  document.getElementById('f-mileage').value = v.mileage;
  document.getElementById('f-status').value  = v.status;
  document.getElementById('f-notes').value   = v.notes;
  openModal('addModal');
}

function saveVehicle() {
  const brand   = document.getElementById('f-brand').value;
  const model   = document.getElementById('f-model').value.trim();
  const year    = parseInt(document.getElementById('f-year').value);
  const color   = document.getElementById('f-color').value.trim();
  const type    = document.getElementById('f-type').value;
  const plate   = document.getElementById('f-plate').value.trim();
  const seats   = parseInt(document.getElementById('f-seats').value);
  const fuel    = document.getElementById('f-fuel').value;
  const trans   = document.getElementById('f-trans').value;
  const rate    = parseFloat(document.getElementById('f-rate').value);
  const mileage = parseInt(document.getElementById('f-mileage').value) || 0;
  const status  = document.getElementById('f-status').value;
  const notes   = document.getElementById('f-notes').value.trim();

  if (!brand || !model || !year || !type || !plate || !seats || !rate) {
    showToast('Please fill in all required fields.'); return;
  }

  if (editingId) {
    const i = vehicles.findIndex(v => v.id===editingId);
    if (i>-1) vehicles[i] = { ...vehicles[i], brand, model, year, color, type, plate, seats, fuel, trans, rate, mileage, status, notes };
    showToast(`${brand} ${model} updated!`, 'success');
  } else {
    vehicles.unshift({ id: nextId++, brand, model, year, color, type, plate, seats, fuel, trans, rate, mileage, status, notes });
    showToast(`${brand} ${model} added to fleet!`, 'success');
  }
  closeModal('addModal');
  filterVehicles();
}

function deleteVehicle(id) {
  const v = vehicles.find(x => x.id===id);
  vehicles = vehicles.filter(x => x.id!==id);
  filterVehicles();
  showToast(`${v.brand} ${v.model} removed from fleet.`, 'error');
}

/* ════ EXPORT ════ */
function exportCSV() {
  const headers = ['ID','Brand','Model','Year','Color','Type','Plate','Seats','Fuel','Transmission','Daily Rate','Mileage','Status'];
  const rows = vehicles.map(v => [v.id, v.brand, v.model, v.year, v.color, v.type, v.plate, v.seats, v.fuel, v.trans, v.rate, v.mileage, v.status]);
  const csv = [headers, ...rows].map(r => r.join(',')).join('\n');
  const blob = new Blob([csv], {type:'text/csv'});
  const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'REVV_Vehicles.csv'; a.click();
  showToast('Exported as REVV_Vehicles.csv', 'success');
}

/* ════ MODAL HELPERS ════ */
function openModal(id) { document.getElementById(id).classList.add('show'); document.body.style.overflow='hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('show'); document.body.style.overflow=''; }
function closeModalOutside(e, id) { if (e.target===document.getElementById(id)) closeModal(id); }

/* ════ TOAST ════ */
function showToast(msg, type='error') {
  const t=document.getElementById('toast'), tm=document.getElementById('toastMsg'), ti=document.getElementById('toastIcon');
  tm.textContent = msg;
  const c = type==='success' ? '#3DBE7A' : '#E8341A';
  t.style.borderLeftColor = c;
  ti.innerHTML = type==='success'
    ? `<circle cx="7.5" cy="7.5" r="6" stroke="${c}" stroke-width="1.3"/><path d="M5 7.5l2 2 3.5-3.5" stroke="${c}" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>`
    : `<circle cx="7.5" cy="7.5" r="6" stroke="${c}" stroke-width="1.3"/><path d="M7.5 5v3" stroke="${c}" stroke-width="1.5" stroke-linecap="round"/><circle cx="7.5" cy="10" r="0.7" fill="${c}"/>`;
  void t.offsetWidth;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3400);
}

// Init
filterVehicles();