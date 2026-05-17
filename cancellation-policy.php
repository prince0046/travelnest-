<?php
require_once __DIR__.'/includes/bootstrap.php';
$pageTitle='Cancellation Policy — TravelNest';
require_once __DIR__.'/includes/header.php';
?>
<div class="sec" style="max-width:860px">
  <h1 class="stitle" style="font-size:28px">Cancellation & Refund Policy</h1>
  <p class="ssub">Last updated: <?= date('d M Y') ?></p>

  <div style="background:#fff;border:1px solid var(--border);border-radius:var(--rl);padding:28px;box-shadow:var(--shadow);line-height:1.9;font-size:14px;color:var(--text2)">

    <h3 style="color:var(--text);margin-bottom:12px">1. Flights</h3>
    <div class="dt-wrap">
    <table class="dt" style="margin-bottom:24px">
      <thead><tr><th>Cancellation Window</th><th>Refund</th></tr></thead>
      <tbody>
        <tr><td>More than 72 hours before departure</td><td><span class="tag t-green">Full refund minus ₹500 fee</span></td></tr>
        <tr><td>24–72 hours before departure</td><td><span class="tag t-amber">50% refund</span></td></tr>
        <tr><td>Less than 24 hours / No-show</td><td><span class="tag t-red">Non-refundable</span></td></tr>
      </tbody>
    </table>
    </div>

    <h3 style="color:var(--text);margin-bottom:12px">2. Hotels</h3>
    <div class="dt-wrap">
    <table class="dt" style="margin-bottom:24px">
      <thead><tr><th>Cancellation Window</th><th>Refund</th></tr></thead>
      <tbody>
        <tr><td>More than 48 hours before check-in</td><td><span class="tag t-green">Full refund</span></td></tr>
        <tr><td>24–48 hours before check-in</td><td><span class="tag t-amber">75% refund</span></td></tr>
        <tr><td>Less than 24 hours / No-show</td><td><span class="tag t-red">First night charged</span></td></tr>
      </tbody>
    </table>
    </div>

    <h3 style="color:var(--text);margin-bottom:12px">3. Trains & Buses</h3>
    <p>Train and bus cancellations follow the respective operator's policy. In general:</p>
    <ul style="margin:10px 0 24px 20px">
      <li>Cancellations 48+ hours before departure: <strong>Full refund minus processing fee</strong></li>
      <li>Cancellations 6–48 hours: <strong>50% refund</strong></li>
      <li>Less than 6 hours: <strong>No refund</strong></li>
    </ul>

    <h3 style="color:var(--text);margin-bottom:12px">4. Cabs</h3>
    <p>Free cancellation up to 1 hour before pickup. Late cancellations incur a flat <strong>₹200</strong> fee.</p>

    <h3 style="color:var(--text);margin:24px 0 12px">5. Holiday Packages</h3>
    <div class="dt-wrap">
    <table class="dt" style="margin-bottom:24px">
      <thead><tr><th>Cancellation Window</th><th>Refund</th></tr></thead>
      <tbody>
        <tr><td>30+ days before travel</td><td><span class="tag t-green">90% refund</span></td></tr>
        <tr><td>15–30 days</td><td><span class="tag t-amber">50% refund</span></td></tr>
        <tr><td>Less than 15 days</td><td><span class="tag t-red">Non-refundable</span></td></tr>
      </tbody>
    </table>
    </div>

    <h3 style="color:var(--text);margin-bottom:12px">6. Cruises</h3>
    <p>Cruise cancellations depend on the cruise line's terms. Standard policy: 30+ days for full refund, 15–30 days for 50% refund, under 15 days non-refundable.</p>

    <div style="margin-top:28px;padding:18px;background:var(--bg2);border-radius:12px">
      <p style="font-weight:600;color:var(--text);margin-bottom:6px">📞 Refund Processing</p>
      <p class="sm">All refunds are processed within <strong>5–7 business days</strong> to your original payment method. For immediate assistance, contact our 24/7 helpline at <strong>1800-103-8747</strong>.</p>
    </div>
  </div>
</div>
<?php require_once __DIR__.'/includes/footer.php'; ?>
