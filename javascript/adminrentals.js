const TYPE_GRADS = {
  Sedan:    'linear-gradient(135deg,#3D8FBE,#3DBE7A)',
  SUV:      'linear-gradient(135deg,#D4A843,#F5642A)',
  Van:      'linear-gradient(135deg,#9A3DBE,#E8341A)',
  Pickup:   'linear-gradient(135deg,#3DBE7A,#3D8FBE)',
  Hatchback:'linear-gradient(135deg,#6A6E75,#3D8FBE)',
};
const TYPE_ICONS = {
  Sedan:'S', SUV:'U', Van:'V', Pickup:'P', Hatchback:'H'
};

/* ════ DATA ════ */
let rentals = [
  { id:1,  rentalId:'RNT-001', customer:'Maria Santos',    cusid:'CUS-001', vehicle:'Toyota Vios',     plate:'AAA 1234', type:'Sedan',    driver:'Self-drive',  status:'completed', pickup:'2026-03-01', ret:'2026-03-04', days:3, rate:1800, total:5400,  location:'Cebu City Office', notes:'Returned on time. No damage.' },
  { id:2,  rentalId:'RNT-002', customer:'Jose Reyes',      cusid:'CUS-002', vehicle:'Ford Everest',    plate:'BBB 2345', type:'SUV',      driver:'With Driver', status:'completed', pickup:'2026-03-05', ret:'2026-03-10', days:5, rate:4500, total:22500, location:'Mactan Airport',   notes:'Corporate trip — Reyes Logistics.' },
  { id:3,  rentalId:'RNT-003', customer:'Ana Cruz',        cusid:'CUS-003', vehicle:'Toyota Vios',     plate:'CCC 3456', type:'Sedan',    driver:'Self-drive',  status:'completed', pickup:'2026-03-08', ret:'2026-03-10', days:2, rate:1800, total:3600,  location:'Cebu City Office', notes:'' },
  { id:4,  rentalId:'RNT-004', customer:'Carlos Villanueva',cusid:'CUS-004',vehicle:'Toyota Fortuner', plate:'DDD 4567', type:'SUV',      driver:'Self-drive',  status:'ongoing',   pickup:'2026-04-08', ret:'2026-04-14', days:6, rate:5000, total:30000, location:'Talisay Branch',   notes:'Family trip — booked SUV.' },
  { id:5,  rentalId:'RNT-005', customer:'Liza Bautista',   cusid:'CUS-005', vehicle:'Honda City',      plate:'EEE 5678', type:'Sedan',    driver:'Self-drive',  status:'completed', pickup:'2026-03-15', ret:'2026-03-17', days:2, rate:1800, total:3600,  location:'Cebu City Office', notes:'' },
  { id:6,  rentalId:'RNT-006', customer:'Marco Garcia',    cusid:'CUS-006', vehicle:'Mercedes GLE',    plate:'FFF 6789', type:'SUV',      driver:'With Driver', status:'ongoing',   pickup:'2026-04-09', ret:'2026-04-16', days:7, rate:9500, total:66500, location:'Mactan Airport',   notes:'High-value client. Premium vehicle requested.' },
  { id:7,  rentalId:'RNT-007', customer:'Grace Torres',    cusid:'CUS-007', vehicle:'Mitsubishi Mirage',plate:'GGG 7890',type:'Hatchback', driver:'Self-drive', status:'completed', pickup:'2026-03-20', ret:'2026-03-22', days:2, rate:1500, total:3000,  location:'Cebu City Office', notes:'' },
  { id:8,  rentalId:'RNT-008', customer:'Ryan Mendoza',    cusid:'CUS-008', vehicle:'Toyota Vios',     plate:'HHH 8901', type:'Sedan',    driver:'Self-drive',  status:'reserved',  pickup:'2026-04-20', ret:'2026-04-22', days:2, rate:1800, total:3600,  location:'Cebu City Office', notes:'First rental.' },
  { id:9,  rentalId:'RNT-009', customer:'Sofia Navarro',   cusid:'CUS-009', vehicle:'Honda BR-V',      plate:'III 9012', type:'SUV',      driver:'Self-drive',  status:'ongoing',   pickup:'2026-04-10', ret:'2026-04-13', days:3, rate:3200, total:9600,  location:'Talisay Branch',   notes:'Weekend renter.' },
  { id:10, rentalId:'RNT-010', customer:'Trish Pascual',   cusid:'CUS-010', vehicle:'Toyota HiAce',    plate:'JJJ 0123', type:'Van',      driver:'With Driver', status:'reserved',  pickup:'2026-04-25', ret:'2026-04-28', days:3, rate:5500, total:16500, location:'Mactan Airport',   notes:'Group travel.' },
  { id:11, rentalId:'RNT-011', customer:'Kevin Aquino',    cusid:'CUS-011', vehicle:'Honda City',      plate:'KKK 1122', type:'Sedan',    driver:'Self-drive',  status:'cancelled', pickup:'2026-03-28', ret:'2026-03-30', days:2, rate:1800, total:3600,  location:'Cebu City Office', notes:'Cancelled by customer.' },
  { id:12, rentalId:'RNT-012', customer:'Maria Santos',    cusid:'CUS-001', vehicle:'Toyota Fortuner', plate:'LLL 2233', type:'SUV',      driver:'Self-drive',  status:'overdue',   pickup:'2026-04-01', ret:'2026-04-05', days:4, rate:5000, total:20000, location:'Cebu City Office', notes:'Customer not responding. Due April 5.' },
  { id:13, rentalId:'RNT-013', customer:'Jose Reyes',      cusid:'CUS-002', vehicle:'Mercedes GLE',    plate:'MMM 3344', type:'SUV',      driver:'With Driver', status:'completed', pickup:'2026-03-10', ret:'2026-03-17', days:7, rate:9500, total:66500, location:'Mactan Airport',   notes:'Business event.' },
  { id:14, rentalId:'RNT-014', customer:'Marco Garcia',    cusid:'CUS-006', vehicle:'Toyota HiAce',    plate:'NNN 4455', type:'Van',      driver:'With Driver', status:'reserved',  pickup:'2026-04-30', ret:'2026-05-04', days:4, rate:5500, total:22000, location:'Mactan Airport',   notes:'Group booking.' },
  { id:15, rentalId:'RNT-015', customer:'Sofia Navarro',   cusid:'CUS-009', vehicle:'Mitsubishi Mirage',plate:'OOO 5566',type:'Hatchback', driver:'Self-drive', status:'ongoing',   pickup:'2026-04-11', ret:'2026-04-14', days:3, rate:1500, total:4500,  location:'Talisay Branch',   notes:'' },
  { id:16, rentalId:'RNT-016', customer:'Jomar Ocampo',    cusid:'CUS-014', vehicle:'Ford Ranger',     plate:'PPP 6677', type:'Pickup',   driver:'Self-drive',  status:'overdue',   pickup:'2026-03-30', ret:'2026-04-03', days:4, rate:4200, total:16800, location:'Cebu City Office', notes:'Blacklisted customer. Pending balance.' },
];
let nextId = 17;
let currentFilter = 'all';
let currentSearch = '';
let currentView = 'grid';
let editingId = null;

