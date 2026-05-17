<?php
require_once __DIR__.'/../includes/bootstrap.php';
if(!loggedIn()){header('Location: '.BASE.'/admin/login.php');exit;}
if(!isAdmin()){header('Location: '.BASE.'/index.php');exit;}

$sec=clean($_GET['sec']??'dashboard');
$act=clean($_GET['act']??'');
$allowed=['dashboard','bookings','users','flights','hotels','packages','trains','buses','cabs','cruises','promos','reviews','support','revenue'];
if(!in_array($sec,$allowed))$sec='dashboard';

// POST handlers
if($_SERVER['REQUEST_METHOD']==='POST'&&$act){
    if(!checkCsrf()){flash('err','Invalid request');header('Location: '.$_SERVER['HTTP_REFERER']);exit;}
    if($act==='add_flight'||$act==='edit_flight'){
        $d=['flight_code'=>clean($_POST['flight_code']),'airline'=>clean($_POST['airline']),'from_city'=>clean($_POST['from_city']),'from_code'=>strtoupper(clean($_POST['from_code'])),'to_city'=>clean($_POST['to_city']),'to_code'=>strtoupper(clean($_POST['to_code'])),'departure_time'=>clean($_POST['dep']),'arrival_time'=>clean($_POST['arr']),'duration'=>clean($_POST['dur']),'stops'=>clean($_POST['stops']),'price'=>(float)$_POST['price'],'class'=>clean($_POST['class']),'seats_available'=>(int)($_POST['seats']??50),'aircraft'=>clean($_POST['aircraft']??''),'terminal'=>clean($_POST['terminal']??'T1'),'baggage'=>clean($_POST['baggage']??'15kg'),'emoji'=>clean($_POST['emoji']??'✈️')];
        if($act==='edit_flight'){DB::update('flights',$d,'id=?',['id'=>(int)$_POST['id']]);flash('ok','Flight updated!');}
        else{DB::insert('flights',$d);flash('ok','Flight added!');}
        header('Location: '.BASE.'/admin/index.php?sec=flights');exit;
    }
    if($act==='add_promo'||$act==='edit_promo'){
        $d=['code'=>strtoupper(clean($_POST['code'])),'description'=>clean($_POST['desc']??''),'discount_type'=>clean($_POST['dtype']),'discount_value'=>(float)$_POST['dval'],'max_discount'=>(float)$_POST['maxd'],'min_booking'=>(float)($_POST['minb']??0),'applicable_type'=>clean($_POST['apptype']??'All'),'usage_limit'=>(int)$_POST['ulimit'],'valid_from'=>clean($_POST['vfrom']),'valid_until'=>clean($_POST['vuntil']),'status'=>clean($_POST['status'])];
        if($act==='edit_promo'){DB::update('promo_codes',$d,'id=?',['id'=>(int)$_POST['id']]);flash('ok','Promo updated!');}
        else{DB::insert('promo_codes',$d);flash('ok','Promo created!');}
        header('Location: '.BASE.'/admin/index.php?sec=promos');exit;
    }
    if($act==='reply_ticket'){
        DB::update('support_tickets',['admin_reply'=>clean($_POST['reply']),'status'=>'Resolved'],'id=?',['id'=>(int)$_POST['tid']]);
        flash('ok','Reply sent');header('Location: '.BASE.'/admin/index.php?sec=support');exit;
    }
}

$me=me();
$secFile=__DIR__.'/sections/'.$sec.'.php';
if(!file_exists($secFile))$secFile=__DIR__.'/sections/dashboard.php';

// Quick stats for top bar
$qs_rev=DB::val("SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE DATE(created_at)=CURDATE() AND booking_status!='Cancelled'");
$qs_bk=DB::val("SELECT COUNT(*) FROM bookings WHERE DATE(created_at)=CURDATE()");
$qs_usr=DB::val("SELECT COUNT(*) FROM users WHERE role='user' AND is_active=1");
$qs_pending=DB::val("SELECT COUNT(*) FROM bookings WHERE booking_status='Pending'");

