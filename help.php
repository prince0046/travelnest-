<?php
require_once __DIR__.'/includes/bootstrap.php';
$pageTitle='Help Center — TravelNest';
require_once __DIR__.'/includes/header.php';
?>
<div class="sec" style="max-width:860px">
  <h1 class="stitle" style="font-size:28px">Help Center</h1>
  <p class="ssub">Find answers to common questions about TravelNest</p>

  <style>
    .faq{border:1px solid var(--border);border-radius:var(--rl);margin-bottom:10px;overflow:hidden;background:#fff;box-shadow:var(--shadow)}
    .faq-q{padding:18px 22px;font-weight:600;font-size:14px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;transition:background .2s}
    .faq-q:hover{background:var(--bg2)}
    .faq-q .arrow{transition:transform .3s;font-size:18px;color:var(--text3)}
    .faq-q.open .arrow{transform:rotate(180deg)}
    .faq-a{max-height:0;overflow:hidden;transition:max-height .35s ease,padding .35s;padding:0 22px;font-size:13px;color:var(--text2);line-height:1.8}
    .faq-a.open{max-height:400px;padding:0 22px 18px}
    .faq-cat{font-size:18px;font-weight:700;margin:32px 0 14px;color:var(--text);display:flex;align-items:center;gap:8px}
    .faq-cat:first-of-type{margin-top:0}
  </style>

  <div class="faq-cat">✈️ Booking & Reservations</div>

  <div class="faq">
    <div class="faq-q" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">How do I book a flight or hotel?<span class="arrow">▾</span></div>
    <div class="faq-a">Browse our Flights or Hotels section, select your preferred option, choose dates and traveller count, then proceed to checkout. You'll receive a booking confirmation with a unique reference number.</div>
  </div>

  <div class="faq">
    <div class="faq-q" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">Can I modify my booking after confirmation?<span class="arrow">▾</span></div>
    <div class="faq-a">Yes, most bookings can be modified up to 24 hours before the travel date. Go to <strong>My Bookings</strong>, select the booking, and click <strong>Modify</strong>. Modification charges may apply depending on the service provider.</div>
  </div>

  <div class="faq">
    <div class="faq-q" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">Where can I find my booking confirmation?<span class="arrow">▾</span></div>
    <div class="faq-a">Your booking confirmation is available in the <strong>My Bookings</strong> section of your account. You can also download a detailed invoice as a PDF from there.</div>
  </div>

  <div class="faq-cat">💳 Payments & Refunds</div>

  <div class="faq">
    <div class="faq-q" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">What payment methods are accepted?<span class="arrow">▾</span></div>
    <div class="faq-a">We accept Credit/Debit Cards (Visa, Mastercard, RuPay), UPI (Google Pay, PhonePe, Paytm), Net Banking, and popular wallets. All transactions are secured with 256-bit SSL encryption.</div>
  </div>

  <div class="faq">
    <div class="faq-q" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">How long do refunds take?<span class="arrow">▾</span></div>
    <div class="faq-a">Refunds are typically processed within 5–7 business days. The amount is credited back to the original payment method. UPI refunds may take 1–3 business days.</div>
  </div>

  <div class="faq-cat">👤 Account & Security</div>

  <div class="faq">
    <div class="faq-q" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">How do I reset my password?<span class="arrow">▾</span></div>
    <div class="faq-a">Click <strong>Forgot your password?</strong> on the login page, enter your registered email, and follow the reset link. Your new password must be at least 6 characters long.</div>
  </div>

  <div class="faq">
    <div class="faq-q" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">How do I change my tier or earn loyalty points?<span class="arrow">▾</span></div>
    <div class="faq-a">Your loyalty tier (Bronze → Silver → Gold → Platinum) upgrades automatically based on your total bookings and spending. Visit your <strong>Profile</strong> to track your current tier and progress.</div>
  </div>

  <div class="faq-cat">🚌 Transport Services</div>

  <div class="faq">
    <div class="faq-q" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">Can I book a cab for outstation trips?<span class="arrow">▾</span></div>
    <div class="faq-a">Yes! Our cab section offers both local and outstation options. Choose your pickup/drop locations, select a vehicle type, and book instantly. Prices include fuel and driver charges.</div>
  </div>

  <div class="faq">
    <div class="faq-q" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open')">Are cruise bookings refundable?<span class="arrow">▾</span></div>
    <div class="faq-a">Cruise cancellation policies vary by operator. Generally, cancellations made 30+ days before departure receive a full refund; 15–30 days receive 50%; less than 15 days are non-refundable.</div>
  </div>

  <div style="margin-top:36px;padding:24px;background:var(--bg2);border-radius:var(--rl);text-align:center">
    <p style="font-weight:600;margin-bottom:8px">Still need help?</p>
    <p class="sm mb16">Our support team is available 24/7</p>
    <a href="<?= BASE ?>/support.php" class="btn btn-primary btn-sm">Contact Support →</a>
    <p class="sm mt12" style="color:var(--text3)">📞 1800-103-8747 (Toll-Free) · 📧 support@travelnest.com</p>
  </div>
</div>
<?php require_once __DIR__.'/includes/footer.php'; ?>
