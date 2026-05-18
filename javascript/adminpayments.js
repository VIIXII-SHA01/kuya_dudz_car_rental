const METHOD_GRADS = {
  Cash:          'linear-gradient(135deg,#3DBE7A,#3D8FBE)',
  Card:          'linear-gradient(135deg,#3D8FBE,#9A3DBE)',
  GCash:         'linear-gradient(135deg,#3D8FBE,#5B9CF6)',
  Maya:          'linear-gradient(135deg,#2EC4B6,#3DBE7A)',
  'Bank Transfer':'linear-gradient(135deg,#D4A843,#F5642A)',
};

/* ════ DATA ════ */
let payments = [
  { id:1,  payId:'PAY-001', customer:'Maria Santos',     cusid:'CUS-001', rentalId:'RNT-001', date:'2026-03-04', due:5400,  paid:5400,  balance:0,    method:'Cash',          ref:'CASH-001',    status:'paid',     notes:'Paid in full upon return.' },
  { id:2,  payId:'PAY-002', customer:'Jose Reyes',       cusid:'CUS-002', rentalId:'RNT-002', date:'2026-03-10', due:22500, paid:22500, balance:0,    method:'Bank Transfer', ref:'BT-20260310',  status:'paid',     notes:'Corporate billing — Reyes Logistics.' },
  { id:3,  payId:'PAY-003', customer:'Ana Cruz',         cusid:'CUS-003', rentalId:'RNT-003', date:'2026-03-10', due:3600,  paid:3600,  balance:0,    method:'GCash',         ref:'GC-2026-0030', status:'paid',     notes:'' },
  { id:4,  payId:'PAY-004', customer:'Carlos Villanueva',cusid:'CUS-004', rentalId:'RNT-004', date:'2026-04-08', due:30000, paid:15000, balance:15000, method:'Cash',          ref:'CASH-004',    status:'partial',  notes:'Down payment only. Balance upon return.' },
  { id:5,  payId:'PAY-005', customer:'Liza Bautista',    cusid:'CUS-005', rentalId:'RNT-005', date:'2026-03-17', due:3600,  paid:3600,  balance:0,    method:'Maya',          ref:'MAYA-005',    status:'paid',     notes:'' },
  { id:6,  payId:'PAY-006', customer:'Marco Garcia',     cusid:'CUS-006', rentalId:'RNT-006', date:'2026-04-09', due:66500, paid:33000, balance:33500, method:'Bank Transfer', ref:'BT-20260409',  status:'partial',  notes:'Partial payment received. Balance on completion.' },
  { id:7,  payId:'PAY-007', customer:'Grace Torres',     cusid:'CUS-007', rentalId:'RNT-007', date:'2026-03-22', due:3000,  paid:3000,  balance:0,    method:'GCash',         ref:'GC-2026-0070', status:'paid',     notes:'' },
  { id:8,  payId:'PAY-008', customer:'Ryan Mendoza',     cusid:'CUS-008', rentalId:'RNT-008', date:'2026-04-20', due:3600,  paid:0,     balance:3600,  method:'Cash',          ref:'—',           status:'pending',  notes:'Payment due on pick-up date.' },
  { id:9,  payId:'PAY-009', customer:'Sofia Navarro',    cusid:'CUS-009', rentalId:'RNT-009', date:'2026-04-10', due:9600,  paid:9600,  balance:0,    method:'GCash',         ref:'GC-2026-0090', status:'paid',     notes:'Weekend renter — settled upfront.' },
  { id:10, payId:'PAY-010', customer:'Trish Pascual',    cusid:'CUS-010', rentalId:'RNT-010', date:'2026-04-25', due:16500, paid:0,     balance:16500, method:'Card',          ref:'—',           status:'pending',  notes:'Awaiting confirmation.' },
  { id:11, payId:'PAY-011', customer:'Kevin Aquino',     cusid:'CUS-011', rentalId:'RNT-011', date:'2026-03-30', due:3600,  paid:3600,  balance:0,    method:'Cash',          ref:'CASH-011',    status:'refunded', notes:'Refunded due to cancellation.' },
  { id:12, payId:'PAY-012', customer:'Maria Santos',     cusid:'CUS-001', rentalId:'RNT-012', date:'2026-04-01', due:20000, paid:5000,  balance:15000, method:'GCash',         ref:'GC-2026-0120', status:'overdue',  notes:'Customer not responding. Overdue since April 5.' },
  { id:13, payId:'PAY-013', customer:'Jose Reyes',       cusid:'CUS-002', rentalId:'RNT-013', date:'2026-03-17', due:66500, paid:66500, balance:0,    method:'Bank Transfer', ref:'BT-20260317',  status:'paid',     notes:'Business event — settled in full.' },
  { id:14, payId:'PAY-014', customer:'Marco Garcia',     cusid:'CUS-006', rentalId:'RNT-014', date:'2026-04-30', due:22000, paid:0,     balance:22000, method:'Bank Transfer', ref:'—',           status:'pending',  notes:'Group booking — invoice sent.' },
  { id:15, payId:'PAY-015', customer:'Sofia Navarro',    cusid:'CUS-009', rentalId:'RNT-015', date:'2026-04-11', due:4500,  paid:4500,  balance:0,    method:'Maya',          ref:'MAYA-015',    status:'paid',     notes:'' },
  { id:16, payId:'PAY-016', customer:'Jomar Ocampo',     cusid:'CUS-014', rentalId:'RNT-016', date:'2026-03-30', due:16800, paid:0,     balance:16800, method:'Cash',          ref:'—',           status:'overdue',  notes:'Blacklisted. Pending balance. Legal action pending.' },
];
let nextId = 17;
let currentFilter = 'all';
let currentSearch = '';
let currentView = 'grid';
let editingId = null;

