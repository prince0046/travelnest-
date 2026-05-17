<?php
require_once __DIR__ . '/includes/bootstrap.php';
mustLogin();
$me = me();
$type = clean($_GET['type'] ?? '');
$id = (int) ($_GET['id'] ?? 0);
$tbl = ['flight' => 'flights', 'hotel' => 'hotels', 'package' => 'packages', 'train' => 'trains', 'bus' => 'buses', 'cab' => 'cabs', 'cruise' => 'cruises'];
if (!isset($tbl[$type]) || !$id) {
  header('Location: ' . BASE . '/index.php');
  exit;
}
$item = DB::one("SELECT * FROM {$tbl[$type]} WHERE id=? AND is_active=1", [$id]);
if (!$item) {
  header('Location: ' . BASE . "/$type" . 's.php');
  exit;
}
$price = (float) ($item['price'] ?? $item['price_per_night'] ?? $item['base_fare'] ?? $item['price_2a'] ?? 0);
$label = $item['name'] ?? ($item['cruise_name'] ?? ($item['train_name'] ?? ($item['operator_name'] ?? ($item['vehicle_name'] ?? ''))));
if ($type === 'flight')
  $label = $item['airline'] . ' ' . $item['flight_code'] . ' ' . $item['from_city'] . '→' . $item['to_city'];
if ($type === 'hotel')
  $label = $item['name'] . ', ' . $item['city'];
