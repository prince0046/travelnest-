<?php
require_once __DIR__.'/includes/bootstrap.php';
$pageTitle = 'The Grand Meridian — Luxury Beach Resort | TravelNest';
require_once __DIR__.'/includes/header.php';
?>

<style>
/* ══════════════════════════════════════════════════════════
   THE GRAND MERIDIAN — PROMOTIONAL PAGE STYLES
   ══════════════════════════════════════════════════════════ */

@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&display=swap');

/* ---------- Theme Variables ---------- */
:root {
  --promo-gold: #C8963E;
  --promo-gold-light: #D4A853;
  --promo-gold-dark: #A67C2E;
  --promo-gold-bg: rgba(200,150,62,0.08);
  --promo-gold-border: rgba(200,150,62,0.18);
  --promo-bg: #ffffff;
  --promo-bg2: #FAFAF7;
  --promo-bg3: #F5F3EE;
  --promo-card: #ffffff;
  --promo-card-border: rgba(0,0,0,0.06);
  --promo-text: #1A1A1A;
  --promo-text2: #5A5A5A;
  --promo-text3: #8A8A8A;
  --promo-shadow: 0 4px 24px rgba(0,0,0,0.06);
  --promo-shadow-hover: 0 12px 40px rgba(0,0,0,0.1);
  --promo-amenity-bg: #F8F7F4;
  --promo-amenity-border: #EDE9E0;
  --promo-banner-bg: linear-gradient(135deg, #1A1A1A 0%, #2A2520 50%, #1C1814 100%);
  --promo-review-bg: #ffffff;
  --promo-overview-bg: linear-gradient(135deg, #FFFDF8 0%, #FEFCF7 50%, #FFF 100%);
  --promo-overview-border: rgba(200,150,62,0.12);
}

[data-promo-theme="dark"] {
  --promo-bg: #121212;
  --promo-bg2: #1A1A1A;
  --promo-bg3: #222222;
  --promo-card: #1E1E1E;
  --promo-card-border: rgba(255,255,255,0.08);
  --promo-text: #F0EDE8;
  --promo-text2: #B0A99E;
  --promo-text3: #706B63;
  --promo-shadow: 0 4px 24px rgba(0,0,0,0.3);
  --promo-shadow-hover: 0 12px 40px rgba(0,0,0,0.5);
  --promo-amenity-bg: #252320;
  --promo-amenity-border: #3A3630;
  --promo-banner-bg: linear-gradient(135deg, #0A0A0A 0%, #1A1610 50%, #0F0D0A 100%);
  --promo-review-bg: #1E1E1E;
  --promo-overview-bg: linear-gradient(135deg, #1E1C18 0%, #1A1816 50%, #1E1E1E 100%);
  --promo-overview-border: rgba(200,150,62,0.2);
}

/* ---------- Theme Toggle ---------- */
.promo-theme-toggle {
  position: fixed;
  bottom: 28px;
  right: 28px;
  z-index: 999;
  width: 54px;
  height: 54px;
  border-radius: 50%;
  border: 2px solid var(--promo-gold);
  background: var(--promo-card);
  color: var(--promo-gold);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  box-shadow: 0 4px 20px rgba(200,150,62,0.25);
  transition: all 0.35s cubic-bezier(0.22,1,0.36,1);
}
.promo-theme-toggle:hover {
  transform: scale(1.1) rotate(15deg);
  box-shadow: 0 6px 28px rgba(200,150,62,0.4);
  background: var(--promo-gold);
  color: #fff;
}
.promo-theme-toggle .icon-sun,
.promo-theme-toggle .icon-moon { pointer-events: none; }
.promo-theme-toggle .icon-moon { display: none; }
[data-promo-theme="dark"] .promo-theme-toggle .icon-sun { display: none; }
[data-promo-theme="dark"] .promo-theme-toggle .icon-moon { display: inline; }

/* ---------- Body bg transition ---------- */
.promo-page-wrapper {
  background: var(--promo-bg);
  transition: background 0.4s ease, color 0.4s ease;
  color: var(--promo-text);
}

/* ---------- Hero Section ---------- */
.promo-hero {
  position: relative;
  width: 100%;
  height: 75vh;
  min-height: 520px;
  overflow: hidden;
  border-radius: 0 0 32px 32px;
}
.promo-hero img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.promo-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    to top,
    rgba(0,0,0,0.72) 0%,
    rgba(0,0,0,0.35) 40%,
    rgba(0,0,0,0.08) 70%,
    transparent 100%
  );
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 48px 40px 56px;
}
.promo-hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(0,140,255,0.9);
  color: #fff;
  padding: 6px 16px;
  border-radius: 24px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 1.2px;
  text-transform: uppercase;
  width: fit-content;
  margin-bottom: 16px;
  backdrop-filter: blur(6px);
}
.promo-hero h1 {
  font-family: 'Playfair Display', serif;
  font-size: clamp(36px, 5vw, 64px);
  font-weight: 700;
  color: #fff;
  line-height: 1.1;
  margin: 0 0 10px;
  text-shadow: 0 2px 20px rgba(0,0,0,0.35);
}
.promo-hero-sub {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(18px, 2.5vw, 26px);
  color: rgba(255,255,255,0.88);
  font-style: italic;
  font-weight: 300;
  letter-spacing: 0.5px;
}
.promo-hero-stars {
  color: #F9A825;
  font-size: 20px;
  margin-top: 12px;
  letter-spacing: 4px;
}

/* ---------- Section Container ---------- */
.promo-section {
  max-width: 1200px;
  margin: 0 auto;
  padding: 64px 24px;
}
.promo-section-header {
  text-align: center;
  margin-bottom: 48px;
}
.promo-section-header h2 {
  font-family: 'Playfair Display', serif;
  font-size: clamp(26px, 3.5vw, 40px);
  font-weight: 600;
  color: var(--text);
  margin: 0 0 8px;
}
.promo-section-header .accent-line {
  width: 56px;
  height: 3px;
  background: linear-gradient(90deg, #008cff, #33a3ff);
  border-radius: 3px;
  margin: 12px auto 16px;
}
.promo-section-header p {
  font-family: 'DM Sans', sans-serif;
  color: var(--text2);
  font-size: 15px;
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.7;
}

/* ---------- Overview Card ---------- */
.promo-overview {
  background: linear-gradient(135deg, #f0f7ff 0%, #f5faff 50%, #FFF 100%);
  border: 1px solid rgba(0,140,255,0.12);
  border-radius: 20px;
  padding: 40px 44px;
  max-width: 900px;
  margin: -40px auto 0;
  position: relative;
  z-index: 10;
  box-shadow: 0 12px 48px rgba(0,0,0,0.08), 0 2px 12px rgba(0,140,255,0.06);
}
.promo-overview-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 32px;
  align-items: start;
}
.promo-overview h3 {
  font-family: 'Playfair Display', serif;
  font-size: 24px;
  font-weight: 600;
  color: var(--text);
  margin: 0 0 6px;
}
.promo-overview .tagline {
  font-family: 'Cormorant Garamond', serif;
  font-size: 18px;
  color: #008cff;
  font-style: italic;
  margin-bottom: 16px;
}
.promo-overview .desc {
  font-size: 14px;
  color: var(--text2);
  line-height: 1.8;
}
.promo-highlights {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}
.promo-highlight-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  background: #fff;
  border-radius: 12px;
  border: 1px solid rgba(0,0,0,0.05);
  font-size: 13px;
  color: var(--text);
  box-shadow: 0 2px 8px rgba(0,0,0,0.03);
  transition: transform 0.2s, box-shadow 0.2s;
}
.promo-highlight-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(0,140,255,0.1);
}
.promo-highlight-item .material-symbols-outlined {
  font-size: 20px;
  color: #008cff;
}

