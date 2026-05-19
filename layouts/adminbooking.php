<?php
require_once __DIR__ . '/../databases/connection1.php';

$bookings = [];
$bookingSummary = [
  'total' => 0,
  'active' => 0,
  'pending' => 0,
  'done' => 0,
  'overdue' => 0,
  'canceled' => 0,
];

function ensureBookingSchema(PDO $conn): void {
    $alterStatements = [
        "ALTER TABLE bookings ADD COLUMN customer_ref VARCHAR(32) DEFAULT NULL",
        "ALTER TABLE bookings ADD COLUMN vehicle_type VARCHAR(80) DEFAULT NULL",
        "ALTER TABLE bookings ADD COLUMN driver_type VARCHAR(50) DEFAULT NULL",
        "ALTER TABLE bookings ADD COLUMN location VARCHAR(255) DEFAULT NULL",
        "ALTER TABLE bookings ADD COLUMN rate DECIMAL(10,2) NOT NULL DEFAULT 0.00",
        "ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','active','done','canceled','overdue') NOT NULL DEFAULT 'pending'"
    ];

    foreach ($alterStatements as $sql) {
        try {
            $conn->exec($sql);
        } catch (PDOException $e) {
            // ignore if the column already exists or if the modification is not applicable
        }
    }
}

try {
    ensureBookingSchema($conn);

    $stmt = $conn->prepare(
        'SELECT
            b.booking_ref AS id,
            b.customer_ref,
            b.vehicle_type,
            b.driver_type,
            b.location,
            b.rate,
            b.pickup_date,
            b.return_date,
            b.days,
            b.amount,
            b.status,
            c.first_name AS cust_first_name,
            c.last_name AS cust_last_name,
            c.email AS cust_email,
            v.make AS vehicle_make,
            v.model AS vehicle_model,
            v.plate_no
         FROM bookings b
         LEFT JOIN customers c ON c.id = b.customer_id
         LEFT JOIN vehicles v ON v.id = b.vehicle_id
         ORDER BY b.created_at DESC'
    );
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $customerName = trim(($row['cust_first_name'] ?? '') . ' ' . ($row['cust_last_name'] ?? '')); 
        $vehicleName = trim(($row['vehicle_make'] ?? '') . ' ' . ($row['vehicle_model'] ?? ''));

        $pickup = $row['pickup_date'] ? (new DateTime($row['pickup_date']))->format('M j, Y') : '—';
        $return = $row['return_date'] ? (new DateTime($row['return_date']))->format('M j, Y') : '—';

        $status = $row['status'] ?: 'pending';

        $bookings[] = [
            'id' => $row['id'] ?? 'BK-0000',
            'customer' => $customerName !== '' ? $customerName : 'Guest Customer',
            'customer_ref' => $row['customer_ref'] ?: '—',
            'email' => $row['cust_email'] ?: '—',
            'vehicle' => $vehicleName !== '' ? $vehicleName : ($row['plate_no'] ? 'Fleet Vehicle' : 'Unknown'),
            'vehicle_type' => $row['vehicle_type'] ?: 'Standard',
            'driver_type' => $row['driver_type'] ?: 'Self-drive',
            'location' => $row['location'] ?: '—',
            'rate' => (float) $row['rate'],
            'plate' => $row['plate_no'] ?: '—',
            'pickup' => $pickup,
            'ret' => $return,
            'days' => (int) $row['days'],
            'amount' => (float) $row['amount'],
            'status' => $status,
        ];

        $bookingSummary['total']++;
        if (isset($bookingSummary[$status])) {
            $bookingSummary[$status]++;
        }
    }
} catch (PDOException $e) {
    $bookings = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>REVV — Booking List</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --black:    #080A0C;
    --dark:     #0E1115;
    --panel:    #13171D;
    --card:     #181D24;
    --card2:    #1E242D;
    --border:   rgba(255,255,255,0.07);
    --border2:  rgba(255,255,255,0.13);
    --red:      #E8341A;
    --red-dim:  rgba(232,52,26,0.14);
    --red-glow: rgba(232,52,26,0.32);
    --orange:   #F5642A;
    --gold:     #D4A843;
    --gold-dim: rgba(212,168,67,0.12);
    --white:    #F2F0EC;
    --muted:    #6A6E75;
    --muted2:   #9A9DA4;
    --green:    #3DBE7A;
    --green-dim:rgba(61,190,122,0.12);
    --blue:     #3D8FBE;
    --blue-dim: rgba(61,143,190,0.12);
  }

  html, body { height: 100%; background: var(--black); color: var(--white); font-family: 'Barlow', sans-serif; overflow-x: hidden; }

  /* ══ LAYOUT ══ */
  .app { display: flex; min-height: 100vh; }

  /* ══ SIDEBAR ══ */
  .sidebar {
    width: 240px;
    flex-shrink: 0;
    background: var(--dark);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; left: 0; bottom: 0;
    z-index: 100; /* above overlay */
    transition: transform 0.28s cubic-bezier(0.4,0,0.2,1);
  }

  /* ══ SIDEBAR OVERLAY ══ */
  .sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    z-index: 99; /* just below sidebar */
    opacity: 0;
    transition: opacity 0.28s ease;
    backdrop-filter: blur(2px);
  }
  .sidebar-overlay.active {
    display: block;
    opacity: 1;
  }

  .sidebar-logo {
    padding: 28px 24px 20px;
    display: flex; align-items: center; gap: 11px;
    border-bottom: 1px solid var(--border);
    text-decoration: none;
    flex-shrink: 0;
  }
  .logo-hex {
    width: 36px; height: 36px; background: var(--red);
    clip-path: polygon(0 0,80% 0,100% 50%,80% 100%,0 100%,20% 50%);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  }
  .logo-wordmark { font-family: 'Bebas Neue', sans-serif; font-size: 28px; letter-spacing: 3px; color: var(--white); line-height: 1; }

  .sidebar-section { padding: 20px 12px 8px; }
  .sidebar-section-label { font-size: 9.5px; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); font-weight: 500; padding: 0 12px; margin-bottom: 6px; }

  .nav-item {
    display: flex; align-items: center; gap: 11px;
    padding: 10px 12px; border-radius: 3px; cursor: pointer;
    color: var(--muted2); font-size: 14px; font-weight: 400;
    text-decoration: none; transition: all 0.18s; position: relative; margin-bottom: 1px;
  }
  .nav-item:hover { background: rgba(255,255,255,0.04); color: var(--white); }
  .nav-item.active { background: var(--red-dim); color: var(--white); font-weight: 500; }
  .nav-item.active::before {
    content: ''; position: absolute; left: 0; top: 4px; bottom: 4px;
    width: 3px; background: var(--red); border-radius: 0 3px 3px 0;
  }
  .nav-icon { opacity: 0.55; flex-shrink: 0; }
  .nav-item.active .nav-icon, .nav-item:hover .nav-icon { opacity: 1; }
  .nav-badge { margin-left: auto; background: var(--red); color: white; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 10px; font-family: 'Barlow Condensed', sans-serif; }

  .sidebar-bottom { margin-top: auto; border-top: 1px solid var(--border); padding: 16px 12px; flex-shrink: 0; }
  .user-card { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 3px; cursor: pointer; transition: background 0.18s; }
  .user-card:hover { background: rgba(255,255,255,0.04); }
  .user-avatar { width: 34px; height: 34px; background: linear-gradient(135deg,var(--red),var(--orange)); border-radius: 3px; display: flex; align-items: center; justify-content: center; font-family: 'Bebas Neue', sans-serif; font-size: 16px; flex-shrink: 0; }
  .user-name { font-size: 13px; font-weight: 500; color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .user-role { font-size: 11px; color: var(--muted); }

  /* ══ MAIN ══ */
  .main { flex: 1; margin-left: 240px; display: flex; flex-direction: column; min-height: 100vh; transition: margin-left 0.28s cubic-bezier(0.4,0,0.2,1); }

  /* ══ TOPBAR ══ */
  .topbar {
    height: 64px; background: var(--dark); border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 16px; padding: 0 32px;
    position: sticky; top: 0; z-index: 40;
  }

  /* Hamburger — hidden on desktop, shown on mobile via CSS */
  .menu-toggle {
    display: none;
    width: 38px; height: 38px;
    background: var(--card); border: 1px solid var(--border); border-radius: 3px;
    cursor: pointer; align-items: center; justify-content: center;
    flex-shrink: 0; transition: border-color 0.2s, background 0.2s;
  }
  .menu-toggle:hover { border-color: var(--border2); background: var(--card2); }

  .topbar-title { font-family: 'Bebas Neue', sans-serif; font-size: 22px; letter-spacing: 3px; color: var(--white); white-space: nowrap; }
  .topbar-divider { width: 1px; height: 22px; background: var(--border2); flex-shrink: 0; }
  .search-wrap { flex: 1; max-width: 360px; position: relative; }
  .search-icon { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); opacity: 0.35; pointer-events: none; }
  .search-input { width: 100%; background: var(--card); border: 1px solid var(--border); border-radius: 3px; padding: 9px 14px 9px 40px; color: var(--white); font-family: 'Barlow', sans-serif; font-size: 13.5px; outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
  .search-input::placeholder { color: var(--muted); }
  .search-input:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(232,52,26,0.09); }
  .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 12px; }
  .topbar-date { font-size: 12px; color: var(--muted2); display: flex; align-items: center; gap: 7px; }
  .icon-btn { width: 38px; height: 38px; background: var(--card); border: 1px solid var(--border); border-radius: 3px; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative; transition: border-color 0.2s; }
  .icon-btn:hover { border-color: var(--border2); }
  .notif-dot { position: absolute; top: 6px; right: 6px; width: 7px; height: 7px; background: var(--red); border-radius: 50%; border: 2px solid var(--dark); animation: pulse 2s ease-in-out infinite; }
  @keyframes pulse { 0%,100%{transform:scale(1)}50%{transform:scale(1.3)} }

  /* ══ CONTENT ══ */
  .content {
    flex: 1; padding: 32px;
    background: var(--black);
    background-image: radial-gradient(ellipse 60% 40% at 0% 0%,rgba(232,52,26,0.04) 0%,transparent 60%),
                      radial-gradient(ellipse 50% 40% at 100% 100%,rgba(212,168,67,0.03) 0%,transparent 60%);
  }

  .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 14px; }
  .page-eyebrow { font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: var(--red); font-weight: 500; }
  .page-title { font-family: 'Bebas Neue', sans-serif; font-size: clamp(28px,3vw,38px); letter-spacing: 2px; line-height: 1; }
  .page-sub { font-size: 13px; color: var(--muted2); font-weight: 300; margin-top: 3px; }

  .btn-primary { display: inline-flex; align-items: center; gap: 8px; padding: 11px 20px; background: var(--red); border: none; border-radius: 3px; color: white; font-family: 'Barlow Condensed', sans-serif; font-size: 13px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; cursor: pointer; position: relative; overflow: hidden; transition: transform 0.15s, box-shadow 0.2s; }
  .btn-primary::before { content:''; position:absolute; inset:0; background:linear-gradient(135deg,rgba(255,255,255,0.1),transparent 60%); }
  .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px var(--red-glow); }
  .btn-primary span, .btn-primary svg { position: relative; z-index: 1; }
  .btn-ghost { padding: 11px 16px; display: inline-flex; align-items: center; gap: 7px; background: var(--card2); border: 1px solid var(--border); border-radius: 3px; color: var(--muted2); font-family: 'Barlow Condensed', sans-serif; font-size: 13px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; cursor: pointer; transition: all 0.18s; }
  .btn-ghost:hover { border-color: var(--border2); color: var(--white); }

  /* ══ FILTER BAR ══ */
  .filter-bar { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
  .filter-tabs { display: flex; background: var(--card); border: 1px solid var(--border); border-radius: 3px; overflow: hidden; }
  .ftab { padding: 8px 16px; font-family: 'Barlow Condensed', sans-serif; font-size: 12px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: var(--muted2); cursor: pointer; border: none; background: none; transition: all 0.18s; border-right: 1px solid var(--border); white-space: nowrap; }
  .ftab:last-child { border-right: none; }
  .ftab:hover { color: var(--white); background: rgba(255,255,255,0.04); }
  .ftab.active { background: var(--red-dim); color: var(--red); }
  .filter-select { background: var(--card); border: 1px solid var(--border); border-radius: 3px; padding: 8px 32px 8px 12px; color: var(--white); font-family: 'Barlow', sans-serif; font-size: 13px; outline: none; cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%236A6E75' stroke-width='1.4' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; transition: border-color 0.2s; }
  .filter-select:focus { border-color: var(--red); }
  .filter-select option { background: var(--card); }
  .filter-date-range { display: flex; align-items: center; gap: 6px; background: var(--card); border: 1px solid var(--border); border-radius: 3px; padding: 8px 12px; font-size: 13px; color: var(--muted2); cursor: pointer; transition: border-color 0.2s; }
  .filter-date-range:hover { border-color: var(--border2); }
  .filter-spacer { flex: 1; }
  .results-count { font-size: 12px; color: var(--muted); white-space: nowrap; }
  .results-count strong { color: var(--white); }

  /* ══ SUMMARY STRIP ══ */
  .summary-strip { display: grid; grid-template-columns: repeat(5,1fr); gap: 1px; background: var(--border); border: 1px solid var(--border); border-radius: 4px; overflow: hidden; margin-bottom: 20px; }
  .sstrip-item { background: var(--card); padding: 14px 18px; display: flex; flex-direction: column; gap: 3px; }
  .sstrip-val { font-family: 'Bebas Neue', sans-serif; font-size: 26px; letter-spacing: 1.5px; line-height: 1; }
  .sstrip-lab { font-size: 10px; text-transform: uppercase; letter-spacing: 1.2px; color: var(--muted2); font-weight: 500; }

  /* ══ TABLE ══ */
  .table-card { background: var(--card); border: 1px solid var(--border); border-radius: 4px; overflow: hidden; }
  .table-wrap { position: relative; overflow-x: auto; min-height: 300px; }
  table { width: 100%; border-collapse: collapse; }
  thead th { text-align: left; padding: 12px 18px; font-size: 9.5px; font-weight: 500; letter-spacing: 1.8px; text-transform: uppercase; color: var(--muted); border-bottom: 1px solid var(--border); white-space: nowrap; cursor: pointer; user-select: none; transition: color 0.18s; }
  thead th:hover { color: var(--muted2); }
  thead th.sorted { color: var(--red); }
  .th-inner { display: flex; align-items: center; gap: 5px; }
  .sort-icon { opacity: 0.4; transition: opacity 0.18s; }
  thead th.sorted .sort-icon { opacity: 1; }
  tbody tr { border-bottom: 1px solid var(--border); transition: background 0.15s; cursor: pointer; }
  tbody tr:last-child { border-bottom: none; }
  tbody tr:hover { background: rgba(255,255,255,0.025); }
  tbody td { padding: 14px 18px; font-size: 13.5px; color: var(--white); vertical-align: middle; white-space: nowrap; }

  .cb-wrap { display: flex; align-items: center; justify-content: center; }
  .cb { width: 16px; height: 16px; border: 1px solid var(--border2); border-radius: 2px; background: var(--card2); appearance: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s; flex-shrink: 0; }
  .cb:checked { background: var(--red); border-color: var(--red); }
  .cb:checked::after { content:''; display:block; width:9px; height:5px; border-left:2px solid white; border-bottom:2px solid white; transform:rotate(-45deg) translateY(-1px); }

  .bid { font-family: 'Barlow Condensed', sans-serif; font-size: 13px; letter-spacing: 1.2px; color: var(--red); font-weight: 700; }
  .customer-cell { display: flex; align-items: center; gap: 10px; }
  .cust-avatar { width: 32px; height: 32px; border-radius: 3px; display: flex; align-items: center; justify-content: center; font-family: 'Bebas Neue', sans-serif; font-size: 13px; flex-shrink: 0; }
  .cust-name { font-size: 13.5px; font-weight: 500; }
  .cust-email { font-size: 11px; color: var(--muted2); margin-top: 1px; }
  .car-name { font-size: 13.5px; font-weight: 500; }
  .car-type { font-size: 11px; color: var(--muted2); margin-top: 1px; }
  .plate { display: inline-flex; align-items: center; gap: 5px; background: var(--card2); border: 1px solid var(--border2); border-radius: 2px; padding: 4px 9px; font-family: 'Barlow Condensed', sans-serif; font-size: 12px; letter-spacing: 2px; color: var(--white); font-weight: 700; }
  .date-main { font-size: 13px; font-weight: 500; }
  .date-day { font-size: 11px; color: var(--muted2); margin-top: 1px; }
  .duration-pill { display: inline-flex; align-items: center; gap: 5px; background: var(--card2); border: 1px solid var(--border); border-radius: 2px; padding: 4px 9px; font-size: 12px; color: var(--muted2); font-family: 'Barlow Condensed', sans-serif; letter-spacing: 0.5px; }
  .amount { font-family: 'Bebas Neue', sans-serif; font-size: 20px; letter-spacing: 1px; }

  .badge { display: inline-flex; align-items: center; gap: 5px; padding: 5px 11px; border-radius: 2px; font-size: 10.5px; font-weight: 500; font-family: 'Barlow Condensed', sans-serif; letter-spacing: 1px; text-transform: uppercase; }
  .badge-dot { width: 5px; height: 5px; border-radius: 50%; }
  .badge.active   { background: var(--green-dim); color: var(--green);  border: 1px solid rgba(61,190,122,0.25); }
  .badge.pending  { background: var(--gold-dim);  color: var(--gold);   border: 1px solid rgba(212,168,67,0.25); }
  .badge.overdue  { background: rgba(255, 144, 0, 0.12);  color: #ff8f00; border: 1px solid rgba(255,144,0,0.2); }
  .badge.done     { background: var(--blue-dim);  color: var(--blue);   border: 1px solid rgba(61,143,190,0.25); }
  .badge.canceled { background: var(--red-dim);   color: #ff6b54;       border: 1px solid rgba(232,52,26,0.25); }
  .badge.active .badge-dot { background: var(--green); animation: blink 2s ease-in-out infinite; }
  .badge.pending .badge-dot { background: var(--gold); }
  .badge.overdue .badge-dot { background: #ff8f00; }
  .badge.done .badge-dot { background: var(--blue); }
  .badge.canceled .badge-dot { background: #ff6b54; }
  @keyframes blink { 0%,100%{opacity:1}50%{opacity:0.3} }

  .actions-cell { display: flex; align-items: center; gap: 6px; }
  .act-btn { width: 30px; height: 30px; border-radius: 3px; border: 1px solid var(--border); background: var(--card2); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.18s; color: var(--muted2); }
  .act-btn:hover { border-color: var(--border2); color: var(--white); }
  .act-btn.view:hover  { border-color: rgba(61,143,190,0.5);  color: var(--blue); background: var(--blue-dim); }
  .act-btn.edit:hover  { border-color: rgba(212,168,67,0.5);  color: var(--gold); background: var(--gold-dim); }
  .act-btn.del:hover   { border-color: rgba(232,52,26,0.5);   color: var(--red);  background: var(--red-dim); }

  .table-footer { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-top: 1px solid var(--border); flex-wrap: wrap; gap: 10px; }
  .tf-info { font-size: 12.5px; color: var(--muted2); }
  .tf-info strong { color: var(--white); }
  .pagination { display: flex; gap: 4px; }
  .pg-btn { width: 32px; height: 32px; border-radius: 3px; border: 1px solid var(--border); background: var(--card2); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px; color: var(--muted2); font-family: 'Barlow Condensed', sans-serif; font-weight: 700; transition: all 0.18s; }
  .pg-btn:hover { border-color: var(--border2); color: var(--white); }
  .pg-btn.active { background: var(--red); border-color: var(--red); color: white; }
  .pg-btn:disabled { opacity: 0.3; cursor: default; }

  .empty-state { display: none; position: absolute; inset: 0; flex-direction: column; align-items: center; justify-content: center; padding: 20px; text-align: center; }
  .empty-state.show { display: flex; }
  .empty-icon { width: 72px; height: 72px; background: var(--red-dim); border: 1px solid rgba(232,52,26,0.2); border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
  .empty-title { font-family: 'Bebas Neue', sans-serif; font-size: 28px; letter-spacing: 2px; margin-bottom: 8px; }
  .empty-sub { font-size: 13.5px; color: var(--muted2); font-weight: 300; line-height: 1.6; max-width: 300px; }

  /* ══ MODAL ══ */
  .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 200; display: none; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
  .modal-overlay.show { display: flex; }
  .modal { background: var(--panel); border: 1px solid var(--border2); border-radius: 4px; width: 100%; max-width: 560px; max-height: 90vh; overflow-y: auto; animation: slideUp 0.3s ease backwards; }
  @keyframes slideUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }
  .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 22px 24px; border-bottom: 1px solid var(--border); position: sticky; top: 0; background: var(--panel); z-index: 2; }
  .modal-title { font-family: 'Bebas Neue', sans-serif; font-size: 24px; letter-spacing: 2px; }
  .modal-close { width: 32px; height: 32px; background: var(--card2); border: 1px solid var(--border); border-radius: 3px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.18s; color: var(--muted2); }
  .modal-close:hover { border-color: var(--red); color: var(--red); background: var(--red-dim); }
  .modal-body { padding: 24px; }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
  .form-row.full { grid-template-columns: 1fr; }
  .form-group { display: flex; flex-direction: column; gap: 7px; }
  .form-label { font-size: 10px; font-weight: 500; letter-spacing: 1.5px; text-transform: uppercase; color: var(--muted); }
  .form-input, .form-select { background: var(--card); border: 1px solid var(--border); border-radius: 3px; padding: 11px 14px; color: var(--white); font-family: 'Barlow', sans-serif; font-size: 14px; outline: none; transition: border-color 0.2s, box-shadow 0.2s; width: 100%; }
  .form-input::placeholder { color: var(--muted); }
  .form-input:focus, .form-select:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(232,52,26,0.09); }
  .form-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%236A6E75' stroke-width='1.4' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px; }
  .form-select option { background: var(--card); }
  .modal-footer { display: flex; gap: 10px; justify-content: flex-end; padding: 16px 24px; border-top: 1px solid var(--border); }

  /* ══ TOAST ══ */
  .toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(80px); background: var(--card); border: 1px solid var(--border2); border-left: 3px solid var(--red); border-radius: 3px; padding: 12px 20px; font-size: 13.5px; color: var(--white); display: flex; align-items: center; gap: 10px; z-index: 300; transition: transform 0.35s cubic-bezier(0.34,1.56,0.64,1); box-shadow: 0 8px 32px rgba(0,0,0,0.5); white-space: nowrap; }
  .toast.show { transform: translateX(-50%) translateY(0); }
  .toast.success { border-left-color: var(--green); }

  /* ══════════════════════════════════
     RESPONSIVE
  ══════════════════════════════════ */

  /* Below 960px: sidebar slides off-screen, hamburger appears */
  @media (max-width: 960px) {
    .sidebar {
      transform: translateX(-100%);
    }
    .sidebar.open {
      transform: translateX(0);
      box-shadow: 4px 0 32px rgba(0,0,0,0.5);
    }
    .main { margin-left: 0; }
    .menu-toggle { display: flex; }
    .topbar { padding: 0 16px; }
    .content { padding: 20px 16px; }
  }

  @media (max-width: 640px) {
    .summary-strip { grid-template-columns: 1fr 1fr; }
    .form-row { grid-template-columns: 1fr; }
    .search-wrap { display: none; }
    .topbar-date { display: none; }
  }

  @media (max-width: 400px) {
    .summary-strip { grid-template-columns: 1fr; }
  }
</style>
</head>
<body>
<div class="app">

  <!-- ══ SIDEBAR ══ -->
  <?php include("../navs/adminnavs.php"); ?>

  <!-- Overlay (click to close sidebar on mobile) -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- ══ MAIN ══ -->
  <div class="main" id="mainContent">

    <!-- TOPBAR -->
    <header class="topbar">
      <!-- Hamburger button — visible only on mobile via CSS -->
      <button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" color="white">
          <path d="M3 5h12M3 9h12M3 13h12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
      </button>

      <div class="topbar-title">Bookings</div>
      <div class="topbar-divider"></div>
      <div class="search-wrap">
        <svg class="search-icon" width="15" height="15" viewBox="0 0 15 15" fill="none" color="white"><circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.4"/><path d="M10.5 10.5l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        <input class="search-input" id="searchInput" placeholder="Search by customer, vehicle, plate, ID…" oninput="filterTable()">
      </div>
      <div class="topbar-right">
        <div class="topbar-date">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><rect x="1.5" y="2.5" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M4 2.5V1M9 2.5V1M1.5 5.5h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          Sat, 12 April 2026
        </div>
        <div class="icon-btn">
          <svg width="17" height="17" viewBox="0 0 17 17" fill="none" color="#9A9DA4"><path d="M8.5 2a5 5 0 0 1 5 5v3l1.5 2H2L3.5 10V7a5 5 0 0 1 5-5z" stroke="currentColor" stroke-width="1.4"/><path d="M7 13.5a1.5 1.5 0 0 0 3 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
          <div class="notif-dot"></div>
        </div>
        <div class="icon-btn">
          <div style="width:22px;height:22px;background:linear-gradient(135deg,var(--red),var(--orange));border-radius:2px;display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:11px;color:white">JG</div>
        </div>
      </div>
    </header>

    <!-- CONTENT -->
    <div class="content">

      <div class="page-header">
        <div>
          <div class="page-eyebrow">Fleet Management</div>
          <div class="page-title">Bookings & Rentals</div>
          <div class="page-sub">Manage and track all vehicle reservations and rental details</div>
        </div>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
          <button class="btn-ghost" onclick="exportCSV()">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M2 10v2h10v-2M7 2v7M4 6l3 3 3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Export
          </button>
          <button class="btn-primary" onclick="openModal()">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="white"><path d="M7 2v10M2 7h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <span>New Booking</span>
          </button>
        </div>
      </div>

      <!-- Summary strip -->
      <div class="summary-strip">
        <div class="sstrip-item"><div class="sstrip-val" id="summaryTotal"><?php echo $bookingSummary['total']; ?></div><div class="sstrip-lab">Total Bookings</div></div>
        <div class="sstrip-item"><div class="sstrip-val" id="summaryActive" style="color:var(--green)"><?php echo $bookingSummary['active']; ?></div><div class="sstrip-lab">Active Rentals</div></div>
        <div class="sstrip-item"><div class="sstrip-val" id="summaryPending" style="color:var(--gold)"><?php echo $bookingSummary['pending']; ?></div><div class="sstrip-lab">Pending</div></div>
        <div class="sstrip-item"><div class="sstrip-val" id="summaryDone" style="color:var(--blue)"><?php echo $bookingSummary['done']; ?></div><div class="sstrip-lab">Completed</div></div>
        <div class="sstrip-item"><div class="sstrip-val" id="summaryOverdue" style="color:var(--orange)">0</div><div class="sstrip-lab">Overdue</div></div>
        <div class="sstrip-item"><div class="sstrip-val" id="summaryCanceled" style="color:var(--red)"><?php echo $bookingSummary['canceled']; ?></div><div class="sstrip-lab">Canceled</div></div>
      </div>

      <!-- Filter bar -->
      <div class="filter-bar">
        <div class="filter-tabs">
          <button class="ftab active" onclick="setFilter('all',this)">All</button>
          <button class="ftab" onclick="setFilter('active',this)">Active</button>
          <button class="ftab" onclick="setFilter('pending',this)">Pending</button>
          <button class="ftab" onclick="setFilter('done',this)">Completed</button>
          <button class="ftab" onclick="setFilter('overdue',this)">Overdue</button>
          <button class="ftab" onclick="setFilter('canceled',this)">Canceled</button>
        </div>
        <select class="filter-select" onchange="filterTable()">
          <option value="">All Vehicles</option>
          <option>Toyota Vios 1.3L</option>
          <option>Honda City RS</option>
          <option>Mitsubishi Mirage</option>
          <option>Ford EcoSport</option>
          <option>Hyundai Accent</option>
          <option>Suzuki Swift</option>
        </select>
        <div class="filter-date-range">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><rect x="1.5" y="2.5" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M4 2.5V1M9 2.5V1M1.5 5.5h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          Apr 1 – Apr 30, 2026
        </div>
        <div class="filter-spacer"></div>
        <div class="results-count" id="resultsCount"><strong><?php echo $bookingSummary['total']; ?></strong> bookings found</div>
      </div>

      <!-- Table -->
      <div class="table-card">
        <div class="table-wrap">
          <table id="bookingTable">
            <thead>
              <tr>
                <th style="width:40px"><div class="cb-wrap"><input type="checkbox" class="cb" id="checkAll" onchange="toggleAll(this)"></div></th>
                <th onclick="sortTable('id')" class="sorted"><div class="th-inner">Booking ID <svg class="sort-icon" width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M5 2v6M2 5l3-3 3 3" stroke="#E8341A" stroke-width="1.3" stroke-linecap="round"/></svg></div></th>
                <th onclick="sortTable('customer')"><div class="th-inner">Customer <svg class="sort-icon" width="10" height="10" viewBox="0 0 10 10" fill="none" color="#6A6E75"><path d="M5 2v6M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div></th>
                <th onclick="sortTable('vehicle')"><div class="th-inner">Vehicle <svg class="sort-icon" width="10" height="10" viewBox="0 0 10 10" fill="none" color="#6A6E75"><path d="M5 2v6M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div></th>
                <th>Plate No.</th>
                <th onclick="sortTable('pickup')"><div class="th-inner">Pickup Date <svg class="sort-icon" width="10" height="10" viewBox="0 0 10 10" fill="none" color="#6A6E75"><path d="M5 2v6M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div></th>
                <th>Return Date</th>
                <th>Duration</th>
                <th onclick="sortTable('amount')"><div class="th-inner">Amount <svg class="sort-icon" width="10" height="10" viewBox="0 0 10 10" fill="none" color="#6A6E75"><path d="M5 2v6M2 5l3-3 3 3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg></div></th>
                <th>Status</th>
                <th style="text-align:center">Actions</th>
              </tr>
            </thead>
            <tbody id="tableBody"></tbody>
          </table>
          <div class="empty-state" id="emptyState">
            <div class="empty-icon">
              <svg width="72" height="72" viewBox="0 0 32 32" fill="none" color="#E8341A">
                <circle cx="16" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/>
                <path d="M5 29c0-6.08 4.92-11 11-11s11 4.92 11 11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
              </svg>
            </div>
            <div class="empty-title">No Bookings Found</div>
            <div class="empty-sub">No bookings match your current filters. Try adjusting the search or status filter.</div>
          </div>
        </div>
        <div class="table-footer">
          <div class="tf-info" id="tfInfo"><?php echo $bookingSummary['total'] > 0 ? 'Showing <strong>1–'.min(10, $bookingSummary['total']).'</strong> of <strong>'.$bookingSummary['total'].'</strong> bookings' : 'No results'; ?></div>
          <div class="pagination">
            <button class="pg-btn" disabled><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M8 2L4 6l4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>
            <button class="pg-btn active">1</button>
            <button class="pg-btn">2</button>
            <button class="pg-btn"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M4 2l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>
          </div>
        </div>
      </div>
    </div><!-- /content -->
  </div><!-- /main -->
</div><!-- /app -->

<!-- ══ ADD BOOKING MODAL ══ -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModalOutside(event)">
  <div class="modal">
    <div class="modal-head">
      <div>
        <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--red);margin-bottom:4px">Fleet Management</div>
        <div class="modal-title" id="modalTitleText">New Booking</div>
      </div>
      <div class="modal-close" onclick="closeModal()">
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" color="currentColor"><path d="M2 2l10 10M12 2L2 12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      </div>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label class="form-label">Customer Name</label><input class="form-input" id="f-customer" placeholder="Full name"></div>
        <div class="form-group"><label class="form-label">Customer ID</label><input class="form-input" id="f-customer-ref" placeholder="e.g. CUST-0042"></div>
      </div>
      <div class="form-row full">
        <div class="form-group"><label class="form-label">Customer Email</label><input class="form-input" id="f-email" placeholder="customer@example.com" type="email"></div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Vehicle</label>
          <select class="form-select" id="f-vehicle">
            <option value="">Select vehicle…</option>
            <option>Toyota Vios 1.3L</option>
            <option>Honda City RS</option>
            <option>Mitsubishi Mirage</option>
            <option>Ford EcoSport</option>
            <option>Hyundai Accent</option>
            <option>Suzuki Swift</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Plate Number</label><input class="form-input" id="f-plate" placeholder="e.g. ABC-1234"></div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Vehicle Type</label>
          <select class="form-select" id="f-vehicle-type">
            <option value="">Choose type…</option>
            <option>Sedan</option>
            <option>SUV</option>
            <option>Compact</option>
            <option>MPV</option>
            <option>Premium</option>
            <option>Electric</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Driver Type</label><select class="form-select" id="f-driver-type"><option value="Self-drive">Self-drive</option><option value="With driver">With driver</option></select></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Pickup Date</label><input class="form-input" id="f-pickup" type="date"></div>
        <div class="form-group"><label class="form-label">Return Date</label><input class="form-input" id="f-return" type="date"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Location</label><input class="form-input" id="f-location" placeholder="Pickup location"></div>
        <div class="form-group"><label class="form-label">Rate (₱/day)</label><input class="form-input" id="f-rate" type="number" placeholder="0.00"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Total Amount (₱)</label><input class="form-input" id="f-amount" type="number" placeholder="0.00"></div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select class="form-select" id="f-status">
            <option value="pending">Pending</option>
            <option value="active">Active</option>
            <option value="overdue">Overdue</option>
            <option value="done">Completed</option>
            <option value="canceled">Canceled</option>
          </select>
        </div>
      </div>
      <div class="form-row full">
        <div class="form-group"><label class="form-label">Notes (optional)</label><input class="form-input" id="f-notes" placeholder="Any special requests or notes…"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-ghost" onclick="closeModal()">Cancel</button>
      <button class="btn-primary" id="saveBookingButton" onclick="saveBooking()">
        <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M2 6.5l3.5 3.5 5.5-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span id="saveBookingText">Save Booking</span>
      </button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">
  <svg width="15" height="15" viewBox="0 0 15 15" fill="none" id="toastIcon"></svg>
  <span id="toastMsg"></span>
</div>

<script>
/* ════════════════════════════════════
   SIDEBAR TOGGLE — the fix
════════════════════════════════════ */
const sidebar  = document.getElementById('sidebar');
const overlay  = document.getElementById('sidebarOverlay');
const menuBtn  = document.getElementById('menuToggle');

function openSidebar() {
  sidebar.classList.add('open');
  overlay.classList.add('active');
  document.body.style.overflow = 'hidden'; // prevent background scroll
}

function closeSidebar() {
  sidebar.classList.remove('open');
  overlay.classList.remove('active');
  document.body.style.overflow = '';
}

// Hamburger click
menuBtn.addEventListener('click', () => {
  sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
});

// Click overlay to close
overlay.addEventListener('click', closeSidebar);

// Close on any nav link click (mobile)
sidebar.querySelectorAll('.nav-item').forEach(item => {
  item.addEventListener('click', () => {
    if (window.innerWidth <= 960) closeSidebar();
  });
});

// Close when window goes back to desktop size
window.addEventListener('resize', () => {
  if (window.innerWidth > 960) closeSidebar();
});

// Escape key closes sidebar
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeSidebar();
});

