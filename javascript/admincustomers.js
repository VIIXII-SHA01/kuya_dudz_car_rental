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
let customers = Array.isArray(window.serverCustomers) ? window.serverCustomers : [];
let nextId = Array.isArray(window.serverCustomers) ? null : 15;
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

function assetPath(path) {
  if (!path) return '';
  return path.startsWith('/') ? path : `/rent/${path}`;
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
          <div class="dc-avatar" style="background:${d.photo ? 'transparent' : d.bg}">
            ${d.photo ? `<img src="${assetPath(d.photo)}" alt="${d.fname} ${d.lname}" style="width:100%;height:100%;object-fit:cover;border-radius:8px" onerror="this.style.display='none'" />` : ini(d)}
          </div>
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
          <div class="t-avatar" style="background:${d.photo ? 'transparent' : d.bg};position:relative;overflow:hidden">
            ${d.photo ? `<img src="${assetPath(d.photo)}" alt="${d.fname} ${d.lname}" style="width:100%;height:100%;object-fit:cover;border-radius:8px" onerror="this.style.display='none'" />` : ini(d)}
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
function updateSummaryCounts() {
  const counts = customers.reduce((acc, d) => {
    acc.total += 1;
    acc[d.status] = (acc[d.status] || 0) + 1;
    return acc;
  }, { total: 0, active: 0, vip: 0, inactive: 0, blacklisted: 0 });
  document.querySelector('.sstrip-item:nth-child(1) .sstrip-val').textContent = counts.total;
  document.querySelector('.sstrip-item:nth-child(2) .sstrip-val').textContent = counts.active;
  document.querySelector('.sstrip-item:nth-child(3) .sstrip-val').textContent = counts.vip;
  document.querySelector('.sstrip-item:nth-child(4) .sstrip-val').textContent = counts.inactive;
  document.querySelector('.sstrip-item:nth-child(5) .sstrip-val').textContent = counts.blacklisted;
}
function filterCustomers() {
  currentSearch = document.getElementById('searchInput').value;
  const data = getFiltered();
  document.getElementById('resultsCount').innerHTML=`<strong>${data.length}</strong> customer${data.length!==1?'s':''}`;
  if(currentView==='grid') renderGrid(data); else renderTable(data);
  updateSummaryCounts();
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
      <div class="detail-avatar" style="${d.photo ? 'background:transparent' : `background:${d.bg}`}">
        ${d.photo ? `<img src="${assetPath(d.photo)}" alt="${d.fname} ${d.lname}" style="width:100%;height:100%;object-fit:cover;border-radius:8px" onerror="this.style.display='none'" />` : ini(d)}
      </div>
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
      ${d.documents && d.documents.length ? `<div class="detail-row"><span class="detail-key">ID Document</span><span class="detail-val" style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;line-height:1.4;color:var(--muted2)">${d.documents.map(doc=>`<a href="#" onclick="event.preventDefault(); showCustomerDocumentPreview('${assetPath(doc.file)}', '${doc.file.split('/').pop().replace(/'/g, "\\'")}');" style="color:var(--white);text-decoration:underline">${doc.file.split('/').pop()}</a>`).join('')}</span></div>` : ''}
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
  ['f-fname','f-lname','f-email','f-phone','f-dob','f-address','f-idtype','f-idnum','f-emergency','f-notes','f-photo','f-document'].forEach(id=>{const el=document.getElementById(id); if(el) el.value='';});
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
  ['f-photo','f-document'].forEach(id=>{const el=document.getElementById(id); if(el) el.value='';});
  selectedBg=d.bg;
  document.getElementById('modalAvatarPreview').style.background=d.bg;
  document.getElementById('avatarInitials').textContent=ini(d);
  document.querySelectorAll('.av-opt').forEach(a=>a.classList.toggle('selected',a.dataset.bg===d.bg));
  openModal('addModal');
}
async function saveCustomer() {
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

  const payload = {
    action: editingId ? 'update' : 'create',
    id: editingId,
    first_name: fname,
    last_name: lname,
    email,
    phone,
    dob,
    address,
    idtype,
    idnum,
    emergency,
    tier,
    status,
    notes,
    avatar_bg: selectedBg,
  };

  const hasServer = Array.isArray(window.serverCustomers);
  const photoFile = document.getElementById('f-photo').files[0];
  const documentFile = document.getElementById('f-document').files[0];

  if (!hasServer) {
    if (editingId) {
      const i = customers.findIndex(d=>d.id===editingId);
      if (i>-1) customers[i] = { ...customers[i], fname, lname, email, phone, dob, address, idtype, idnum, emergency, tier, status, notes, bg:selectedBg };
      showToast(`${fname} ${lname} updated!`, 'success');
    } else {
      customers.unshift({ id: nextId++, fname, lname, email, phone, dob, address, idtype, idnum, emergency, tier, status, notes, bg:selectedBg, rentals:0, spent:0, joined:new Date().toISOString().split('T')[0], photo:null, documents:[] });
      showToast(`${fname} ${lname} added!`, 'success');
    }
    closeModal('addModal');
    filterCustomers();
    return;
  }

  try {
    const response = await fetch('/rent/php/customer_action.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const json = await response.json();
    if (!response.ok || json.error) {
      throw new Error(json.error || 'Unable to save customer.');
    }

    let customer = json.customer;
    if (photoFile) {
      customer = await uploadCustomerFile('upload_photo', customer.id, 'f-photo');
    }
    if (documentFile) {
      customer = await uploadCustomerFile('upload_document', customer.id, 'f-document');
    }

    if (editingId) {
      customers = customers.map(d => d.id === editingId ? customer : d);
      showToast(`${fname} ${lname} updated!`, 'success');
    } else {
      customers.unshift(customer);
      showToast(`${fname} ${lname} added!`, 'success');
    }

    closeModal('addModal');
    filterCustomers();
  } catch (error) {
    showToast(error.message || 'Unable to save customer. Please try again.', 'error');
  }
}

async function uploadCustomerFile(action, id, inputId) {
  const input = document.getElementById(inputId);
  if (!input || !input.files.length) return null;

  const form = new FormData();
  form.append('action', action);
  form.append('id', id);
  form.append(inputId === 'f-photo' ? 'photo' : 'document', input.files[0]);

  const response = await fetch('/rent/php/customer_action.php', {
    method: 'POST',
    body: form,
  });
  const json = await response.json();
  if (!response.ok || json.error) {
    throw new Error(json.error || 'Unable to upload file.');
  }
  return json.customer;
}

function showCustomerDocumentPreview(src, title) {
  document.getElementById('docPreviewTitle').textContent = title || 'Document';
  document.getElementById('docPreviewFrame').src = src;
  openModal('docPreviewModal');
}

function deleteCustomer(id) {
  const d = customers.find(x=>x.id===id);
  if (!d) return;

  const hasServer = Array.isArray(window.serverCustomers);
  if (!hasServer) {
    customers = customers.filter(x => x.id !== id);
    filterCustomers();
    showToast(`${d.fname} ${d.lname} removed.`, 'error');
    return;
  }

  if (!window.confirm(`Delete ${d.fname} ${d.lname}?`)) return;

  fetch('/rent/php/customer_action.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'delete', id }),
  })
  .then(r => r.json().then(data => ({ ok: r.ok, data })))
  .then(({ ok, data }) => {
    if (!ok || data.error) {
      showToast(data.error || 'Unable to delete customer.', 'error');
      return;
    }
    customers = customers.filter(x => x.id !== id);
    filterCustomers();
    showToast(`${d.fname} ${d.lname} removed.`, 'error');
  })
  .catch(() => showToast('Unable to delete customer. Please try again.', 'error'));
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