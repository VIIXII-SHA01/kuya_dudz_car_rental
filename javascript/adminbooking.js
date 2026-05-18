/* ════ DATA ════ */
const AVATARS = [
  'linear-gradient(135deg,#E8341A,#F5642A)',
  'linear-gradient(135deg,#3D8FBE,#3DBE7A)',
  'linear-gradient(135deg,#D4A843,#F5642A)',
  'linear-gradient(135deg,#6A6E75,#9A9DA4)',
  'linear-gradient(135deg,#3DBE7A,#3D8FBE)',
  'linear-gradient(135deg,#E8341A,#D4A843)',
  'linear-gradient(135deg,#9A9DA4,#3D8FBE)',
  'linear-gradient(135deg,#3DBE7A,#D4A843)',
];

let bookings = [
  { id:'BK-0091', customer:'Maria Lopez',     email:'maria@email.com',  vehicle:'Toyota Vios 1.3L',    plate:'ABC-1234', pickup:'Apr 10, 2026', ret:'Apr 13, 2026', days:3,  amount:2400, status:'active' },
  { id:'BK-0090', customer:'Rico Castillo',   email:'rico@email.com',   vehicle:'Honda City RS',       plate:'XYZ-5678', pickup:'Apr 11, 2026', ret:'Apr 14, 2026', days:3,  amount:1800, status:'pending' },
  { id:'BK-0089', customer:'Ana Santos',      email:'ana@email.com',    vehicle:'Mitsubishi Mirage',   plate:'DEF-9012', pickup:'Apr 5, 2026',  ret:'Apr 9, 2026',  days:4,  amount:3200, status:'done' },
  { id:'BK-0088', customer:'Jose Dela Cruz',  email:'jose@email.com',   vehicle:'Hyundai Accent',      plate:'GHI-3456', pickup:'Apr 3, 2026',  ret:'Apr 5, 2026',  days:2,  amount:0,    status:'canceled' },
  { id:'BK-0087', customer:'Karen Reyes',     email:'karen@email.com',  vehicle:'Ford EcoSport',       plate:'JKL-7890', pickup:'Apr 8, 2026',  ret:'Apr 13, 2026', days:5,  amount:4500, status:'active' },
  { id:'BK-0086', customer:'Luis Torres',     email:'luis@email.com',   vehicle:'Suzuki Swift',        plate:'PQR-3344', pickup:'Apr 6, 2026',  ret:'Apr 8, 2026',  days:2,  amount:1600, status:'done' },
  { id:'BK-0085', customer:'Grace Mendoza',   email:'grace@email.com',  vehicle:'Toyota Vios 1.3L',    plate:'ABC-1234', pickup:'Apr 2, 2026',  ret:'Apr 5, 2026',  days:3,  amount:2400, status:'done' },
  { id:'BK-0084', customer:'Renz Aquino',     email:'renz@email.com',   vehicle:'Honda City RS',       plate:'XYZ-5678', pickup:'Apr 14, 2026', ret:'Apr 16, 2026', days:2,  amount:1200, status:'pending' },
  { id:'BK-0083', customer:'Mia Villanueva',  email:'mia@email.com',    vehicle:'Mitsubishi Mirage',   plate:'DEF-9012', pickup:'Apr 15, 2026', ret:'Apr 18, 2026', days:3,  amount:2400, status:'active' },
  { id:'BK-0082', customer:'Jake Bautista',   email:'jake@email.com',   vehicle:'Ford EcoSport',       plate:'JKL-7890', pickup:'Apr 1, 2026',  ret:'Apr 3, 2026',  days:2,  amount:1800, status:'done' },
  { id:'BK-0081', customer:'Sofia Lim',       email:'sofia@email.com',  vehicle:'Toyota Vios 1.3L',    plate:'ABC-1234', pickup:'Apr 16, 2026', ret:'Apr 20, 2026', days:4,  amount:3200, status:'active' },
  { id:'BK-0080', customer:'Mark Navarro',    email:'mark@email.com',   vehicle:'Suzuki Swift',        plate:'PQR-3344', pickup:'Apr 9, 2026',  ret:'Apr 11, 2026', days:2,  amount:1600, status:'done' },
  { id:'BK-0079', customer:'Lea Cunanan',     email:'lea@email.com',    vehicle:'Honda City RS',       plate:'XYZ-5678', pickup:'Apr 12, 2026', ret:'Apr 15, 2026', days:3,  amount:1800, status:'pending' },
  { id:'BK-0078', customer:'Dan Pascual',     email:'dan@email.com',    vehicle:'Hyundai Accent',      plate:'GHI-3456', pickup:'Apr 4, 2026',  ret:'Apr 7, 2026',  days:3,  amount:2100, status:'active' },
  { id:'BK-0077', customer:'Trish Gomez',     email:'trish@email.com',  vehicle:'Mitsubishi Mirage',   plate:'DEF-9012', pickup:'Apr 7, 2026',  ret:'Apr 9, 2026',  days:2,  amount:1600, status:'active' },
];

