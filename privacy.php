<?php
require_once __DIR__.'/includes/bootstrap.php';
$pageTitle='Privacy Policy — TravelNest';
require_once __DIR__.'/includes/header.php';
?>
<div class="sec" style="max-width:860px">
  <h1 class="stitle" style="font-size:28px">Privacy Policy</h1>
  <p class="ssub">Last updated: <?= date('d M Y') ?></p>

  <div style="background:#fff;border:1px solid var(--border);border-radius:var(--rl);padding:28px;box-shadow:var(--shadow);line-height:1.9;font-size:14px;color:var(--text2)">

    <h3 style="color:var(--text);margin-bottom:12px">1. Information We Collect</h3>
    <p>When you use TravelNest, we collect:</p>
    <ul style="margin:10px 0 20px 20px">
      <li><strong>Account information:</strong> Name, email, phone number, city</li>
      <li><strong>Booking details:</strong> Travel dates, destinations, passenger information</li>
      <li><strong>Payment data:</strong> Payment method type (card details are processed securely by our payment partners and never stored on our servers)</li>
      <li><strong>Usage data:</strong> Pages visited, search queries, device and browser type</li>
    </ul>

    <h3 style="color:var(--text);margin-bottom:12px">2. How We Use Your Data</h3>
    <ul style="margin:10px 0 20px 20px">
      <li>Processing and managing your bookings</li>
      <li>Sending booking confirmations and travel updates</li>
      <li>Improving our services and personalizing your experience</li>
      <li>Detecting fraud and ensuring platform security</li>
      <li>Complying with legal obligations</li>
    </ul>

    <h3 style="color:var(--text);margin-bottom:12px">3. Data Sharing</h3>
    <p>We share your data only with:</p>
    <ul style="margin:10px 0 20px 20px">
      <li><strong>Service providers:</strong> Airlines, hotels, transport operators — to fulfill your bookings</li>
      <li><strong>Payment processors:</strong> To securely process transactions</li>
      <li><strong>Legal authorities:</strong> When required by law or court order</li>
    </ul>
    <p>We <strong>never sell</strong> your personal data to third parties for marketing purposes.</p>

    <h3 style="color:var(--text);margin:24px 0 12px">4. Cookies</h3>
    <p>We use essential cookies for session management and authentication. We do not use third-party advertising cookies. You can manage cookies through your browser settings.</p>

    <h3 style="color:var(--text);margin:24px 0 12px">5. Data Security</h3>
    <p>Your data is protected with:</p>
    <ul style="margin:10px 0 20px 20px">
      <li>256-bit SSL encryption for all data in transit</li>
      <li>Bcrypt password hashing</li>
      <li>CSRF token protection on all forms</li>
      <li>Regular security audits (ISO 27001 certified)</li>
    </ul>

    <h3 style="color:var(--text);margin:24px 0 12px">6. Your Rights</h3>
    <p>You have the right to:</p>
    <ul style="margin:10px 0 20px 20px">
      <li><strong>Access</strong> your personal data via your Profile page</li>
      <li><strong>Correct</strong> inaccurate information</li>
      <li><strong>Delete</strong> your account by contacting support</li>
      <li><strong>Withdraw consent</strong> for marketing communications</li>
    </ul>

    <h3 style="color:var(--text);margin:24px 0 12px">7. Contact</h3>
    <p>For privacy-related queries, contact our Data Protection Officer:</p>
    <p style="margin-top:8px"><strong>📧</strong> privacy@travelnest.com &nbsp;|&nbsp; <strong>📞</strong> 1800-103-8747</p>
    <p class="sm mt8">TravelNest Pvt. Ltd., Mumbai, India · CIN: U63040MH2020PTC123456</p>
  </div>
</div>
<?php require_once __DIR__.'/includes/footer.php'; ?>