// Sidebar navigation groups
$sidebarGroups = [
  'Overview' => [
    'dashboard' => ['material-symbols-outlined' => 'dashboard', 'label' => 'Dashboard'],
  ],
  'Operations' => [
    'bookings' => ['material-symbols-outlined' => 'receipt_long', 'label' => 'Bookings'],
    'users'    => ['material-symbols-outlined' => 'group', 'label' => 'Users & Customers'],
  ],
  'Inventory' => [
    'flights'  => ['material-symbols-outlined' => 'flight', 'label' => 'Flights'],
    'hotels'   => ['material-symbols-outlined' => 'hotel', 'label' => 'Hotels'],
    'packages' => ['material-symbols-outlined' => 'inventory_2', 'label' => 'Packages'],
    'trains'   => ['material-symbols-outlined' => 'train', 'label' => 'Trains'],
    'buses'    => ['material-symbols-outlined' => 'directions_bus', 'label' => 'Buses'],
    'cabs'     => ['material-symbols-outlined' => 'local_taxi', 'label' => 'Cabs'],
    'cruises'  => ['material-symbols-outlined' => 'sailing', 'label' => 'Cruises'],
  ],
  'Marketing' => [
    'promos'   => ['material-symbols-outlined' => 'redeem', 'label' => 'Promotions'],
  ],
  'Insights' => [
    'revenue'  => ['material-symbols-outlined' => 'monitoring', 'label' => 'Revenue & Analytics'],
    'reviews'  => ['material-symbols-outlined' => 'star', 'label' => 'Reviews'],
    'support'  => ['material-symbols-outlined' => 'headset_mic', 'label' => 'Support Tickets'],
  ],
];
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Super Admin — TravelNest</title>
<meta name="csrf" content="<?= csrf() ?>">
<meta name="base" content="<?= BASE ?>">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE ?>/assets/css/style.css">
<style>
/* ═══════════════════════════════════════════════════════
   SUPER ADMIN — MAKEMYTRIP-STYLE 2026 DASHBOARD
   ═══════════════════════════════════════════════════════ */