let currentFilter = 'all';
let currentSearch = '';
let nextId = 76;

/* ════ RENDER ════ */
function initials(name) { return name.split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase(); }
function avatarBg(name) { const i = name.charCodeAt(0) % AVATARS.length; return AVATARS[i]; }

function statusBadge(s) {
  const map = {
    active:   ['active',  'Active'],
    pending:  ['pending', 'Pending'],
    done:     ['done',    'Completed'],
    canceled: ['canceled','Canceled'],
  };
  const [cls, label] = map[s] || ['pending','Unknown'];
  return `<span class="badge ${cls}"><span class="badge-dot"></span>${label}</span>`;
}

function amountColor(s) {
  return s === 'active' ? 'var(--green)' : s === 'pending' ? 'var(--gold)' : s === 'done' ? 'var(--blue)' : 'var(--muted)';
}

function renderTable() {
  let data = bookings.filter(b => {
    const matchFilter = currentFilter === 'all' || b.status === currentFilter;
    const q = currentSearch.toLowerCase();
    const matchSearch = !q ||
      b.id.toLowerCase().includes(q) ||
      b.customer.toLowerCase().includes(q) ||
      b.vehicle.toLowerCase().includes(q) ||
      b.plate.toLowerCase().includes(q) ||
      b.email.toLowerCase().includes(q);
    return matchFilter && matchSearch;
  });

  const tbody = document.getElementById('tableBody');
  const empty = document.getElementById('emptyState');
  const rc    = document.getElementById('resultsCount');
  const tfi   = document.getElementById('tfInfo');

  if (data.length === 0) {
    tbody.innerHTML = '';
    empty.classList.add('show');
    rc.innerHTML = '<strong>0</strong> bookings found';
    tfi.innerHTML = 'No results';
    return;
  }
  empty.classList.remove('show');
  rc.innerHTML = `<strong>${data.length}</strong> booking${data.length!==1?'s':''} found`;
  tfi.innerHTML = `Showing <strong>1–${Math.min(10,data.length)}</strong> of <strong>${data.length}</strong> booking${data.length!==1?'s':''}`;

  tbody.innerHTML = data.map((b, idx) => `
    <tr data-id="${b.id}">
      <td><div class="cb-wrap"><input type="checkbox" class="cb row-cb"></div></td>
      <td><span class="bid">#${b.id}</span></td>
      <td>
        <div class="customer-cell">
          <div class="cust-avatar" style="background:${avatarBg(b.customer)}">${initials(b.customer)}</div>
          <div>
            <div class="cust-name">${b.customer}</div>
            <div class="cust-email">${b.email}</div>
          </div>
        </div>
      </td>
      <td>
        <div class="car-name">${b.vehicle}</div>
        <div class="car-type">Sedan</div>
      </td>
      <td><span class="plate"><svg width="11" height="11" viewBox="0 0 11 11" fill="none" color="#6A6E75"><rect x="1" y="2.5" width="9" height="6" rx="1" stroke="currentColor" stroke-width="1.1"/></svg>${b.plate}</span></td>
      <td>
        <div class="date-main">${b.pickup}</div>
        <div class="date-day">Pickup</div>
      </td>
      <td>
        <div class="date-main">${b.ret}</div>
        <div class="date-day">Return</div>
      </td>
      <td><span class="duration-pill"><svg width="11" height="11" viewBox="0 0 11 11" fill="none" color="currentColor"><circle cx="5.5" cy="5.5" r="4" stroke="currentColor" stroke-width="1.1"/><path d="M5.5 3.5v2l1.5 1" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/></svg>${b.days} day${b.days!==1?'s':''}</span></td>
      <td><span class="amount" style="color:${amountColor(b.status)}">₱${b.amount > 0 ? b.amount.toLocaleString() : '—'}</span></td>
      <td>${statusBadge(b.status)}</td>
      <td>
        <div class="actions-cell" style="justify-content:center">
          <div class="act-btn view" title="View" onclick="viewBooking('${b.id}',event)">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M1 6.5s2.5-4 5.5-4 5.5 4 5.5 4-2.5 4-5.5 4-5.5-4-5.5-4z" stroke="currentColor" stroke-width="1.2"/><circle cx="6.5" cy="6.5" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
          </div>
          <div class="act-btn edit" title="Edit" onclick="editBooking('${b.id}',event)">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 9.5L9.5 2l1.5 1.5-7.5 7.5H2V9.5z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div class="act-btn del" title="Delete" onclick="deleteBooking('${b.id}',event)">
            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 3.5h9M5 3.5V2h3v1.5M10 3.5l-.7 7.5H3.7L3 3.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
        </div>
      </td>
    </tr>
  `).join('');
}