/* ════ HELPERS ════ */
function typeIcon(type) { return TYPE_ICONS[type]||'R'; }
function typeGrad(type) { return TYPE_GRADS[type]||TYPE_GRADS.Sedan; }

function typeChip(type) {
  const cls = 'vc-'+type.toLowerCase();
  return `<span class="vc-chip ${cls}">${type}</span>`;
}

function badgeHTML(s) {
  const map = {
    ongoing:   ['ongoing','Ongoing'],
    reserved:  ['reserved','Reserved'],
    completed: ['completed','Completed'],
    cancelled: ['cancelled','Cancelled'],
    overdue:   ['overdue','Overdue'],
  };
  const [cls, label] = map[s]||['cancelled','Unknown'];
  return `<span class="badge ${cls}"><span class="badge-dot"></span>${label}</span>`;
}

function fmtMoney(n) { return '₱'+n.toLocaleString(); }

function fmtDate(str) {
  if(!str) return '—';
  return new Date(str).toLocaleDateString('en-PH',{year:'numeric',month:'short',day:'numeric'});
}

function calcTotal() {
  const rate = parseFloat(document.getElementById('f-rate').value)||0;
  const days  = parseFloat(document.getElementById('f-days').value)||0;
  const total = rate * days;
  document.getElementById('f-total').value = total > 0 ? '₱'+total.toLocaleString() : '';
}

