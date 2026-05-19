/* ════ AVATAR GRADIENTS ════ */
const GRADS = [
  'linear-gradient(135deg,#E8341A,#F5642A)',
  'linear-gradient(135deg,#3D8FBE,#3DBE7A)',
  'linear-gradient(135deg,#D4A843,#F5642A)',
  'linear-gradient(135deg,#3DBE7A,#3D8FBE)',
  'linear-gradient(135deg,#9A3DBE,#E8341A)',
  'linear-gradient(135deg,#6A6E75,#3D8FBE)',
];
let selectedBg = GRADS[0];

function pickAvatar(el) {
  document.querySelectorAll('.av-opt').forEach(a=>a.classList.remove('selected'));
  el.classList.add('selected');
  selectedBg = el.dataset.bg;
  document.getElementById('modalAvatarPreview').style.background = selectedBg;
}
function updateInitials() {
  const f = document.getElementById('f-fname').value.trim();
  const l = document.getElementById('f-lname').value.trim();
  const ini = ((f[0]||'')+(l[0]||'')).toUpperCase() || '?';
  document.getElementById('avatarInitials').textContent = ini;
}

/* ════ DATA ════ */
let drivers = [];
let filteredDrivers = [];
let currentFilter = 'all';
let currentSearch = '';
let currentView = 'grid';
let currentPage = 1;
const pageSize = 10;
let editingId = null;

async function loadDrivers() {
  try {
    const response = await fetch('/rent/php/driver_action.php?per_page=1000');
    const json = await response.json();
    if (!response.ok || !Array.isArray(json.drivers)) {
      throw new Error(json.error || 'Unable to load drivers.');
    }
    drivers = json.drivers.map(driver => ({
      ...driver,
      bg: driver.photo ? 'transparent' : (driver.avatar_bg || driver.bg || GRADS[0]),
    }));
    updateSummaryCounts();
    filterDrivers();
  } catch (error) {
    showToast(error.message || 'Unable to load drivers.');
  }
}

function updateSummaryCounts() {
  const total = drivers.length;
  const available = drivers.filter(d => d.status === 'available').length;
  const onDuty = drivers.filter(d => d.status === 'on-duty').length;
  const offDuty = drivers.filter(d => d.status === 'off-duty').length;
  const suspended = drivers.filter(d => d.status === 'suspended').length;

  document.getElementById('totalDriversCount').textContent = total;
  document.getElementById('availableDriversCount').textContent = available;
  document.getElementById('onDutyDriversCount').textContent = onDuty;
  document.getElementById('offDutyDriversCount').textContent = offDuty;
  document.getElementById('suspendedDriversCount').textContent = suspended;
}

/* ════ HELPERS ════ */
function ini(d) { return (d.fname[0]+d.lname[0]).toUpperCase(); }
function expLevel(y) { return y < 3 ? 'Junior' : y < 7 ? 'Mid-Level' : 'Senior'; }
function expColor(y) { return y < 3 ? 'var(--blue)' : y < 7 ? 'var(--gold)' : 'var(--green)'; }

function stars(r) {
  let s = '';
  for(let i=1;i<=5;i++) {
    const full = i <= Math.floor(r);
    const half = !full && i === Math.ceil(r) && r%1 >= 0.5;
    s += `<svg class="star ${full||half?'filled':'empty'}" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 1l1.35 2.73L10.5 4.2l-2.25 2.19.53 3.1L6 8l-2.78 1.49.53-3.1L1.5 4.2l3.15-.47L6 1z" fill="${full?'#D4A843':half?'url(#half)':'none'}" stroke="#D4A843" stroke-width="0.5"/></svg>`;
  }
  return s;
}

function badgeHTML(s) {
  const map = { available:['available','Available'], 'on-duty':['on-duty','On Duty'], 'off-duty':['off-duty','Off Duty'], suspended:['suspended','Suspended'] };
  const [cls, label] = map[s]||['off-duty','Unknown'];
  return `<span class="badge ${cls}"><span class="badge-dot"></span>${label}</span>`;
}

function assetPath(src) {
  if (!src) return '';
  if (src.startsWith('/rent/')) return src;
  if (src.startsWith('/')) return '/rent' + src;
  return '/rent/' + src;
}

