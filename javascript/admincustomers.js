const GRADS = [
  'linear-gradient(135deg,#E8341A,#F5642A)',
  'linear-gradient(135deg,#3D8FBE,#3DBE7A)',
  'linear-gradient(135deg,#D4A843,#F5642A)',
  'linear-gradient(135deg,#3DBE7A,#3D8FBE)',
  'linear-gradient(135deg,#9A3DBE,#E8341A)',
  'linear-gradient(135deg,#6A6E75,#3D8FBE)',
];
let selectedBg = GRADS[1];

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
let customers = [
  { id:1,  fname:'Maria',    lname:'Santos',     email:'maria@gmail.com',    phone:'+63 917 100 1111', dob:'1990-04-12', address:'Cebu City',          idtype:"Driver's License", idnum:'N01-90-111111', emergency:'+63 917 999 0001', tier:'Gold',     status:'vip',        rentals:34, spent:87500,  joined:'2021-03-10', notes:'Preferred customer, always pays on time.', bg:GRADS[2] },
  { id:2,  fname:'Jose',     lname:'Reyes',      email:'jose@gmail.com',     phone:'+63 918 200 2222', dob:'1985-09-22', address:'Mandaue City',        idtype:'Passport',         idnum:'P1234567A',    emergency:'+63 918 999 0002', tier:'Platinum', status:'vip',        rentals:52, spent:134000, joined:'2020-01-15', notes:'Corporate account — Reyes Logistics Inc.', bg:GRADS[3] },
  { id:3,  fname:'Ana',      lname:'Cruz',       email:'ana@gmail.com',      phone:'+63 919 300 3333', dob:'1995-07-08', address:'Lapu-Lapu City',      idtype:'National ID',      idnum:'NID-003',      emergency:'+63 919 999 0003', tier:'Silver',   status:'active',     rentals:12, spent:28400,  joined:'2022-06-20', notes:'', bg:GRADS[4] },
  { id:4,  fname:'Carlos',   lname:'Villanueva', email:'carlos@gmail.com',   phone:'+63 920 400 4444', dob:'1988-12-01', address:'Talisay City',        idtype:'SSS ID',           idnum:'SSS-004',      emergency:'+63 920 999 0004', tier:'Gold',     status:'active',     rentals:27, spent:61200,  joined:'2021-09-05', notes:'Rents SUVs for family trips.', bg:GRADS[5] },
  { id:5,  fname:'Liza',     lname:'Bautista',   email:'liza@gmail.com',     phone:'+63 921 500 5555', dob:'1993-02-14', address:'Consolacion, Cebu',   idtype:"Driver's License", idnum:'N01-93-555555', emergency:'+63 921 999 0005', tier:'Basic',    status:'active',     rentals:5,  spent:9800,   joined:'2023-11-12', notes:'', bg:GRADS[0] },
  { id:6,  fname:'Marco',    lname:'Garcia',     email:'marco@gmail.com',    phone:'+63 922 600 6666', dob:'1987-06-30', address:'Minglanilla, Cebu',   idtype:'Passport',         idnum:'P7654321B',    emergency:'+63 922 999 0006', tier:'Platinum', status:'vip',        rentals:61, spent:198000, joined:'2019-07-01', notes:'High-value client. Prefers premium vehicles.', bg:GRADS[1] },
  { id:7,  fname:'Grace',    lname:'Torres',     email:'grace@gmail.com',    phone:'+63 923 700 7777', dob:'1996-03-18', address:'Carcar City, Cebu',   idtype:'National ID',      idnum:'NID-007',      emergency:'+63 923 999 0007', tier:'Silver',   status:'active',     rentals:9,  spent:21600,  joined:'2022-04-08', notes:'', bg:GRADS[2] },
  { id:8,  fname:'Ryan',     lname:'Mendoza',    email:'ryan@gmail.com',     phone:'+63 924 800 8888', dob:'1991-10-25', address:'Toledo City, Cebu',   idtype:'SSS ID',           idnum:'SSS-008',      emergency:'+63 924 999 0008', tier:'Basic',    status:'active',     rentals:3,  spent:6300,   joined:'2024-01-20', notes:'', bg:GRADS[3] },
  { id:9,  fname:'Sofia',    lname:'Navarro',    email:'sofia@gmail.com',    phone:'+63 925 900 9999', dob:'1994-08-11', address:'Naga City, Cebu',     idtype:"Driver's License", idnum:'N01-94-999999', emergency:'+63 925 999 0009', tier:'Gold',     status:'active',     rentals:19, spent:46000,  joined:'2021-12-03', notes:'Frequent weekend renter.', bg:GRADS[4] },
  { id:10, fname:'Trish',    lname:'Pascual',    email:'trish@gmail.com',    phone:'+63 926 010 1010', dob:'1989-05-05', address:'Danao City, Cebu',    idtype:'National ID',      idnum:'NID-010',      emergency:'+63 926 999 0010', tier:'Silver',   status:'active',     rentals:14, spent:33200,  joined:'2022-08-17', notes:'', bg:GRADS[5] },
  { id:11, fname:'Kevin',    lname:'Aquino',     email:'kevin@gmail.com',    phone:'+63 927 011 1111', dob:'1992-01-29', address:'Bogo City, Cebu',     idtype:'Postal ID',        idnum:'POST-011',     emergency:'+63 927 999 0011', tier:'Basic',    status:'inactive',   rentals:2,  spent:4200,   joined:'2023-05-30', notes:'Account dormant for 8 months.', bg:GRADS[0] },
  { id:12, fname:'Diana',    lname:'Lim',        email:'diana@gmail.com',    phone:'+63 928 012 1212', dob:'1998-11-17', address:'Badian, Cebu',        idtype:"Driver's License", idnum:'N01-98-121212', emergency:'+63 928 999 0012', tier:'Basic',    status:'inactive',   rentals:1,  spent:2100,   joined:'2024-03-14', notes:'', bg:GRADS[1] },
  { id:13, fname:'Patrick',  lname:'Ramos',      email:'patrick@gmail.com',  phone:'+63 929 013 1313', dob:'1984-07-04', address:'Alcoy, Cebu',         idtype:'Passport',         idnum:'P1313131C',    emergency:'+63 929 999 0013', tier:'Basic',    status:'inactive',   rentals:4,  spent:8500,   joined:'2023-02-28', notes:'Moved abroad, account inactive.', bg:GRADS[2] },
  { id:14, fname:'Jomar',    lname:'Ocampo',     email:'jomar@gmail.com',    phone:'+63 930 014 1414', dob:'1983-03-21', address:'Medellin, Cebu',      idtype:'SSS ID',           idnum:'SSS-014',      emergency:'+63 930 999 0014', tier:'Silver',   status:'blacklisted', rentals:8, spent:18400,  joined:'2021-07-19', notes:'Blacklisted due to vehicle damage dispute and unpaid balance.', bg:GRADS[3] },
];
let nextId = 15;
let currentFilter = 'all';
let currentSearch = '';
let currentView = 'grid';
let editingId = null;