/* ════ RENDER GRID ════ */
function renderGrid(data) {
  const el = document.getElementById('gridView');
  const empty = document.getElementById('emptyGrid');
  if(!data.length){ el.innerHTML=''; empty.classList.add('show'); return; }
  empty.classList.remove('show');
  el.innerHTML = data.map(d=>`
    <div class="rental-card" onclick="viewRental(${d.id})">
      <div class="rc-header">
        <div class="rc-id-block">
          <div class="rc-rental-id">${d.rentalId}</div>
          <div class="rc-vehicle-tag">${d.cusid} · ${typeChip(d.type)}</div>
        </div>
        ${badgeHTML(d.status)}
      </div>
      <div class="rc-body">
        <div class="rc-vehicle-name">${d.vehicle}</div>
        <div class="rc-plate">${d.plate}</div>
        <div class="rc-info-row">
          <svg class="rc-info-icon" width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><circle cx="6.5" cy="5.5" r="3" stroke="currentColor" stroke-width="1.2"/><path d="M2 12c0-2.76 2.01-5 4.5-5s4.5 2.24 4.5 5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          ${d.customer}
        </div>
        <div class="rc-info-row">
          <svg class="rc-info-icon" width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><rect x="1.5" y="2" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M4 2V1M9 2V1M1.5 5h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          ${fmtDate(d.pickup)} → ${fmtDate(d.ret)}
        </div>
        <div class="rc-info-row">
          <svg class="rc-info-icon" width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M6.5 1.5c-2.21 0-4 1.79-4 4 0 2.98 4 7 4 7s4-4.02 4-7c0-2.21-1.79-4-4-4z" stroke="currentColor" stroke-width="1.2"/><circle cx="6.5" cy="5.5" r="1.3" stroke="currentColor" stroke-width="1.1"/></svg>
          ${d.location}
        </div>
        <div class="rc-info-row">
          <svg class="rc-info-icon" width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><circle cx="6.5" cy="6.5" r="5.5" stroke="currentColor" stroke-width="1.2"/><path d="M6.5 4v3l2 1.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          ${d.driver}
        </div>
      </div>
      <div class="rc-stats">
        <div class="rc-stat"><div class="rc-stat-val" style="color:var(--blue)">${d.days}</div><div class="rc-stat-lab">Days</div></div>
        <div class="rc-stat"><div class="rc-stat-val" style="color:var(--muted2);font-size:13px;padding-top:3px">${fmtMoney(d.rate)}</div><div class="rc-stat-lab">Per Day</div></div>
        <div class="rc-stat"><div class="rc-stat-val" style="color:var(--green);font-size:13px;padding-top:3px">${fmtMoney(d.total)}</div><div class="rc-stat-lab">Total</div></div>
      </div>
      <div class="rc-actions">
        <button class="rc-act-btn view" onclick="viewRental(${d.id});event.stopPropagation()">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M1 6.5s2.5-4 5.5-4 5.5 4 5.5 4-2.5 4-5.5 4-5.5-4-5.5-4z" stroke="currentColor" stroke-width="1.2"/><circle cx="6.5" cy="6.5" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
          View
        </button>
        <button class="rc-act-btn edit" onclick="openEditModal(${d.id});event.stopPropagation()">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 9.5L9.5 2l1.5 1.5-7.5 7.5H2V9.5z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Edit
        </button>
        <button class="rc-act-btn del" onclick="deleteRental(${d.id});event.stopPropagation()">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 3.5h9M5 3.5V2h3v1.5M10 3.5l-.7 7.5H3.7L3 3.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Remove
        </button>
      </div>
    </div>
  `).join('');
}