/* --- Admin Top Nav --- */
.adm-topnav {
  background: #fff;
  border-bottom: 1px solid #eee;
  padding: 0 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
  position: sticky;
  top: 0;
  z-index: 300;
  box-shadow: 0 1px 8px rgba(0,0,0,0.04);
}
.adm-brand {
  display: flex;
  align-items: center;
  gap: 12px;
}
.adm-brand-logo {
  font-family: 'Inter', sans-serif;
  font-size: 24px;
  font-weight: 800;
  color: #008cff;
  letter-spacing: -0.8px;
}
.adm-brand-logo span {
  color: #0f172a;
}
.adm-brand-badge {
  background: linear-gradient(135deg, #008cff, #0070cc);
  color: #fff;
  padding: 4px 12px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}
.adm-search {
  flex: 1;
  max-width: 420px;
  margin: 0 32px;
  position: relative;
}
.adm-search input {
  width: 100%;
  padding: 10px 16px 10px 42px;
  border: 1.5px solid #eee;
  border-radius: 12px;
  font-size: 13px;
  background: #f8f9fa;
  transition: all 0.25s;
  outline: none;
}
.adm-search input:focus {
  border-color: #008cff;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(0,140,255,0.1);
}
.adm-search .material-symbols-outlined {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 18px;
  color: #94a3b8;
}
.adm-nav-right {
  display: flex;
  align-items: center;
  gap: 8px;
}
.adm-notif-btn {
  position: relative;
  background: none;
  border: 1px solid #eee;
  border-radius: 10px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}
.adm-notif-btn:hover {
  background: #f0f7ff;
  border-color: #008cff;
}
.adm-notif-btn .material-symbols-outlined {
  font-size: 20px;
  color: #64748b;
}
.adm-notif-dot {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 8px;
  height: 8px;
  background: #ef4444;
  border-radius: 50%;
  border: 2px solid #fff;
}
.adm-profile {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 6px 14px 6px 6px;
  border-radius: 12px;
  border: 1px solid #eee;
  cursor: pointer;
  transition: all 0.2s;
}
.adm-profile:hover {
  background: #f8f9fa;
  border-color: #ddd;
}
.adm-avatar {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: linear-gradient(135deg, #008cff, #0070cc);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-weight: 700;
  font-size: 14px;
}
.adm-profile-name {
  font-size: 13px;
  font-weight: 600;
  color: #0f172a;
}
.adm-profile-role {
  font-size: 10px;
  color: #94a3b8;
}

/* --- Admin Sidebar --- */
.adm-sidebar {
  width: 260px;
  background: #fff;
  border-right: 1px solid #f0f0f0;
  min-height: calc(100vh - 64px);
  padding: 16px 12px;
  position: fixed;
  top: 64px;
  left: 0;
  overflow-y: auto;
  z-index: 200;
  transition: width 0.3s;
}
.adm-sidebar::-webkit-scrollbar {
  width: 4px;
}
.adm-sidebar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 4px;
}
.adm-side-group {
  margin-bottom: 20px;
}
.adm-side-group-title {
  font-size: 10px;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 1px;
  padding: 0 16px;
  margin-bottom: 6px;
}
.adm-side-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 500;
  color: #64748b;
  text-decoration: none;
  transition: all 0.2s;
  margin-bottom: 2px;
  position: relative;
}
.adm-side-link:hover {
  background: #f0f7ff;
  color: #008cff;
}
.adm-side-link .material-symbols-outlined {
  font-size: 20px;
  transition: color 0.2s;
}
.adm-side-link:hover .material-symbols-outlined {
  color: #008cff;
}
.adm-side-link.active {
  background: linear-gradient(135deg, rgba(0,140,255,0.08), rgba(0,140,255,0.04));
  color: #008cff;
  font-weight: 600;
}
.adm-side-link.active::before {
  content: '';
  position: absolute;
  left: 0;
  top: 8px;
  bottom: 8px;
  width: 3px;
  background: #008cff;
  border-radius: 0 3px 3px 0;
}
.adm-side-link.active .material-symbols-outlined {
  color: #008cff;
}
.adm-side-count {
  margin-left: auto;
  background: #fef2f2;
  color: #ef4444;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: 700;
}