/* ════ HELPERS ════ */
function ini(d) { return (d.fname[0]+d.lname[0]).toUpperCase(); }

function tierChip(t) {
  const cls = t==='Platinum'?'tier-platinum':t==='Gold'?'tier-gold':t==='Silver'?'tier-silver':'tier-basic';
  const icon = t==='Platinum'?'★':t==='Gold'?'✦':t==='Silver'?'◆':'·';
  return `<span class="tier-chip ${cls}">${icon} ${t}</span>`;
}

function badgeHTML(s) {
  const map = { active:['active','Active'], vip:['vip','VIP'], inactive:['inactive','Inactive'], blacklisted:['blacklisted','Blacklisted'] };
  const [cls, label] = map[s]||['inactive','Unknown'];
  return `<span class="badge ${cls}"><span class="badge-dot"></span>${label}</span>`;
}

function fmtMoney(n) { return '₱'+n.toLocaleString(); }

function fmtDate(str) {
  if(!str) return '—';
  return new Date(str).toLocaleDateString('en-PH',{year:'numeric',month:'short',day:'numeric'});
}

/* ════ RENDER GRID ════ */
function renderGrid(data) {
  const el = document.getElementById('gridView');
  const empty = document.getElementById('emptyGrid');
  if(!data.length){ el.innerHTML=''; empty.classList.add('show'); return; }
  empty.classList.remove('show');
  el.innerHTML = data.map(d=>`
    <div class="customer-card" onclick="viewCustomer(${d.id})">
      <div class="dc-header">
        <div class="dc-avatar-wrap">
          <div class="dc-avatar" style="background:${d.bg}">${ini(d)}</div>
        </div>
        ${badgeHTML(d.status)}
      </div>
      <div class="dc-body">
        <div class="dc-name">${d.fname} ${d.lname}</div>
        <div class="dc-id">CUS-${String(d.id).padStart(3,'0')} · ${tierChip(d.tier)}</div>
        <div class="dc-info-row">
          <svg class="dc-info-icon" width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 3.5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-6z" stroke="currentColor" stroke-width="1.2"/><path d="M2 4.5l4.5 3 4.5-3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          ${d.email}
        </div>
        <div class="dc-info-row">
          <svg class="dc-info-icon" width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2.5 2h2l1 3-1.5 1a8 8 0 0 0 3 3l1-1.5 3 1v2a1 1 0 0 1-1 1A9 9 0 0 1 1.5 3a1 1 0 0 1 1-1z" stroke="currentColor" stroke-width="1.2"/></svg>
          ${d.phone}
        </div>
        <div class="dc-info-row">
          <svg class="dc-info-icon" width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><rect x="1.5" y="2" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M4 2V1M9 2V1M1.5 5h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          Member since ${fmtDate(d.joined)}
        </div>
        <div class="dc-info-row">
          <svg class="dc-info-icon" width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M6.5 1.5c-2.21 0-4 1.79-4 4 0 2.98 4 7 4 7s4-4.02 4-7c0-2.21-1.79-4-4-4z" stroke="currentColor" stroke-width="1.2"/><circle cx="6.5" cy="5.5" r="1.3" stroke="currentColor" stroke-width="1.1"/></svg>
          ${d.address}
        </div>
      </div>
      <div class="dc-stats">
        <div class="dc-stat"><div class="dc-stat-val" style="color:var(--red)">${d.rentals}</div><div class="dc-stat-lab">Rentals</div></div>
        <div class="dc-stat"><div class="dc-stat-val" style="color:var(--green);font-size:14px;padding-top:2px">${fmtMoney(d.spent)}</div><div class="dc-stat-lab">Total Spent</div></div>
        <div class="dc-stat"><div class="dc-stat-val" style="color:var(--blue)">${d.tier}</div><div class="dc-stat-lab">Tier</div></div>
      </div>
      <div class="dc-actions">
        <button class="dc-act-btn view" onclick="viewCustomer(${d.id});event.stopPropagation()">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M1 6.5s2.5-4 5.5-4 5.5 4 5.5 4-2.5 4-5.5 4-5.5-4-5.5-4z" stroke="currentColor" stroke-width="1.2"/><circle cx="6.5" cy="6.5" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
          View
        </button>
        <button class="dc-act-btn edit" onclick="openEditModal(${d.id});event.stopPropagation()">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 9.5L9.5 2l1.5 1.5-7.5 7.5H2V9.5z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Edit
        </button>
        <button class="dc-act-btn del" onclick="deleteCustomer(${d.id});event.stopPropagation()">
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
    <tr onclick="viewCustomer(${d.id})">
      <td>
        <div class="driver-cell">
          <div class="t-avatar" style="background:${d.bg};position:relative;overflow:hidden">
            ${ini(d)}
            <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,0.12),transparent 60%)"></div>
          </div>
          <div>
            <div class="t-name">${d.fname} ${d.lname}</div>
            <div class="t-sub">CUS-${String(d.id).padStart(3,'0')} · ${d.email}</div>
          </div>
        </div>
      </td>
      <td style="color:var(--muted2);font-size:13px">${d.phone}</td>
      <td style="color:var(--muted2);font-size:13px">${fmtDate(d.joined)}</td>
      <td>${tierChip(d.tier)}</td>
      <td style="font-family:'Bebas Neue',sans-serif;font-size:18px;letter-spacing:1px;color:var(--red)">${d.rentals}</td>
      <td style="font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:700;color:var(--green)">${fmtMoney(d.spent)}</td>
      <td>${badgeHTML(d.status)}</td>
      <td>
        <div style="display:flex;align-items:center;gap:6px;justify-content:center">
          <div class="act-btn view" onclick="viewCustomer(${d.id});event.stopPropagation()" title="View"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M1 6.5s2.5-4 5.5-4 5.5 4 5.5 4-2.5 4-5.5 4-5.5-4-5.5-4z" stroke="currentColor" stroke-width="1.2"/><circle cx="6.5" cy="6.5" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg></div>
          <div class="act-btn edit" onclick="openEditModal(${d.id});event.stopPropagation()" title="Edit"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 9.5L9.5 2l1.5 1.5-7.5 7.5H2V9.5z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
          <div class="act-btn del" onclick="deleteCustomer(${d.id});event.stopPropagation()" title="Remove"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 3.5h9M5 3.5V2h3v1.5M10 3.5l-.7 7.5H3.7L3 3.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </div>
      </td>
    </tr>
  `).join('');
}