/* ════ RENDER TABLE ════ */
function renderTable(data) {
  const tbody = document.getElementById('tableBody');
  const empty = document.getElementById('emptyTable');
  const tfi   = document.getElementById('tfInfo');
  if(!data.length){ tbody.innerHTML=''; empty.classList.add('show'); tfi.innerHTML='No results'; return; }
  empty.classList.remove('show');
  tfi.innerHTML=`Showing <strong>1–${Math.min(10,data.length)}</strong> of <strong>${data.length}</strong>`;
  tbody.innerHTML = data.map(d=>`
    <tr onclick="viewRental(${d.id})">
      <td>
        <div class="driver-cell">
          <div class="t-avatar" style="background:${typeGrad(d.type)};position:relative;overflow:hidden">
            ${typeIcon(d.type)}
            <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,0.12),transparent 60%)"></div>
          </div>
          <div>
            <div class="t-name">${d.rentalId}</div>
            <div class="t-sub">${d.plate} · ${d.type}</div>
          </div>
        </div>
      </td>
      <td>
        <div class="t-name">${d.customer}</div>
        <div class="t-sub">${d.cusid}</div>
      </td>
      <td style="font-weight:500">${d.vehicle}</td>
      <td style="color:var(--muted2);font-size:13px">${fmtDate(d.pickup)}</td>
      <td style="color:var(--muted2);font-size:13px">${fmtDate(d.ret)}</td>
      <td style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;color:var(--blue)">${d.days}</td>
      <td style="font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:700;color:var(--green)">${fmtMoney(d.total)}</td>
      <td>${badgeHTML(d.status)}</td>
      <td>
        <div style="display:flex;align-items:center;gap:6px;justify-content:center">
          <div class="act-btn view" onclick="viewRental(${d.id});event.stopPropagation()" title="View"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M1 6.5s2.5-4 5.5-4 5.5 4 5.5 4-2.5 4-5.5 4-5.5-4-5.5-4z" stroke="currentColor" stroke-width="1.2"/><circle cx="6.5" cy="6.5" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg></div>
          <div class="act-btn edit" onclick="openEditModal(${d.id});event.stopPropagation()" title="Edit"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 9.5L9.5 2l1.5 1.5-7.5 7.5H2V9.5z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
          <div class="act-btn del" onclick="deleteRental(${d.id});event.stopPropagation()" title="Remove"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 3.5h9M5 3.5V2h3v1.5M10 3.5l-.7 7.5H3.7L3 3.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </div>
      </td>
    </tr>
  `).join('');
}

/* ════ FILTER ════ */
function getFiltered() {
  const q = currentSearch.toLowerCase();
  const typeF = document.getElementById('typeFilter').value;
  return rentals.filter(d => {
    const matchStatus = currentFilter==='all' || d.status===currentFilter;
    const matchSearch = !q || d.rentalId.toLowerCase().includes(q) || d.customer.toLowerCase().includes(q) || d.vehicle.toLowerCase().includes(q) || d.plate.toLowerCase().includes(q) || d.cusid.toLowerCase().includes(q);
    const matchType   = !typeF || d.type===typeF;
    return matchStatus && matchSearch && matchType;
  });
}
function filterRentals() {
  currentSearch = document.getElementById('searchInput').value;
  const data = getFiltered();
  document.getElementById('resultsCount').innerHTML=`<strong>${data.length}</strong> rental${data.length!==1?'s':''}`;
  if(currentView==='grid') renderGrid(data); else renderTable(data);
}
function setFilter(val,btn) {
  currentFilter=val;
  document.querySelectorAll('.ftab').forEach(t=>t.classList.remove('active'));
  btn.classList.add('active');
  filterRentals();
}

/* ════ VIEW TOGGLE ════ */
function setView(v) {
  currentView=v;
  document.getElementById('gridToggle').classList.toggle('active',v==='grid');
  document.getElementById('listToggle').classList.toggle('active',v==='list');
  document.getElementById('gridView').style.display = v==='grid'?'':'none';
  document.getElementById('emptyGrid').style.display = v==='grid'?'':'none';
  document.getElementById('tableView').style.display = v==='list'?'block':'none';
  filterRentals();
}