/* ════ HELPERS ════ */
function methodChip(m) {
  const cls = 'mc-'+m.toLowerCase().replace(/\s+/g,'');
  return `<span class="method-chip ${cls}">${m}</span>`;
}

function badgeHTML(s) {
  const map = {
    paid:     ['paid','Paid'],
    pending:  ['pending','Pending'],
    overdue:  ['overdue','Overdue'],
    partial:  ['partial','Partial'],
    refunded: ['refunded','Refunded'],
  };
  const [cls, label] = map[s]||['pending','Unknown'];
  return `<span class="badge ${cls}"><span class="badge-dot"></span>${label}</span>`;
}

function fmtMoney(n) { return '₱'+Number(n).toLocaleString(); }
function fmtDate(str) {
  if(!str||str==='—') return '—';
  return new Date(str).toLocaleDateString('en-PH',{year:'numeric',month:'short',day:'numeric'});
}

function calcBalance() {
  const due  = parseFloat(document.getElementById('f-due').value)||0;
  const paid = parseFloat(document.getElementById('f-paid').value)||0;
  const bal  = due - paid;
  document.getElementById('f-balance').value = bal >= 0 ? '₱'+bal.toLocaleString() : '—';
}

function updateStrip() {
  const totalCollected = payments.filter(p=>p.status==='paid').reduce((s,p)=>s+p.paid,0)
    + payments.filter(p=>p.status==='partial').reduce((s,p)=>s+p.paid,0);
  document.getElementById('strip-total').textContent   = '₱'+totalCollected.toLocaleString();
  document.getElementById('strip-paid').textContent    = payments.filter(p=>p.status==='paid').length;
  document.getElementById('strip-pending').textContent = payments.filter(p=>p.status==='pending').length;
  document.getElementById('strip-overdue').textContent = payments.filter(p=>p.status==='overdue').length;
  document.getElementById('strip-partial').textContent = payments.filter(p=>p.status==='partial').length;
}

