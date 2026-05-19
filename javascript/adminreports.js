/* ════ DATA ════ */
const REPORT_API = '/rent/php/report_action.php';
let monthData = [];
let fleetMix = [];
let topVehicles = [];
let statusData = [];
let insights = [];
let totalMix = 0;
let reportStats = {
  days: '—',
  utilization: '—',
  repeat: '—',
};
let currentKpis = {
  revenue: 0,
  rentals: 0,
  avg: 0,
  overdue: 0,
  revenueDelta: '',
  rentalsDelta: '',
  avgDelta: '',
  overdueDelta: '',
};

const periodRanges = {
  week:    'Apr 6 – Apr 12, 2026',
  month:   'Apr 1 – Apr 12, 2026',
  quarter: 'Jan 1 – Apr 12, 2026',
  year:    'Jan 1 – Apr 12, 2026',
};

async function loadReport() {
  try {
    const response = await fetch(REPORT_API);
    const result = await response.json();
    if (!response.ok || result.error) {
      throw new Error(result.error || 'Unable to load report data.');
    }

    currentKpis = {
      revenue: result.kpis.revenue || 0,
      rentals: result.kpis.rentals || 0,
      avg: result.kpis.avg || 0,
      overdue: result.kpis.overdue || 0,
    };
    monthData = Array.isArray(result.revenueByMonth) ? result.revenueByMonth : [];
    fleetMix = Array.isArray(result.fleetMix) ? result.fleetMix : [];
    totalMix = result.totalMix || fleetMix.reduce((sum, item) => sum + (item.count || 0), 0);
    topVehicles = Array.isArray(result.topVehicles) ? result.topVehicles : [];
    statusData = Array.isArray(result.statusData) ? result.statusData : [];
    insights = Array.isArray(result.insights) ? result.insights : [];

    updateKpis();
    renderMetricCards();
    renderRevenueChart();
    renderDonut();
    renderTopVehicles();
    renderStatusGrid();
    renderInsights();
  } catch (err) {
    console.warn('Report API load failed:', err);
    showToast('Unable to load report data from the server.','error');
    renderMetricCards();
    renderRevenueChart();
    renderDonut();
    renderTopVehicles();
    renderStatusGrid();
    renderInsights();
  }
}

function updateKpis() {
  document.getElementById('kpi-revenue').textContent = '₱' + Number(currentKpis.revenue).toLocaleString();
  document.getElementById('kpi-rentals').textContent = Number(currentKpis.rentals).toLocaleString();
  document.getElementById('kpi-avg').textContent = '₱' + Number(currentKpis.avg).toLocaleString();
  document.getElementById('kpi-overdue').textContent = '₱' + Number(currentKpis.overdue).toLocaleString();
  document.getElementById('kpi-revenue-delta').textContent = currentKpis.revenueDelta;
  document.getElementById('kpi-rentals-delta').textContent = currentKpis.rentalsDelta;
  document.getElementById('kpi-avg-delta').textContent = currentKpis.avgDelta;
  document.getElementById('kpi-overdue-delta').textContent = currentKpis.overdueDelta;
}

function renderMetricCards() {
  document.getElementById('metric-days').textContent = reportStats.days;
  document.getElementById('metric-utilization').textContent = reportStats.utilization;
  document.getElementById('metric-repeat').textContent = reportStats.repeat;
}