$tax = round($price * TAX_RATE);
$total = $price + $tax;
$err = '';
$bookingSuccess = false;
$successData = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!checkCsrf()) {
    $err = 'Invalid request. Refresh and try again.';
  } else {
    $pax = max(1, (int) ($_POST['pax'] ?? 1));
    $promo = strtoupper(clean($_POST['promo'] ?? ''));
    if ($type === 'train' && isset($_POST['tcls'])) {
      $cm = ['1A' => 'price_1a', '2A' => 'price_2a', '3A' => 'price_3a', 'SL' => 'price_sl'];
      $ck = clean($_POST['tcls'] ?? '');
      if (isset($cm[$ck]) && $item[$cm[$ck]] > 0)
        $price = (float) $item[$cm[$ck]];
    }
    $base = $price * $pax;
    $tax2 = round($base * TAX_RATE);
    $disc = 0;
    if ($promo) {
      $pc = DB::one("SELECT * FROM promo_codes WHERE code=? AND status IN('Active','Expiring') AND valid_until>=CURDATE() AND used_count<usage_limit", [$promo]);
      if ($pc && $base >= $pc['min_booking']) {
        $disc = $pc['discount_type'] === 'percentage' ? min(round($base * $pc['discount_value'] / 100), (float) $pc['max_discount']) : (float) $pc['discount_value'];
      } else {
        // Loyalty Discount check
        $loyalty = DB::one("SELECT * FROM bookings WHERE booking_ref=? AND user_id=? AND booking_status IN('Confirmed','Completed')", [$promo, $me['id']]);
        if ($loyalty) {
            $disc = round($base * 0.10); // 10% off for using a past booking ID
        }
      }
    }
    $ttl = max(0, $base + $tax2 - $disc);
    $ref = genRef();
    $pnr = genPNR();
    $payMethod = clean($_POST['pay'] ?? 'UPI');
    DB::insert('bookings', ['booking_ref' => $ref, 'user_id' => $me['id'], 'booking_type' => ucfirst($type), 'item_id' => $id, 'item_name' => $label, 'travel_date' => clean($_POST['tdate'] ?? ''), 'passengers' => $pax, 'base_amount' => $base, 'tax_amount' => $tax2, 'discount_amount' => $disc, 'total_amount' => $ttl, 'promo_code' => $promo ?: null, 'payment_method' => $payMethod, 'booking_status' => 'Confirmed', 'payment_status' => 'Paid', 'passenger_name' => clean($_POST['pname'] ?? $me['name']), 'passenger_email' => clean($_POST['pemail'] ?? $me['email']), 'passenger_phone' => clean($_POST['pphone'] ?? $me['phone'] ?? ''), 'pnr_number' => $pnr]);
    if ($promo && $disc > 0)
      DB::q("UPDATE promo_codes SET used_count=used_count+1 WHERE code=?", [$promo]);
    DB::q("UPDATE users SET total_bookings=total_bookings+1,total_spent=total_spent+? WHERE id=?", [$ttl, $me['id']]);
    $bookingSuccess = true;
    $txnId = 'TXN' . strtoupper(substr(md5($ref . time()), 0, 12));
    $successData = ['ref' => $ref, 'pnr' => $pnr, 'txn' => $txnId, 'amount' => $ttl, 'pay' => $payMethod, 'label' => $label, 'type' => ucfirst($type), 'pname' => clean($_POST['pname'] ?? $me['name']), 'pemail' => clean($_POST['pemail'] ?? $me['email'])];
    // Send booking confirmation email
    $successData['invoice_url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . BASE . '/invoice.php?ref=' . $ref;
    sendBookingEmail($successData);
  }
}
// If booking was successful, show confirmation page
if ($bookingSuccess) {
  $pageTitle = 'Payment Confirmed — TravelNest';
  require_once __DIR__ . '/includes/header.php';
  ?>
  <div class="sec" style="max-width:600px;text-align:center">
    <div class="pay-confirm-card">
      <div class="pay-confirm-anim">
        <div class="pay-confirm-circle">
          <svg class="pay-confirm-check" viewBox="0 0 52 52">
            <path class="pay-confirm-check-path" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
          </svg>
        </div>
      </div>
      <h2 style="font-size:28px;margin-bottom:8px;color:var(--green)">Payment Successful!</h2>
      <p class="sm mb8">Your booking has been confirmed and payment processed securely.</p>
      <div style="display:inline-flex;align-items:center;gap:6px;padding:6px 16px;background:rgba(22,163,74,.06);border:1px solid rgba(22,163,74,.2);border-radius:20px;font-size:12px;color:#16a34a;font-weight:500;margin-bottom:20px">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M8 1a4 4 0 014 4v2h1a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1h1V5a4 4 0 014-4z" fill="#16a34a"/></svg>
        Confirmation email sent to <strong><?= $successData['pemail'] ?></strong>
      </div>

      <div class="card2 mb16" style="text-align:left">
        <div class="info-row"><span class="sm">Transaction ID</span><span class="fw6" style="font-family:'Inter',sans-serif;color:var(--text);font-size:13px;letter-spacing:.3px"><?= $successData['txn'] ?></span></div>
        <div class="info-row"><span class="sm">Booking Ref</span><span class="fw6 acc"><?= $successData['ref'] ?></span></div>
        <div class="info-row"><span class="sm">PNR Number</span><span class="fw5"><?= $successData['pnr'] ?></span></div>
        <div class="info-row"><span class="sm">Booking</span><span class="fw5"><?= clean($successData['label']) ?></span></div>
        <div class="info-row"><span class="sm">Type</span><span class="tag t-blue"><?= $successData['type'] ?></span></div>
        <div class="info-row"><span class="sm">Passenger</span><span class="fw5"><?= $successData['pname'] ?></span></div>
        <div class="info-row"><span class="sm">Payment Method</span><span class="tag t-amber"><?= strtoupper($successData['pay']) ?></span></div>
        <div class="info-row" style="border-bottom:none"><span class="sm">Amount Paid</span><span class="fw6 acc" style="font-size:22px;font-family:'Inter',sans-serif"><?= rupee($successData['amount']) ?></span></div>
      </div>

      <div class="flex g8 cc mb8" style="flex-wrap:wrap">
        <a href="<?= BASE ?>/invoice.php?ref=<?= $successData['ref'] ?>" class="btn btn-primary btn-lg">📄 View Invoice</a>
        <a href="<?= BASE ?>/bookings.php" class="btn btn-ghost">My Bookings</a>
        <button class="btn btn-outline" onclick="navigator.clipboard?.writeText(window.location.origin+'<?= BASE ?>/invoice.php?ref=<?= $successData['ref'] ?>').then(()=>alert('Invoice link copied!'))">🔗 Share Invoice</button>
      </div>
      <div class="pgw-razorpay-footer" style="border:none;margin-top:8px">
        <svg width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M8 1a4 4 0 014 4v2h1a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1h1V5a4 4 0 014-4z" fill="#94a3b8"/></svg>
        <span style="font-size:11px">Secured by <strong>Razorpay</strong> · PCI DSS Level 1</span>
      </div>

      <p class="xs" style="color:var(--text3)">Redirecting to invoice in <span id="countdown">5</span> seconds...</p>
    </div>
  </div>
  <style>
    .pay-confirm-card {
      animation: confirmFadeIn .6s ease
    }

    @keyframes confirmFadeIn {
      from {
        opacity: 0;
        transform: translateY(20px)
      }

      to {
        opacity: 1;
        transform: translateY(0)
      }
    }

    .pay-confirm-anim {
      width: 100px;
      height: 100px;
      margin: 0 auto 24px;
      position: relative
    }

    .pay-confirm-circle {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      background: rgba(52, 211, 153, .1);
      border: 3px solid var(--green);
      display: flex;
      align-items: center;
      justify-content: center;
      animation: confirmCirclePop .6s cubic-bezier(.4, 0, .2, 1)
    }

    @keyframes confirmCirclePop {
      0% {
        transform: scale(0);
        opacity: 0
      }

      60% {
        transform: scale(1.2)
      }

      100% {
        transform: scale(1);
        opacity: 1
      }
    }

    .pay-confirm-check {
      width: 40px;
      height: 40px
    }

    .pay-confirm-check-path {
      stroke: var(--green);
      stroke-width: 4;
      stroke-linecap: round;
      stroke-linejoin: round;
      stroke-dasharray: 48;
      stroke-dashoffset: 48;
      animation: confirmCheckDraw .5s .4s ease forwards
    }

    @keyframes confirmCheckDraw {
      to {
        stroke-dashoffset: 0
      }
    }
  </style>
  <script>
    let cd = 5; const cdEl = document.getElementById('countdown');
    const cdTimer = setInterval(() => { cd--; if (cdEl) cdEl.textContent = cd; if (cd <= 0) { clearInterval(cdTimer); window.location = '<?= BASE ?>/invoice.php?ref=<?= $successData['ref'] ?>'; } }, 1000);
  </script>
  <?php require_once __DIR__ . '/includes/footer.php';
  exit;
}
$pageTitle = 'Book — TravelNest';
require_once __DIR__ . '/includes/header.php'; ?>
<div class="sec" style="max-width:860px">
  <h2 class="stitle tc">Complete Your Booking</h2>
  <p class="ssub tc">Secure checkout — your data is encrypted</p>
  <?php if ($err): ?>
    <div class="flash err"><?= clean($err) ?></div><?php endif; ?>

  <!-- Wizard Progress -->
  <div class="wizard-progress">
    <div class="wizard-step active" onclick="wizardGo(1)"><span class="step-num">1</span> Traveller</div>
    <div class="wizard-connector"></div>
    <div class="wizard-step" onclick="wizardGo(2)"><span class="step-num">2</span> Add-ons</div>
    <div class="wizard-connector"></div>
    <div class="wizard-step" onclick="wizardGo(3)"><span class="step-num">3</span> Payment</div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start">
    <form method="POST" id="book-form">
      <input type="hidden" name="csrf" value="<?= csrf() ?>">
      <input type="hidden" id="base-amt" name="_base" value="<?= $price ?>">
      <input type="hidden" id="disc-amt" name="discount_applied" value="0">

      <!-- Step 1: Traveller Details -->
      <div class="step-panel active">
        <div class="card mb16">
          <div class="flex g12 mb16">
            <div>
              <div class="fw6" style="font-size:15px"><?= clean($label) ?></div>
              <?php if ($type === 'flight'): ?>
                <div class="sm mt4"><?= $item['departure_time'] ?> → <?= $item['arrival_time'] ?> ·
                  <?= clean($item['duration']) ?> · <?= $item['stops'] ?>
                </div><?php endif; ?>
              <?php if ($type === 'hotel'): ?>
                <div class="sm mt4">📍 <?= clean($item['city']) ?> · <?= str_repeat('★', (int) $item['stars']) ?> ·
                  <?= clean($item['meal_plan'] ?? '') ?>
                </div><?php endif; ?>
              <?php if ($type === 'train'): ?>
                <div class="sm mt4"><?= clean($item['from_station']) ?> → <?= clean($item['to_station']) ?> ·
                  <?= $item['departure_time'] ?> → <?= $item['arrival_time'] ?>
                </div><?php endif; ?>
              <?php if ($type === 'package'): ?>
                <div class="sm mt4">📍 <?= clean($item['destination']) ?> · 🌙 <?= $item['nights'] ?> Nights</div>
              <?php endif; ?>
              <?php if ($type === 'bus'): ?>
                <div class="sm mt4"><?= clean($item['from_city']) ?> → <?= clean($item['to_city']) ?> ·
                  <?= $item['departure_time'] ?> · <?= clean($item['bus_type'] ?? '') ?>
                </div><?php endif; ?>
            </div>
          </div>
          <h3 style="font-size:15px;margin-bottom:16px">👤 Passenger Details</h3>
          <div class="g2 mb12">
            <div class="fg"><label>Full Name</label><input name="pname" value="<?= clean($me['name']) ?>" required>
            </div>
            <div class="fg"><label>Email</label><input type="email" name="pemail" value="<?= clean($me['email']) ?>"
                required></div>
          </div>
          <div class="g2 mb12">
            <div class="fg"><label>Phone</label><input name="pphone" value="<?= clean($me['phone'] ?? '') ?>" required>
            </div>
            <div class="fg"><label>Travel Date</label><input type="text" name="tdate" placeholder="dd/mm/yyyy"
                pattern="\d{2}/\d{2}/\d{4}" maxlength="10" title="Format: dd/mm/yyyy" oninput="formatDateInput(event)"
                required></div>
          </div>
          <?php if ($type === 'train'): ?>
            <div class="g2 mb12">
              <div class="fg"><label>Class</label><select name="tcls" onchange="updateTrain(this)">
                  <?php foreach (['1A' => 'price_1a', '2A' => 'price_2a', '3A' => 'price_3a', 'SL' => 'price_sl'] as $lbl => $col):
                    if ($item[$col] > 0): ?>
                      <option value="<?= $lbl ?>" data-price="<?= $item[$col] ?>"><?= $lbl ?> — <?= rupee($item[$col]) ?>
                      </option>
                    <?php endif; endforeach; ?>
                </select></div>
              <div class="fg"><label>Quota</label><select name="quota">
                  <option>General</option>
                  <option>Tatkal</option>
                  <option>Ladies</option>
                  <option>Senior Citizen</option>
                </select></div>
            </div>
          <?php endif; ?>
          <div class="fg"><label>Passengers / Guests</label><select name="pax" id="pax" onchange="recalcTotal()">
              <option value="1">1</option><?php for ($i = 2; $i <= 6; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?>
            </select></div>
        </div>
        <button type="button" class="btn btn-primary w100" onclick="wizardGo(2)">Continue to Add-ons →</button>
      </div>

      <!-- Step 2: Add-ons -->
      <div class="step-panel">
        <div class="card mb16">
          <h3 style="font-size:15px;margin-bottom:16px">🎁 Enhance Your Trip</h3>
          <div style="display:grid;gap:10px">
            <div class="addon-item" data-price="199" onclick="toggleAddon(this)">
              <input type="checkbox" name="addons[]" value="travel_insurance" style="display:none">
              <div class="addon-icon">🛡️</div>
              <div style="flex:1">
                <div class="fw5" style="font-size:14px">Travel Insurance</div>
                <div class="xs">Trip cancellation, medical emergency & baggage loss cover up to ₹5,00,000</div>
              </div>
              <div class="tr">
                <div class="fw6 acc">₹199</div>
                <div class="xs">per person</div>
              </div>
              <div class="addon-check">✓</div>
            </div>
            <div class="addon-item" data-price="349" onclick="toggleAddon(this)">
              <input type="checkbox" name="addons[]" value="meal" style="display:none">
              <div class="addon-icon">🍽️</div>
              <div style="flex:1">
                <div class="fw5" style="font-size:14px">Meal Preference</div>
                <div class="xs">Pre-book your meal — Veg / Non-veg / Jain options available</div>
              </div>
              <div class="tr">
                <div class="fw6 acc">₹349</div>
                <div class="xs">per person</div>
              </div>
              <div class="addon-check">✓</div>
            </div>
            <div class="addon-item" data-price="499" onclick="toggleAddon(this)">
              <input type="checkbox" name="addons[]" value="extra_baggage" style="display:none">
              <div class="addon-icon">🧳</div>
              <div style="flex:1">
                <div class="fw5" style="font-size:14px">Extra Baggage (15 kg)</div>
                <div class="xs">Additional 15 kg checked baggage allowance</div>
              </div>
              <div class="tr">
                <div class="fw6 acc">₹499</div>
                <div class="xs">one-time</div>
              </div>
              <div class="addon-check">✓</div>
            </div>
            <div class="addon-item" data-price="299" onclick="toggleAddon(this)">
              <input type="checkbox" name="addons[]" value="priority_seat" style="display:none">
              <div class="addon-icon">💺</div>
              <div style="flex:1">
                <div class="fw5" style="font-size:14px">Priority Seating</div>
                <div class="xs">Choose your preferred seat — window, aisle, extra legroom</div>
              </div>
              <div class="tr">
                <div class="fw6 acc">₹299</div>
                <div class="xs">per person</div>
              </div>
              <div class="addon-check">✓</div>
            </div>
            <div class="addon-item" data-price="599" onclick="toggleAddon(this)">
              <input type="checkbox" name="addons[]" value="airport_transfer" style="display:none">
              <div class="addon-icon">🚐</div>
              <div style="flex:1">
                <div class="fw5" style="font-size:14px">Airport / Station Transfer</div>
                <div class="xs">AC cab pickup & drop from airport or station to hotel</div>
              </div>
              <div class="tr">
                <div class="fw6 acc">₹599</div>
                <div class="xs">one-way</div>
              </div>
              <div class="addon-check">✓</div>
            </div>
          </div>
          <div class="flex sb mt12 card2 p12" id="addon-total-row" style="display:none">
            <span class="sm fw5">Add-ons Total</span>
            <span class="fw6 acc" id="addon-total-val">₹0</span>
          </div>
        </div>
        <div class="card mb16">
          <h3 style="font-size:15px;margin-bottom:14px">🎟️ Promo Code</h3>
          <div class="flex g8"><input id="promo-in" name="promo" placeholder="FIRST50 · SUMMER25 · HOLI2026..."
              style="text-transform:uppercase"><button type="button" class="btn btn-ghost"
              onclick="checkPromo()">Apply</button></div>
          <div id="promo-msg" class="mt8"></div>
        </div>
        <div class="flex g8">
          <button type="button" class="btn btn-ghost w100" onclick="wizardGo(1)">← Back</button>
          <button type="button" class="btn btn-primary w100" onclick="wizardGo(3)">Continue to Payment →</button>
        </div>
      </div>

      <!-- Step 3: Payment -->
      <div class="step-panel">
        <div class="card mb20">
          <h3 style="font-size:15px;margin-bottom:14px">💳 Select Payment Method</h3>

          <!-- Payment Method Tabs -->
          <div class="pgw-tabs">
            <button type="button" class="pgw-tab active" data-method="card" onclick="switchPayTab(this,'card')">
              <svg width="28" height="20" viewBox="0 0 36 24" fill="none"><rect x=".5" y=".5" width="35" height="23" rx="3.5" fill="#1A1F71" stroke="#1A1F71"/><path d="M14.8 16.2H12.5L14 8h2.3l-1.5 8.2zm-4-8.2L8.7 14l-.3-1.5L7.4 9a.9.9 0 00-1-.7H3l-.1.3c1 .3 2 .6 2.7 1L8 16.2h2.3l3.5-8.2h-2zm18.8 8.2h2L29.8 8H28a1 1 0 00-1 .6l-3.2 7.6h2.3l.4-1.2h2.7l.3 1.2zm-2.3-3l1.1-3.2.7 3.2h-1.8zM23 10l.3-1.7a6 6 0 00-2-.4c-2 0-3.4 1.1-3.4 2.6 0 1.2 1 1.8 1.8 2.2.8.4 1 .6 1 1 0 .5-.6.8-1.2.8a4 4 0 01-2-.4l-.3 1.7a5.5 5.5 0 002.2.4c2.2 0 3.5-1 3.5-2.7 0-2-3-2.2-3-3.1 0-.4.4-.8 1.2-.8a4 4 0 011.8.4z" fill="#fff"/></svg>
              <span>Credit / Debit Card</span>
            </button>
            <button type="button" class="pgw-tab" data-method="upi" onclick="switchPayTab(this,'upi')">
              <svg width="28" height="20" viewBox="0 0 60 36" fill="none"><rect width="60" height="36" rx="4" fill="#fff" stroke="#e2e8f0"/><path d="M20 6l5 12-5 12" stroke="#097939" stroke-width="3.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/><path d="M27 6l5 12-5 12" stroke="#ED752E" stroke-width="3.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/><text x="36" y="23" font-family="Arial" font-weight="700" font-size="10" fill="#333">UPI</text></svg>
              <span>UPI</span>
            </button>
            <button type="button" class="pgw-tab" data-method="wallet" onclick="switchPayTab(this,'wallet')">
              <svg width="22" height="20" viewBox="0 0 24 24" fill="none"><rect x="1" y="4" width="22" height="17" rx="3" stroke="#008cff" stroke-width="1.5" fill="rgba(0,140,255,.06)"/><rect x="15" y="11" width="5" height="4" rx="1" fill="#008cff"/><circle cx="17.5" cy="13" r=".8" fill="#fff"/><path d="M5 4V3a2 2 0 012-2h10a2 2 0 012 2v1" stroke="#008cff" stroke-width="1.5"/></svg>
              <span>Wallets</span>
            </button>
            <button type="button" class="pgw-tab" data-method="nb" onclick="switchPayTab(this,'nb')">
              <svg width="22" height="20" viewBox="0 0 24 24" fill="none"><path d="M3 21h18M4 17h16M5 10h14M12 3l9 7H3l9-7z" stroke="#1e40af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 10v7M12 10v7M17 10v7" stroke="#1e40af" stroke-width="1.5"/></svg>
              <span>Net Banking</span>
            </button>
          </div>

          <input type="hidden" name="pay" id="pay-method-val" value="card">

          <!-- ═══ Card Panel ═══ -->
          <div class="pgw-panel active" id="pgw-panel-card">
            <div class="pgw-card-head">
              <div class="fw5" style="font-size:13px">Enter Card Details</div>
              <div class="pgw-card-logos" id="card-type-logos">
                <svg width="40" height="26" viewBox="0 0 48 32" class="card-logo visa-logo active"><rect width="48" height="32" rx="4" fill="#1A1F71"/><path d="M19.4 21.2h-2.8L18.5 11h2.8l-1.9 10.2zm-5-10.2l-2.6 7-0.3-1.6-.9-4.7a1.1 1.1 0 00-1.2-.8H5.1l-.1.3a9 9 0 013.3 1.3l2.5 6.7h2.8L17.2 11h-2.8zm23.5 10.2h2.4L38 11h-2.2a1.1 1.1 0 00-1.1.7l-3.9 9.5h2.8l.5-1.5h3.3l.4 1.5zm-2.8-3.7l1.4-3.8.8 3.8h-2.2zM29 13.2l.4-2.2a7.3 7.3 0 00-2.4-.5c-2.5 0-4.2 1.3-4.2 3.3 0 1.4 1.3 2.2 2.3 2.7 1 .5 1.3.8 1.3 1.3 0 .7-.8 1-1.5 1a5.4 5.4 0 01-2.5-.5l-.4 2.2a8 8 0 002.7.5c2.7 0 4.4-1.3 4.4-3.4 0-2.6-3.6-2.7-3.6-3.9 0-.5.5-1 1.5-1a5 5 0 012.1.5z" fill="#fff"/></svg>
                <svg width="40" height="26" viewBox="0 0 48 32" class="card-logo mc-logo"><rect width="48" height="32" rx="4" fill="#252525"/><circle cx="19" cy="16" r="8" fill="#EB001B" opacity=".9"/><circle cx="29" cy="16" r="8" fill="#F79E1B" opacity=".9"/><path d="M24 9.6a8 8 0 013 6.4 8 8 0 01-3 6.4 8 8 0 01-3-6.4 8 8 0 013-6.4z" fill="#FF5F00"/></svg>
              </div>
            </div>
            <div class="fg">
              <label>Card Number</label>
              <div class="pgw-card-input-wrap">
                <input type="text" name="card_number" id="card-number" placeholder="1234 5678 9012 3456" maxlength="19" oninput="fmtCard(this)" autocomplete="cc-number">
                <span class="pgw-card-detected" id="card-detected-icon"></span>
              </div>
            </div>
            <div class="g2">
              <div class="fg"><label>Expiry (MM/YY)</label><input type="text" name="card_expiry" id="card-expiry" placeholder="MM/YY" maxlength="5" oninput="fmtExpiry(this)" autocomplete="cc-exp"></div>
              <div class="fg"><label>CVV</label><input type="password" name="card_cvv" id="card-cvv" placeholder="•••" maxlength="4" autocomplete="cc-csc"></div>
            </div>
            <div class="fg"><label>Name on Card</label><input type="text" name="card_name" id="card-name" placeholder="As printed on card" autocomplete="cc-name"></div>
            <div class="pgw-encryption-notice">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M8 1a4 4 0 014 4v2h1a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1h1V5a4 4 0 014-4zm2 6V5a2 2 0 10-4 0v2h4z" fill="#16a34a"/></svg>
              <span>End-to-end encrypted · Your card details are never stored</span>
            </div>
          </div>

          <!-- ═══ UPI Panel ═══ -->
          <div class="pgw-panel" id="pgw-panel-upi">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">
              <div>
                <div class="fw5 mb12" style="font-size:13px">Scan QR Code to Pay</div>
                <div class="pgw-qr-box" id="upi-qr-box">
                  <img id="upi-qr-img" src="https://chart.googleapis.com/chart?cht=qr&chs=200x200&chl=upi%3A%2F%2Fpay%3Fpa%3Dtravelnest%40razorpay%26pn%3DTravelNest%26am%3D<?= $total ?>&choe=UTF-8" alt="UPI QR Code" width="180" height="180">
                  <div class="xs mt8" style="text-align:center;color:var(--text3)">Scan with any UPI app</div>
                </div>
              </div>
              <div>
                <div class="fw5 mb12" style="font-size:13px">Or enter UPI ID</div>
                <div class="fg"><input type="text" name="upi_id" id="upi-id-input" placeholder="yourname@upi" style="font-size:14px"></div>
                <div class="fw5 mb8 mt16" style="font-size:13px">Select UPI App</div>
                <div class="pgw-wallet-rows">
                  <label class="pgw-wallet-row selected" onclick="selectUpiApp(this,'gpay')">
                    <input type="radio" name="upi_app" value="gpay" checked style="display:none">
                    <svg width="28" height="28" viewBox="0 0 48 48"><path d="M24 2A22 22 0 112 24 22 22 0 0124 2z" fill="#fff"/><path d="M34.8 22.4L27 28.7l-2.5-4.3 7.8-6.3z" fill="#EA4335"/><path d="M34.8 22.4l-2.5 4.3-7.8-6.3 2.5-4.3z" fill="#FBBC04"/><path d="M24.5 20.4l-2.5 4.3-7.7-6.3 2.5-4.3z" fill="#4285F4"/><path d="M22 24.7l-2.5 4.3-7.7-6.3 2.5-4.3z" fill="#34A853"/></svg>
                    <span class="fw5">Google Pay</span>
                    <span class="pgw-row-check">✓</span>
                  </label>
                  <label class="pgw-wallet-row" onclick="selectUpiApp(this,'phonepe')">
                    <input type="radio" name="upi_app" value="phonepe" style="display:none">
                    <svg width="28" height="28" viewBox="0 0 48 48"><circle cx="24" cy="24" r="22" fill="#5F259F"/><path d="M20 12h4a6 6 0 016 6v2l-6 12h-4l6-12v-2a2 2 0 00-2-2h-4V12z" fill="#fff"/></svg>
                    <span class="fw5">PhonePe</span>
                    <span class="pgw-row-check">✓</span>
                  </label>
                  <label class="pgw-wallet-row" onclick="selectUpiApp(this,'paytm')">
                    <input type="radio" name="upi_app" value="paytm" style="display:none">
                    <svg width="28" height="28" viewBox="0 0 48 48"><circle cx="24" cy="24" r="22" fill="#00BAF2"/><text x="12" y="29" font-family="Arial" font-weight="700" font-size="14" fill="#fff">Pay</text></svg>
                    <span class="fw5">Paytm</span>
                    <span class="pgw-row-check">✓</span>
                  </label>
                </div>
              </div>
            </div>
            <div class="pgw-encryption-notice mt12">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M8 1a4 4 0 014 4v2h1a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1h1V5a4 4 0 014-4zm2 6V5a2 2 0 10-4 0v2h4z" fill="#16a34a"/></svg>
              <span>UPI payments protected by RBI guidelines · Your money is safe</span>
            </div>
          </div>

          <!-- ═══ Wallet Panel ═══ -->
          <div class="pgw-panel" id="pgw-panel-wallet">
            <div class="fw5 mb12" style="font-size:13px">Choose a Wallet</div>
            <div class="pgw-wallet-rows">
              <label class="pgw-wallet-row selected" onclick="selectWallet(this,'gpay')">
                <input type="radio" name="wallet_app" value="gpay" checked style="display:none">
                <svg width="32" height="32" viewBox="0 0 48 48"><path d="M24 2A22 22 0 112 24 22 22 0 0124 2z" fill="#fff" stroke="#e5e7eb"/><path d="M34.8 22.4L27 28.7l-2.5-4.3 7.8-6.3z" fill="#EA4335"/><path d="M34.8 22.4l-2.5 4.3-7.8-6.3 2.5-4.3z" fill="#FBBC04"/><path d="M24.5 20.4l-2.5 4.3-7.7-6.3 2.5-4.3z" fill="#4285F4"/><path d="M22 24.7l-2.5 4.3-7.7-6.3 2.5-4.3z" fill="#34A853"/></svg>
                <div>
                  <div class="fw5">Google Pay</div>
                  <div class="xs">Pay via GPay wallet</div>
                </div>
                <span class="pgw-row-check">✓</span>
              </label>
              <label class="pgw-wallet-row" onclick="selectWallet(this,'paypal')">
                <input type="radio" name="wallet_app" value="paypal" style="display:none">
                <svg width="32" height="32" viewBox="0 0 48 48"><circle cx="24" cy="24" r="22" fill="#003087" stroke="#002f6c"/><path d="M19 35l1-7h3c5 0 8-3 9-7 .6-3-.4-5-2.4-6.4A8.4 8.4 0 0024 13h-6a1.3 1.3 0 00-1.3 1.1l-3.4 20a.8.8 0 00.8.9H19z" fill="#fff"/><path d="M31.4 14.6c-.5 5-4.2 8.4-9.4 8.4h-2l-1.4 8.4h3.4c4.6 0 8.2-3 8.8-7.6l.6-3.6c.4-2.4-.4-4.2-2-5.2z" fill="#009cde" opacity=".6"/></svg>
                <div>
                  <div class="fw5">PayPal</div>
                  <div class="xs">International wallet</div>
                </div>
                <span class="pgw-row-check">✓</span>
              </label>
              <label class="pgw-wallet-row" onclick="selectWallet(this,'phonepe')">
                <input type="radio" name="wallet_app" value="phonepe" style="display:none">
                <svg width="32" height="32" viewBox="0 0 48 48"><circle cx="24" cy="24" r="22" fill="#5F259F"/><path d="M20 12h4a6 6 0 016 6v2l-6 12h-4l6-12v-2a2 2 0 00-2-2h-4V12z" fill="#fff"/></svg>
                <div>
                  <div class="fw5">PhonePe</div>
                  <div class="xs">PhonePe wallet balance</div>
                </div>
                <span class="pgw-row-check">✓</span>
              </label>
              <label class="pgw-wallet-row" onclick="selectWallet(this,'paytm')">
                <input type="radio" name="wallet_app" value="paytm" style="display:none">
                <svg width="32" height="32" viewBox="0 0 48 48"><circle cx="24" cy="24" r="22" fill="#00BAF2" stroke="#009dd9"/><text x="10" y="29" font-family="Arial" font-weight="800" font-size="14" fill="#fff">Pay</text><text x="30" y="29" font-family="Arial" font-weight="500" font-size="8" fill="#fff">tm</text></svg>
                <div>
                  <div class="fw5">Paytm</div>
                  <div class="xs">Paytm wallet / postpaid</div>
                </div>
                <span class="pgw-row-check">✓</span>
              </label>
            </div>
          </div>

          <!-- ═══ Net Banking Panel ═══ -->
          <div class="pgw-panel" id="pgw-panel-nb">
            <div class="fw5 mb12" style="font-size:13px">Select Your Bank</div>
            <div class="fg">
              <select name="nb_bank" id="nb-bank-select" style="font-size:14px;padding:14px 16px">
                <option value="">— Choose your bank —</option>
                <option value="SBI">State Bank of India (SBI)</option>
                <option value="HDFC">HDFC Bank</option>
                <option value="ICICI">ICICI Bank</option>
                <option value="Axis">Axis Bank</option>
                <option value="Kotak">Kotak Mahindra Bank</option>
                <option value="PNB">Punjab National Bank</option>
                <option value="BOB">Bank of Baroda</option>
                <option value="Yes">Yes Bank</option>
                <option value="IDFC">IDFC First Bank</option>
                <option value="IndusInd">IndusInd Bank</option>
                <option value="Union">Union Bank of India</option>
                <option value="Canara">Canara Bank</option>
                <option value="Federal">Federal Bank</option>
                <option value="IOB">Indian Overseas Bank</option>
              </select>
            </div>
            <div class="pgw-popular-banks">
              <div class="xs fw6 mb8" style="text-transform:uppercase;letter-spacing:.5px">Popular Banks</div>
              <div class="pgw-bank-chips">
                <button type="button" class="pgw-bank-chip" onclick="quickBank(this,'SBI')">
                  <svg width="20" height="20" viewBox="0 0 24 24"><circle cx="12" cy="12" r="11" fill="#1a4f9e" stroke="#1a4f9e"/><text x="5" y="16" font-family="Arial" font-weight="700" font-size="9" fill="#fff">SBI</text></svg>
                  SBI
                </button>
                <button type="button" class="pgw-bank-chip" onclick="quickBank(this,'HDFC')">
                  <svg width="20" height="20" viewBox="0 0 24 24"><circle cx="12" cy="12" r="11" fill="#004c8f"/><text x="3" y="16" font-family="Arial" font-weight="700" font-size="7" fill="#fff">HDFC</text></svg>
                  HDFC
                </button>
                <button type="button" class="pgw-bank-chip" onclick="quickBank(this,'ICICI')">
                  <svg width="20" height="20" viewBox="0 0 24 24"><circle cx="12" cy="12" r="11" fill="#f58220"/><text x="3" y="16" font-family="Arial" font-weight="700" font-size="7" fill="#fff">ICICI</text></svg>
                  ICICI
                </button>
                <button type="button" class="pgw-bank-chip" onclick="quickBank(this,'Axis')">
                  <svg width="20" height="20" viewBox="0 0 24 24"><circle cx="12" cy="12" r="11" fill="#97144d"/><text x="3" y="16" font-family="Arial" font-weight="700" font-size="7" fill="#fff">Axis</text></svg>
                  Axis
                </button>
                <button type="button" class="pgw-bank-chip" onclick="quickBank(this,'Kotak')">
                  <svg width="20" height="20" viewBox="0 0 24 24"><circle cx="12" cy="12" r="11" fill="#ed1c24"/><text x="2" y="16" font-family="Arial" font-weight="700" font-size="6.5" fill="#fff">Kotak</text></svg>
                  Kotak
                </button>
                <button type="button" class="pgw-bank-chip" onclick="quickBank(this,'PNB')">
                  <svg width="20" height="20" viewBox="0 0 24 24"><circle cx="12" cy="12" r="11" fill="#c41e3a"/><text x="4" y="16" font-family="Arial" font-weight="700" font-size="8" fill="#fff">PNB</text></svg>
                  PNB
                </button>
              </div>
            </div>
            <div class="pgw-encryption-notice mt12">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M8 1a4 4 0 014 4v2h1a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1h1V5a4 4 0 014-4zm2 6V5a2 2 0 10-4 0v2h4z" fill="#16a34a"/></svg>
              <span>Redirected to bank's secure portal for authentication</span>
            </div>
          </div>

          <!-- ═══ Security Badges ═══ -->
          <div class="pgw-security-section">
            <div class="pgw-security-badges">
              <div class="pgw-security-badge">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 2l8 4v5c0 5.5-3.4 10.7-8 12-4.6-1.3-8-6.5-8-12V6l8-4z" fill="#16a34a" opacity=".15" stroke="#16a34a" stroke-width="1.5"/><path d="M9 12l2 2 4-4" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <div>
                  <div class="fw6" style="font-size:11px">SSL / TLS</div>
                  <div style="font-size:9px;color:var(--text3)">Encrypted</div>
                </div>
              </div>
              <div class="pgw-security-badge">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 2l8 4v5c0 5.5-3.4 10.7-8 12-4.6-1.3-8-6.5-8-12V6l8-4z" fill="#2563eb" opacity=".15" stroke="#2563eb" stroke-width="1.5"/><text x="7" y="16" font-family="Arial" font-weight="800" font-size="7" fill="#2563eb">PCI</text></svg>
                <div>
                  <div class="fw6" style="font-size:11px">PCI DSS</div>
                  <div style="font-size:9px;color:var(--text3)">Level 1</div>
                </div>
              </div>
              <div class="pgw-security-badge">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M12 2l8 4v5c0 5.5-3.4 10.7-8 12-4.6-1.3-8-6.5-8-12V6l8-4z" fill="#7c3aed" opacity=".15" stroke="#7c3aed" stroke-width="1.5"/><text x="5" y="16" font-family="Arial" font-weight="800" font-size="6" fill="#7c3aed">AES</text></svg>
                <div>
                  <div class="fw6" style="font-size:11px">256-bit AES</div>
                  <div style="font-size:9px;color:var(--text3)">Encryption</div>
                </div>
              </div>
            </div>
            <div class="pgw-security-tags">
              <span class="pgw-sec-tag"><svg width="10" height="10" viewBox="0 0 16 16"><path d="M8 1a4 4 0 014 4v2h1a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1h1V5a4 4 0 014-4z" fill="#2563eb"/></svg> 3D Secure</span>
              <span class="pgw-sec-tag"><svg width="10" height="10" viewBox="0 0 16 16"><circle cx="8" cy="8" r="7" fill="#16a34a"/><text x="4" y="12" font-size="8" fill="#fff" font-weight="700">✓</text></svg> OTP Verified</span>
              <span class="pgw-sec-tag"><svg width="10" height="10" viewBox="0 0 16 16"><rect x="1" y="1" width="14" height="14" rx="3" fill="#7c3aed"/><text x="3" y="12" font-size="8" fill="#fff" font-weight="700">T</text></svg> Tokenisation</span>
              <span class="pgw-sec-tag"><svg width="10" height="10" viewBox="0 0 16 16"><circle cx="8" cy="8" r="7" fill="#d97706"/><text x="3" y="12" font-size="7" fill="#fff" font-weight="700">AI</text></svg> Fraud AI</span>
            </div>
          </div>
        </div>

        <div class="flex g8 mb16">
          <button type="button" class="btn btn-ghost w100" onclick="wizardGo(2)">← Back</button>
          <button type="submit" id="pay-btn" class="btn btn-primary btn-lg w100" onclick="return validateAndPay()">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="flex-shrink:0"><path d="M8 1a4 4 0 014 4v2h1a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1h1V5a4 4 0 014-4zm2 6V5a2 2 0 10-4 0v2h4z" fill="#fff"/></svg>
            Confirm & Pay <?= rupee($total) ?></button>
        </div>

        <!-- Razorpay Footer -->
        <div class="pgw-razorpay-footer">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M8 1a4 4 0 014 4v2h1a1 1 0 011 1v6a1 1 0 01-1 1H3a1 1 0 01-1-1V8a1 1 0 011-1h1V5a4 4 0 014-4zm2 6V5a2 2 0 10-4 0v2h4z" fill="#64748b"/></svg>
          <span>Secured by <strong>Razorpay</strong> · PCI DSS Level 1 Compliant</span>
        </div>

        <!-- Pay Processing Overlay -->
        <div id="pay-processing" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.65);backdrop-filter:blur(10px);z-index:9999;align-items:center;justify-content:center;flex-direction:column">
          <div class="pay-gateway-card">
            <div class="pgw-header">
              <div class="pgw-header-logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 2l8 4v5c0 5.5-3.4 10.7-8 12-4.6-1.3-8-6.5-8-12V6l8-4z" fill="#008cff" opacity=".15" stroke="#008cff" stroke-width="1.5"/><path d="M9 12l2 2 4-4" stroke="#008cff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <div>
                <div class="fw6" style="font-size:16px;color:#0f172a">Secure Payment Gateway</div>
                <div class="xs">256-bit SSL · End-to-end Encrypted</div>
              </div>
            </div>
            <div class="pgw-steps">
              <div class="pgw-step active" id="pgw-step-1"><span class="pgw-dot"></span><span>Validating payment details…</span></div>
              <div class="pgw-step" id="pgw-step-2"><span class="pgw-dot"></span><span>Connecting to payment gateway…</span></div>
              <div class="pgw-step" id="pgw-step-3"><span class="pgw-dot"></span><span>3D Secure authentication…</span></div>
              <div class="pgw-step" id="pgw-step-4"><span class="pgw-dot"></span><span>Confirming payment…</span></div>
            </div>
            <div class="pgw-progress"><div class="pgw-bar" id="pgw-bar"></div></div>
            <div id="pgw-status" class="pgw-status">Please do not close or refresh this window</div>
            <div class="pgw-brands">
              <svg width="36" height="22" viewBox="0 0 48 32"><rect width="48" height="32" rx="4" fill="#1A1F71"/><path d="M19 21h-3L18 11h3l-2 10zm23 0h2L42 11h-2a1 1 0 00-1 .7l-4 9.3h3l.5-1.3h3l.3 1.3zm-3-3.5l1.4-4 .7 4h-2.1zM29 13l.4-2a7 7 0 00-2.4-.5c-2.5 0-4 1.3-4 3.2 0 1.4 1.3 2.2 2.2 2.7 1 .5 1.3.8 1.3 1.2 0 .7-.8 1-1.5 1a5 5 0 01-2.4-.5l-.4 2.1c.7.3 1.8.5 2.7.5 2.7 0 4.3-1.3 4.3-3.3 0-2.6-3.6-2.7-3.6-3.9 0-.5.5-1 1.5-1a5 5 0 012 .5z" fill="#fff"/></svg>
              <svg width="36" height="22" viewBox="0 0 48 32"><rect width="48" height="32" rx="4" fill="#252525"/><circle cx="19" cy="16" r="7" fill="#EB001B" opacity=".9"/><circle cx="29" cy="16" r="7" fill="#F79E1B" opacity=".9"/><path d="M24 10.4a7 7 0 012.6 5.6 7 7 0 01-2.6 5.6 7 7 0 01-2.6-5.6 7 7 0 012.6-5.6z" fill="#FF5F00"/></svg>
              <span class="tag t-green" style="font-size:10px">UPI</span>
              <span class="tag t-purple" style="font-size:10px">RuPay</span>
            </div>
            <div style="text-align:center;margin-top:12px">
              <span class="xs">Powered by <strong>Razorpay</strong></span>
            </div>
          </div>
        </div>
      </div>
    </form>

    <!-- Sticky Fare Sidebar -->
    <div style="position:sticky;top:80px">
      <div class="card" style="background:var(--bg3)">
        <h3 style="font-size:15px;margin-bottom:14px">💰 Fare Summary</h3>
        <div class="flex sb mb8"><span class="sm">Base fare</span><span class="sm"
            id="base-show"><?= rupee($price) ?></span></div>
        <div class="flex sb mb8"><span class="sm">GST & Fees (12%)</span><span class="sm"><?= rupee($tax) ?></span>
        </div>
        <div class="flex sb mb8 hidden" id="disc-row"><span class="sm grn">Promo Discount</span><span class="sm grn"
            id="disc-show">-₹0</span></div>
        <div class="flex sb mb8 hidden" id="addon-row"><span class="sm" style="color:var(--accent)">Add-ons</span><span
            class="sm" style="color:var(--accent)" id="addon-show">+₹0</span></div>
        <div class="flex sb" style="padding-top:10px;border-top:1px solid var(--border)"><span
            class="fw5">Total</span><span class="fw6 acc" id="total-show"
            style="font-size:22px"><?= rupee($total) ?></span></div>
      </div>
    </div>
  </div>
