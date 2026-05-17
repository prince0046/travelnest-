<?php
require_once __DIR__.'/includes/bootstrap.php';
$pageTitle='Terms of Use — TravelNest';
require_once __DIR__.'/includes/header.php';
?>
<div class="sec" style="max-width:860px">
  <h1 class="stitle" style="font-size:28px">Terms of Use</h1>
  <p class="ssub">Last updated: <?= date('d M Y') ?></p>

  <div style="background:#fff;border:1px solid var(--border);border-radius:var(--rl);padding:28px;box-shadow:var(--shadow);line-height:1.9;font-size:14px;color:var(--text2)">

    <h3 style="color:var(--text);margin-bottom:12px">1. Acceptance of Terms</h3>
    <p>By accessing or using TravelNest ("the Platform"), you agree to be bound by these Terms of Use. If you do not agree, please discontinue use immediately.</p>

    <h3 style="color:var(--text);margin:24px 0 12px">2. Eligibility</h3>
    <p>You must be at least 18 years old to create an account. By using the Platform, you represent that you have the legal authority to enter into these terms.</p>

    <h3 style="color:var(--text);margin:24px 0 12px">3. Account Responsibilities</h3>
    <ul style="margin:10px 0 20px 20px">
      <li>You are responsible for maintaining the confidentiality of your account credentials</li>
      <li>You must provide accurate, current, and complete information during registration</li>
      <li>You are liable for all activities under your account</li>
      <li>Notify us immediately if you suspect unauthorized access</li>
    </ul>

    <h3 style="color:var(--text);margin:24px 0 12px">4. Booking & Payments</h3>
    <ul style="margin:10px 0 20px 20px">
      <li>All bookings are subject to availability and confirmation by the service provider</li>
      <li>Prices displayed include applicable taxes unless stated otherwise</li>
      <li>TravelNest acts as an intermediary — the contract for services is between you and the service provider</li>
      <li>Payment is required at the time of booking unless otherwise specified</li>
    </ul>

    <h3 style="color:var(--text);margin:24px 0 12px">5. Cancellations</h3>
    <p>Cancellation and refund terms vary by service type. Please refer to our <a href="<?= BASE ?>/cancellation-policy.php" style="color:var(--accent);font-weight:500">Cancellation Policy</a> for detailed information.</p>

    <h3 style="color:var(--text);margin:24px 0 12px">6. Prohibited Activities</h3>
    <p>You may not:</p>
    <ul style="margin:10px 0 20px 20px">
      <li>Use the Platform for fraudulent or illegal purposes</li>
      <li>Attempt to access other users' accounts or data</li>
      <li>Scrape, crawl, or index the Platform without written permission</li>
      <li>Interfere with the Platform's security features or infrastructure</li>
      <li>Post false reviews or misleading content</li>
    </ul>

    <h3 style="color:var(--text);margin:24px 0 12px">7. Intellectual Property</h3>
    <p>All content on TravelNest — including logos, text, images, and software — is owned by TravelNest Pvt. Ltd. or its licensors and is protected by applicable intellectual property laws.</p>

    <h3 style="color:var(--text);margin:24px 0 12px">8. Limitation of Liability</h3>
    <p>TravelNest is an aggregator platform. We are not liable for:</p>
    <ul style="margin:10px 0 20px 20px">
      <li>Service quality provided by third-party operators</li>
      <li>Delays, cancellations, or schedule changes by airlines, hotels, or transport operators</li>
      <li>Loss or damage arising from use of the Platform, except where caused by our negligence</li>
    </ul>

    <h3 style="color:var(--text);margin:24px 0 12px">9. Governing Law</h3>
    <p>These terms are governed by the laws of India. Any disputes shall be subject to the exclusive jurisdiction of courts in Mumbai, Maharashtra.</p>

    <h3 style="color:var(--text);margin:24px 0 12px">10. Changes to Terms</h3>
    <p>We may update these terms periodically. Continued use of the Platform after changes constitutes acceptance of the revised terms. Material changes will be communicated via email or on-platform notification.</p>

    <div style="margin-top:28px;padding:18px;background:var(--bg2);border-radius:12px">
      <p style="font-weight:600;color:var(--text);margin-bottom:6px">📧 Questions?</p>
      <p class="sm">Contact our legal team at <strong>legal@travelnest.com</strong> or call <strong>1800-103-8747</strong>.</p>
      <p class="sm mt4">TravelNest Pvt. Ltd. · Mumbai, India · CIN: U63040MH2020PTC123456 · GSTIN: 27AAACT1234A1Z5</p>
    </div>
  </div>
</div>
<?php require_once __DIR__.'/includes/footer.php'; ?>
