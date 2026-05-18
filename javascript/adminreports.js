/* ════ DATA ════ */
const monthData = [
  { month:'Jan', revenue:42500, rentals:4, color:'#3D8FBE' },
  { month:'Feb', revenue:58200, rentals:5, color:'#3D8FBE' },
  { month:'Mar', revenue:97400, rentals:6, color:'#E8341A' },
  { month:'Apr', revenue:75500, rentals:3, color:'#D4A843' }, // partial
];
const maxRev = Math.max(...monthData.map(m=>m.revenue));

const fleetMix = [
  { type:'SUV',       count:6, color:'#D4A843' },
  { type:'Sedan',     count:5, color:'#3D8FBE' },
  { type:'Van',       count:2, color:'#9A3DBE' },
  { type:'Pickup',    count:1, color:'#3DBE7A' },
  { type:'Hatchback', count:2, color:'#6A6E75' },
];
const totalMix = fleetMix.reduce((s,d)=>s+d.count,0);

const topVehicles = [
  { name:'Mercedes GLE', plate:'MMM 3344 / FFF 6789', revenue:133000, max:133000 },
  { name:'Toyota Fortuner', plate:'DDD 4567 / LLL 2233', revenue:50000, max:133000 },
  { name:'Toyota HiAce', plate:'JJJ 0123 / NNN 4455', revenue:38500, max:133000 },
  { name:'Ford Everest', plate:'BBB 2345', revenue:22500, max:133000 },
  { name:'Ford Ranger', plate:'PPP 6677', revenue:16800, max:133000 },
];

const statusData = [
  { label:'Ongoing',   val:4,  color:'var(--blue)',  bar:'#3D8FBE' },
  { label:'Reserved',  val:3,  color:'var(--gold)',  bar:'#D4A843' },
  { label:'Completed', val:7,  color:'var(--green)', bar:'#3DBE7A' },
  { label:'Overdue',   val:2,  color:'var(--red)',   bar:'#E8341A' },
];

const insights = [
  { icon:'📈', iconBg:'var(--green-dim)', iconBdr:'rgba(61,190,122,0.2)', title:'Revenue Peaking in March', desc:'March 2026 saw ₱97,400 in revenue — the highest month this quarter, driven by high-value SUV and van bookings.' },
  { icon:'⚠️', iconBg:'var(--red-dim)',   iconBdr:'rgba(232,52,26,0.2)',  title:'2 Overdue Rentals Require Action', desc:'RNT-012 (Maria Santos) and RNT-016 (Jomar Ocampo) are overdue with ₱36,800 in pending balances.' },
  { icon:'🚗', iconBg:'var(--blue-dim)',  iconBdr:'rgba(61,143,190,0.2)', title:'SUVs Are Your Top Earner', desc:'SUV rentals account for 6 of 16 total rentals and the majority of revenue. Consider expanding the SUV fleet.' },
  { icon:'🔁', iconBg:'var(--gold-dim)',  iconBdr:'rgba(212,168,67,0.2)', title:'Strong Repeat Customer Rate', desc:'44% of rentals this quarter came from returning customers — Maria Santos and Jose Reyes each booked twice.' },
];

const periodRanges = {
  week:    'Apr 6 – Apr 12, 2026',
  month:   'Apr 1 – Apr 12, 2026',
  quarter: 'Jan 1 – Apr 12, 2026',
  year:    'Jan 1 – Apr 12, 2026',
};

/* ════ RENDER: REVENUE BAR CHART ════ */
function renderRevenueChart() {
  const el = document.getElementById('revenueChart');
  el.innerHTML = monthData.map(m => {
    const pct = (m.revenue / maxRev * 100).toFixed(1);
    const isMax = m.revenue === maxRev;
    const fillColor = isMax ? 'linear-gradient(90deg,var(--red),var(--orange))' : 'linear-gradient(90deg,rgba(61,143,190,0.6),rgba(61,143,190,0.9))';
    return `
      <div class="bar-row">
        <div class="bar-month">${m.month}</div>
        <div class="bar-track">
          <div class="bar-fill" style="width:${pct}%;background:${fillColor};animation:growBar 0.8s ease forwards">
            <span class="bar-amount">${m.rentals} rentals</span>
          </div>
        </div>
        <div class="bar-val">₱${(m.revenue/1000).toFixed(0)}k</div>
      </div>`;
  }).join('');
}