/* ════ RENDER GRID ════ */
function renderGrid(data) {
  const el    = document.getElementById('gridView');
  const empty = document.getElementById('emptyGrid');
  if(!data.length){ el.innerHTML=''; empty.classList.add('show'); return; }
  empty.classList.remove('show');
  el.innerHTML = data.map(d=>`
    <div class="payment-card ${d.status}" onclick="viewPayment(${d.id})">
      <div class="pc-header">
        <div class="pc-id-block">
          <div class="pc-pay-id">${d.payId}</div>
          <div class="pc-rental-tag">${d.rentalId} · ${d.cusid}</div>
        </div>
        ${badgeHTML(d.status)}
      </div>
      <div class="pc-body">
        <div class="pc-amount" style="color:${d.status==='paid'?'var(--green)':d.status==='overdue'?'#ff6b54':d.status==='partial'?'var(--purple)':d.status==='refunded'?'var(--blue)':'var(--gold)'}">${fmtMoney(d.paid)}</div>
        <div style="font-size:11px;color:var(--muted);margin-bottom:10px;font-family:'Barlow Condensed',sans-serif;letter-spacing:1px">PAID OF ${fmtMoney(d.due)} DUE</div>
        <div class="pc-info-row">
          <svg class="pc-info-icon" width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><circle cx="6.5" cy="5.5" r="3" stroke="currentColor" stroke-width="1.2"/><path d="M2 12c0-2.76 2.01-5 4.5-5s4.5 2.24 4.5 5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          ${d.customer}
        </div>
        <div class="pc-info-row">
          <svg class="pc-info-icon" width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><rect x="1.5" y="2" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M4 2V1M9 2V1M1.5 5h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          ${fmtDate(d.date)}
        </div>
        <div class="pc-info-row" style="margin-top:4px">
          ${methodChip(d.method)}
          ${d.ref && d.ref!=='—' ? `<span style="font-size:11px;color:var(--muted);font-family:'Barlow Condensed',sans-serif;letter-spacing:1px">${d.ref}</span>` : ''}
        </div>
      </div>
      <div class="pc-stats">
        <div class="pc-stat"><div class="pc-stat-val" style="color:var(--green)">${fmtMoney(d.paid)}</div><div class="pc-stat-lab">Paid</div></div>
        <div class="pc-stat"><div class="pc-stat-val" style="color:var(--gold)">${fmtMoney(d.due)}</div><div class="pc-stat-lab">Total Due</div></div>
        <div class="pc-stat"><div class="pc-stat-val" style="color:${d.balance>0?'var(--red)':'var(--muted2)'}">${fmtMoney(d.balance)}</div><div class="pc-stat-lab">Balance</div></div>
      </div>
      <div class="pc-actions">
        <button class="pc-act-btn view" onclick="viewPayment(${d.id});event.stopPropagation()">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M1 6.5s2.5-4 5.5-4 5.5 4 5.5 4-2.5 4-5.5 4-5.5-4-5.5-4z" stroke="currentColor" stroke-width="1.2"/><circle cx="6.5" cy="6.5" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
          View
        </button>
        <button class="pc-act-btn edit" onclick="openEditModal(${d.id});event.stopPropagation()">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 9.5L9.5 2l1.5 1.5-7.5 7.5H2V9.5z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Edit
        </button>
        <button class="pc-act-btn del" onclick="deletePayment(${d.id});event.stopPropagation()">
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
    <tr onclick="viewPayment(${d.id})">
      <td>
        <div class="driver-cell">
          <div class="t-avatar" style="background:${METHOD_GRADS[d.method]||METHOD_GRADS['Cash']}">
            <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,0.12),transparent 60%)"></div>
            <svg width="18" height="14" viewBox="0 0 18 14" fill="none" style="position:relative;z-index:1"><rect x="1" y="2" width="16" height="10" rx="2" stroke="white" stroke-width="1.4"/><path d="M1 5.5h16" stroke="white" stroke-width="1.4"/><path d="M5 9h3" stroke="white" stroke-width="1.4" stroke-linecap="round"/></svg>
          </div>
          <div>
            <div class="t-name">${d.payId}</div>
            <div class="t-sub">${d.ref && d.ref!=='—' ? d.ref : 'No reference'}</div>
          </div>
        </div>
      </td>
      <td>
        <div class="t-name">${d.customer}</div>
        <div class="t-sub">${d.cusid}</div>
      </td>
      <td style="font-family:'Barlow Condensed',sans-serif;letter-spacing:1px;color:var(--muted2)">${d.rentalId}</td>
      <td>${methodChip(d.method)}</td>
      <td style="color:var(--muted2);font-size:13px">${fmtDate(d.date)}</td>
      <td style="font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:700;color:var(--green)">${fmtMoney(d.paid)}</td>
      <td style="font-family:'Barlow Condensed',sans-serif;font-size:14px;font-weight:700;color:${d.balance>0?'var(--red)':'var(--muted)'}">${fmtMoney(d.balance)}</td>
      <td>${badgeHTML(d.status)}</td>
      <td>
        <div style="display:flex;align-items:center;gap:6px;justify-content:center">
          <div class="act-btn view" onclick="viewPayment(${d.id});event.stopPropagation()" title="View"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M1 6.5s2.5-4 5.5-4 5.5 4 5.5 4-2.5 4-5.5 4-5.5-4-5.5-4z" stroke="currentColor" stroke-width="1.2"/><circle cx="6.5" cy="6.5" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg></div>
          <div class="act-btn edit" onclick="openEditModal(${d.id});event.stopPropagation()" title="Edit"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 9.5L9.5 2l1.5 1.5-7.5 7.5H2V9.5z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
          <div class="act-btn del" onclick="deletePayment(${d.id});event.stopPropagation()" title="Remove"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 3.5h9M5 3.5V2h3v1.5M10 3.5l-.7 7.5H3.7L3 3.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </div>
      </td>
    </tr>
  `).join('');
}