</div>
<script src="<?= BASE ?>/assets/js/app.js"></script>
<script>
  const BASE_PRICE = <?= $price ?>; let disc = 0; let addonTotal = 0;
  function recalcTotal() {
    const pax = parseInt(document.getElementById('pax')?.value || 1);
    const base = BASE_PRICE * pax; const tax = Math.round(base * .12); const tot = Math.max(0, base + tax - disc + addonTotal);
    document.getElementById('base-show').textContent = '₹' + base.toLocaleString('en-IN');
    document.getElementById('total-show').textContent = '₹' + tot.toLocaleString('en-IN');
    document.getElementById('pay-btn').textContent = 'Confirm & Pay ₹' + tot.toLocaleString('en-IN');
    document.getElementById('base-amt').value = base;
  }
  function updateTrain(sel) {
    const pr = parseFloat(sel.options[sel.selectedIndex].dataset.price || 0);
    if (pr) { window._baseOverride = pr; document.getElementById('base-amt').value = pr; recalcTotal(); }
  }
  const _origCheckPromo = window.checkPromo;
  window.checkPromo = function () {
    const code = document.getElementById('promo-in')?.value?.trim().toUpperCase();
    const base = parseFloat(document.getElementById('base-amt')?.value || BASE_PRICE);
    if (!code) return;
    fetch(_BASE + '/api.php?a=promo&code=' + encodeURIComponent(code) + '&amount=' + base).then(r => r.json()).then(d => {
      const msg = document.getElementById('promo-msg');
      if (d.ok) { disc = d.disc; document.getElementById('disc-amt').value = disc; document.getElementById('disc-row').classList.remove('hidden'); document.getElementById('disc-show').textContent = '-₹' + disc.toLocaleString('en-IN'); if (msg) msg.innerHTML = '<span class="tag t-green">' + d.label + ' — saving ₹' + disc.toLocaleString('en-IN') + '</span>'; recalcTotal(); }
      else { disc = 0; document.getElementById('disc-amt').value = 0; document.getElementById('disc-row').classList.add('hidden'); if (msg) msg.innerHTML = '<span class="tag t-red">' + (d.msg || 'Invalid code') + '</span>'; recalcTotal(); }
    });
  };

  /* ─── Payment Gateway — Premium Tab & Panel Logic ─── */
  function switchPayTab(btn, method) {
    document.querySelectorAll('.pgw-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.pgw-panel').forEach(p => p.classList.remove('active'));
    const panel = document.getElementById('pgw-panel-' + method);
    if (panel) { panel.classList.add('active'); panel.style.animation = 'none'; panel.offsetHeight; panel.style.animation = 'paySubIn .3s ease'; }
    const hid = document.getElementById('pay-method-val');
    if (hid) hid.value = method;
  }

  function fmtCard(inp) {
    let v = inp.value.replace(/\D/g, '').slice(0, 16);
    inp.value = v.replace(/(\d{4})/g, '$1 ').trim();
    // Card type detection
    const first = v.charAt(0);
    const visaLogo = document.querySelector('.visa-logo');
    const mcLogo = document.querySelector('.mc-logo');
    const detected = document.getElementById('card-detected-icon');
    if (visaLogo) visaLogo.classList.toggle('active', first === '4' || !first);
    if (mcLogo) mcLogo.classList.toggle('active', first === '5' || first === '2' || !first);
    if (detected) {
      if (first === '4') detected.innerHTML = '<span style="color:#1A1F71;font-weight:700;font-size:12px">VISA</span>';
      else if (first === '5' || first === '2') detected.innerHTML = '<span style="color:#EB001B;font-weight:700;font-size:12px">MC</span>';
      else detected.innerHTML = '';
    }
  }

  function fmtExpiry(inp) {
    let v = inp.value.replace(/\D/g, '');
    if (v.length > 2) v = v.slice(0, 2) + '/' + v.slice(2, 4);
    inp.value = v;
  }

  function selectUpiApp(row, app) {
    const parent = row.closest('.pgw-wallet-rows') || row.closest('.pgw-panel');
    if (parent) parent.querySelectorAll('.pgw-wallet-row').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');
    const radio = row.querySelector('input[type=radio]');
    if (radio) radio.checked = true;
  }

  function selectWallet(row, wallet) {
    document.querySelectorAll('#pgw-panel-wallet .pgw-wallet-row').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');
    const radio = row.querySelector('input[type=radio]');
    if (radio) radio.checked = true;
  }

  function quickBank(btn, bank) {
    document.querySelectorAll('.pgw-bank-chip').forEach(c => c.classList.remove('selected'));
    btn.classList.add('selected');
    const sel = document.getElementById('nb-bank-select');
    if (sel) { sel.value = bank; }
  }

  function validateAndPay() {
    const method = document.getElementById('pay-method-val')?.value || 'card';
    if (method === 'card') {
      const cn = (document.getElementById('card-number')?.value || '').replace(/\s/g, '');
      const ex = document.getElementById('card-expiry')?.value || '';
      const cv = document.getElementById('card-cvv')?.value || '';
      const nm = document.getElementById('card-name')?.value || '';
      if (cn.length < 15) { alert('Please enter a valid card number.'); return false; }
      if (!/^\d{2}\/\d{2}$/.test(ex)) { alert('Please enter a valid expiry date (MM/YY).'); return false; }
      if (cv.length < 3) { alert('Please enter a valid CVV.'); return false; }
      if (!nm.trim()) { alert('Please enter the name on card.'); return false; }
    }
    if (method === 'nb') {
      const bk = document.getElementById('nb-bank-select')?.value || '';
      if (!bk) { alert('Please select a bank for Net Banking.'); return false; }
    }
    if (method === 'upi') {
      const uid = document.getElementById('upi-id-input')?.value || '';
      const app = document.querySelector('#pgw-panel-upi input[name=upi_app]:checked')?.value || '';
      if (!app && !uid.trim()) { alert('Please select a UPI app or enter your UPI ID.'); return false; }
    }
    // Show processing overlay with 4-step animation
    const ov = document.getElementById('pay-processing');
    if (ov) ov.style.display = 'flex';
    const btn = document.getElementById('pay-btn');
    if (btn) { btn.disabled = true; btn.textContent = 'Processing…'; }
    const bar = document.getElementById('pgw-bar');
    const status = document.getElementById('pgw-status');
    const steps = [
      { id: 'pgw-step-1', bar: 25, msg: 'Validating your payment details…' },
      { id: 'pgw-step-2', bar: 50, msg: 'Connecting to payment gateway…' },
      { id: 'pgw-step-3', bar: 75, msg: 'Authenticating with 3D Secure…' },
      { id: 'pgw-step-4', bar: 100, msg: 'Payment confirmed! Redirecting…' },
    ];
    let i = 0;
    function nextStep() {
      if (i > 0) {
        const prev = document.getElementById(steps[i - 1].id);
        if (prev) { prev.classList.remove('active'); prev.classList.add('done'); }
      }
      if (i < steps.length) {
        const cur = document.getElementById(steps[i].id);
        if (cur) cur.classList.add('active');
        if (bar) bar.style.width = steps[i].bar + '%';
        if (status) status.textContent = steps[i].msg;
        i++;
        setTimeout(nextStep, i < steps.length ? 900 : 700);
      } else {
        document.getElementById('book-form').submit();
      }
    }
    setTimeout(nextStep, 500);
    return false;
  }

  /* ─── Add-on Toggle ─── */
  function toggleAddon(el) {
    el.classList.toggle('selected');
    const cb = el.querySelector('input[type=checkbox]');
    if (cb) cb.checked = el.classList.contains('selected');
    addonTotal = 0;
    document.querySelectorAll('.addon-item.selected').forEach(item => {
      addonTotal += parseInt(item.dataset.price || 0);
    });
    const addonRow = document.getElementById('addon-row');
    const addonShow = document.getElementById('addon-show');
    const addonTotalRow = document.getElementById('addon-total-row');
    const addonTotalVal = document.getElementById('addon-total-val');
    if (addonTotal > 0) {
      if (addonRow) addonRow.classList.remove('hidden');
      if (addonShow) addonShow.textContent = '+₹' + addonTotal.toLocaleString('en-IN');
      if (addonTotalRow) addonTotalRow.style.display = 'flex';
      if (addonTotalVal) addonTotalVal.textContent = '₹' + addonTotal.toLocaleString('en-IN');
    } else {
      if (addonRow) addonRow.classList.add('hidden');
      if (addonTotalRow) addonTotalRow.style.display = 'none';
    }
    recalcTotal();
  }
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>