/* ════════════════════════════════════
   BOOKING DATA & LOGIC
════════════════════════════════════ */
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

let bookings = <?php echo json_encode($bookings, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

let currentFilter = 'all';
let currentSearch = '';
let nextId = 76;
let editMode = false;
let viewMode = false;
let editingRef = null;

function initials(name) { return name.split(' ').map(w=>w[0]).join('').slice(0,2).toUpperCase(); }
function avatarBg(name) { return AVATARS[name.charCodeAt(0) % AVATARS.length]; }

function statusBadge(s) {
  const map = {
    active:['active','Active'],
    pending:['pending','Pending'],
    overdue:['overdue','Overdue'],
    done:['done','Completed'],
    canceled:['canceled','Canceled']
  };
  const [cls, label] = map[s] || ['pending','Unknown'];
  return `<span class="badge ${cls}"><span class="badge-dot"></span>${label}</span>`;
}
function amountColor(s) {
  return s==='active' ? 'var(--green)' : s==='pending' ? 'var(--gold)' : s==='overdue' ? '#ff8f00' : s==='done' ? 'var(--blue)' : 'var(--muted)';
}

function renderTable() {
  const data = bookings.filter(b => {
    const matchFilter = currentFilter==='all' || b.status===currentFilter;
    const q = currentSearch.toLowerCase();
    const matchSearch = !q || b.id.toLowerCase().includes(q) || b.customer.toLowerCase().includes(q) || b.customer_ref.toLowerCase().includes(q) || b.vehicle.toLowerCase().includes(q) || b.vehicle_type.toLowerCase().includes(q) || b.driver_type.toLowerCase().includes(q) || b.location.toLowerCase().includes(q) || b.plate.toLowerCase().includes(q) || b.email.toLowerCase().includes(q);
    return matchFilter && matchSearch;
  });

  const tbody = document.getElementById('tableBody');
  const empty = document.getElementById('emptyState');
  const rc    = document.getElementById('resultsCount');
  const tfi   = document.getElementById('tfInfo');

  if (!data.length) {
    tbody.innerHTML = '';
    empty.classList.add('show');
    rc.innerHTML = '<strong>0</strong> bookings found';
    tfi.innerHTML = 'No results';
    updateSummaryCounts();
    return;
  }
  empty.classList.remove('show');
  rc.innerHTML = `<strong>${data.length}</strong> booking${data.length!==1?'s':''} found`;
  tfi.innerHTML = `Showing <strong>1–${Math.min(10,data.length)}</strong> of <strong>${data.length}</strong> booking${data.length!==1?'s':''}`;

  tbody.innerHTML = data.map(b => `
    <tr data-id="${b.id}">
      <td><div class="cb-wrap"><input type="checkbox" class="cb row-cb"></div></td>
      <td><span class="bid">#${b.id}</span></td>
      <td>
        <div class="customer-cell">
          <div class="cust-avatar" style="background:${avatarBg(b.customer)}">${initials(b.customer)}</div>
          <div><div class="cust-name">${b.customer}</div><div class="cust-email">${b.email}</div></div>
        </div>
      </td>
      <td><div class="car-name">${b.vehicle}</div><div class="car-type">${b.vehicle_type || 'Sedan'}</div></td>
      <td><span class="plate"><svg width="11" height="11" viewBox="0 0 11 11" fill="none" color="#6A6E75"><rect x="1" y="2.5" width="9" height="6" rx="1" stroke="currentColor" stroke-width="1.1"/></svg>${b.plate}</span></td>
      <td><div class="date-main">${b.pickup}</div><div class="date-day">Pickup</div></td>
      <td><div class="date-main">${b.ret}</div><div class="date-day">Return</div></td>
      <td><span class="duration-pill"><svg width="11" height="11" viewBox="0 0 11 11" fill="none" color="currentColor"><circle cx="5.5" cy="5.5" r="4" stroke="currentColor" stroke-width="1.1"/><path d="M5.5 3.5v2l1.5 1" stroke="currentColor" stroke-width="1.1" stroke-linecap="round"/></svg>${b.days} day${b.days!==1?'s':''}</span></td>
      <td><span class="amount" style="color:${amountColor(b.status)}">₱${b.amount>0?b.amount.toLocaleString():'—'}</span></td>
      <td>${statusBadge(b.status)}</td>
      <td>
        <div class="actions-cell" style="justify-content:center">
          <div class="act-btn view" title="View" onclick="viewBooking('${b.id}',event)"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M1 6.5s2.5-4 5.5-4 5.5 4 5.5 4-2.5 4-5.5 4-5.5-4-5.5-4z" stroke="currentColor" stroke-width="1.2"/><circle cx="6.5" cy="6.5" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg></div>
          <div class="act-btn edit" title="Edit" onclick="editBooking('${b.id}',event)"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 9.5L9.5 2l1.5 1.5-7.5 7.5H2V9.5z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
          <div class="act-btn del" title="Delete" onclick="deleteBooking('${b.id}',event)"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><path d="M2 3.5h9M5 3.5V2h3v1.5M10 3.5l-.7 7.5H3.7L3 3.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        </div>
      </td>
    </tr>
  `).join('');
  updateSummaryCounts();
}

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

function viewBooking(id, e) {
  e.stopPropagation();
  const booking = bookings.find(b => b.id === id);
  if (!booking) { showToast('Booking not found','error'); return; }
  openModal({ mode:'view', booking });
}
function editBooking(id, e) {
  e.stopPropagation();
  const booking = bookings.find(b => b.id === id);
  if (!booking) { showToast('Booking not found','error'); return; }
  openModal({ mode:'edit', booking });
}
async function deleteBooking(id, e) {
  e.stopPropagation();
  if (!window.confirm('Delete booking #' + id + '?')) return;

  try {
    const response = await fetch('/rent/php/booking_action.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'delete', booking_ref: id })
    });
    const result = await response.json();
    if (!response.ok || result.error) {
      showToast(result.error || 'Unable to delete booking.','error');
      return;
    }

    bookings = bookings.filter(b => b.id !== id);
    renderTable();
    showToast('Booking #' + id + ' removed.','error');
  } catch (err) {
    showToast('Unable to delete booking.','error');
  }
}
function toggleAll(cb) { document.querySelectorAll('.row-cb').forEach(c => c.checked = cb.checked); }