/* ════ FILTER ════ */
function getFiltered() {
  const q      = currentSearch.toLowerCase();
  const methF  = document.getElementById('methodFilter').value;
  return payments.filter(d => {
    const matchStatus = currentFilter==='all' || d.status===currentFilter;
    const matchSearch = !q || d.payId.toLowerCase().includes(q) || d.customer.toLowerCase().includes(q) || d.rentalId.toLowerCase().includes(q) || d.cusid.toLowerCase().includes(q) || d.ref.toLowerCase().includes(q);
    const matchMethod = !methF || d.method===methF;
    return matchStatus && matchSearch && matchMethod;
  });
}
function filterPayments() {
  currentSearch = document.getElementById('searchInput').value;
  const data = getFiltered();
  document.getElementById('resultsCount').innerHTML=`<strong>${data.length}</strong> payment${data.length!==1?'s':''}`;
  if(currentView==='grid') renderGrid(data); else renderTable(data);
}
function setFilter(val,btn) {
  currentFilter=val;
  document.querySelectorAll('.ftab').forEach(t=>t.classList.remove('active'));
  btn.classList.add('active');
  filterPayments();
}

/* ════ VIEW TOGGLE ════ */
function setView(v) {
  currentView=v;
  document.getElementById('gridToggle').classList.toggle('active',v==='grid');
  document.getElementById('listToggle').classList.toggle('active',v==='list');
  document.getElementById('gridView').style.display   = v==='grid'?'':'none';
  document.getElementById('emptyGrid').style.display  = v==='grid'?'':'none';
  document.getElementById('tableView').style.display  = v==='list'?'block':'none';
  filterPayments();
}

/* ════ VIEW DETAIL ════ */
function viewPayment(id) {
  const d = payments.find(x=>x.id===id); if(!d) return;
  document.getElementById('detailTitle').textContent = d.payId+' — '+d.customer;
  document.getElementById('detailEditBtn').onclick = ()=>{ closeModal('detailModal'); openEditModal(id); };
  document.getElementById('detailContent').innerHTML = `
    <div class="detail-hero">
      <div class="detail-icon" style="background:${METHOD_GRADS[d.method]||METHOD_GRADS['Cash']}">
        <svg width="42" height="32" viewBox="0 0 42 32" fill="none"><rect x="2" y="4" width="38" height="24" rx="4" stroke="white" stroke-width="2"/><path d="M2 12h38" stroke="white" stroke-width="2"/><path d="M10 21h8M28 21h4" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
      </div>
      <div class="detail-info">
        <div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted2);margin-bottom:4px">${d.payId} · ${d.rentalId}</div>
        <div class="detail-name">${fmtMoney(d.paid)}</div>
        <div class="detail-meta">
          ${badgeHTML(d.status)}
          ${methodChip(d.method)}
        </div>
      </div>
    </div>
    <div class="detail-stat-grid">
      <div class="detail-stat"><div class="detail-stat-val" style="color:var(--green);font-size:16px;padding-top:4px">${fmtMoney(d.paid)}</div><div class="detail-stat-lab">Amount Paid</div></div>
      <div class="detail-stat"><div class="detail-stat-val" style="color:var(--gold);font-size:16px;padding-top:4px">${fmtMoney(d.due)}</div><div class="detail-stat-lab">Total Due</div></div>
      <div class="detail-stat"><div class="detail-stat-val" style="color:${d.balance>0?'var(--red)':'var(--muted)'};font-size:16px;padding-top:4px">${fmtMoney(d.balance)}</div><div class="detail-stat-lab">Balance</div></div>
      <div class="detail-stat"><div class="detail-stat-val" style="font-size:13px;padding-top:5px">${fmtDate(d.date)}</div><div class="detail-stat-lab">Date</div></div>
    </div>
    <div class="detail-body">
      <div class="detail-row"><span class="detail-key">Customer</span><span class="detail-val">${d.customer}</span></div>
      <div class="detail-row"><span class="detail-key">Customer ID</span><span class="detail-val" style="font-family:'Barlow Condensed',sans-serif;letter-spacing:1.5px">${d.cusid}</span></div>
      <div class="detail-row"><span class="detail-key">Rental ID</span><span class="detail-val" style="font-family:'Barlow Condensed',sans-serif;letter-spacing:1.5px">${d.rentalId}</span></div>
      <div class="detail-row"><span class="detail-key">Payment Method</span><span class="detail-val">${d.method}</span></div>
      <div class="detail-row"><span class="detail-key">Reference No.</span><span class="detail-val" style="font-family:'Barlow Condensed',sans-serif;letter-spacing:1.5px">${d.ref}</span></div>
      <div class="detail-row"><span class="detail-key">Payment Date</span><span class="detail-val">${fmtDate(d.date)}</span></div>
      ${d.notes?`<div class="detail-row"><span class="detail-key">Notes</span><span class="detail-val" style="max-width:260px;text-align:right;white-space:normal;line-height:1.5;color:var(--muted2)">${d.notes}</span></div>`:''}
    </div>
  `;
  openModal('detailModal');
}