/* ════ FILTER ════ */
function getFiltered() {
  const q = currentSearch.toLowerCase();
  const tierF = document.getElementById('tierFilter').value;
  return customers.filter(d => {
    const matchStatus = currentFilter==='all' || d.status===currentFilter;
    const matchSearch = !q || (d.fname+' '+d.lname).toLowerCase().includes(q) || d.email.toLowerCase().includes(q) || d.phone.includes(q) || d.address.toLowerCase().includes(q);
    const matchTier = !tierF || d.tier===tierF;
    return matchStatus && matchSearch && matchTier;
  });
}
function filterCustomers() {
  currentSearch = document.getElementById('searchInput').value;
  const data = getFiltered();
  document.getElementById('resultsCount').innerHTML=`<strong>${data.length}</strong> customer${data.length!==1?'s':''}`;
  if(currentView==='grid') renderGrid(data); else renderTable(data);
}
function setFilter(val,btn) {
  currentFilter=val;
  document.querySelectorAll('.ftab').forEach(t=>t.classList.remove('active'));
  btn.classList.add('active');
  filterCustomers();
}

/* ════ VIEW TOGGLE ════ */
function setView(v) {
  currentView=v;
  document.getElementById('gridToggle').classList.toggle('active',v==='grid');
  document.getElementById('listToggle').classList.toggle('active',v==='list');
  document.getElementById('gridView').style.display = v==='grid'?'':'none';
  document.getElementById('emptyGrid').style.display = v==='grid'?'':'none';
  document.getElementById('tableView').style.display = v==='list'?'block':'none';
  filterCustomers();
}