function showDocumentPreview(src, label) {
  const previewModal = document.getElementById('docPreviewModal');
  const previewTitle = document.getElementById('docPreviewTitle');
  const previewFrame = document.getElementById('docPreviewFrame');
  if (!previewModal || !previewFrame) return;
  previewTitle.textContent = label || 'Driver Document';
  previewFrame.src = src;
  openModal('docPreviewModal');
}

function onlineDotColor(s) {
  return s==='available'?'var(--green)':s==='on-duty'?'var(--gold)':s==='suspended'?'#ff6b54':'var(--muted)';
}

/* ════ RENDER GRID ════ */
function renderGrid(data) {
  const el = document.getElementById('gridView');
  const empty = document.getElementById('emptyGrid');
  if(!data.length){ el.innerHTML=''; empty.classList.add('show'); return; }
  empty.classList.remove('show');
  el.innerHTML = data.map(d=>`
    <div class="driver-card" onclick="viewDriver(${d.id})">
      <div class="dc-header">
        <div class="dc-avatar-wrap">
          <div class="dc-avatar" style="background:${d.bg};overflow:hidden;position:relative">
            ${d.photo ? `<img src="${assetPath(d.photo)}" alt="${d.fname} ${d.lname}" style="width:100%;height:100%;object-fit:cover;border-radius:8px">` : `${ini(d)}<div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,0.12),transparent 60%)"></div>`}
          </div>
          <div class="dc-online ${d.status==='available'?'active':d.status==='on-duty'?'on-duty':'offline'}" style="background:${onlineDotColor(d.status)}"></div>
        </div>
        ${badgeHTML(d.status)}
      </div>
      <div class="dc-body">
        <div class="dc-name">${d.fname} ${d.lname}</div>
        <div class="dc-id">DRV-${String(d.id).padStart(3,'0')} · ${d.lictype}</div>
        <div class="dc-info-row">
          <svg class="dc-info-icon" width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><rect x="1.5" y="2" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M4 2V1M9 2V1M1.5 5h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/><circle cx="4.5" cy="8" r="1" fill="currentColor"/></svg>
          ${d.license}
        </div>
        <div class="dc-info-row">
          <svg class="dc-info-icon" width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><rect x="1.5" y="1.5" width="10" height="10" rx="2" stroke="currentColor" stroke-width="1.2"/><path d="M4 7l2 2 3.5-3.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          <span style="color:${expColor(d.exp)};font-weight:500">${expLevel(d.exp)}</span>&nbsp;· ${d.exp} yrs exp
        </div>
        <div class="dc-info-row">
          <svg class="dc-info-icon" width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M6.5 1.5c-2.21 0-4 1.79-4 4 0 2.98 4 7 4 7s4-4.02 4-7c0-2.21-1.79-4-4-4z" stroke="currentColor" stroke-width="1.2"/><circle cx="6.5" cy="5.5" r="1.3" stroke="currentColor" stroke-width="1.1"/></svg>
          ${d.address}
        </div>
        <div class="dc-info-row">
          <div class="star-row">${stars(d.rating)}<span class="rating-val">${d.rating.toFixed(1)}</span><span class="rating-count">(${d.trips} trips)</span></div>
        </div>
      </div>
      <div class="dc-stats">
        <div class="dc-stat"><div class="dc-stat-val" style="color:var(--red)">${d.trips}</div><div class="dc-stat-lab">Trips</div></div>
        <div class="dc-stat"><div class="dc-stat-val" style="color:var(--gold)">${d.rating.toFixed(1)}</div><div class="dc-stat-lab">Rating</div></div>
        <div class="dc-stat"><div class="dc-stat-val" style="color:var(--blue)">${d.exp}</div><div class="dc-stat-lab">Yrs Exp</div></div>
      </div>
      <div class="dc-actions">
        <button class="dc-act-btn view" onclick="viewDriver(${d.id});event.stopPropagation()">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M1 6.5s2.5-4 5.5-4 5.5 4 5.5 4-2.5 4-5.5 4-5.5-4-5.5-4z" stroke="currentColor" stroke-width="1.2"/><circle cx="6.5" cy="6.5" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
          View
        </button>
        <button class="dc-act-btn edit" onclick="openEditModal(${d.id});event.stopPropagation()">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 9.5L9.5 2l1.5 1.5-7.5 7.5H2V9.5z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Edit
        </button>
        <button class="dc-act-btn del" onclick="deleteDriver(${d.id});event.stopPropagation()">
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
  const tfi = document.getElementById('tfInfo');
  const total = data.length;
  const start = (currentPage - 1) * pageSize;
  const pageItems = data.slice(start, start + pageSize);
  if(!pageItems.length){ tbody.innerHTML=''; empty.classList.add('show'); tfi.innerHTML='No results'; renderPagination(total); return; }
  empty.classList.remove('show');
  tfi.innerHTML=`Showing <strong>${start+1}–${start+pageItems.length}</strong> of <strong>${total}</strong>`;
  tbody.innerHTML = pageItems.map(d=>`
    <tr onclick="viewDriver(${d.id})">
      <td>
        <div class="driver-cell">
          <div class="t-avatar" style="background:${d.bg};position:relative;overflow:hidden">
            ${d.photo ? `<img src="${assetPath(d.photo)}" alt="${d.fname} ${d.lname}" style="width:100%;height:100%;object-fit:cover;border-radius:4px">` : `${ini(d)}<div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,0.12),transparent 60%)"></div>`}
          </div>
          <div>
            <div class="t-name">${d.fname} ${d.lname}</div>
            <div class="t-sub">DRV-${String(d.id).padStart(3,'0')} · ${d.email}</div>
          </div>
        </div>
      </td>
      <td><span class="license-badge">${d.license}</span></td>
      <td style="color:var(--muted2);font-size:13px">${d.phone}</td>
      <td><span style="color:${expColor(d.exp)};font-size:13px;font-weight:500">${expLevel(d.exp)}</span><span style="color:var(--muted);font-size:11px;margin-left:5px">${d.exp} yrs</span></td>
      <td>
        <div style="display:flex;align-items:center;gap:4px">
          <div class="star-row">${stars(d.rating)}</div>
          <span style="font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:700;color:var(--gold);margin-left:4px">${d.rating.toFixed(1)}</span>
        </div>
      </td>
      <td style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;color:var(--red)">${d.trips}</td>
      <td>${badgeHTML(d.status)}</td>
      <td>
        <div style="display:flex;align-items:center;gap:6px;justify-content:center">
          <div class="act-btn view" onclick="viewDriver(${d.id});event.stopPropagation()" title="View"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M1 6.5s2.5-4 5.5-4 5.5 4 5.5 4-2.5 4-5.5 4-5.5-4-5.5-4z" stroke="currentColor" stroke-width="1.2"/><circle cx="6.5" cy="6.5" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg></div>
          <div class="act-btn edit" onclick="openEditModal(${d.id});event.stopPropagation()" title="Edit"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 9.5L9.5 2l1.5 1.5-7.5 7.5H2V9.5z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
          <div class="act-btn del" onclick="deleteDriver(${d.id});event.stopPropagation()" title="Remove"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 3.5h9M5 3.5V2h3v1.5M10 3.5l-.7 7.5H3.7L3 3.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </div>
      </td>
    </tr>
  `).join('');
}

/* ════ FILTER ════ */
function getFiltered() {
  const q = currentSearch.toLowerCase();
  const expF = document.getElementById('expFilter').value;
  return drivers.filter(d => {
    const matchStatus = currentFilter==='all' || d.status===currentFilter;
    const matchSearch = !q || (d.fname+' '+d.lname).toLowerCase().includes(q) || d.license.toLowerCase().includes(q) || d.phone.includes(q) || d.email.toLowerCase().includes(q);
    const matchExp = !expF || (expF==='junior'&&d.exp<3) || (expF==='mid'&&d.exp>=3&&d.exp<7) || (expF==='senior'&&d.exp>=7);
    return matchStatus && matchSearch && matchExp;
  });
}
function filterDrivers() {
  currentSearch = document.getElementById('searchInput').value;
  currentPage = 1;
  applyFilters();
}
function applyFilters() {
  filteredDrivers = getFiltered();
  document.getElementById('resultsCount').innerHTML=`<strong>${filteredDrivers.length}</strong> driver${filteredDrivers.length!==1?'s':''}`;
  if(currentView==='grid') {
    renderGrid(filteredDrivers);
  } else {
    renderTable(filteredDrivers);
  }
}
function setFilter(val,btn) {
  currentFilter=val;
  document.querySelectorAll('.ftab').forEach(t=>t.classList.remove('active'));
  btn.classList.add('active');
  filterDrivers();
}

function renderPagination(total) {
  const container = document.getElementById('pagination');
  const pageCount = Math.max(1, Math.ceil(total / pageSize));
  currentPage = Math.min(currentPage, pageCount);

  const buttons = [];
  buttons.push(`<button class="pg-btn" ${currentPage===1?'disabled':''} onclick="setPage(${currentPage-1})"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M8 2L4 6l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>`);

  const start = Math.max(1, currentPage - 2);
  const end = Math.min(pageCount, start + 4);
  for (let i = start; i <= end; i++) {
    buttons.push(`<button class="pg-btn ${i===currentPage?'active':''}" onclick="setPage(${i})">${i}</button>`);
  }

  buttons.push(`<button class="pg-btn" ${currentPage===pageCount?'disabled':''} onclick="setPage(${currentPage+1})"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M4 2l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>`);
  container.innerHTML = buttons.join('');
}

function setPage(page) {
  if (page < 1) return;
  currentPage = page;
  applyFilters();
}

/* ════ VIEW TOGGLE ════ */
function setView(v) {
  currentView=v;
  document.getElementById('gridToggle').classList.toggle('active',v==='grid');
  document.getElementById('listToggle').classList.toggle('active',v==='list');
  document.getElementById('gridView').style.display = v==='grid'?'':'none';
  document.getElementById('emptyGrid').style.display = v==='grid'?'':'none';
  document.getElementById('tableView').style.display = v==='list'?'block':'none';
  filterDrivers();
}

/* ════ VIEW DETAIL ════ */
function viewDriver(id) {
  const d = drivers.find(x=>x.id===id); if(!d) return;
  document.getElementById('detailTitle').textContent = d.fname+' '+d.lname;
  document.getElementById('detailEditBtn').onclick = ()=>{ closeModal('detailModal'); openEditModal(id); };
  document.getElementById('detailContent').innerHTML = `
    <div class="detail-hero">
      <div class="detail-avatar" style="${d.photo ? `background:transparent` : `background:${d.bg}`}">
        ${d.photo ? `<img src="${assetPath(d.photo)}" alt="${d.fname} ${d.lname}" style="width:100%;height:100%;object-fit:cover;border-radius:8px">` : ini(d)}
        <div class="detail-online" style="background:${onlineDotColor(d.status)}"></div>
      </div>
      <div class="detail-info">
        <div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted2);margin-bottom:4px">DRV-${String(d.id).padStart(3,'0')} · ${d.lictype}</div>
        <div class="detail-name">${d.fname} ${d.lname}</div>
        <div class="detail-meta">
          ${badgeHTML(d.status)}
          <div style="display:flex;align-items:center;gap:3px">${stars(d.rating)}<span style="font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:700;color:var(--gold);margin-left:4px">${d.rating.toFixed(1)}</span></div>
        </div>
      </div>
    </div>
    <div class="detail-stat-grid">
      <div class="detail-stat"><div class="detail-stat-val" style="color:var(--red)">${d.trips}</div><div class="detail-stat-lab">Total Trips</div></div>
      <div class="detail-stat"><div class="detail-stat-val" style="color:var(--gold)">${d.rating.toFixed(1)}</div><div class="detail-stat-lab">Avg Rating</div></div>
      <div class="detail-stat"><div class="detail-stat-val" style="color:var(--blue)">${d.exp}</div><div class="detail-stat-lab">Years Exp</div></div>
      <div class="detail-stat"><div class="detail-stat-val" style="color:${expColor(d.exp)}">${expLevel(d.exp)}</div><div class="detail-stat-lab">Level</div></div>
    </div>
    <div class="detail-body">
      <div class="detail-row"><span class="detail-key">Email</span><span class="detail-val">${d.email}</span></div>
      <div class="detail-row"><span class="detail-key">Phone</span><span class="detail-val">${d.phone}</span></div>
      <div class="detail-row"><span class="detail-key">Address</span><span class="detail-val">${d.address}</span></div>
      <div class="detail-row"><span class="detail-key">Date of Birth</span><span class="detail-val">${d.dob ? new Date(d.dob).toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'}) : '—'}</span></div>
      <div class="detail-row"><span class="detail-key">License No.</span><span class="detail-val" style="font-family:'Barlow Condensed',sans-serif;letter-spacing:1.5px">${d.license}</span></div>
      <div class="detail-row"><span class="detail-key">License Expiry</span><span class="detail-val ${d.expiry&&new Date(d.expiry)<new Date()?'':''}" style="color:${d.expiry&&new Date(d.expiry)<new Date()?'#ff6b54':'var(--white)'}">${d.expiry ? new Date(d.expiry).toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'}) : '—'}</span></div>
      <div class="detail-row"><span class="detail-key">License Type</span><span class="detail-val">${d.lictype}</span></div>
      ${d.notes?`<div class="detail-row"><span class="detail-key">Notes</span><span class="detail-val" style="max-width:260px;text-align:right;white-space:normal;line-height:1.5;color:var(--muted2)">${d.notes}</span></div>`:''}
      ${d.documents && d.documents.length ? `<div class="detail-row"><span class="detail-key">Documents</span><span class="detail-val" style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;line-height:1.4;color:var(--muted2)">${d.documents.map(doc=>`<a href="#" onclick="event.preventDefault(); showDocumentPreview('${assetPath(doc.file)}', '${doc.file.split('/').pop().replace(/'/g, "\\'")}');" style="color:var(--white);text-decoration:underline">${doc.file.split('/').pop()}</a>`).join('')}</span></div>` : ''}
    </div>
  `;
  openModal('detailModal');
}

/* ════ ADD / EDIT ════ */
function openAddModal() {
  editingId=null;
  document.getElementById('modalTitle').textContent='Add Driver';
  document.getElementById('saveBtnLabel').textContent='Save Driver';
  ['f-fname','f-lname','f-email','f-phone','f-dob','f-address','f-license','f-expiry','f-exp','f-notes'].forEach(id=>{document.getElementById(id).value='';});
  ['f-photo','f-document'].forEach(id=>{const el=document.getElementById(id); if(el) el.value='';});
  document.getElementById('f-lictype').selectedIndex=0;
  document.getElementById('f-status').selectedIndex=0;
  selectedBg=GRADS[0];
  document.getElementById('modalAvatarPreview').style.background=GRADS[0];
  document.querySelectorAll('.av-opt').forEach((a,i)=>a.classList.toggle('selected',i===0));
  document.getElementById('avatarInitials').textContent='?';
  openModal('addModal');
}
function openEditModal(id) {
  const d=drivers.find(x=>x.id===id); if(!d) return;
  editingId=id;
  document.getElementById('modalTitle').textContent=`Edit — ${d.fname} ${d.lname}`;
  document.getElementById('saveBtnLabel').textContent='Save Changes';
  document.getElementById('f-fname').value=d.fname;
  document.getElementById('f-lname').value=d.lname;
  document.getElementById('f-email').value=d.email;
  document.getElementById('f-phone').value=d.phone;
  document.getElementById('f-dob').value=d.dob;
  document.getElementById('f-address').value=d.address;
  document.getElementById('f-license').value=d.license;
  document.getElementById('f-expiry').value=d.expiry;
  document.getElementById('f-exp').value=d.exp;
  document.getElementById('f-lictype').value=d.lictype;
  document.getElementById('f-status').value=d.status;
  document.getElementById('f-notes').value=d.notes;
  ['f-photo','f-document'].forEach(id=>{const el=document.getElementById(id); if(el) el.value='';});
  selectedBg=d.bg;
  document.getElementById('modalAvatarPreview').style.background=d.bg;
  document.getElementById('avatarInitials').textContent=ini(d);
  document.querySelectorAll('.av-opt').forEach(a=>a.classList.toggle('selected',a.dataset.bg===d.bg));
  openModal('addModal');
}
async function saveDriver() {
  const fname = document.getElementById('f-fname').value.trim();
  const lname = document.getElementById('f-lname').value.trim();
  const email = document.getElementById('f-email').value.trim();
  const phone = document.getElementById('f-phone').value.trim();
  const dob   = document.getElementById('f-dob').value;
  const address = document.getElementById('f-address').value.trim();
  const license = document.getElementById('f-license').value.trim();
  const expiry  = document.getElementById('f-expiry').value;
  const exp     = parseInt(document.getElementById('f-exp').value)||0;
  const lictype = document.getElementById('f-lictype').value;
  const status  = document.getElementById('f-status').value;
  const notes   = document.getElementById('f-notes').value.trim();
  if(!fname||!lname||!license) { showToast('First name, last name, and license are required.'); return; }

  const payload = {
    action: editingId ? 'update' : 'create',
    id: editingId,
    first_name: fname,
    last_name: lname,
    email,
    phone,
    dob,
    address,
    license_no: license,
    license_expiry: expiry,
    experience_years: exp,
    license_type: lictype,
    status,
    notes,
    avatar_bg: selectedBg,
  };

  try {
    const response = await fetch('/rent/php/driver_action.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const json = await response.json();
    if (!response.ok || !json.driver) {
      throw new Error(json.error || 'Unable to save driver.');
    }

    let driver = { ...json.driver, bg: json.driver.avatar_bg || selectedBg };
    const photoFile = document.getElementById('f-photo').files[0];
    const documentFile = document.getElementById('f-document').files[0];
    if (photoFile) {
      driver = await uploadDriverFile('upload_photo', driver.id, 'f-photo');
    }
    if (documentFile) {
      driver = await uploadDriverFile('upload_document', driver.id, 'f-document');
    }

    if (editingId) {
      const idx = drivers.findIndex(d => d.id === editingId);
      if (idx > -1) drivers[idx] = driver;
      showToast(`${fname} ${lname} updated!`, 'success');
    } else {
      drivers.unshift(driver);
      showToast(`${fname} ${lname} added to fleet!`, 'success');
    }

    closeModal('addModal');
    updateSummaryCounts();
    filterDrivers();
  } catch (error) {
    showToast(error.message || 'Unable to save driver.');
  }
}

async function uploadDriverFile(action, id, inputId) {
  const input = document.getElementById(inputId);
  if (!input || !input.files.length) return null;

  const form = new FormData();
  form.append('action', action);
  form.append('id', id);
  form.append(inputId === 'f-photo' ? 'photo' : 'document', input.files[0]);

  const response = await fetch('/rent/php/driver_action.php', {
    method: 'POST',
    body: form,
  });
  const json = await response.json();
  if (!response.ok || !json.driver) {
    throw new Error(json.error || 'Unable to upload file.');
  }
  return { ...json.driver, bg: json.driver.avatar_bg || selectedBg };
}

async function deleteDriver(id) {
  const driver = drivers.find(x => x.id === id);
  if (!driver) return;
  if (!confirm(`Delete ${driver.fname} ${driver.lname}? This action cannot be undone.`)) return;

  try {
    const response = await fetch('/rent/php/driver_action.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'delete', id }),
    });
    const json = await response.json();
    if (!response.ok || json.error) {
      throw new Error(json.error || 'Unable to delete driver.');
    }

    drivers = drivers.filter(x => x.id !== id);
    updateSummaryCounts();
    filterDrivers();
    showToast(`${driver.fname} ${driver.lname} removed.`, 'error');
  } catch (error) {
    showToast(error.message || 'Unable to delete driver.');
  }
}

/* ════ EXPORT ════ */
function exportCSV() {
  const h=['ID','First Name','Last Name','Email','Phone','DOB','Address','License','Expiry','Experience','License Type','Status','Rating','Trips'];
  const r=drivers.map(d=>[d.id,d.fname,d.lname,d.email,d.phone,d.dob,d.address,d.license,d.expiry,d.exp,d.lictype,d.status,d.rating,d.trips]);
  const csv=[h,...r].map(row=>row.join(',')).join('\n');
  const blob=new Blob([csv],{type:'text/csv'});
  const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='REVV_Drivers.csv';a.click();
  showToast('Exported as REVV_Drivers.csv','success');
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

function setupEventHandlers() {
  const gridBtn = document.getElementById('gridToggle');
  const listBtn = document.getElementById('listToggle');
  const exportBtn = document.getElementById('exportBtn');
  const addBtn = document.getElementById('addDriverBtn');
  const searchInput = document.getElementById('searchInput');
  const expSelect = document.getElementById('expFilter');

  if (gridBtn) gridBtn.addEventListener('click', () => setView('grid'));
  if (listBtn) listBtn.addEventListener('click', () => setView('list'));
  if (exportBtn) exportBtn.addEventListener('click', exportCSV);
  if (addBtn) addBtn.addEventListener('click', openAddModal);
  if (searchInput) searchInput.addEventListener('input', filterDrivers);
  if (expSelect) expSelect.addEventListener('change', filterDrivers);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    setupEventHandlers();
    loadDrivers();
  });
} else {
  setupEventHandlers();
  loadDrivers();
}