/* ════ ADD / EDIT ════ */
function openAddModal() {
  editingId=null;
  document.getElementById('modalTitle').textContent='Record Payment';
  document.getElementById('saveBtnLabel').textContent='Save Payment';
  ['f-customer','f-cusid','f-rentalid','f-ref','f-notes'].forEach(id=>{document.getElementById(id).value='';});
  document.getElementById('f-due').value='';
  document.getElementById('f-paid').value='';
  document.getElementById('f-balance').value='';
  document.getElementById('f-date').value='';
  document.getElementById('f-method').selectedIndex=0;
  document.getElementById('f-status').selectedIndex=0;
  openModal('addModal');
}
function openEditModal(id) {
  const d=payments.find(x=>x.id===id); if(!d) return;
  editingId=id;
  document.getElementById('modalTitle').textContent=`Edit — ${d.payId}`;
  document.getElementById('saveBtnLabel').textContent='Save Changes';
  document.getElementById('f-customer').value=d.customer;
  document.getElementById('f-cusid').value=d.cusid;
  document.getElementById('f-rentalid').value=d.rentalId;
  document.getElementById('f-date').value=d.date;
  document.getElementById('f-due').value=d.due;
  document.getElementById('f-paid').value=d.paid;
  document.getElementById('f-balance').value='₱'+d.balance.toLocaleString();
  document.getElementById('f-method').value=d.method;
  document.getElementById('f-ref').value=d.ref;
  document.getElementById('f-status').value=d.status;
  document.getElementById('f-notes').value=d.notes;
  openModal('addModal');
}
function savePayment() {
  const customer = document.getElementById('f-customer').value.trim();
  const cusid    = document.getElementById('f-cusid').value.trim();
  const rentalId = document.getElementById('f-rentalid').value.trim();
  const date     = document.getElementById('f-date').value;
  const due      = parseFloat(document.getElementById('f-due').value)||0;
  const paid     = parseFloat(document.getElementById('f-paid').value)||0;
  const balance  = due - paid;
  const method   = document.getElementById('f-method').value;
  const ref      = document.getElementById('f-ref').value.trim()||'—';
  const status   = document.getElementById('f-status').value;
  const notes    = document.getElementById('f-notes').value.trim();
  if(!customer||!rentalId||!date) { showToast('Customer, rental ID, and date are required.'); return; }
  if(editingId) {
    const i=payments.findIndex(d=>d.id===editingId);
    if(i>-1) payments[i]={...payments[i],customer,cusid,rentalId,date,due,paid,balance,method,ref,status,notes};
    showToast(`${payments[payments.findIndex(d=>d.id===editingId)].payId} updated!`,'success');
  } else {
    const newId='PAY-'+String(nextId).padStart(3,'0');
    payments.unshift({id:nextId++,payId:newId,customer,cusid,rentalId,date,due,paid,balance,method,ref,status,notes});
    showToast(`${newId} recorded!`,'success');
  }
  closeModal('addModal');
  updateStrip();
  filterPayments();
}
function deletePayment(id) {
  const d=payments.find(x=>x.id===id);
  payments=payments.filter(x=>x.id!==id);
  updateStrip();
  filterPayments();
  showToast(`${d.payId} removed.`,'error');
}

/* ════ EXPORT ════ */
function exportCSV() {
  const h=['Payment ID','Customer','Customer ID','Rental ID','Date','Method','Reference','Total Due','Amount Paid','Balance','Status','Notes'];
  const r=payments.map(d=>[d.payId,d.customer,d.cusid,d.rentalId,d.date,d.method,d.ref,d.due,d.paid,d.balance,d.status,d.notes]);
  const csv=[h,...r].map(row=>row.join(',')).join('\n');
  const blob=new Blob([csv],{type:'text/csv'});
  const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='REVV_Payments.csv';a.click();
  showToast('Exported as REVV_Payments.csv','success');
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

updateStrip();
filterPayments();