function openModal({ mode='create', booking=null } = {}) {
  const title = document.getElementById('modalTitleText');
  const saveText = document.getElementById('saveBookingText');
  const saveButton = document.getElementById('saveBookingButton');

  editMode = mode === 'edit';
  viewMode = mode === 'view';
  editingRef = booking ? booking.id : null;

  if (mode === 'create') {
    title.textContent = 'New Booking';
    saveText.textContent = 'Save Booking';
    saveButton.style.display = 'inline-flex';
    fillModalFields({});
    toggleModalFields(false);
  } else if (mode === 'edit') {
    title.textContent = 'Edit Booking';
    saveText.textContent = 'Save Changes';
    saveButton.style.display = 'inline-flex';
    fillModalFields(booking);
    toggleModalFields(false);
  } else {
    title.textContent = 'Booking Details';
    saveButton.style.display = 'none';
    fillModalFields(booking);
    toggleModalFields(true);
  }

  document.getElementById('modalOverlay').classList.add('show');
  document.body.style.overflow = 'hidden';
}
function closeModal() {
  document.getElementById('modalOverlay').classList.remove('show');
  document.body.style.overflow = '';
  editMode = false;
  viewMode = false;
  editingRef = null;
  fillModalFields({});
  toggleModalFields(false);
}
function closeModalOutside(e) {
  if (e.target === document.getElementById('modalOverlay')) closeModal();
}