/* ════ VIEW DETAIL ════ */
function viewRental(id) {
  const d = rentals.find(x=>x.id===id); if(!d) return;
  document.getElementById('detailTitle').textContent = d.rentalId+' — '+d.vehicle;
  document.getElementById('detailEditBtn').onclick = ()=>{ closeModal('detailModal'); openEditModal(id); };
  document.getElementById('detailContent').innerHTML = `
    <div class="detail-hero">
      <div class="detail-icon" style="background:${typeGrad(d.type)}">
        <svg width="40" height="30" viewBox="0 0 40 30" fill="none"><path d="M3 22L10 8h20l7 14" stroke="white" stroke-width="2.5" stroke-linecap="round"/><rect x="2" y="21" width="36" height="7" rx="3" stroke="white" stroke-width="2"/><circle cx="10" cy="28" r="3" fill="white"/><circle cx="30" cy="28" r="3" fill="white"/></svg>
      </div>
      <div class="detail-info">
        <div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted2);margin-bottom:4px">${d.rentalId} · ${d.plate}</div>
        <div class="detail-name">${d.vehicle}</div>
        <div class="detail-meta">
          ${badgeHTML(d.status)}
          ${typeChip(d.type)}
        </div>
      </div>
    </div>
    <div class="detail-stat-grid">
      <div class="detail-stat"><div class="detail-stat-val" style="color:var(--blue)">${d.days}</div><div class="detail-stat-lab">Days Rented</div></div>
      <div class="detail-stat"><div class="detail-stat-val" style="color:var(--muted2);font-size:15px;padding-top:4px">${fmtMoney(d.rate)}</div><div class="detail-stat-lab">Rate/Day</div></div>
      <div class="detail-stat"><div class="detail-stat-val" style="color:var(--green);font-size:15px;padding-top:4px">${fmtMoney(d.total)}</div><div class="detail-stat-lab">Total Amount</div></div>
      <div class="detail-stat"><div class="detail-stat-val" style="font-size:13px;padding-top:5px">${d.driver}</div><div class="detail-stat-lab">Driver</div></div>
    </div>
    <div class="detail-body">
      <div class="detail-row"><span class="detail-key">Customer</span><span class="detail-val">${d.customer}</span></div>
      <div class="detail-row"><span class="detail-key">Customer ID</span><span class="detail-val" style="font-family:'Barlow Condensed',sans-serif;letter-spacing:1.5px">${d.cusid}</span></div>
      <div class="detail-row"><span class="detail-key">Pick-up Date</span><span class="detail-val">${fmtDate(d.pickup)}</span></div>
      <div class="detail-row"><span class="detail-key">Return Date</span><span class="detail-val">${fmtDate(d.ret)}</span></div>
      <div class="detail-row"><span class="detail-key">Pick-up Location</span><span class="detail-val">${d.location}</span></div>
      <div class="detail-row"><span class="detail-key">Plate Number</span><span class="detail-val" style="font-family:'Barlow Condensed',sans-serif;letter-spacing:2px">${d.plate}</span></div>
      ${d.notes?`<div class="detail-row"><span class="detail-key">Notes</span><span class="detail-val" style="max-width:260px;text-align:right;white-space:normal;line-height:1.5;color:var(--muted2)">${d.notes}</span></div>`:''}
    </div>
  `;
  openModal('detailModal');
}