/* ---------- Room Cards ---------- */
.promo-rooms-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}
.promo-room-card {
  background: #fff;
  border-radius: 20px;
  overflow: hidden;
  border: 1px solid rgba(0,0,0,0.06);
  box-shadow: 0 4px 24px rgba(0,0,0,0.05);
  transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.35s;
}
.promo-room-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 16px 48px rgba(0,0,0,0.1), 0 4px 12px rgba(0,140,255,0.08);
}
.promo-room-img {
  position: relative;
  height: 220px;
  overflow: hidden;
}
.promo-room-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}
.promo-room-card:hover .promo-room-img img {
  transform: scale(1.06);
}
.promo-room-badge {
  position: absolute;
  top: 14px;
  left: 14px;
  background: rgba(0,0,0,0.6);
  backdrop-filter: blur(8px);
  color: #fff;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.5px;
}
.promo-room-badge.best {
  background: linear-gradient(135deg, #008cff, #0070cc);
}
.promo-room-body {
  padding: 22px 20px 24px;
}
.promo-room-body h4 {
  font-family: 'Playfair Display', serif;
  font-size: 19px;
  font-weight: 600;
  color: var(--text);
  margin: 0 0 6px;
}
.promo-room-body .room-size {
  font-size: 12px;
  color: var(--text2);
  margin-bottom: 14px;
}
.promo-room-price {
  display: flex;
  align-items: baseline;
  gap: 6px;
  margin-bottom: 16px;
}
.promo-room-price .amount {
  font-family: 'Inter', sans-serif;
  font-size: 22px;
  font-weight: 700;
  color: #008cff;
}
.promo-room-price .per {
  font-size: 13px;
  color: var(--text2);
}
.promo-room-price .original {
  font-size: 14px;
  color: #aaa;
  text-decoration: line-through;
  margin-left: 4px;
}
.promo-room-amenities {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-bottom: 18px;
}
.promo-room-amenity {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 10px;
  background: #F8FAFC;
  border: 1px solid #EEF2F7;
  border-radius: 8px;
  font-size: 11px;
  color: #64748B;
}
.promo-room-amenity .material-symbols-outlined {
  font-size: 14px;
  color: #008cff;
}
.promo-room-cta {
  display: flex;
  gap: 10px;
}
.promo-room-cta .btn-book {
  flex: 1;
  padding: 11px 20px;
  background: linear-gradient(135deg, #008cff, #0070cc);
  color: #fff;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  text-align: center;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}
.promo-room-cta .btn-book:hover {
  background: linear-gradient(135deg, #0070cc, #005bb5);
  box-shadow: 0 6px 20px rgba(0,140,255,0.35);
  transform: translateY(-1px);
}
.promo-room-cta .btn-details {
  padding: 11px 16px;
  background: transparent;
  color: var(--text);
  border: 1.5px solid rgba(0,0,0,0.12);
  border-radius: 12px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}
.promo-room-cta .btn-details:hover {
  border-color: #008cff;
  color: #008cff;
  background: #f0f7ff;
}

/* ---------- Facilities Grid ---------- */
.promo-facilities-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}
.promo-facility-card {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  height: 240px;
  cursor: default;
  box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}
.promo-facility-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.22, 1, 0.36, 1);
}
.promo-facility-card:hover img {
  transform: scale(1.08);
}
.promo-facility-label {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 20px 18px 16px;
  background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 100%);
  color: #fff;
}
.promo-facility-label h5 {
  font-family: 'Playfair Display', serif;
  font-size: 17px;
  font-weight: 600;
  margin: 0 0 2px;
}
.promo-facility-label span {
  font-size: 12px;
  opacity: 0.85;
}