/* ════ VIEW DETAIL ════ */
function viewCustomer(id) {
  const d = customers.find(x=>x.id===id); if(!d) return;
  document.getElementById('detailTitle').textContent = d.fname+' '+d.lname;
  document.getElementById('detailEditBtn').onclick = ()=>{ closeModal('detailModal'); openEditModal(id); };
  document.getElementById('detailContent').innerHTML = `
    <div class="detail-hero">
      <div class="detail-avatar" style="background:${d.bg}">${ini(d)}</div>
      <div class="detail-info">
        <div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted2);margin-bottom:4px">CUS-${String(d.id).padStart(3,'0')} · ${d.idtype}</div>
        <div class="detail-name">${d.fname} ${d.lname}</div>
        <div class="detail-meta">
          ${badgeHTML(d.status)}
          ${tierChip(d.tier)}
        </div>
      </div>
    </div>
    <div class="detail-stat-grid">
      <div class="detail-stat"><div class="detail-stat-val" style="color:var(--red)">${d.rentals}</div><div class="detail-stat-lab">Total Rentals</div></div>
      <div class="detail-stat"><div class="detail-stat-val" style="color:var(--green);font-size:16px;padding-top:4px">${fmtMoney(d.spent)}</div><div class="detail-stat-lab">Total Spent</div></div>
      <div class="detail-stat"><div class="detail-stat-val" style="color:var(--blue)">${d.tier}</div><div class="detail-stat-lab">Membership</div></div>
      <div class="detail-stat"><div class="detail-stat-val" style="font-size:14px;padding-top:6px">${fmtDate(d.joined)}</div><div class="detail-stat-lab">Member Since</div></div>
    </div>
    <div class="detail-body">
      <div class="detail-row"><span class="detail-key">Email</span><span class="detail-val">${d.email}</span></div>
      <div class="detail-row"><span class="detail-key">Phone</span><span class="detail-val">${d.phone}</span></div>
      <div class="detail-row"><span class="detail-key">Address</span><span class="detail-val">${d.address}</span></div>
      <div class="detail-row"><span class="detail-key">Date of Birth</span><span class="detail-val">${d.dob ? new Date(d.dob).toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'}) : '—'}</span></div>
      <div class="detail-row"><span class="detail-key">ID Type</span><span class="detail-val">${d.idtype}</span></div>
      <div class="detail-row"><span class="detail-key">ID Number</span><span class="detail-val" style="font-family:'Barlow Condensed',sans-serif;letter-spacing:1.5px">${d.idnum||'—'}</span></div>
      <div class="detail-row"><span class="detail-key">Emergency Contact</span><span class="detail-val">${d.emergency||'—'}</span></div>
      ${d.notes?`<div class="detail-row"><span class="detail-key">Notes</span><span class="detail-val" style="max-width:260px;text-align:right;white-space:normal;line-height:1.5;color:var(--muted2)">${d.notes}</span></div>`:''}
    </div>
  `;
  openModal('detailModal');
}