/* ════ ADD / EDIT ════ */
function openAddModal() {
  editingId=null;
  document.getElementById('modalTitle').textContent='New Rental';
  document.getElementById('saveBtnLabel').textContent='Save Rental';
  ['f-customer','f-cusid','f-vehicle','f-plate','f-location','f-notes'].forEach(id=>{document.getElementById(id).value='';});
  document.getElementById('f-rate').value='';
  document.getElementById('f-days').value='';
  document.getElementById('f-total').value='';
  document.getElementById('f-pickup').value='';
  document.getElementById('f-return').value='';
  document.getElementById('f-type').selectedIndex=0;
  document.getElementById('f-driver').selectedIndex=0;
  document.getElementById('f-status').selectedIndex=0;
  openModal('addModal');
}
function openEditModal(id) {
  const d=rentals.find(x=>x.id===id); if(!d) return;
  editingId=id;
  document.getElementById('modalTitle').textContent=`Edit — ${d.rentalId}`;
  document.getElementById('saveBtnLabel').textContent='Save Changes';
  document.getElementById('f-customer').value=d.customer;
  document.getElementById('f-cusid').value=d.cusid;
  document.getElementById('f-vehicle').value=d.vehicle;
  document.getElementById('f-plate').value=d.plate;
  document.getElementById('f-type').value=d.type;
  document.getElementById('f-driver').value=d.driver;
  document.getElementById('f-status').value=d.status;
  document.getElementById('f-pickup').value=d.pickup;
  document.getElementById('f-return').value=d.ret;
  document.getElementById('f-location').value=d.location;
  document.getElementById('f-rate').value=d.rate;
  document.getElementById('f-days').value=d.days;
  document.getElementById('f-total').value='₱'+d.total.toLocaleString();
  document.getElementById('f-notes').value=d.notes;
  openModal('addModal');
}
function saveRental() {
  const customer = document.getElementById('f-customer').value.trim();
  const cusid    = document.getElementById('f-cusid').value.trim();
  const vehicle  = document.getElementById('f-vehicle').value.trim();
  const plate    = document.getElementById('f-plate').value.trim();
  const type     = document.getElementById('f-type').value;
  const driver   = document.getElementById('f-driver').value;
  const status   = document.getElementById('f-status').value;
  const pickup   = document.getElementById('f-pickup').value;
  const ret      = document.getElementById('f-return').value;
  const location = document.getElementById('f-location').value.trim();
  const rate     = parseFloat(document.getElementById('f-rate').value)||0;
  const days     = parseInt(document.getElementById('f-days').value)||0;
  const total    = rate * days;
  const notes    = document.getElementById('f-notes').value.trim();
  if(!customer||!vehicle||!pickup) { showToast('Customer, vehicle, and pick-up date are required.'); return; }
  if(editingId) {
    const i=rentals.findIndex(d=>d.id===editingId);
    if(i>-1) rentals[i]={...rentals[i],customer,cusid,vehicle,plate,type,driver,status,pickup,ret,location,rate,days,total,notes};
    showToast(`${rentals[rentals.findIndex(d=>d.id===editingId)].rentalId} updated!`,'success');
  } else {
    const newId = 'RNT-'+String(nextId).padStart(3,'0');
    rentals.unshift({id:nextId++,rentalId:newId,customer,cusid,vehicle,plate,type,driver,status,pickup,ret,location,rate,days,total,notes});
    showToast(`${newId} created!`,'success');
  }
  closeModal('addModal');
  filterRentals();
}
function deleteRental(id) {
  const d=rentals.find(x=>x.id===id);
  rentals=rentals.filter(x=>x.id!==id);
  filterRentals();
  showToast(`${d.rentalId} removed.`,'error');
}

/* ════ EXPORT ════ */
function exportCSV() {
  const h=['Rental ID','Customer','Customer ID','Vehicle','Plate','Type','Driver','Status','Pick-up','Return','Days','Rate/Day','Total','Location','Notes'];
  const r=rentals.map(d=>[d.rentalId,d.customer,d.cusid,d.vehicle,d.plate,d.type,d.driver,d.status,d.pickup,d.ret,d.days,d.rate,d.total,d.location,d.notes]);
  const csv=[h,...r].map(row=>row.join(',')).join('\n');
  const blob=new Blob([csv],{type:'text/csv'});
  const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='REVV_Rentals.csv';a.click();
  showToast('Exported as REVV_Rentals.csv','success');
}

/* ════ MODAL ════ */
function openModal(id){document.getElementById(id).classList.add('show');document.body.style.overflow='hidden';}
function closeModal(id){document.getElementById(id).classList.remove('show');document.body.style.overflow='';}
function closeModalOutside(e,id){if(e.target===document.getElementById(id))closeModal(id);}

/* ════ TOAST ════ */
function showToast(msg,type='error'){
  const t=document.getElementById('toast'),tm=document.getElementById('toastMsg'),ti=document.getElementById('toastIcon');
  tm.textContent=msg;
  const c=type==='success'?'#3DBE7A':'#E8341A';
  t.style.borderLeftColor=c;
  ti.innerHTML=type==='success'
    ?`<circle cx="7.5" cy="7.5" r="6" stroke="${c}" stroke-width="1.3"/><path d="M5 7.5l2 2 3.5-3.5" stroke="${c}" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>`
    :`<circle cx="7.5" cy="7.5" r="6" stroke="${c}" stroke-width="1.3"/><path d="M7.5 5v3" stroke="${c}" stroke-width="1.5" stroke-linecap="round"/><circle cx="7.5" cy="10" r="0.7" fill="${c}"/>`;
  void t.offsetWidth;
  t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'),3400);
}

filterRentals();