/* ---------- Booking Banner ---------- */
.promo-booking-banner {
  background: linear-gradient(135deg, #1A1A2E 0%, #16213E 50%, #0F3460 100%);
  border-radius: 24px;
  padding: 56px 48px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 40px;
  position: relative;
  overflow: hidden;
  max-width: 1200px;
  margin: 0 auto 64px;
}
.promo-booking-banner::before {
  content: '';
  position: absolute;
  top: -60%;
  right: -10%;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(0,140,255,0.15) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}
.promo-booking-banner::after {
  content: '';
  position: absolute;
  bottom: -40%;
  left: -5%;
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, rgba(0,140,255,0.1) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}
.promo-banner-text {
  position: relative;
  z-index: 1;
}
.promo-banner-text h3 {
  font-family: 'Playfair Display', serif;
  font-size: clamp(24px, 3vw, 34px);
  font-weight: 600;
  color: #fff;
  margin: 0 0 8px;
}
.promo-banner-text p {
  color: rgba(255,255,255,0.7);
  font-size: 15px;
  line-height: 1.7;
  max-width: 480px;
}
.promo-banner-actions {
  display: flex;
  flex-direction: column;
  gap: 12px;
  position: relative;
  z-index: 1;
}
.promo-banner-actions .btn-big {
  padding: 16px 36px;
  background: linear-gradient(135deg, #008cff, #0070cc);
  color: #fff;
  border: none;
  border-radius: 14px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
}
.promo-banner-actions .btn-big:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 32px rgba(0,140,255,0.4);
}
.promo-banner-actions .btn-outline {
  padding: 14px 32px;
  background: transparent;
  color: #fff;
  border: 1.5px solid rgba(255,255,255,0.25);
  border-radius: 14px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
  text-decoration: none;
  text-align: center;
}
.promo-banner-actions .btn-outline:hover {
  border-color: rgba(0,140,255,0.6);
  background: rgba(0,140,255,0.1);
}

/* ---------- Guest Reviews ---------- */
.promo-reviews-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}
.promo-review-card {
  background: #fff;
  border: 1px solid rgba(0,0,0,0.05);
  border-radius: 16px;
  padding: 28px 24px;
  box-shadow: 0 2px 16px rgba(0,0,0,0.04);
  transition: transform 0.3s, box-shadow 0.3s;
}
.promo-review-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 32px rgba(0,0,0,0.07);
}
.promo-review-stars {
  color: #F9A825;
  font-size: 15px;
  letter-spacing: 2px;
  margin-bottom: 12px;
}
.promo-review-text {
  font-size: 14px;
  color: var(--text2);
  line-height: 1.7;
  font-style: italic;
  margin-bottom: 16px;
}
.promo-review-author {
  display: flex;
  align-items: center;
  gap: 10px;
}
.promo-review-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: linear-gradient(135deg, #008cff, #33a3ff);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-weight: 700;
  font-size: 15px;
}
.promo-review-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--text);
}
.promo-review-date {
  font-size: 11px;
  color: var(--text2);
}