/* Live indicator */
.adm-live {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  background: rgba(239,68,68,0.06);
  border: 1px solid rgba(239,68,68,0.12);
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  color: #ef4444;
}
.adm-live-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #ef4444;
  animation: livePulse 1.5s ease-in-out infinite;
}
@keyframes livePulse {
  0%,100% { opacity: 1; box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
  50% { opacity: 0.7; box-shadow: 0 0 0 4px rgba(239,68,68,0); }
}

/* --- Main Content --- */
.adm-main {
  margin-left: 260px;
  padding: 24px;
  min-height: calc(100vh - 64px);
  background: #f8f9fb;
}

/* --- Platform Stats Banner --- */
.adm-platform-stats {
  display: flex;
  align-items: center;
  gap: 24px;
  padding: 12px 20px;
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  border-radius: 14px;
  margin-bottom: 20px;
  overflow: hidden;
  position: relative;
}
.adm-platform-stats::before {
  content: '';
  position: absolute;
  right: -40px;
  top: -40px;
  width: 200px;
  height: 200px;
  background: radial-gradient(circle, rgba(0,140,255,0.12) 0%, transparent 70%);
  border-radius: 50%;
}
.adm-ps-item {
  display: flex;
  align-items: center;
  gap: 8px;
  color: rgba(255,255,255,0.85);
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}
.adm-ps-item strong {
  color: #008cff;
  font-weight: 700;
}
.adm-ps-divider {
  width: 1px;
  height: 20px;
  background: rgba(255,255,255,0.15);
}

/* --- Quick Stats Mini Bar --- */
.adm-quick-stats {
  display: flex;
  gap: 8px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.adm-qs-item {
  flex: 1;
  min-width: 150px;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  background: #fff;
  border-radius: 12px;
  border: 1px solid #f0f0f0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.adm-qs-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.adm-qs-icon .material-symbols-outlined {
  font-size: 22px;
  color: #fff;
}
.adm-qs-val {
  font-size: 18px;
  font-weight: 700;
  font-family: 'Inter', sans-serif;
  color: #0f172a;
}
.adm-qs-label {
  font-size: 11px;
  color: #94a3b8;
  font-weight: 500;
}

/* Admin Menu Toggle Mobile */
.adm-menu-toggle {
  display: none;
  background: none;
  border: 1px solid #eee;
  color: #0f172a;
  padding: 6px 10px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 20px;
  line-height: 1;
}

/* Responsive */
@media (max-width: 960px) {
  .adm-menu-toggle { display: block; }
  .adm-sidebar {
    display: none;
    position: fixed;
    z-index: 400;
    width: 260px;
    top: 64px;
    left: 0;
    bottom: 0;
    background: #fff;
    box-shadow: 4px 0 20px rgba(0,0,0,0.1);
  }
  .adm-sidebar.mobile-open { display: block; }
  .adm-main { margin-left: 0; }
  .adm-search { display: none; }
  .adm-platform-stats { flex-wrap: wrap; gap: 12px; }
}
</style>
</head>
<body style="background:#f8f9fb">
<div class="toast" id="toast"></div>

<!-- ═══════ TOP NAVIGATION ═══════ -->
<nav class="adm-topnav">
  <div class="adm-brand">
    <button class="adm-menu-toggle" onclick="document.querySelector('.adm-sidebar').classList.toggle('mobile-open')">☰</button>
    <div class="adm-brand-logo">Travel<span>Nest</span></div>
    <span class="adm-brand-badge">Super Admin</span>
    <span class="adm-live"><span class="adm-live-dot"></span>Live</span>
  </div>

  <div class="adm-search">
    <span class="material-symbols-outlined">search</span>
    <input type="text" placeholder="Search bookings, users, hotels, flights...">
  </div>

  <div class="adm-nav-right">
    <a href="<?= BASE ?>/index.php" target="_blank" style="text-decoration:none;display:flex;align-items:center;gap:4px;padding:8px 14px;border-radius:10px;border:1px solid #eee;font-size:12px;font-weight:500;color:#64748b;transition:all .2s" onmouseover="this.style.borderColor='#008cff';this.style.color='#008cff'" onmouseout="this.style.borderColor='#eee';this.style.color='#64748b'">
      <span class="material-symbols-outlined" style="font-size:16px">language</span> View Site
    </a>
    <button class="adm-notif-btn" title="Notifications">
      <span class="material-symbols-outlined">notifications</span>
      <?php if($qs_pending > 0): ?><span class="adm-notif-dot"></span><?php endif; ?>
    </button>
    <div class="adm-profile">
      <div class="adm-avatar"><?= strtoupper(substr($me['name'],0,1)) ?></div>
      <div>
        <div class="adm-profile-name"><?= clean($me['name']) ?></div>
        <div class="adm-profile-role">Super Admin</div>
      </div>
    </div>
    <a href="<?= BASE ?>/logout.php" style="display:flex;align-items:center;padding:8px;border-radius:8px;border:1px solid #eee;transition:all .2s" title="Logout" onmouseover="this.style.background='#fef2f2';this.style.borderColor='#ef4444'" onmouseout="this.style.background='';this.style.borderColor='#eee'">
      <span class="material-symbols-outlined" style="font-size:18px;color:#94a3b8">logout</span>
    </a>
  </div>
</nav>

<?= getFlash() ?>

<div style="display:flex">
  <!-- ═══════ SIDEBAR ═══════ -->
  <aside class="adm-sidebar">
    <?php foreach($sidebarGroups as $group => $items): ?>
      <div class="adm-side-group">
        <div class="adm-side-group-title"><?= $group ?></div>
        <?php foreach($items as $key => $item):
          $isActive = ($sec === $key);
        ?>
          <a class="adm-side-link<?= $isActive ? ' active' : '' ?>"
             href="<?= BASE ?>/admin/index.php?sec=<?= $key ?>">
            <span class="material-symbols-outlined"><?= $item['material-symbols-outlined'] ?></span>
            <?= $item['label'] ?>
            <?php if($key === 'bookings' && $qs_pending > 0): ?>
              <span class="adm-side-count"><?= $qs_pending ?></span>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <!-- Platform Stats at bottom -->
    <div style="border-top:1px solid #f0f0f0;padding-top:16px;margin-top:8px">
      <div style="text-align:center;padding:12px">
        <div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px">Platform</div>
        <div style="font-size:20px;font-weight:800;color:#008cff;font-family:'Inter',sans-serif"><?= number_format($qs_usr) ?></div>
        <div style="font-size:11px;color:#64748b">Active Users</div>
      </div>
    </div>
  </aside>

  <!-- ═══════ MAIN CONTENT ═══════ -->
  <main class="adm-main">
    <!-- Platform Banner -->
    <div class="adm-platform-stats">
      <div class="adm-ps-item"><span class="material-symbols-outlined" style="font-size:16px;color:#008cff">verified</span> Serving <strong><?= number_format($qs_usr) ?>+</strong> Users</div>
      <div class="adm-ps-divider"></div>
      <div class="adm-ps-item"><span class="material-symbols-outlined" style="font-size:16px;color:#008cff">hotel</span> <strong>80,000+</strong> Hotels</div>
      <div class="adm-ps-divider"></div>
      <div class="adm-ps-item"><span class="material-symbols-outlined" style="font-size:16px;color:#008cff">flight</span> <strong>300+</strong> Airlines</div>
      <div class="adm-ps-divider"></div>
      <div class="adm-ps-item"><span class="material-symbols-outlined" style="font-size:16px;color:#008cff">public</span> <strong>120+</strong> Countries</div>
      <div class="adm-ps-divider"></div>
      <div class="adm-ps-item" style="margin-left:auto;color:rgba(255,255,255,.5);font-size:11px">
        <span class="material-symbols-outlined" style="font-size:14px">schedule</span>
        <?= date('D, d M Y · H:i') ?> IST
      </div>
    </div>

    <!-- Quick Stats Bar -->
    <div class="adm-quick-stats">
      <div class="adm-qs-item">
        <div class="adm-qs-icon" style="background:linear-gradient(135deg,#008cff,#0070cc)">
          <span class="material-symbols-outlined">payments</span>
        </div>
        <div>
          <div class="adm-qs-val"><?= rupee($qs_rev) ?></div>
          <div class="adm-qs-label">Today's Revenue</div>
        </div>
      </div>
      <div class="adm-qs-item">
        <div class="adm-qs-icon" style="background:linear-gradient(135deg,#2563eb,#1d4ed8)">
          <span class="material-symbols-outlined">confirmation_number</span>
        </div>
        <div>
          <div class="adm-qs-val"><?= $qs_bk ?></div>
          <div class="adm-qs-label">Today's Bookings</div>
        </div>
      </div>
      <div class="adm-qs-item">
        <div class="adm-qs-icon" style="background:linear-gradient(135deg,#16a34a,#15803d)">
          <span class="material-symbols-outlined">group</span>
        </div>
        <div>
          <div class="adm-qs-val"><?= number_format($qs_usr) ?></div>
          <div class="adm-qs-label">Active Users</div>
        </div>
      </div>
      <div class="adm-qs-item">
        <div class="adm-qs-icon" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
          <span class="material-symbols-outlined">pending_actions</span>
        </div>
        <div>
          <div class="adm-qs-val"><?= $qs_pending ?></div>
          <div class="adm-qs-label">Pending Actions</div>
        </div>
      </div>
    </div>

    <!-- Detail modal -->
    <div class="ov" id="det-modal">
      <div class="mod">
        <div class="mh"><h3 id="det-title"></h3><button class="mx" onclick="closeMod('det-modal')">✕</button></div>
        <div class="mb" id="det-body"></div>
      </div>
    </div>

    <?php require $secFile; ?>
  </main>
</div>
<script src="<?= BASE ?>/assets/js/app.js"></script>
</body></html>
