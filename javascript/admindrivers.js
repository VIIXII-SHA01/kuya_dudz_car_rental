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
let drivers = [
  { id:1,  fname:'Marco',   lname:'Reyes',      email:'marco@revv.ph',   phone:'+63 917 111 2233', dob:'1990-03-15', address:'Cebu City',          license:'N01-23-456789', expiry:'2026-08-10', exp:8,  lictype:'Professional',    status:'available', rating:4.9, trips:312, notes:'',                              bg:GRADS[0] },
  { id:2,  fname:'Dan',     lname:'Santos',     email:'dan@revv.ph',     phone:'+63 918 222 3344', dob:'1992-07-22', address:'Mandaue City',        license:'N01-20-123456', expiry:'2025-12-31', exp:5,  lictype:'Professional',    status:'on-duty',   rating:4.7, trips:198, notes:'Currently with booking BK-0091', bg:GRADS[1] },
  { id:3,  fname:'Lea',     lname:'Villanueva', email:'lea@revv.ph',     phone:'+63 919 333 4455', dob:'1995-01-10', address:'Lapu-Lapu City',      license:'N01-21-234567', expiry:'2027-03-20', exp:3,  lictype:'Professional',    status:'available', rating:4.8, trips:145, notes:'',                              bg:GRADS[2] },
  { id:4,  fname:'Rico',    lname:'Bautista',   email:'rico@revv.ph',    phone:'+63 920 444 5566', dob:'1988-11-05', address:'Talisay City',        license:'N01-18-345678', expiry:'2026-05-15', exp:10, lictype:'Professional',    status:'on-duty',   rating:4.6, trips:421, notes:'Senior driver — preferred for SUVs', bg:GRADS[3] },
  { id:5,  fname:'Mia',     lname:'Gonzales',   email:'mia@revv.ph',     phone:'+63 921 555 6677', dob:'1997-06-18', address:'Consolacion, Cebu',   license:'N01-22-456789', expiry:'2027-11-08', exp:2,  lictype:'Professional',    status:'available', rating:4.5, trips:87,  notes:'',                              bg:GRADS[4] },
  { id:6,  fname:'Jake',    lname:'Torres',     email:'jake@revv.ph',    phone:'+63 922 666 7788', dob:'1985-09-30', address:'Minglanilla, Cebu',   license:'N01-15-567890', expiry:'2024-09-30', exp:14, lictype:'Professional',    status:'off-duty',  rating:4.3, trips:634, notes:'On leave until April 20',       bg:GRADS[5] },
  { id:7,  fname:'Sofia',   lname:'Lim',        email:'sofia@revv.ph',   phone:'+63 923 777 8899', dob:'1993-04-12', address:'Carcar City, Cebu',   license:'N01-19-678901', expiry:'2026-07-22', exp:6,  lictype:'Professional',    status:'on-duty',   rating:4.8, trips:267, notes:'',                              bg:GRADS[0] },
  { id:8,  fname:'Luis',    lname:'Navarro',    email:'luis@revv.ph',    phone:'+63 924 888 9900', dob:'1991-12-25', address:'Toledo City, Cebu',   license:'N01-17-789012', expiry:'2025-06-14', exp:9,  lictype:'Professional',    status:'available', rating:4.7, trips:389, notes:'',                              bg:GRADS[1] },
  { id:9,  fname:'Grace',   lname:'Mendoza',    email:'grace@revv.ph',   phone:'+63 925 999 0011', dob:'1996-08-07', address:'Naga City, Cebu',     license:'N01-22-890123', expiry:'2028-01-30', exp:2,  lictype:'Non-Professional',status:'available', rating:4.4, trips:63,  notes:'',                              bg:GRADS[2] },
  { id:10, fname:'Ryan',    lname:'Cruz',       email:'ryan@revv.ph',    phone:'+63 926 000 1122', dob:'1989-02-14', address:'Danao City, Cebu',    license:'N01-16-901234', expiry:'2025-10-05', exp:12, lictype:'Professional',    status:'on-duty',   rating:4.9, trips:511, notes:'Top-rated driver',              bg:GRADS[3] },
  { id:11, fname:'Trish',   lname:'Aquino',     email:'trish@revv.ph',   phone:'+63 927 111 2233', dob:'1994-05-28', address:'Bogo City, Cebu',     license:'N01-20-012345', expiry:'2026-04-18', exp:4,  lictype:'Professional',    status:'off-duty',  rating:4.6, trips:176, notes:'Rest day today',                bg:GRADS[4] },
  { id:12, fname:'Jomar',   lname:'Pascual',    email:'jomar@revv.ph',   phone:'+63 928 222 3344', dob:'1987-10-03', address:'Badian, Cebu',        license:'N01-14-123450', expiry:'2024-03-01', exp:16, lictype:'Professional',    status:'suspended', rating:3.2, trips:708, notes:'Under investigation — multiple complaints', bg:GRADS[5] },
];
let nextId = 13;
let currentFilter = 'all';
let currentSearch = '';
let currentView = 'grid';
let editingId = null;

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
          <div class="dc-avatar" style="background:${d.bg}">${ini(d)}<div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,0.12),transparent 60%)"></div></div>
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
  if(!data.length){ tbody.innerHTML=''; empty.classList.add('show'); tfi.innerHTML='No results'; return; }
  empty.classList.remove('show');
  tfi.innerHTML=`Showing <strong>1–${Math.min(10,data.length)}</strong> of <strong>${data.length}</strong>`;
  tbody.innerHTML = data.map(d=>`
    <tr onclick="viewDriver(${d.id})">
      <td>
        <div class="driver-cell">
          <div class="t-avatar" style="background:${d.bg};position:relative;overflow:hidden">
            ${ini(d)}
            <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,0.12),transparent 60%)"></div>
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
  const data = getFiltered();
  document.getElementById('resultsCount').innerHTML=`<strong>${data.length}</strong> driver${data.length!==1?'s':''}`;
  if(currentView==='grid') renderGrid(data); else renderTable(data);
}
function setFilter(val,btn) {
  currentFilter=val;
  document.querySelectorAll('.ftab').forEach(t=>t.classList.remove('active'));
  btn.classList.add('active');
  filterDrivers();
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
      <div class="detail-avatar" style="background:${d.bg}">
        ${ini(d)}
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
  selectedBg=d.bg;
  document.getElementById('modalAvatarPreview').style.background=d.bg;
  document.getElementById('avatarInitials').textContent=ini(d);
  document.querySelectorAll('.av-opt').forEach(a=>a.classList.toggle('selected',a.dataset.bg===d.bg));
  openModal('addModal');
}
function saveDriver() {
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
  if(editingId) {
    const i=drivers.findIndex(d=>d.id===editingId);
    if(i>-1) drivers[i]={...drivers[i],fname,lname,email,phone,dob,address,license,expiry,exp,lictype,status,notes,bg:selectedBg};
    showToast(`${fname} ${lname} updated!`,'success');
  } else {
    drivers.unshift({id:nextId++,fname,lname,email,phone,dob,address,license,expiry,exp,lictype,status,notes,bg:selectedBg,rating:5.0,trips:0});
    showToast(`${fname} ${lname} added to fleet!`,'success');
  }
  closeModal('addModal');
  filterDrivers();
}
function deleteDriver(id) {
  const d=drivers.find(x=>x.id===id);
  drivers=drivers.filter(x=>x.id!==id);
  filterDrivers();
  showToast(`${d.fname} ${d.lname} removed.`,'error');
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

filterDrivers();