/* ---------- Responsive ---------- */
@media (max-width: 900px) {
  .promo-rooms-grid,
  .promo-facilities-grid,
  .promo-reviews-grid {
    grid-template-columns: 1fr;
  }
  .promo-overview-grid {
    grid-template-columns: 1fr;
  }
  .promo-overview {
    padding: 28px 22px;
    margin: -30px 16px 0;
  }
  .promo-booking-banner {
    flex-direction: column;
    text-align: center;
    padding: 36px 24px;
    margin: 0 16px 48px;
  }
  .promo-banner-text p {
    max-width: 100%;
  }
  .promo-hero {
    height: 55vh;
    min-height: 380px;
    border-radius: 0 0 20px 20px;
  }
  .promo-hero-overlay {
    padding: 32px 20px 40px;
  }
  .promo-highlights {
    grid-template-columns: 1fr;
  }
}
@media (min-width: 901px) and (max-width: 1100px) {
  .promo-rooms-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .promo-facilities-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .promo-reviews-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* ---------- Animations ---------- */
@keyframes promoFadeUp {
  from { opacity: 0; transform: translateY(30px); }
  to   { opacity: 1; transform: translateY(0); }
}
.promo-animate {
  opacity: 0;
  animation: promoFadeUp 0.7s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}
.promo-animate.d1 { animation-delay: 0.1s; }
.promo-animate.d2 { animation-delay: 0.2s; }
.promo-animate.d3 { animation-delay: 0.3s; }
.promo-animate.d4 { animation-delay: 0.4s; }
</style>


<!-- ══════════════════ HERO SECTION ══════════════════ -->
<section class="promo-hero" id="promo-hero">
  <img src="<?= BASE ?>/assets/images/promo/hotel_exterior.png"
       alt="The Grand Meridian — Luxury Beach Resort, Goa"
       loading="eager">
  <div class="promo-hero-overlay">
    <div class="promo-hero-badge">
      <span class="material-symbols-outlined" style="font-size:14px">verified</span>
      TravelNest Certified · 5-Star Luxury
    </div>
    <h1>The Grand Meridian</h1>
    <div class="promo-hero-sub">Where the ocean meets opulence — Goa's finest luxury beach resort</div>
    <div class="promo-hero-stars">★ ★ ★ ★ ★</div>
  </div>
</section>


<!-- ══════════════════ OVERVIEW CARD ══════════════════ -->
<div class="promo-overview promo-animate d1">
  <div class="promo-overview-grid">
    <div>
      <h3>Welcome to The Grand Meridian</h3>
      <div class="tagline">"Experience paradise, redefined."</div>
      <p class="desc">
        Nestled along the pristine shores of South Goa, The Grand Meridian is a breathtaking 
        5-star luxury beach resort that blends contemporary elegance with tropical serenity. 
        Featuring 180 exquisitely designed rooms, world-class dining, an award-winning spa, 
        and direct beachfront access — every moment here is crafted for indulgence. 
        Whether you're celebrating romance, seeking tranquil rejuvenation, or hosting an 
        exclusive event, The Grand Meridian is your destination of distinction.
      </p>
    </div>
    <div>
      <div class="promo-highlights">
        <div class="promo-highlight-item">
          <span class="material-symbols-outlined">location_on</span>
          <span>South Goa, India</span>
        </div>
        <div class="promo-highlight-item">
          <span class="material-symbols-outlined">beach_access</span>
          <span>Private Beach</span>
        </div>
        <div class="promo-highlight-item">
          <span class="material-symbols-outlined">star</span>
          <span>4.9 / 5 Rating</span>
        </div>
        <div class="promo-highlight-item">
          <span class="material-symbols-outlined">wifi</span>
          <span>Free High-Speed WiFi</span>
        </div>
        <div class="promo-highlight-item">
          <span class="material-symbols-outlined">restaurant</span>
          <span>3 Fine-Dining Venues</span>
        </div>
        <div class="promo-highlight-item">
          <span class="material-symbols-outlined">spa</span>
          <span>Award-Winning Spa</span>
        </div>
        <div class="promo-highlight-item">
          <span class="material-symbols-outlined">pool</span>
          <span>Infinity Pool</span>
        </div>
        <div class="promo-highlight-item">
          <span class="material-symbols-outlined">local_airport</span>
          <span>Airport Transfer</span>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- ══════════════════ ROOM SUGGESTIONS ══════════════════ -->
<section class="promo-section" id="promo-rooms">
  <div class="promo-section-header promo-animate d2">
    <h2>Handpicked Room Suggestions</h2>
    <div class="accent-line"></div>
    <p>Curated for every kind of traveler — from romantic getaways to lavish celebrations. Each room promises ocean breezes, luxury linens, and unforgettable sunsets.</p>
  </div>

  <div class="promo-rooms-grid">

    <!-- Room 1: Deluxe King -->
    <div class="promo-room-card promo-animate d2">
      <div class="promo-room-img">
        <img src="<?= BASE ?>/assets/images/promo/room_deluxe_king.png"
             alt="Deluxe King Room — The Grand Meridian" loading="lazy">
        <span class="promo-room-badge">Couples Favorite</span>
      </div>
      <div class="promo-room-body">
        <h4>Deluxe King Room</h4>
        <div class="room-size">42 m² · King Bed · Garden & Partial Sea View</div>
        <div class="promo-room-price">
          <span class="amount">₹12,500</span>
          <span class="per">/ night</span>
          <span class="original">₹16,000</span>
        </div>
        <div class="promo-room-amenities">
          <span class="promo-room-amenity"><span class="material-symbols-outlined">king_bed</span> King Bed</span>
          <span class="promo-room-amenity"><span class="material-symbols-outlined">visibility</span> Sea View</span>
          <span class="promo-room-amenity"><span class="material-symbols-outlined">bathtub</span> Rain Shower</span>
          <span class="promo-room-amenity"><span class="material-symbols-outlined">wifi</span> WiFi</span>
          <span class="promo-room-amenity"><span class="material-symbols-outlined">local_bar</span> Minibar</span>
          <span class="promo-room-amenity"><span class="material-symbols-outlined">ac_unit</span> Climate</span>
        </div>
        <div class="promo-room-cta">
          <a href="<?= BASE ?>/book.php?type=hotel&id=4" class="btn-book">
            <span class="material-symbols-outlined" style="font-size:18px">calendar_month</span>
            Book Now
          </a>
          <button class="btn-details" onclick="showHotel(4)">Details</button>
        </div>
      </div>
    </div>

    <!-- Room 2: Ocean View Suite -->
    <div class="promo-room-card promo-animate d3">
      <div class="promo-room-img">
        <img src="<?= BASE ?>/assets/images/promo/room_ocean_suite.png"
             alt="Ocean View Suite — The Grand Meridian" loading="lazy">
        <span class="promo-room-badge best">Best Seller</span>
      </div>
      <div class="promo-room-body">
        <h4>Ocean View Suite</h4>
        <div class="room-size">68 m² · King Bed · Panoramic Ocean View · Private Balcony</div>
        <div class="promo-room-price">
          <span class="amount">₹22,000</span>
          <span class="per">/ night</span>
          <span class="original">₹28,500</span>
        </div>
        <div class="promo-room-amenities">
          <span class="promo-room-amenity"><span class="material-symbols-outlined">king_bed</span> King Bed</span>
          <span class="promo-room-amenity"><span class="material-symbols-outlined">balcony</span> Balcony</span>
          <span class="promo-room-amenity"><span class="material-symbols-outlined">living</span> Living Area</span>
          <span class="promo-room-amenity"><span class="material-symbols-outlined">bathtub</span> Jacuzzi</span>
          <span class="promo-room-amenity"><span class="material-symbols-outlined">room_service</span> 24h Service</span>
          <span class="promo-room-amenity"><span class="material-symbols-outlined">coffee</span> Nespresso</span>
        </div>
        <div class="promo-room-cta">
          <a href="<?= BASE ?>/book.php?type=hotel&id=4" class="btn-book">
            <span class="material-symbols-outlined" style="font-size:18px">calendar_month</span>
            Book Now
          </a>
          <button class="btn-details" onclick="showHotel(4)">Details</button>
        </div>
      </div>
    </div>

    <!-- Room 3: Presidential Suite -->
    <div class="promo-room-card promo-animate d4">
      <div class="promo-room-img">
        <img src="<?= BASE ?>/assets/images/promo/room_presidential.png"
             alt="Presidential Suite — The Grand Meridian" loading="lazy">
        <span class="promo-room-badge" style="background:linear-gradient(135deg,#B8860B,#DAA520)">Ultra Luxury</span>
      </div>
      <div class="promo-room-body">
        <h4>Presidential Suite</h4>
        <div class="room-size">120 m² · Super King Bed · Penthouse · Sunset Panorama</div>
        <div class="promo-room-price">
          <span class="amount">₹55,000</span>
          <span class="per">/ night</span>
          <span class="original">₹72,000</span>
        </div>
        <div class="promo-room-amenities">
          <span class="promo-room-amenity"><span class="material-symbols-outlined">king_bed</span> Super King</span>
          <span class="promo-room-amenity"><span class="material-symbols-outlined">dining</span> Private Dining</span>
          <span class="promo-room-amenity"><span class="material-symbols-outlined">hot_tub</span> Private Pool</span>
          <span class="promo-room-amenity"><span class="material-symbols-outlined">diamond</span> Butler Service</span>
          <span class="promo-room-amenity"><span class="material-symbols-outlined">wine_bar</span> Wine Cellar</span>
          <span class="promo-room-amenity"><span class="material-symbols-outlined">local_laundry_service</span> Laundry</span>
        </div>
        <div class="promo-room-cta">
          <a href="<?= BASE ?>/book.php?type=hotel&id=4" class="btn-book">
            <span class="material-symbols-outlined" style="font-size:18px">calendar_month</span>
            Book Now
          </a>
          <button class="btn-details" onclick="showHotel(4)">Details</button>
        </div>
      </div>
    </div>

  </div>
</section>


<!-- ══════════════════ FACILITIES & AMENITIES ══════════════════ -->
<section class="promo-section" style="padding-top:0" id="promo-facilities">
  <div class="promo-section-header promo-animate d2">
    <h2>World-Class Facilities</h2>
    <div class="accent-line"></div>
    <p>Every corner of The Grand Meridian is designed to elevate your experience — from sunrise yoga to moonlit fine dining.</p>
  </div>

  <div class="promo-facilities-grid">
    <div class="promo-facility-card promo-animate d1">
      <img src="<?= BASE ?>/assets/images/promo/facility_pool.png"
           alt="Infinity Pool — The Grand Meridian" loading="lazy">
      <div class="promo-facility-label">
        <h5>Rooftop Infinity Pool</h5>
        <span>Panoramic ocean views · Poolside cocktails</span>
      </div>
    </div>

    <div class="promo-facility-card promo-animate d2">
      <img src="https://images.unsplash.com/photo-1544161515-4ab6ce6db874?w=600&h=400&fit=crop"
           alt="Spa & Wellness — The Grand Meridian" loading="lazy">
      <div class="promo-facility-label">
        <h5>Spa & Wellness Center</h5>
        <span>Ayurvedic treatments · Steam & sauna</span>
      </div>
    </div>

    <div class="promo-facility-card promo-animate d3">
      <img src="https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&h=400&fit=crop"
           alt="Fine Dining — The Grand Meridian" loading="lazy">
      <div class="promo-facility-label">
        <h5>Signature Restaurant</h5>
        <span>Multi-cuisine · Beachfront dining</span>
      </div>
    </div>

    <div class="promo-facility-card promo-animate d1">
      <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=600&h=400&fit=crop"
           alt="Fitness Center — The Grand Meridian" loading="lazy">
      <div class="promo-facility-label">
        <h5>Fitness Center</h5>
        <span>State-of-the-art equipment · Personal trainers</span>
      </div>
    </div>

    <div class="promo-facility-card promo-animate d2">
      <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=600&h=400&fit=crop"
           alt="Conference Hall — The Grand Meridian" loading="lazy">
      <div class="promo-facility-label">
        <h5>Grand Conference Hall</h5>
        <span>500-seat capacity · AV equipped</span>
      </div>
    </div>

    <div class="promo-facility-card promo-animate d3">
      <img src="https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?w=600&h=400&fit=crop"
           alt="Tropical Gardens — The Grand Meridian" loading="lazy">
      <div class="promo-facility-label">
        <h5>Tropical Gardens & Lobby</h5>
        <span>Landscaped courtyards · Sunset lounge</span>
      </div>
    </div>
  </div>
</section>


<!-- ══════════════════ GUEST REVIEWS ══════════════════ -->
<section class="promo-section" style="padding-top:0" id="promo-reviews">
  <div class="promo-section-header promo-animate d1">
    <h2>What Our Guests Say</h2>
    <div class="accent-line"></div>
    <p>Rated 4.9/5 across 2,400+ verified reviews on TravelNest</p>
  </div>

  <div class="promo-reviews-grid">
    <div class="promo-review-card promo-animate d1">
      <div class="promo-review-stars">★ ★ ★ ★ ★</div>
      <p class="promo-review-text">"Absolutely magical stay! The Ocean View Suite was breathtaking — waking up to that panoramic sunrise was unforgettable. The staff went above and beyond. Already planning our anniversary return!"</p>
      <div class="promo-review-author">
        <div class="promo-review-avatar">A</div>
        <div>
          <div class="promo-review-name">Ananya & Rohan S.</div>
          <div class="promo-review-date">Couple · Stayed Feb 2026</div>
        </div>
      </div>
    </div>

    <div class="promo-review-card promo-animate d2">
      <div class="promo-review-stars">★ ★ ★ ★ ★</div>
      <p class="promo-review-text">"The Presidential Suite is an absolute dream. Private pool, butler service, and that sunset dining experience on the terrace — worth every rupee. The spa treatments are world-class too."</p>
      <div class="promo-review-author">
        <div class="promo-review-avatar">M</div>
        <div>
          <div class="promo-review-name">Meera K.</div>
          <div class="promo-review-date">Business · Stayed Mar 2026</div>
        </div>
      </div>
    </div>

    <div class="promo-review-card promo-animate d3">
      <div class="promo-review-stars">★ ★ ★ ★ ★</div>
      <p class="promo-review-text">"Best family vacation we've ever had! Kids loved the pool, we loved the spa, and the restaurant's seafood was incredible. The concierge even arranged a private beach bonfire for us!"</p>
      <div class="promo-review-author">
        <div class="promo-review-avatar">V</div>
        <div>
          <div class="promo-review-name">Vikram & Priya T.</div>
          <div class="promo-review-date">Family · Stayed Jan 2026</div>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ══════════════════ BOOKING CTA BANNER ══════════════════ -->
<div style="max-width:1248px;margin:0 auto;padding:0 24px 64px">
  <div class="promo-booking-banner promo-animate d2">
    <div class="promo-banner-text">
      <h3>Ready to Experience The Grand Meridian?</h3>
      <p>Book your stay today and save up to 25% with TravelNest exclusive rates. Free cancellation available on select rooms. Limited availability for peak season — reserve now!</p>
    </div>
    <div class="promo-banner-actions">
      <a href="<?= BASE ?>/book.php?type=hotel&id=4" class="btn-big">
        <span class="material-symbols-outlined">hotel</span>
        Reserve Your Stay
      </a>
      <a href="tel:18001038747" class="btn-outline">
        📞 Call 1800-103-8747
      </a>
    </div>
  </div>
</div>


<!-- ══════════════════ SCROLL ANIMATION ══════════════════ -->
<script>
(function(){
  const els = document.querySelectorAll('.promo-animate');
  if(!('IntersectionObserver' in window)){
    els.forEach(e => e.style.opacity = '1');
    return;
  }
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if(e.isIntersecting){
        e.target.style.animationPlayState = 'running';
        io.unobserve(e.target);
      }
    });
  }, { threshold: 0.15 });
  els.forEach(el => {
    el.style.animationPlayState = 'paused';
    io.observe(el);
  });
})();
</script>

<?php require_once __DIR__.'/includes/footer.php'; ?>
