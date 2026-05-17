</main><!-- /#main-content -->

<footer role="contentinfo">
  <div class="ft-grid">
    <div>
      <div class="ft-logo" aria-label="TravelNest">Travel<span>Nest</span></div>
      <p class="sm mt8" style="line-height:1.7">India's most trusted travel platform. Flights, Hotels, Trains, Buses, Cabs &amp; Holiday Packages — all in one place.</p>
      <div class="flex g8 mt16" aria-label="Certifications">
        <span class="tag t-gold">ISO 27001</span>
        <span class="tag t-blue">IATA</span>
        <span class="tag t-green">RBI</span>
      </div>
    </div>
    <div>
      <div class="ft-h">Travel</div>
      <ul class="ft-list" aria-label="Travel links">
        <li><a href="<?= BASE ?>/flights.php">✈️ Flights</a></li>
        <li><a href="<?= BASE ?>/hotels.php">🏨 Hotels</a></li>
        <li><a href="<?= BASE ?>/packages.php">📦 Packages</a></li>
        <li><a href="<?= BASE ?>/trains.php">🚆 Trains</a></li>
        <li><a href="<?= BASE ?>/buses.php">🚌 Buses</a></li>
        <li><a href="<?= BASE ?>/cabs.php">🚕 Cabs</a></li>
        <li><a href="<?= BASE ?>/cruises.php">🚢 Cruises</a></li>
      </ul>
    </div>
    <div>
      <div class="ft-h">Support</div>
      <ul class="ft-list" aria-label="Support links">
        <li><a href="<?= BASE ?>/bookings.php">My Bookings</a></li>
        <li><a href="<?= BASE ?>/wishlist.php">Wishlist</a></li>
        <li><a href="<?= BASE ?>/help.php">Help Center</a></li>
        <li><a href="<?= BASE ?>/cancellation-policy.php">Cancellation Policy</a></li>
        <li><a href="<?= BASE ?>/privacy.php">Privacy Policy</a></li>
        <li><a href="<?= BASE ?>/terms.php">Terms of Use</a></li>
      </ul>
    </div>
    <div>
      <div class="ft-h">Contact</div>
      <p class="sm mb4">📞 1800-103-8747 (Toll-Free)</p>
      <p class="sm mb4">📧 support@travelnest.com</p>
      <p class="sm mb4">🕐 24/7 Customer Support</p>
      <p class="sm mb12">🏢 Mumbai, India</p>
      <div class="ft-h mt16">Stay Updated</div>
      <p class="sm mb8">Get the latest travel deals in your inbox.</p>
      <div class="flex g8" style="flex-wrap:wrap">
        <label for="footer-email" class="hidden">Email address</label>
        <input type="email" id="footer-email" name="email" placeholder="you@email.com"
               style="flex:1;min-width:0;padding:8px 12px;font-size:13px" autocomplete="email">
        <button class="btn btn-primary btn-sm" onclick="toast('Subscribed! 🎉')" aria-label="Subscribe to newsletter">Subscribe</button>
      </div>
    </div>
  </div>
  <div class="ft-bot" role="contentinfo" aria-label="Legal information">
    <span>© <?= date('Y') ?> TravelNest Pvt. Ltd. | CIN: U63040MH2020PTC123456 | GSTIN: 27AAACT1234A1Z5</span>
    <span>RBI Approved · IATA Accredited · ISO 27001 Certified</span>
  </div>
</footer>
<script src="<?= BASE ?>/assets/js/app.js?v=<?= time() ?>"></script>
</body>
</html>