function fillModalFields(booking) {
  document.getElementById('f-customer').value = booking?.customer || '';
  document.getElementById('f-customer-ref').value = booking?.customer_ref || '';
  document.getElementById('f-email').value = booking?.email || '';
  document.getElementById('f-vehicle').value = booking?.vehicle || '';
  document.getElementById('f-vehicle-type').value = booking?.vehicle_type || '';
  document.getElementById('f-driver-type').value = booking?.driver_type || 'Self-drive';
  document.getElementById('f-location').value = booking?.location || '';
  document.getElementById('f-plate').value = booking?.plate || '';
  document.getElementById('f-pickup').value = booking?.pickup ? formatInputDate(booking.pickup) : '';
  document.getElementById('f-return').value = booking?.ret ? formatInputDate(booking.ret) : '';
  document.getElementById('f-rate').value = booking?.rate ?? '';
  document.getElementById('f-amount').value = booking?.amount ?? '';
  document.getElementById('f-status').value = booking?.status || 'pending';
  document.getElementById('f-notes').value = booking?.notes || '';
}

function toggleModalFields(disabled) {
  ['f-customer','f-customer-ref','f-email','f-vehicle','f-vehicle-type','f-driver-type','f-location','f-plate','f-pickup','f-return','f-rate','f-amount','f-status','f-notes'].forEach(id => {
    const input = document.getElementById(id);
    if (input) input.disabled = disabled;
  });
}