/* ════ FILTER ════ */
function setFilter(val, btn) {
  currentFilter = val;
  document.querySelectorAll('.ftab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  renderTable();
}
function filterTable() {
  currentSearch = document.getElementById('searchInput').value;
  renderTable();
}

/* ════ ACTIONS ════ */
function viewBooking(id, e) {
  e.stopPropagation();
  showToast('Viewing booking #' + id, 'success');
}
function editBooking(id, e) {
  e.stopPropagation();
  showToast('Edit mode for #' + id + ' (coming soon)', 'success');
}
function deleteBooking(id, e) {
  e.stopPropagation();
  bookings = bookings.filter(b => b.id !== id);
  renderTable();
  showToast('Booking #' + id + ' removed.', 'error');
}

function toggleAll(cb) {
  document.querySelectorAll('.row-cb').forEach(c => c.checked = cb.checked);
}

/* ════ MODAL ════ */
function openModal() {
  document.getElementById('modalOverlay').classList.add('show');
  document.body.style.overflow = 'hidden';
}
function closeModal() {
  document.getElementById('modalOverlay').classList.remove('show');
  document.body.style.overflow = '';
}
function closeModalOutside(e) {
  if (e.target === document.getElementById('modalOverlay')) closeModal();
}

function addBooking() {
  const customer = document.getElementById('f-customer').value.trim();
  const vehicle  = document.getElementById('f-vehicle').value;
  const plate    = document.getElementById('f-plate').value.trim();
  const pickup   = document.getElementById('f-pickup').value;
  const ret      = document.getElementById('f-return').value;
  const amount   = parseFloat(document.getElementById('f-amount').value) || 0;
  const status   = document.getElementById('f-status').value;
  const email    = document.getElementById('f-email').value.trim();

  if (!customer || !vehicle || !plate || !pickup || !ret) {
    showToast('Please fill in all required fields.'); return;
  }

  const pDate = new Date(pickup), rDate = new Date(ret);
  const days  = Math.max(1, Math.round((rDate - pDate) / 86400000));

  const fmt = d => { const [y,m,dy] = d.split('-'); const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; return `${months[+m-1]} ${+dy}, ${y}`; };

  const id = `BK-00${nextId--}`;
  bookings.unshift({ id, customer, email: email||'—', vehicle, plate, pickup: fmt(pickup), ret: fmt(ret), days, amount, status });

  closeModal();
  ['f-customer','f-email','f-vehicle','f-plate','f-pickup','f-return','f-amount','f-notes'].forEach(id => {
    const el = document.getElementById(id);
    if (el.tagName === 'SELECT') el.selectedIndex = 0;
    else el.value = '';
  });
  currentFilter = 'all';
  document.querySelectorAll('.ftab').forEach((t,i) => t.classList.toggle('active', i===0));
  renderTable();
  showToast(`Booking #${id} created successfully!`, 'success');
}

/* ════ EXPORT ════ */
function exportCSV() {
  const headers = ['Booking ID','Customer','Email','Vehicle','Plate','Pickup','Return','Days','Amount','Status'];
  const rows = bookings.map(b => [b.id, b.customer, b.email, b.vehicle, b.plate, b.pickup, b.ret, b.days, b.amount, b.status]);
  const csv = [headers, ...rows].map(r => r.join(',')).join('\n');
  const blob = new Blob([csv], {type:'text/csv'});
  const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'REVV_Bookings.csv'; a.click();
  showToast('Exported as REVV_Bookings.csv', 'success');
}

/* ════ SORT ════ */
let sortDir = {};
function sortTable(col) {
  sortDir[col] = !sortDir[col];
  const dir = sortDir[col] ? 1 : -1;
  bookings.sort((a, b) => {
    let av = a[col === 'id' ? 'id' : col === 'customer' ? 'customer' : col === 'vehicle' ? 'vehicle' : col === 'pickup' ? 'pickup' : col === 'amount' ? 'amount' : 'id'];
    let bv = b[col === 'id' ? 'id' : col === 'customer' ? 'customer' : col === 'vehicle' ? 'vehicle' : col === 'pickup' ? 'pickup' : col === 'amount' ? 'amount' : 'id'];
    if (typeof av === 'number') return (av - bv) * dir;
    return av.localeCompare(bv) * dir;
  });
  renderTable();
}

/* ════ TOAST ════ */
function showToast(msg, type = 'error') {
  const t = document.getElementById('toast');
  const tm = document.getElementById('toastMsg');
  const ti = document.getElementById('toastIcon');
  tm.textContent = msg;
  const c = type === 'success' ? '#3DBE7A' : '#E8341A';
  t.style.borderLeftColor = c;
  ti.innerHTML = type === 'success'
    ? `<circle cx="7.5" cy="7.5" r="6" stroke="${c}" stroke-width="1.3"/><path d="M5 7.5l2 2 3.5-3.5" stroke="${c}" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>`
    : `<circle cx="7.5" cy="7.5" r="6" stroke="${c}" stroke-width="1.3"/><path d="M7.5 5v3" stroke="${c}" stroke-width="1.5" stroke-linecap="round"/><circle cx="7.5" cy="10" r="0.7" fill="${c}"/>`;
  void t.offsetWidth;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3400);
}

// Init
renderTable();