<?php
require_once __DIR__ . '/includes/bootstrap.php';
$ref = clean($_GET['ref'] ?? '');
$adminView = isset($_GET['admin']) && isAdmin();
if ($adminView) {
  $bk = DB::one("SELECT * FROM bookings WHERE booking_ref=?", [$ref]);
} else {
  mustLogin();
  $me = me();
  $bk = DB::one("SELECT * FROM bookings WHERE booking_ref=? AND user_id=?", [$ref, $me['id']]);
}
if (!$bk) {
  header('Location: ' . BASE . '/bookings.php');
  exit;
}
$me2 = DB::one("SELECT * FROM users WHERE id=?", [$bk['user_id']]);
$cabImages = [
  'Maruti Suzuki Swift' => 'assets/images/cabs/1images.jpeg',
  'Hyundai i20' => 'assets/images/cabs/2images.jpeg',
  'Tata Tiago' => 'assets/images/cabs/3images.jpeg',
  'Maruti Suzuki Baleno' => 'assets/images/cabs/4images.jpeg',
  'Honda City' => 'assets/images/cabs/5images.jpeg',
  'Hyundai Verna' => 'assets/images/cabs/6images.jpeg',
  'Toyota Innova Crysta' => 'assets/images/cabs/7images.jpeg',
  'Mahindra XUV700' => 'assets/images/cabs/8images.jpeg',
  'Tata Nexon' => 'assets/images/cabs/9images.jpeg',
  'Kia Seltos' => 'assets/images/cabs/10images.jpeg',
  'MG Hector' => 'assets/images/cabs/11images.jpeg',
  'Hyundai Creta' => 'assets/images/cabs/12images.jpeg',
  'Toyota Fortuner' => 'assets/images/cabs/13images.jpeg',
  'Mahindra Thar' => 'assets/images/cabs/14images.jpeg',
  'Tata Harrier' => 'assets/images/cabs/15images.jpeg',
  'Kia Carnival' => 'assets/images/cabs/16images.jpeg',
  'Mercedes-Benz E-Class' => 'assets/images/cabs/17images.jpeg',
  'Toyota Camry' => 'assets/images/cabs/18images.jpeg',
  'BMW 5 Series' => 'assets/images/cabs/19images.jpeg',
  'Audi A6' => 'assets/images/cabs/20images.jpeg',
  // Legacy names fallback
  'Wagon R / Alto' => 'assets/images/cabs/1images.jpeg',
  'Swift Dzire / Etios' => 'assets/images/cabs/5images.jpeg',
  'Innova Crysta / XL6' => 'assets/images/cabs/7images.jpeg',
  'Honda City / Ciaz' => 'assets/images/cabs/5images.jpeg',
  'Mercedes E-Class / BMW 5' => 'assets/images/cabs/17images.jpeg',
  'Tata Nexon EV' => 'assets/images/cabs/9images.jpeg',
  'MG ZS EV' => 'assets/images/cabs/11images.jpeg',
  'Hyundai Ioniq 5' => 'assets/images/cabs/12images.jpeg',
  'BYD e6' => 'assets/images/cabs/3images.jpeg',
  'Tata Tiago EV' => 'assets/images/cabs/3images.jpeg',
  'BMW iX' => 'assets/images/cabs/19images.jpeg',
];
$defaultCabImg = 'assets/images/cabs/1images.jpeg';
$lat = 28.6139;
$lng = 77.209;
$loc = 'India';
$cab = null;
if (strtolower($bk['booking_type']) === 'hotel') {
  $h = DB::one("SELECT * FROM hotels WHERE id=?", [$bk['item_id']]);
  if ($h) {
    $lat = $h['latitude'];
    $lng = $h['longitude'];
    $loc = $h['name'] . ', ' . $h['city'];
  }
} elseif (strtolower($bk['booking_type']) === 'flight') {
  $f = DB::one("SELECT * FROM flights WHERE id=?", [$bk['item_id']]);
  if ($f) {
    $lat = 28.556;
    $lng = 77.1;
    $loc = $f['to_city'] . ' Airport';
  }
} elseif (strtolower($bk['booking_type']) === 'package') {
  $p = DB::one("SELECT * FROM packages WHERE id=?", [$bk['item_id']]);
  if ($p)
    $loc = $p['destination'];
} elseif (strtolower($bk['booking_type']) === 'cab') {
  $cab = DB::one("SELECT * FROM cabs WHERE id=?", [$bk['item_id']]);
  if ($cab) {
    $lat = 28.6139;
    $lng = 77.209;
    $loc = 'Delhi, India';
  }
} // Default location for cab
$mapUrl = "https://www.openstreetmap.org/export/embed.html?bbox=" . ($lng - .08) . "," . ($lat - .08) . "," . ($lng + .08) . "," . ($lat + .08) . "&layer=mapnik&marker=$lat,$lng";
$statusColor = ['Confirmed' => '#059669', 'Cancelled' => '#dc2626', 'Pending' => '#d97706', 'Completed' => '#0ea5e9'];
$stampCol = $statusColor[$bk['booking_status']] ?? '#6b7280';
// Static map image via OpenStreetMap tile (no iframe needed – works on all mobile browsers)
$zoom = 14;
$staticMapUrl = "https://staticmap.openstreetmap.de/staticmap.php?center={$lat},{$lng}&zoom={$zoom}&size=760x260&maptype=mapnik&markers={$lat},{$lng},red";
// Deep-link URLs per platform (set in JS)
$gmapWebUrl = "https://www.google.com/maps/dir/?api=1&destination=" . urlencode($lat . ',' . $lng);
?><!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Invoice <?= clean($ref) ?> — TravelNest</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@700&family=DM+Sans:wght@400;500;600&display=swap"
    rel="stylesheet">
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'DM Sans','Inter',sans-serif;background:#f8fafc;padding:24px;color:#0f172a}
    .wrap{max-width:780px;margin:0 auto}
    .actions{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap}
    .btn{display:inline-flex;align-items:center;gap:6px;padding:10px 22px;border:none;border-radius:10px;cursor:pointer;font-family:'DM Sans','Inter',sans-serif;font-size:14px;font-weight:500;text-decoration:none;transition:all .2s}
    .btn:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,.1)}
    .bp{background:#008cff;color:#fff}.bp:hover{background:#0070cc}
    .bg{background:#fff;color:#374151;border:1px solid #e2e8f0}.bg:hover{border-color:#008cff;color:#008cff}
    .inv{background:#fff;border-radius:16px;padding:40px;box-shadow:0 4px 24px rgba(0,0,0,.06);position:relative;overflow:hidden;border:1px solid #e2e8f0}
    .inv-stamp{position:absolute;top:50%;right:40px;transform:translateY(-50%) rotate(-20deg);font-size:32px;font-weight:700;padding:10px 28px;border:5px solid;border-radius:14px;opacity:.12;text-transform:uppercase;letter-spacing:3px;pointer-events:none;color:<?=$stampCol?>;border-color:<?=$stampCol?>}
    .ilogo{font-family:'Inter','DM Sans',sans-serif;font-size:28px;font-weight:700;color:#008cff;letter-spacing:-.5px}
    .ilogo span{color:#0f172a}
    .ih{display:flex;justify-content:space-between;align-items:flex-start;padding-bottom:20px;margin-bottom:20px;border-bottom:2px solid #008cff}
    table{width:100%;border-collapse:collapse;margin:14px 0;font-size:13px}
    th{background:#f8fafc;padding:10px 14px;text-align:left;font-size:12px;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.5px}
    td{padding:10px 14px;border-bottom:1px solid #f1f5f9}
    .total{background:linear-gradient(135deg,#f0f7ff,#ffedd5);padding:16px 20px;border-radius:10px;display:flex;justify-content:space-between;align-items:center;margin:16px 0;border:1px solid #fed7aa}
    .ig{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:20px}
    .ib{background:#f8fafc;border-radius:10px;padding:16px;border:1px solid #e2e8f0}
    .map-wrap{height:260px;border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;margin:14px 0;position:relative;background:#f1f5f9}
    .map-wrap iframe{position:absolute;top:-10px;left:-10px;width:calc(100% + 20px);height:calc(100% + 20px);border:none}
    .map-label{font-size:12px;color:#64748b;display:flex;align-items:center;gap:4px}
    .qr-box{display:flex;gap:16px;align-items:center;margin-top:20px;padding:16px;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0}
    .qr-placeholder{width:80px;height:80px;border:2px dashed #cbd5e1;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-direction:column;font-size:9px;color:#94a3b8;gap:2px;flex-shrink:0}
    .qr-placeholder::before{content:'▦';font-size:28px;opacity:.4}
    .note{margin-top:20px;padding:14px;background:#f8fafc;border-radius:10px;font-size:11px;color:#94a3b8;line-height:1.8;border:1px solid #e2e8f0}
    @media print{.actions{display:none}body{background:#fff;padding:0}.inv{box-shadow:none;padding:24px;border-radius:0;border:none}.map-wrap{break-inside:avoid}}
    @media(max-width:600px){.ig{grid-template-columns:1fr}.ih{flex-direction:column;gap:16px}.inv{padding:24px 18px}}
  </style>
</head>

<body>
  <div class="wrap">
    <div class="actions">
      <button class="btn bp" onclick="window.print()">🖨 Print / Save PDF</button>
      <a class="btn bg" href="<?= BASE ?>/bookings.php">← My Bookings</a>
      <button class="btn bg"
        onclick="navigator.clipboard?.writeText(window.location.href).then(()=>alert('Link copied!'))">🔗 Share</button>
    </div>
    <div class="inv">
      <div class="inv-stamp"><?= clean($bk['booking_status']) ?></div>
      <div class="ih">
        <div>
          <div class="ilogo">Travel<span>Nest</span></div>
          <div style="font-size:11px;color:#9ca3af;margin-top:4px">India's Most Trusted Travel Platform</div>
          <div style="font-size:11px;color:#9ca3af">CIN: U63040MH2020PTC123456 · GSTIN: 27AAACT1234A1Z5</div>
          <div style="font-size:11px;color:#9ca3af">1800-103-8747 · support@travelnest.com</div>
        </div>
        <div style="text-align:right">
          <div style="font-size:24px;font-weight:700;color:#0f172a;font-family:'Inter','DM Sans',sans-serif">TAX INVOICE</div>
          <div style="font-size:13px;color:#6b7280;margin-top:4px">#<?= clean($bk['booking_ref']) ?></div>
          <div style="font-size:12px;color:#6b7280">Date: <?= fmtDate($bk['created_at']) ?></div>
          <div style="font-size:12px;color:#6b7280">PNR: <?= clean($bk['pnr_number'] ?? 'N/A') ?></div>
        </div>
      </div>
      <div class="ig">
        <div class="ib">
          <div
            style="font-size:11px;color:#9ca3af;margin-bottom:6px;text-transform:uppercase;letter-spacing:.6px;font-weight:600">
            Billed To</div>
          <div style="font-weight:600;font-size:16px"><?= clean($bk['passenger_name'] ?? $me2['name'] ?? 'Guest') ?></div>
          <div style="font-size:13px;color:#6b7280"><?= clean($bk['passenger_email'] ?? $me2['email'] ?? '') ?></div>
          <div style="font-size:13px;color:#6b7280"><?= clean($bk['passenger_phone'] ?? '') ?></div>
        </div>
        <div class="ib" style="text-align:right">
          <div
            style="font-size:11px;color:#9ca3af;margin-bottom:8px;text-transform:uppercase;letter-spacing:.6px;font-weight:600">
            Status</div>
          <span
            style="background:<?= $bk['booking_status'] === 'Confirmed' ? '#d1fae5' : '#fef3c7' ?>;color:<?= $stampCol ?>;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600">✓
            <?= clean($bk['booking_status']) ?></span>
          <div style="font-size:12px;color:#9ca3af;margin-top:8px"><?= clean($bk['booking_type']) ?> ·
            <?= clean($bk['payment_method'] ?? 'UPI') ?></div>
          <?php if ($bk['travel_date']): ?>
            <div style="font-size:12px;font-weight:500;margin-top:4px">Travel: <?= fmtDate($bk['travel_date']) ?></div>
          <?php endif; ?>
        </div>
      </div>
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Description</th>
            <th>Details</th>
            <th>Pax</th>
            <th style="text-align:right">Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td><?= clean($bk['booking_type']) ?></td>
            <td style="color:#374151"><?= clean($bk['item_name']) ?></td>
            <td><?= $bk['passengers'] ?? 1 ?></td>
            <td style="text-align:right"><?= rupee($bk['base_amount']) ?></td>
          </tr>
          <tr>
            <td>2</td>
            <td>GST (12%)</td>
            <td>Central + State Tax</td>
            <td>—</td>
            <td style="text-align:right"><?= rupee($bk['tax_amount']) ?></td>
          </tr>
          <?php if ($bk['discount_amount'] > 0): ?>
            <tr>
              <td>3</td>
              <td style="color:#059669">Promo Discount</td>
              <td style="color:#059669">Code: <?= clean($bk['promo_code'] ?? '') ?></td>
              <td>—</td>
              <td style="text-align:right;color:#059669">-<?= rupee($bk['discount_amount']) ?></td>
            </tr><?php endif; ?>
        </tbody>
      </table>
      <div class="total"><span style="font-weight:600;color:#9a3412;font-size:15px">Total Amount Paid</span><span
          style="font-size:28px;font-weight:700;color:#008cff;font-family:'Inter','DM Sans',sans-serif"><?= rupee($bk['total_amount']) ?></span>
      </div>

      <div class="qr-box">
        <div class="qr-placeholder">Scan to<br>verify</div>
        <div>
          <div style="font-weight:600;font-size:13px;margin-bottom:4px">Digital Verification</div>
          <div style="font-size:11px;color:#9ca3af">Scan this QR code at the check-in counter for instant verification
            of your booking.</div>
        </div>
      </div>
      <?php if (!empty($cab)):
        $cabImg = $cabImages[$cab['vehicle_name']] ?? $defaultCabImg; ?>
        <div style="margin-top:20px;text-align:center">
          <img src="<?= $cabImg ?>" alt="Vehicle" style="max-width:200px;border-radius:10px;border:1px solid #e5e7eb">
          <div style="font-size:12px;color:#6b7280;margin-top:6px;font-weight:500"><?= clean($cab['vehicle_name']) ?></div>
        </div>
      <?php endif; ?>

      <div style="margin-top:20px">
        <div style="font-weight:600;margin-bottom:8px;font-size:13px">📍 Destination: <?= clean($loc) ?></div>

        <!-- Map container: iframe embed with overflow crop to eliminate scrollbars -->
        <div id="map-container" class="map-wrap" title="Tap to open in Maps app">
          <iframe id="map-iframe" src="<?= $mapUrl ?>" title="Destination Map" loading="lazy" scrolling="no" referrerpolicy="no-referrer"></iframe>
          <!-- Fallback: shown if iframe fails -->
          <div id="map-fallback" style="display:none;position:absolute;inset:0;width:100%;height:100%;background:linear-gradient(135deg,#f1f5f9,#e2e8f0);align-items:center;justify-content:center;flex-direction:column;gap:12px;z-index:1">
            <div style="font-size:48px">🗺️</div>
            <div style="font-size:14px;color:#374151;font-weight:500"><?= clean($loc) ?></div>
            <div style="font-size:12px;color:#64748b">Tap to open in Maps</div>
          </div>
          <!-- Click overlay -->
          <div id="map-click-overlay" style="position:absolute;inset:0;z-index:3;display:flex;align-items:flex-end;padding:12px;cursor:pointer;" onclick="openMaps()">
            <div style="background:rgba(15,23,42,.65);color:#fff;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:500;display:flex;align-items:center;gap:6px;backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,.15)">
              📌 <?= clean($loc) ?> — Tap to open in Maps
            </div>
          </div>
        </div>

        <div
          style="display:flex;align-items:center;justify-content:space-between;margin-top:10px;flex-wrap:wrap;gap:8px;">
          <div class="map-label">📌 <?= clean($loc) ?></div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a id="directions-link" href="<?= $gmapWebUrl ?>" target="_blank" rel="noopener"
              style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:#008cff;color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .2s;box-shadow:0 2px 8px rgba(0,140,255,.2)"
              onmouseover="this.style.background='#0070cc'" onmouseout="this.style.background='#008cff'">
              🗺️ Get Directions
            </a>
            <button onclick="openMaps()"
              style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:#0f172a;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:all .2s"
              onmouseover="this.style.background='#1e293b'" onmouseout="this.style.background='#0f172a'">
              📱 Open in Maps App
            </button>
          </div>
        </div>
      </div>
      <div class="note"><strong>Important:</strong> Carry this invoice and a valid govt-issued photo ID at the time of
        travel. Cancellation charges apply per fare rules. Refunds processed within 5–7 working
        days.<br><strong>Support:</strong> 1800-103-8747 · support@travelnest.com · 24/7</div>
    </div>
  </div>
  <script>
    (function () {
      const lat = <?= $lat ?>;
      const lng = <?= $lng ?>;
      const loc = <?= json_encode($loc) ?>;
      const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
      const isAndroid = /Android/.test(navigator.userAgent);

      // Build the best "open in maps" URL for the current platform
      function getMapsUrl() {
        if (isIOS) {
          // Apple Maps deep link — most reliable on iPhone/iPad
          return `maps://?daddr=${lat},${lng}&dirflg=d`;
        } else if (isAndroid) {
          // google.navigation is the most reliable deep link to open Google Maps turn-by-turn
          return `google.navigation:q=${lat},${lng}`;
        } else {
          // Desktop — open Google Maps in browser
          return `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
        }
      }

      // Fallback URL if native app isn't installed
      const webFallback = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;

      window.openMaps = function () {
        const nativeUrl = getMapsUrl();
        if (isAndroid) {
          // Try native Google Maps; if it fails (app not installed), open web
          const intentUrl = `intent://maps.google.com/maps?daddr=${lat},${lng}#Intent;scheme=https;package=com.google.android.apps.maps;end`;
          // Try intent URL first for Android
          window.location.href = intentUrl;
          // After short delay, if still here, fall back to web
          setTimeout(function () { window.open(webFallback, '_blank'); }, 1500);
        } else {
          window.location.href = nativeUrl;
          // Fallback for iOS if Maps not available
          if (isIOS) setTimeout(function () { window.open(webFallback, '_blank'); }, 1500);
        }
      };

      // Update the "Get Directions" link to use coordinate-based URL
      const directionsLink = document.getElementById('directions-link');
      if (directionsLink) {
        directionsLink.href = isAndroid || isIOS
          ? getMapsUrl()
          : `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
        if (isAndroid || isIOS) {
          directionsLink.removeAttribute('target');
          directionsLink.addEventListener('click', function (e) {
            e.preventDefault();
            window.openMaps();
          });
        }
      }

      // Also wire up the map container click
      const mapContainer = document.getElementById('map-container');
      if (mapContainer) {
        mapContainer.addEventListener('click', function (e) {
          // Only auto-open on mobile
          if (isAndroid || isIOS) {
            e.preventDefault();
            window.openMaps();
          } else {
            window.open(`https://www.google.com/maps/search/?api=1&query=${lat},${lng}`, '_blank');
          }
        });
      }
    })();
  </script>
</body>
</html>