/* ════ RENDER: DONUT ════ */
function renderDonut() {
  const cx = 80, cy = 80, r = 60, strokeW = 18;
  const circumference = 2 * Math.PI * r;
  let offset = 0;
  let paths = '';
  fleetMix.forEach(d => {
    const pct = d.count / totalMix;
    const dash = pct * circumference;
    const gap  = circumference - dash;
    paths += `<circle cx="${cx}" cy="${cy}" r="${r}" fill="none" stroke="${d.color}" stroke-width="${strokeW}" stroke-dasharray="${dash} ${gap}" stroke-dashoffset="${-offset}" stroke-linecap="butt" style="transition:stroke-dashoffset 0.8s ease"/>`;
    offset += dash;
  });
  // Center text
  paths += `<text x="${cx}" y="${cy-6}" text-anchor="middle" font-family="'Bebas Neue',sans-serif" font-size="22" fill="#F2F0EC" letter-spacing="1">${totalMix}</text>`;
  paths += `<text x="${cx}" y="${cy+12}" text-anchor="middle" font-family="'Barlow Condensed',sans-serif" font-size="10" fill="#6A6E75" letter-spacing="2" text-transform="uppercase">TOTAL</text>`;

  document.getElementById('donutSVG').innerHTML = `<g transform="rotate(-90 ${cx} ${cy})">${paths.split('<text').map((p,i)=>i===0?p:'<text'+p).join('')}</g>`.replace(/<g[^>]*>([\s\S]*)<\/g>/,'') + paths;

  // Correct approach:
  const svgEl = document.getElementById('donutSVG');
  svgEl.innerHTML = '';
  const g = document.createElementNS('http://www.w3.org/2000/svg','g');
  g.setAttribute('transform',`rotate(-90 ${cx} ${cy})`);
  offset = 0;
  fleetMix.forEach(d => {
    const pct = d.count / totalMix;
    const dash = pct * circumference;
    const gap  = circumference - dash;
    const circ = document.createElementNS('http://www.w3.org/2000/svg','circle');
    circ.setAttribute('cx',cx); circ.setAttribute('cy',cy); circ.setAttribute('r',r);
    circ.setAttribute('fill','none'); circ.setAttribute('stroke',d.color);
    circ.setAttribute('stroke-width',strokeW);
    circ.setAttribute('stroke-dasharray',`${dash} ${gap}`);
    circ.setAttribute('stroke-dashoffset',-offset);
    g.appendChild(circ);
    offset += dash;
  });
  svgEl.appendChild(g);

  // Center label
  const t1 = document.createElementNS('http://www.w3.org/2000/svg','text');
  t1.setAttribute('x',cx); t1.setAttribute('y',cy+5);
  t1.setAttribute('text-anchor','middle');
  t1.setAttribute('font-family',"'Bebas Neue',sans-serif");
  t1.setAttribute('font-size','26'); t1.setAttribute('fill','#F2F0EC');
  t1.setAttribute('letter-spacing','1'); t1.textContent = totalMix;
  svgEl.appendChild(t1);
  const t2 = document.createElementNS('http://www.w3.org/2000/svg','text');
  t2.setAttribute('x',cx); t2.setAttribute('y',cy+20);
  t2.setAttribute('text-anchor','middle');
  t2.setAttribute('font-family',"'Barlow Condensed',sans-serif");
  t2.setAttribute('font-size','9'); t2.setAttribute('fill','#6A6E75');
  t2.setAttribute('letter-spacing','2'); t2.textContent = 'RENTALS';
  svgEl.appendChild(t2);

  // Legend
  document.getElementById('donutLegend').innerHTML = fleetMix.map(d=>`
    <div class="legend-row">
      <div class="legend-dot-label">
        <div class="legend-dot" style="background:${d.color}"></div>
        <span class="legend-label">${d.type}</span>
      </div>
      <div class="legend-val">${d.count} <span style="color:var(--muted);font-weight:400">(${Math.round(d.count/totalMix*100)}%)</span></div>
    </div>`).join('');
}

/* ════ RENDER: TOP VEHICLES ════ */
function renderTopVehicles() {
  document.getElementById('topVehiclesBody').innerHTML = topVehicles.map((v,i)=>{
    const pct = (v.revenue/v.max*100).toFixed(0);
    const isTop = i === 0;
    const barColor = isTop ? 'linear-gradient(90deg,var(--red),var(--orange))' : 'linear-gradient(90deg,rgba(61,143,190,0.5),rgba(61,143,190,0.8))';
    return `
      <tr>
        <td><div class="rank-num${isTop?' top':''}">${i+1}</div></td>
        <td>
          <div class="rank-name">${v.name}</div>
          <div class="rank-sub">${v.plate}</div>
          <div class="rank-bar-track"><div class="rank-bar-fill" style="width:${pct}%;background:${barColor}"></div></div>
        </td>
        <td><div class="rank-val" style="color:${isTop?'var(--green)':'var(--white)'}">₱${v.revenue.toLocaleString()}</div></td>
      </tr>`;
  }).join('');
}

/* ════ RENDER: STATUS GRID ════ */
function renderStatusGrid() {
  document.getElementById('statusGrid').innerHTML = statusData.map(s=>`
    <div class="status-tile">
      <div class="status-tile-val" style="color:${s.color}">${s.val}</div>
      <div class="status-tile-lab">${s.label}</div>
      <div class="status-tile-bar" style="background:${s.bar};width:${Math.round(s.val/16*100)}%"></div>
    </div>`).join('');
}

/* ════ RENDER: INSIGHTS ════ */
function renderInsights() {
  document.getElementById('insightList').innerHTML = insights.map(i=>`
    <div class="insight-item">
      <div class="insight-icon" style="background:${i.iconBg};border:1px solid ${i.iconBdr}">${i.icon}</div>
      <div>
        <div class="insight-title">${i.title}</div>
        <div class="insight-desc">${i.desc}</div>
      </div>
    </div>`).join('');
}

/* ════ PERIOD TABS ════ */
function setPeriod(p, btn) {
  document.querySelectorAll('.ptab').forEach(t=>t.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('periodRange').textContent = periodRanges[p];
  showToast('Report period updated to: '+btn.textContent);
}

/* ════ EXPORT / PRINT ════ */
function exportReport() { showToast('Exporting report as PDF…'); }
function printReport()  { window.print(); }

/* ════ TOAST ════ */
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toastMsg').textContent = msg;
  t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'), 3200);
}

/* ════ INIT ════ */
renderRevenueChart();
renderDonut();
renderTopVehicles();
renderStatusGrid();
renderInsights();