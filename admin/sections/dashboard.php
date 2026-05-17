<?php
// ── Dashboard Data ──
$s = [
  'rev'   => DB::val("SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE booking_status!='Cancelled'"),
  'bk'    => DB::val("SELECT COUNT(*) FROM bookings"),
  'usr'   => DB::val("SELECT COUNT(*) FROM users WHERE role='user'"),
  'tod_b' => DB::val("SELECT COUNT(*) FROM bookings WHERE DATE(created_at)=CURDATE()"),
  'tod_r' => DB::val("SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE DATE(created_at)=CURDATE() AND booking_status!='Cancelled'"),
  'pend'  => DB::val("SELECT COUNT(*) FROM bookings WHERE booking_status='Pending'"),
  'cancel'=> DB::val("SELECT COUNT(*) FROM bookings WHERE booking_status='Cancelled'"),
  'conf'  => DB::val("SELECT COUNT(*) FROM bookings WHERE booking_status='Confirmed'"),
  'hotels'=> DB::val("SELECT COUNT(*) FROM hotels WHERE is_active=1"),
  'flt'   => DB::val("SELECT COUNT(*) FROM flights WHERE is_active=1"),
];
$cancelRate = $s['bk'] > 0 ? round($s['cancel'] / $s['bk'] * 100, 1) : 0;
$avgOrder   = $s['bk'] > 0 ? round($s['rev'] / $s['bk']) : 0;

$rec     = DB::all("SELECT b.*,u.name un FROM bookings b LEFT JOIN users u ON b.user_id=u.id ORDER BY b.created_at DESC LIMIT 10");
$routes  = DB::all("SELECT item_name,COUNT(*) cnt,SUM(total_amount) rev FROM bookings WHERE booking_type='Flight' AND booking_status!='Cancelled' GROUP BY item_name ORDER BY rev DESC LIMIT 5");
$mix     = DB::all("SELECT booking_type,COUNT(*) cnt FROM bookings WHERE booking_status!='Cancelled' GROUP BY booking_type ORDER BY cnt DESC");
$monthly = DB::all("SELECT DATE_FORMAT(created_at,'%b %Y') m,COUNT(*) bk,COALESCE(SUM(total_amount),0) rev FROM bookings WHERE booking_status!='Cancelled' GROUP BY YEAR(created_at),MONTH(created_at) ORDER BY YEAR(created_at) DESC,MONTH(created_at) DESC LIMIT 6");
$spark   = DB::all("SELECT DATE(created_at) d,COALESCE(SUM(total_amount),0) rev,COUNT(*) bk FROM bookings WHERE booking_status!='Cancelled' AND created_at>=DATE_SUB(CURDATE(),INTERVAL 6 DAY) GROUP BY DATE(created_at) ORDER BY d ASC");

// Top city by revenue
$topCity = DB::one("SELECT COALESCE(f.to_city, SUBSTRING_INDEX(b.item_name,' ',2)) city, SUM(b.total_amount) rev FROM bookings b LEFT JOIN flights f ON b.item_id=f.id AND b.booking_type='Flight' WHERE b.booking_status!='Cancelled' GROUP BY city ORDER BY rev DESC LIMIT 1");

// Pie chart data
$mixTotal = max(1, array_sum(array_column($mix, 'cnt')));
$mixColors = ['Flight'=>'#008cff','Hotel'=>'#2563eb','Package'=>'#16a34a','Train'=>'#7c3aed','Bus'=>'#0d9488','Cab'=>'#ec4899','Cruise'=>'#f59e0b'];

// Sparkline for chart
$maxSpark = max(1, max(array_column($spark, 'rev') ?: [1]));
$sparkLabels = array_map(fn($s) => date('d M', strtotime($s['d'])), $spark);
$sparkRevs   = array_column($spark, 'rev');
$sparkBks    = array_column($spark, 'bk');
?>

<style>
/* Dashboard 2026 Styles */
.dash-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
  flex-wrap: wrap;
  gap: 12px;
}
.dash-title {
  font-size: 24px;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.5px;
}
.dash-subtitle {
  font-size: 13px;
  color: #94a3b8;
  margin-top: 2px;
}

