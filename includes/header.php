<?php
$_me = me();
$_pg = basename($_SERVER['SCRIPT_NAME'],'.php');
?><!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="theme-color" content="#008cff">
<title><?= $pageTitle ?? 'TravelNest — India\'s Travel Platform' ?></title>
<meta name="description" content="Book flights, hotels, trains, buses, cabs, cruises &amp; holiday packages at best prices. TravelNest — India's trusted travel platform.">
<meta name="csrf" content="<?= csrf() ?>">
<meta name="base" content="<?= BASE ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE ?>/assets/css/style.css?v=<?= time() ?>">
</head>
<body>

<!-- Skip to main content (keyboard / screen-reader accessibility) -->
<a class="skip-link" href="#main-content">Skip to main content</a>

<div class="toast" id="toast" role="status" aria-live="polite" aria-atomic="true"></div>

<!-- Mobile Drawer Overlay -->
<div class="nav-drawer-overlay" id="nav-overlay" aria-hidden="true" role="presentation"></div>

<!-- Mobile Navigation Drawer -->
<nav class="nav-drawer" id="nav-drawer" aria-label="Mobile navigation" aria-hidden="true">
  <button class="drawer-close" onclick="closeDrawer()" aria-label="Close navigation menu">✕</button>
  <div style="font-family:'Inter',sans-serif;font-size:22px;font-weight:700;color:var(--accent);margin-bottom:20px" aria-hidden="true">Travel<span style="color:var(--text)">Nest</span></div>
  <?php
  $mnavs = [
    'index'   => ['🏠','Home'],
    'offers'  => ['🎁','Offers'],
    'flights' => ['✈️','Flights'],
    'hotels'  => ['🏨','Hotels'],
    'packages'=> ['📦','Packages'],
    'trains'  => ['🚆','Trains'],
    'buses'   => ['🚌','Buses'],
    'cabs'    => ['🚕','Cabs'],
    'cruises' => ['🚢','Cruises'],
  ];
  foreach($mnavs as $p => $l){
    $isOn  = ($_pg === $p) ? ' on' : '';
    $aria  = $isOn ? ' aria-current="page"' : '';
    echo "<a class='$isOn' href='".BASE."/$p.php'$aria>{$l[0]} {$l[1]}</a>";
  }
  ?>
  <div style="border-top:1px solid var(--border);margin:16px 0;padding-top:16px">
  <?php if($_me): ?>
    <?php if($_me['role'] === 'admin'): ?>
      <a href="<?= BASE ?>/admin/index.php">📊 Dashboard</a>
    <?php else: ?>
      <a href="<?= BASE ?>/profile.php">👤 My Profile</a>
      <a href="<?= BASE ?>/wishlist.php">❤️ Wishlist</a>
      <a href="<?= BASE ?>/bookings.php">📋 My Bookings</a>
      <a href="<?= BASE ?>/support.php">🎧 Support</a>
    <?php endif; ?>
    <a href="<?= BASE ?>/logout.php">🚪 Logout</a>
  <?php else: ?>
    <a href="<?= BASE ?>/login.php">🔑 Login</a>
    <a href="<?= BASE ?>/register.php">✨ Sign Up</a>
  <?php endif; ?>
  </div>
</nav>

<!-- Main site navigation -->
<nav class="main-nav" role="navigation" aria-label="Main navigation">
  <a class="logo" href="<?= BASE ?>/index.php" aria-label="TravelNest — go to homepage">Travel<span>Nest</span></a>
  <div class="nav-links" role="menubar" aria-label="Site pages">
    <?php
    $navs = [
      'index'   => 'Home',
      'offers'  => 'Offers',
      'flights' => 'Flights',
      'hotels'  => 'Hotels',
      'packages'=> 'Packages',
      'trains'  => 'Trains',
      'buses'   => 'Buses',
      'cabs'    => 'Cabs',
      'cruises' => 'Cruises',
    ];
    foreach($navs as $p => $l){
      $isOn = ($_pg === $p) || ($_pg === 'home' && $p === 'index');
      $cls  = $isOn ? 'nb on' : 'nb';
      $aria = $isOn ? ' aria-current="page"' : '';
      echo "<a class='$cls' href='".BASE."/$p.php'$aria role='menuitem'>$l</a>";
    }
    ?>
  </div>
  <div class="nav-right">
    <?php if($_me): ?>
      <?php if($_me['role'] === 'admin'): ?>
        <span class="tag t-gold" style="font-size:11px">Admin</span>
        <a class="btn btn-primary btn-sm" href="<?= BASE ?>/admin/index.php">Dashboard</a>
        <a class="btn btn-ghost btn-sm"   href="<?= BASE ?>/logout.php">Logout</a>
      <?php else: ?>
        <a href="<?= BASE ?>/profile.php" class="sm" style="color:var(--text);font-weight:600;margin-right:8px;text-decoration:none"
           aria-label="Your profile: <?= clean(explode(' ',$_me['name'])[0]) ?>">
            Hi, <?= clean(explode(' ',$_me['name'])[0]) ?>
        </a>
        <a class="btn btn-ghost btn-sm" href="<?= BASE ?>/wishlist.php" title="Wishlist" aria-label="My wishlist">❤️</a>
        <a class="btn btn-ghost btn-sm" href="<?= BASE ?>/bookings.php">My Bookings</a>
        <a class="btn btn-ghost btn-sm" href="<?= BASE ?>/support.php">Support</a>
        <a class="btn btn-ghost btn-sm" href="<?= BASE ?>/logout.php">Logout</a>
      <?php endif; ?>
    <?php else: ?>
      <a class="btn btn-ghost btn-sm"   href="<?= BASE ?>/login.php">Login</a>
      <a class="btn btn-primary btn-sm" href="<?= BASE ?>/register.php">Sign Up</a>
    <?php endif; ?>
    <button class="btn btn-ghost btn-sm" onclick="toggleTheme()" id="theme-toggle"
            aria-label="Toggle dark mode" aria-pressed="false" title="Toggle Dark Mode">🌙</button>
    <button class="nav-toggle" onclick="openDrawer()"
            aria-label="Open navigation menu" aria-expanded="false" aria-controls="nav-drawer">☰</button>
  </div>
</nav>

<?= getFlash() ?>

<!-- Main content landmark -->
<main id="main-content" tabindex="-1" style="outline:none">