/* ════ RENDER: REVENUE BAR CHART ════ */
function renderRevenueChart() {
  const el = document.getElementById('revenueChart');
  if (!monthData.length) {
    el.innerHTML = '<div class="empty-state">No revenue data available for the selected period.</div>';
    return;
  }

  const maxRev = Math.max(...monthData.map(m => m.revenue), 1);
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
        <div class="bar-val">₱${(m.revenue / 1000).toFixed(0)}k</div>
      </div>`;
  }).join('');
}

/* ════ RENDER: DONUT ════ */
function renderDonut() {
  const cx = 80;
  const cy = 80;
  const r = 60;
  const strokeW = 18;
  const circumference = 2 * Math.PI * r;
  const svgEl = document.getElementById('donutSVG');
  svgEl.innerHTML = '';

  if (!totalMix) {
    document.getElementById('donutLegend').innerHTML = '<div class="legend-empty">No fleet mix data available.</div>';
    return;
  }

  let offset = 0;
  fleetMix.forEach(d => {
    const pct = totalMix > 0 ? d.count / totalMix : 0;
    const dash = pct * circumference;
    const gap = circumference - dash;
    const circ = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
    circ.setAttribute('cx', cx);
    circ.setAttribute('cy', cy);
    circ.setAttribute('r', r);
    circ.setAttribute('fill', 'none');
    circ.setAttribute('stroke', d.color);
    circ.setAttribute('stroke-width', strokeW.toString());
    circ.setAttribute('stroke-dasharray', `${dash} ${gap}`);
    circ.setAttribute('stroke-dashoffset', (-offset).toString());
    circ.setAttribute('stroke-linecap', 'butt');
    circ.setAttribute('transform', `rotate(-90 ${cx} ${cy})`);
    svgEl.appendChild(circ);
    offset += dash;
  });

  const label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
  label.setAttribute('x', cx.toString());
  label.setAttribute('y', (cy + 5).toString());
  label.setAttribute('text-anchor', 'middle');
  label.setAttribute('font-family', "'Bebas Neue',sans-serif");
  label.setAttribute('font-size', '26');
  label.setAttribute('fill', '#F2F0EC');
  label.setAttribute('letter-spacing', '1');
  label.textContent = totalMix.toString();
  svgEl.appendChild(label);

  const sublabel = document.createElementNS('http://www.w3.org/2000/svg', 'text');
  sublabel.setAttribute('x', cx.toString());
  sublabel.setAttribute('y', (cy + 20).toString());
  sublabel.setAttribute('text-anchor', 'middle');
  sublabel.setAttribute('font-family', "'Barlow Condensed',sans-serif");
  sublabel.setAttribute('font-size', '9');
  sublabel.setAttribute('fill', '#6A6E75');
  sublabel.setAttribute('letter-spacing', '2');
  sublabel.textContent = 'RENTALS';
  svgEl.appendChild(sublabel);

  document.getElementById('donutLegend').innerHTML = fleetMix.map(d => `
    <div class="legend-row">
      <div class="legend-dot-label">
        <div class="legend-dot" style="background:${d.color}"></div>
        <span class="legend-label">${d.type}</span>
      </div>
      <div class="legend-val">${d.count} <span style="color:var(--muted);font-weight:400">(${totalMix > 0 ? Math.round(d.count / totalMix * 100) : 0}%)</span></div>
    </div>`).join('');
}

/* ════ RENDER: TOP VEHICLES ════ */
function renderTopVehicles() {
  const body = document.getElementById('topVehiclesBody');
  if (!topVehicles.length) {
    body.innerHTML = '<tr><td colspan="3" class="empty-row">No vehicle revenue data available.</td></tr>';
    return;
  }

  const maxRevenue = Math.max(...topVehicles.map(v => v.revenue), 1);
  body.innerHTML = topVehicles.map((v,i)=>{
    const pct = (v.revenue / maxRevenue * 100).toFixed(0);
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
        <td><div class="rank-val" style="color:${isTop?'var(--green)':'var(--white)'}">₱${Number(v.revenue).toLocaleString()}</div></td>
      </tr>`;
  }).join('');
}

/* ════ RENDER: STATUS GRID ════ */
function renderStatusGrid() {
  const grid = document.getElementById('statusGrid');
  if (!statusData.length) {
    grid.innerHTML = '<div class="status-empty">No rental status data available.</div>';
    return;
  }

  grid.innerHTML = statusData.map(s=>`
    <div class="status-tile">
      <div class="status-tile-val" style="color:${s.color}">${s.val}</div>
      <div class="status-tile-lab">${s.label}</div>
      <div class="status-tile-bar" style="background:${s.bar};width:${Math.round(s.val/16*100)}%"></div>
    </div>`).join('');
}

/* ════ RENDER: INSIGHTS ════ */
function renderInsights() {
  const list = document.getElementById('insightList');
  if (!insights.length) {
    list.innerHTML = '<div class="insight-empty">Insufficient data to generate insights.</div>';
    return;
  }

  list.innerHTML = insights.map(i=>`
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
loadReport();