/* KPI Cards */
.dash-kpi-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 14px;
  margin-bottom: 22px;
}
.dash-kpi {
  background: #fff;
  border-radius: 14px;
  padding: 20px 18px;
  border: 1px solid #f0f0f0;
  position: relative;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
  transition: all 0.25s;
}
.dash-kpi:hover {
  border-color: rgba(0,140,255,0.2);
  box-shadow: 0 4px 16px rgba(0,0,0,0.06);
  transform: translateY(-2px);
}
.dash-kpi::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  border-radius: 14px 14px 0 0;
}
.dash-kpi.orange::before { background: linear-gradient(90deg, #008cff, #33a3ff); }
.dash-kpi.blue::before   { background: linear-gradient(90deg, #2563eb, #60a5fa); }
.dash-kpi.green::before  { background: linear-gradient(90deg, #16a34a, #4ade80); }
.dash-kpi.purple::before { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
.dash-kpi.red::before    { background: linear-gradient(90deg, #ef4444, #f87171); }
.dash-kpi.teal::before   { background: linear-gradient(90deg, #0d9488, #2dd4bf); }
.dash-kpi-icon {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
}
.dash-kpi-icon .material-symbols-outlined {
  font-size: 20px;
  color: #fff;
}
.dash-kpi-value {
  font-size: 24px;
  font-weight: 800;
  font-family: 'Inter', sans-serif;
  color: #0f172a;
  letter-spacing: -0.5px;
  line-height: 1;
  margin-bottom: 4px;
}
.dash-kpi-label {
  font-size: 11px;
  color: #94a3b8;
  font-weight: 500;
}
.dash-kpi-delta {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  padding: 2px 6px;
  border-radius: 6px;
  font-size: 10px;
  font-weight: 700;
  margin-top: 6px;
}
.dash-kpi-delta.up { background: #f0fdf4; color: #16a34a; }
.dash-kpi-delta.down { background: #fef2f2; color: #ef4444; }

/* Chart Cards */
.dash-grid-2 {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 16px;
  margin-bottom: 16px;
}
.dash-grid-3 {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 16px;
  margin-bottom: 16px;
}
.dash-card {
  background: #fff;
  border: 1px solid #f0f0f0;
  border-radius: 14px;
  padding: 22px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.dash-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 18px;
}
.dash-card-title {
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
}
.dash-card-link {
  font-size: 12px;
  color: #008cff;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.2s;
}
.dash-card-link:hover { color: #0070cc; }

/* Chart container */
.dash-chart {
  width: 100%;
  height: 200px;
  position: relative;
}
.dash-chart-bars {
  display: flex;
  align-items: flex-end;
  gap: 8px;
  height: 180px;
  padding-top: 20px;
}
.dash-chart-bar-col {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
}
.dash-chart-bar {
  width: 100%;
  max-width: 40px;
  border-radius: 6px 6px 0 0;
  transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
  position: relative;
  cursor: pointer;
  min-height: 4px;
}
.dash-chart-bar:hover {
  opacity: 0.85;
  transform: scaleY(1.02);
  transform-origin: bottom;
}
.dash-chart-bar-label {
  font-size: 10px;
  color: #94a3b8;
  text-align: center;
  white-space: nowrap;
}
.dash-chart-bar-val {
  position: absolute;
  top: -18px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 9px;
  font-weight: 700;
  color: #64748b;
  white-space: nowrap;
  opacity: 0;
  transition: opacity 0.2s;
}
.dash-chart-bar:hover .dash-chart-bar-val { opacity: 1; }

/* Pie chart (CSS) */
.dash-pie-container {
  display: flex;
  align-items: center;
  gap: 24px;
}
.dash-pie {
  width: 140px;
  height: 140px;
  border-radius: 50%;
  position: relative;
  flex-shrink: 0;
}
.dash-pie-center {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%,-50%);
  width: 70px;
  height: 70px;
  background: #fff;
  border-radius: 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.dash-pie-center-val {
  font-size: 18px;
  font-weight: 800;
  color: #0f172a;
}
.dash-pie-center-label {
  font-size: 8px;
  color: #94a3b8;
  text-transform: uppercase;
  font-weight: 600;
}
.dash-pie-legend {
  flex: 1;
}
.dash-pie-legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
  font-size: 12px;
  color: #64748b;
}
.dash-pie-dot {
  width: 10px;
  height: 10px;
  border-radius: 3px;
  flex-shrink: 0;
}
.dash-pie-legend-pct {
  margin-left: auto;
  font-weight: 700;
  color: #0f172a;
}

/* Table */
.dash-table {
  width: 100%;
  border-collapse: collapse;
}
.dash-table th {
  text-align: left;
  font-size: 11px;
  font-weight: 600;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 10px 14px;
  border-bottom: 1px solid #f0f0f0;
  background: #fafbfc;
}
.dash-table td {
  padding: 12px 14px;
  font-size: 13px;
  border-bottom: 1px solid #f8f8f8;
  vertical-align: middle;
}
.dash-table tbody tr {
  transition: background 0.15s;
}
.dash-table tbody tr:hover {
  background: #f0f7ff;
}
.dash-status {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 3px 10px;
  border-radius: 8px;
  font-size: 11px;
  font-weight: 600;
}
.dash-status.confirmed { background: #f0fdf4; color: #16a34a; }
.dash-status.pending   { background: #fffbeb; color: #d97706; }
.dash-status.cancelled { background: #fef2f2; color: #ef4444; }

/* Quick Actions */
.dash-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}
.dash-action-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  border-radius: 10px;
  border: 1.5px solid #eee;
  background: #fff;
  font-size: 13px;
  font-weight: 500;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
}
.dash-action-btn:hover {
  border-color: #008cff;
  color: #008cff;
  background: #f0f7ff;
  transform: translateY(-1px);
}
.dash-action-btn .material-symbols-outlined {
  font-size: 18px;
}
.dash-action-btn.primary {
  background: linear-gradient(135deg, #008cff, #0070cc);
  color: #fff;
  border-color: transparent;
}
.dash-action-btn.primary:hover {
  box-shadow: 0 6px 20px rgba(0,140,255,0.3);
}

/* Top routes bar */
.dash-route-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}
.dash-route-name {
  font-size: 12px;
  font-weight: 500;
  color: #64748b;
  min-width: 120px;
}
.dash-route-track {
  flex: 1;
  height: 8px;
  background: #f1f5f9;
  border-radius: 4px;
  overflow: hidden;
}
.dash-route-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}
.dash-route-val {
  font-size: 11px;
  font-weight: 700;
  color: #0f172a;
  min-width: 70px;
  text-align: right;
}

/* Responsive */
@media (max-width: 1100px) {
  .dash-kpi-grid { grid-template-columns: repeat(3, 1fr); }
  .dash-grid-2 { grid-template-columns: 1fr; }
  .dash-grid-3 { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
  .dash-kpi-grid { grid-template-columns: 1fr 1fr; }
}
</style>

<!-- Dashboard Header -->
<div class="dash-header">
  <div>
    <div class="dash-title">Dashboard</div>
    <div class="dash-subtitle">Welcome back, <?= clean(explode(' ',$me['name'])[0]) ?>. Here's what's happening today.</div>
  </div>
  <div class="dash-actions">
    <a href="?sec=flights" class="dash-action-btn primary">
      <span class="material-symbols-outlined">add_circle</span> Add Flight
    </a>
    <a href="?sec=hotels" class="dash-action-btn">
      <span class="material-symbols-outlined">hotel</span> Add Hotel
    </a>
    <a href="?sec=promos" class="dash-action-btn">
      <span class="material-symbols-outlined">campaign</span> Push Promo
    </a>
    <a href="?sec=revenue" class="dash-action-btn">
      <span class="material-symbols-outlined">bar_chart</span> Reports
    </a>
  </div>
</div>

<!-- KPI Cards -->
<div class="dash-kpi-grid">
  <div class="dash-kpi orange">
    <div class="dash-kpi-icon" style="background:linear-gradient(135deg,#008cff,#0070cc)">
      <span class="material-symbols-outlined">payments</span>
    </div>
    <div class="dash-kpi-value"><?= rupee($s['rev']) ?></div>
    <div class="dash-kpi-label">Total Revenue</div>
    <div class="dash-kpi-delta up">↑ Today: <?= rupee($s['tod_r']) ?></div>
  </div>

  <div class="dash-kpi blue">
    <div class="dash-kpi-icon" style="background:linear-gradient(135deg,#2563eb,#60a5fa)">
      <span class="material-symbols-outlined">confirmation_number</span>
    </div>
    <div class="dash-kpi-value"><?= number_format($s['bk']) ?></div>
    <div class="dash-kpi-label">Total Bookings</div>
    <div class="dash-kpi-delta up">↑ <?= $s['tod_b'] ?> today</div>
  </div>

  <div class="dash-kpi green">
    <div class="dash-kpi-icon" style="background:linear-gradient(135deg,#16a34a,#4ade80)">
      <span class="material-symbols-outlined">group</span>
    </div>
    <div class="dash-kpi-value"><?= number_format($s['usr']) ?></div>
    <div class="dash-kpi-label">Active Users</div>
    <div class="dash-kpi-delta up">↑ Growing</div>
  </div>

  <div class="dash-kpi purple">
    <div class="dash-kpi-icon" style="background:linear-gradient(135deg,#7c3aed,#a78bfa)">
      <span class="material-symbols-outlined">avg_pace</span>
    </div>
    <div class="dash-kpi-value"><?= rupee($avgOrder) ?></div>
    <div class="dash-kpi-label">Avg. Order Value</div>
  </div>

  <div class="dash-kpi red">
    <div class="dash-kpi-icon" style="background:linear-gradient(135deg,#ef4444,#f87171)">
      <span class="material-symbols-outlined">cancel</span>
    </div>
    <div class="dash-kpi-value"><?= $cancelRate ?>%</div>
    <div class="dash-kpi-label">Cancel Rate</div>
    <?php if($cancelRate > 10): ?><div class="dash-kpi-delta down">↓ Needs attention</div><?php endif; ?>
  </div>

  <div class="dash-kpi teal">
    <div class="dash-kpi-icon" style="background:linear-gradient(135deg,#0d9488,#2dd4bf)">
      <span class="material-symbols-outlined">trending_up</span>
    </div>
    <div class="dash-kpi-value"><?= clean($topCity['city'] ?? 'Mumbai') ?></div>
    <div class="dash-kpi-label">Top City by Revenue</div>
  </div>
</div>

<!-- Revenue Chart + Booking Mix -->
<div class="dash-grid-2">
  <!-- Revenue Trend Chart (Bar chart using CSS) -->
  <div class="dash-card">
    <div class="dash-card-header">
      <div class="dash-card-title">Revenue & Bookings Trend (Last 7 Days)</div>
      <a href="?sec=revenue" class="dash-card-link">View Full Report →</a>
    </div>
    <div class="dash-chart-bars">
      <?php foreach($spark as $i => $sp):
        $h = max(4, round($sp['rev'] / $maxSpark * 160));
      ?>
      <div class="dash-chart-bar-col">
        <div class="dash-chart-bar" style="height:<?=$h?>px;background:linear-gradient(to top,#008cff,#33a3ff)">
          <span class="dash-chart-bar-val"><?= '₹'.number_format($sp['rev']) ?></span>
        </div>
        <div class="dash-chart-bar-label"><?= date('d M', strtotime($sp['d'])) ?></div>
      </div>
      <?php endforeach; ?>
      <?php if(empty($spark)): ?>
        <div style="text-align:center;width:100%;color:#94a3b8;font-size:13px;padding:40px 0">No data for the last 7 days</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Booking Mix Pie Chart -->
  <div class="dash-card">
    <div class="dash-card-header">
      <div class="dash-card-title">Bookings by Category</div>
    </div>
    <div class="dash-pie-container">
      <div class="dash-pie" style="background: conic-gradient(<?php
        $offset = 0;
        $gradParts = [];
        foreach($mix as $m){
          $pct = round($m['cnt'] / $mixTotal * 100, 1);
          $color = $mixColors[$m['booking_type']] ?? '#94a3b8';
          $gradParts[] = "$color {$offset}% ".($offset + $pct)."%";
          $offset += $pct;
        }
        echo implode(', ', $gradParts);
      ?>)">
        <div class="dash-pie-center">
          <div class="dash-pie-center-val"><?= $s['bk'] ?></div>
          <div class="dash-pie-center-label">Total</div>
        </div>
      </div>
      <div class="dash-pie-legend">
        <?php foreach($mix as $m):
          $pct = round($m['cnt'] / $mixTotal * 100, 1);
          $color = $mixColors[$m['booking_type']] ?? '#94a3b8';
        ?>
        <div class="dash-pie-legend-item">
          <div class="dash-pie-dot" style="background:<?=$color?>"></div>
          <span><?= $m['booking_type'] ?></span>
          <span class="dash-pie-legend-pct"><?= $pct ?>%</span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<!-- Top Routes + Monthly Revenue -->
<div class="dash-grid-2">
  <!-- Top 5 Routes by Revenue -->
  <div class="dash-card">
    <div class="dash-card-header">
      <div class="dash-card-title">Top 5 Routes by Revenue</div>
      <a href="?sec=flights" class="dash-card-link">All Routes →</a>
    </div>
    <?php
    $maxRouteRev = max(1, $routes[0]['rev'] ?? 1);
    $routeColors = ['#008cff','#2563eb','#16a34a','#7c3aed','#0d9488'];
    foreach($routes as $i => $r): ?>
    <div class="dash-route-bar">
      <span class="dash-route-name"><?= clean($r['item_name']) ?></span>
      <div class="dash-route-track">
        <div class="dash-route-fill" style="width:<?= round($r['rev']/$maxRouteRev*100) ?>%;background:<?= $routeColors[$i%5] ?>"></div>
      </div>
      <span class="dash-route-val"><?= rupee($r['rev']) ?></span>
    </div>
    <?php endforeach; ?>
    <?php if(empty($routes)): ?>
      <div style="text-align:center;color:#94a3b8;font-size:13px;padding:20px">No route data yet</div>
    <?php endif; ?>
  </div>

  <!-- Monthly Revenue Table -->
  <div class="dash-card">
    <div class="dash-card-header">
      <div class="dash-card-title">Monthly Revenue</div>
      <a href="?sec=revenue" class="dash-card-link">Full Report →</a>
    </div>
    <div style="overflow-x:auto">
      <table class="dash-table">
        <thead><tr><th>Month</th><th>Bookings</th><th>Revenue</th></tr></thead>
        <tbody>
        <?php foreach($monthly as $m): ?>
        <tr>
          <td style="font-weight:600"><?= $m['m'] ?></td>
          <td><?= number_format($m['bk']) ?></td>
          <td style="font-weight:700;color:#008cff"><?= rupee($m['rev']) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Recent Bookings Table -->
<div class="dash-card">
  <div class="dash-card-header">
    <div class="dash-card-title">Recent Bookings</div>
    <a href="?sec=bookings" class="dash-card-link">View All Bookings →</a>
  </div>
  <div style="overflow-x:auto">
    <table class="dash-table">
      <thead>
        <tr>
          <th>Booking ID</th>
          <th>Customer</th>
          <th>Type</th>
          <th>Route / Item</th>
          <th>Date</th>
          <th>Amount (₹)</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($rec as $b):
        $stClass = strtolower($b['booking_status'] ?? 'pending');
      ?>
        <tr>
          <td style="font-family:'Inter',monospace;font-size:12px;font-weight:600;color:#008cff"><?= clean($b['booking_ref']) ?></td>
          <td style="font-weight:500"><?= clean($b['un'] ?? 'N/A') ?></td>
          <td><span class="tag t-<?= $b['booking_type']==='Flight'?'blue':($b['booking_type']==='Hotel'?'purple':'teal') ?>" style="font-size:10px"><?= clean($b['booking_type']) ?></span></td>
          <td style="font-size:12px;color:#64748b"><?= clean($b['item_name'] ?? '—') ?></td>
          <td style="font-size:12px;color:#94a3b8"><?= date('d/m/Y', strtotime($b['created_at'])) ?></td>
          <td style="font-weight:700"><?= rupee($b['total_amount']) ?></td>
          <td><span class="dash-status <?= $stClass ?>"><?= clean($b['booking_status']) ?></span></td>
          <td>
            <button class="btn btn-ghost btn-xs" onclick="window.location='?sec=bookings'" style="font-size:11px">
              <span class="material-symbols-outlined" style="font-size:14px">visibility</span> View
            </button>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