function formatInputDate(value) {
  if (!value) return '';
  const date = new Date(value);
  if (Number.isNaN(date.valueOf())) return '';
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${date.getFullYear()}-${month}-${day}`;
}

function getModalData() {
  return {
    customer: document.getElementById('f-customer').value.trim(),
    customer_ref: document.getElementById('f-customer-ref').value.trim(),
    email: document.getElementById('f-email').value.trim(),
    vehicle: document.getElementById('f-vehicle').value,
    vehicle_type: document.getElementById('f-vehicle-type').value,
    driver_type: document.getElementById('f-driver-type').value,
    location: document.getElementById('f-location').value.trim(),
    plate: document.getElementById('f-plate').value.trim(),
    pickup_date: document.getElementById('f-pickup').value,
    return_date: document.getElementById('f-return').value,
    rate: parseFloat(document.getElementById('f-rate').value) || 0,
    amount: parseFloat(document.getElementById('f-amount').value) || 0,
    status: document.getElementById('f-status').value,
    notes: document.getElementById('f-notes').value.trim(),
  };
}

function updateSummaryCounts() {
  const counts = bookings.reduce((acc, b) => {
    acc.total += 1;
    acc[b.status] = (acc[b.status] || 0) + 1;
    return acc;
  }, { total: 0, active: 0, pending: 0, done: 0, overdue: 0, canceled: 0 });
  document.getElementById('summaryTotal').textContent = counts.total;
  document.getElementById('summaryActive').textContent = counts.active;
  document.getElementById('summaryPending').textContent = counts.pending;
  document.getElementById('summaryDone').textContent = counts.done;
  document.getElementById('summaryOverdue').textContent = counts.overdue;
  document.getElementById('summaryCanceled').textContent = counts.canceled;
}

async function saveBooking() {
  if (viewMode) return;

  const bookingData = getModalData();
  if (!bookingData.customer || !bookingData.vehicle || !bookingData.plate || !bookingData.pickup_date || !bookingData.return_date) {
    showToast('Please fill in all required fields.');
    return;
  }

  const action = editMode ? 'update' : 'create';
  const payload = { ...bookingData, action, booking_ref: editingRef };

  try {
    const response = await fetch('/rent/php/booking_action.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const result = await response.json();
    if (!response.ok || result.error) {
      showToast(result.error || 'Unable to save booking.','error');
      return;
    }

    if (editMode) {
      bookings = bookings.map(b => b.id === editingRef ? result.booking : b);
      showToast(`Booking #${editingRef} updated successfully!`, 'success');
    } else {
      bookings.unshift(result.booking);
      showToast(`Booking #${result.booking.id} created successfully!`, 'success');
    }

    renderTable();
    closeModal();
  } catch (err) {
    showToast('Unable to save booking. Please try again.','error');
  }
}