/* ════ ADD / EDIT ════ */
function openAddModal() {
  editingId=null;
  document.getElementById('modalTitle').textContent='Add Customer';
  document.getElementById('saveBtnLabel').textContent='Save Customer';
  ['f-fname','f-lname','f-email','f-phone','f-dob','f-address','f-idnum','f-emergency','f-notes'].forEach(id=>{document.getElementById(id).value='';});
  document.getElementById('f-tier').selectedIndex=0;
  document.getElementById('f-idtype').selectedIndex=0;
  document.getElementById('f-status').selectedIndex=0;
  selectedBg=GRADS[1];
  document.getElementById('modalAvatarPreview').style.background=GRADS[1];
  document.querySelectorAll('.av-opt').forEach((a,i)=>a.classList.toggle('selected',i===1));
  document.getElementById('avatarInitials').textContent='?';
  openModal('addModal');
}
function openEditModal(id) {
  const d=customers.find(x=>x.id===id); if(!d) return;
  editingId=id;
  document.getElementById('modalTitle').textContent=`Edit — ${d.fname} ${d.lname}`;
  document.getElementById('saveBtnLabel').textContent='Save Changes';
  document.getElementById('f-fname').value=d.fname;
  document.getElementById('f-lname').value=d.lname;
  document.getElementById('f-email').value=d.email;
  document.getElementById('f-phone').value=d.phone;
  document.getElementById('f-dob').value=d.dob;
  document.getElementById('f-address').value=d.address;
  document.getElementById('f-idtype').value=d.idtype;
  document.getElementById('f-idnum').value=d.idnum;
  document.getElementById('f-emergency').value=d.emergency;
  document.getElementById('f-tier').value=d.tier;
  document.getElementById('f-status').value=d.status;
  document.getElementById('f-notes').value=d.notes;
  selectedBg=d.bg;
  document.getElementById('modalAvatarPreview').style.background=d.bg;
  document.getElementById('avatarInitials').textContent=ini(d);
  document.querySelectorAll('.av-opt').forEach(a=>a.classList.toggle('selected',a.dataset.bg===d.bg));
  openModal('addModal');
}
function saveCustomer() {
  const fname = document.getElementById('f-fname').value.trim();
  const lname = document.getElementById('f-lname').value.trim();
  const email = document.getElementById('f-email').value.trim();
  const phone = document.getElementById('f-phone').value.trim();
  const dob   = document.getElementById('f-dob').value;
  const address = document.getElementById('f-address').value.trim();
  const idtype  = document.getElementById('f-idtype').value;
  const idnum   = document.getElementById('f-idnum').value.trim();
  const emergency = document.getElementById('f-emergency').value.trim();
  const tier   = document.getElementById('f-tier').value;
  const status = document.getElementById('f-status').value;
  const notes  = document.getElementById('f-notes').value.trim();
  if(!fname||!lname||!email) { showToast('First name, last name, and email are required.'); return; }
  if(editingId) {
    const i=customers.findIndex(d=>d.id===editingId);
    if(i>-1) customers[i]={...customers[i],fname,lname,email,phone,dob,address,idtype,idnum,emergency,tier,status,notes,bg:selectedBg};
    showToast(`${fname} ${lname} updated!`,'success');
  } else {
    customers.unshift({id:nextId++,fname,lname,email,phone,dob,address,idtype,idnum,emergency,tier,status,notes,bg:selectedBg,rentals:0,spent:0,joined:new Date().toISOString().split('T')[0]});
    showToast(`${fname} ${lname} added!`,'success');
  }
  closeModal('addModal');
  filterCustomers();
}
function deleteCustomer(id) {
  const d=customers.find(x=>x.id===id);
  customers=customers.filter(x=>x.id!==id);
  filterCustomers();
  showToast(`${d.fname} ${d.lname} removed.`,'error');
}

/* ════ EXPORT ════ */
function exportCSV() {
  const h=['ID','First Name','Last Name','Email','Phone','DOB','Address','ID Type','ID Number','Emergency','Tier','Status','Rentals','Total Spent','Joined'];
  const r=customers.map(d=>[d.id,d.fname,d.lname,d.email,d.phone,d.dob,d.address,d.idtype,d.idnum,d.emergency,d.tier,d.status,d.rentals,d.spent,d.joined]);
  const csv=[h,...r].map(row=>row.join(',')).join('\n');
  const blob=new Blob([csv],{type:'text/csv'});
  const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='REVV_Customers.csv';a.click();
  showToast('Exported as REVV_Customers.csv','success');
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

filterCustomers();