function exportCSV() {
  const h = ['Booking ID','Customer','Email','Vehicle','Plate','Pickup','Return','Days','Amount','Status'];
  const rows = bookings.map(b => [b.id,b.customer,b.email,b.vehicle,b.plate,b.pickup,b.ret,b.days,b.amount,b.status]);
  const csv = [h,...rows].map(r=>r.join(',')).join('\n');
  const blob = new Blob([csv],{type:'text/csv'});
  const a = document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='REVV_Bookings.csv'; a.click();
  showToast('Exported as REVV_Bookings.csv','success');
}

let sortDir = {};
function sortTable(col) {
  sortDir[col] = !sortDir[col];
  const dir = sortDir[col] ? 1 : -1;
  bookings.sort((a,b) => {
    const av = a[col], bv = b[col];
    if (typeof av==='number') return (av-bv)*dir;
    return String(av).localeCompare(String(bv))*dir;
  });
  renderTable();
}

function showToast(msg, type='error') {
  const t=document.getElementById('toast'), tm=document.getElementById('toastMsg'), ti=document.getElementById('toastIcon');
  tm.textContent=msg;
  const c=type==='success'?'#3DBE7A':'#E8341A';
  t.style.borderLeftColor=c;
  ti.innerHTML=type==='success'
    ?`<circle cx="7.5" cy="7.5" r="6" stroke="${c}" stroke-width="1.3"/><path d="M5 7.5l2 2 3.5-3.5" stroke="${c}" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>`
    :`<circle cx="7.5" cy="7.5" r="6" stroke="${c}" stroke-width="1.3"/><path d="M7.5 5v3" stroke="${c}" stroke-width="1.5" stroke-linecap="round"/><circle cx="7.5" cy="10" r="0.7" fill="${c}"/>`;
  void t.offsetWidth;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3400);
}

window.addEventListener('error', event => {
  showToast('JS error: ' + (event.message || 'Unknown error'), 'error');
  console.error(event.error || event.message, event.error);
});
window.addEventListener('unhandledrejection', event => {
  showToast('Unhandled promise error', 'error');
  console.error('Unhandled promise rejection:', event.reason);
});

renderTable();
</script>
</body>
</html>