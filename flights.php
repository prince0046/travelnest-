<?php
require_once __DIR__.'/includes/bootstrap.php';
$q=clean($_GET['q']??'');$al=clean($_GET['al']??'');$st=clean($_GET['st']??'');$cl=clean($_GET['cl']??'');$rng=clean($_GET['rng']??'');$so=clean($_GET['so']??'price');$pg=max(1,(int)($_GET['pg']??1));
$w=['is_active=1'];$p=[];
if($q){$w[]='(airline LIKE ? OR flight_code LIKE ? OR from_city LIKE ? OR to_city LIKE ? OR from_code LIKE ? OR to_code LIKE ?)';$lk="%$q%";for($i=0;$i<6;$i++)$p[]=$lk;}
if($al){$w[]='airline=?';$p[]=$al;}if($st){$w[]='stops=?';$p[]=$st;}if($cl){$w[]='class=?';$p[]=$cl;}
if($rng){[$lo,$hi]=explode('-',$rng.'-999999');$w[]='price BETWEEN ? AND ?';$p[]=$lo;$p[]=$hi;}
$om=['price'=>'price ASC','price-d'=>'price DESC','dep'=>'departure_time ASC','dur'=>'duration ASC'];$ord=$om[$so]??'price ASC';
$res=DB::paginate("SELECT * FROM flights WHERE ".implode(' AND ',$w)." ORDER BY $ord",$p,$pg);
$airlines=DB::all("SELECT DISTINCT airline FROM flights WHERE is_active=1 ORDER BY airline");
$base=BASE."/flights.php?q=".urlencode($q)."&al=".urlencode($al)."&st=".urlencode($st)."&cl=".urlencode($cl)."&rng=".urlencode($rng)."&so=$so";

// Find cheapest & fastest for recommendation badges
$cheapestId=null;$fastestId=null;$cheapestPrice=PHP_INT_MAX;$fastestDur='99:99';
foreach($res['data'] as $f){
  if($f['price']<$cheapestPrice){$cheapestPrice=$f['price'];$cheapestId=$f['id'];}
  $durClean=preg_replace('/[^0-9:]/','',$f['duration']??'');
  if($durClean && $durClean<$fastestDur){$fastestDur=$durClean;$fastestId=$f['id'];}
}

// Airline color map
$airlineColors=[
  'IndiGo'=>'#4338ca','Air India'=>'#dc2626','SpiceJet'=>'#eab308',
  'Vistara'=>'#7c3aed','GoAir'=>'#059669','AirAsia'=>'#ef4444',
  'Air India Express'=>'#c2410c','Akasa Air'=>'#f97316',
  'Emirates'=>'#c8a951','Singapore Airlines'=>'#1a237e',
  'British Airways'=>'#2563eb','Lufthansa'=>'#0d47a1',
  'Thai Airways'=>'#7b1fa2','Qatar Airways'=>'#5c0d24',
];
$airlineAbbr=[
  'IndiGo'=>'6E','Air India'=>'AI','SpiceJet'=>'SG','Vistara'=>'UK',
  'GoAir'=>'G8','AirAsia'=>'I5','Air India Express'=>'IX',
  'Akasa Air'=>'QP','Emirates'=>'EK','Singapore Airlines'=>'SQ',
  'British Airways'=>'BA','Lufthansa'=>'LH','Thai Airways'=>'TG',
  'Qatar Airways'=>'QR',
];

// Aircraft types based on airline
$aircraftMap=[
  'IndiGo'=>'Airbus A320neo','Air India'=>'Boeing 787','SpiceJet'=>'Boeing 737 MAX',
  'Vistara'=>'Airbus A321neo','GoAir'=>'Airbus A320','AirAsia'=>'Airbus A320',
  'Akasa Air'=>'Boeing 737 MAX','Emirates'=>'Airbus A380','Singapore Airlines'=>'Boeing 777',
  'British Airways'=>'Boeing 787','Lufthansa'=>'Airbus A350','Qatar Airways'=>'Boeing 777',
];

$pageTitle='Flights — TravelNest';require_once __DIR__.'/includes/header.php';?>

<style>
/* ═══════════════════════════════════════════════════════
   FLIGHTS 2026 — Premium Redesign
   ═══════════════════════════════════════════════════════ */

/* --- Page Header --- */
.fl-page-header {
  max-width: 1200px;
  margin: 0 auto;
  padding: 36px 24px 0;
}
.fl-page-header h2 {
  font-size: 30px;
  font-weight: 700;
  letter-spacing: -0.5px;
  color: var(--text);
  margin: 0 0 4px;
}
.fl-page-header .fl-sub {
  color: var(--text2);
  font-size: 14px;
  margin-bottom: 24px;
}

/* --- Quick Sort Tabs --- */
.fl-sort-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}
.fl-sort-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 9px 18px;
  border-radius: 28px;
  border: 1.5px solid var(--border);
  background: #fff;
  font-size: 13px;
  font-weight: 500;
  color: var(--text2);
  cursor: pointer;
  transition: all 0.25s;
  text-decoration: none;
  white-space: nowrap;
}
.fl-sort-chip:hover {
  border-color: #0d9488;
  color: #0d9488;
  background: rgba(13,148,136,0.04);
}
.fl-sort-chip.active {
  background: linear-gradient(135deg, #0d9488, #0891b2);
  color: #fff;
  border-color: transparent;
  box-shadow: 0 4px 14px rgba(13,148,136,0.25);
}
.fl-sort-chip .material-symbols-outlined {
  font-size: 16px;
}
.fl-sort-divider {
  width: 1px;
  height: 28px;
  background: var(--border);
  margin: 0 4px;
}

/* --- Recommendation Badge --- */
.fl-rec-tag {
  position: absolute;
  top: -1px;
  right: 20px;
  padding: 4px 14px 6px;
  border-radius: 0 0 10px 10px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.3px;
  text-transform: uppercase;
  z-index: 2;
}
.fl-rec-tag.cheapest {
  background: linear-gradient(135deg, #16a34a, #15803d);
  color: #fff;
}
.fl-rec-tag.fastest {
  background: linear-gradient(135deg, #2563eb, #1d4ed8);
  color: #fff;
}

/* --- Flight Card 2026 --- */
.fl-card {
  background: #fff;
  border: 1px solid rgba(0,0,0,0.06);
  border-radius: 16px;
  padding: 0;
  margin-bottom: 14px;
  transition: all 0.35s cubic-bezier(0.22, 1, 0.36, 1);
  position: relative;
  overflow: hidden;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
  cursor: pointer;
}
.fl-card:hover {
  border-color: rgba(13,148,136,0.3);
  box-shadow: 0 8px 32px rgba(0,0,0,0.08), 0 2px 8px rgba(13,148,136,0.06);
  transform: translateY(-3px);
}
.fl-card-inner {
  display: flex;
  align-items: center;
  padding: 24px 28px;
  gap: 24px;
}

/* Airline Section */
.fl-airline {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  min-width: 90px;
  flex-shrink: 0;
}
.fl-airline-logo {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  font-weight: 800;
  color: #fff;
  letter-spacing: -0.5px;
  box-shadow: 0 3px 12px rgba(0,0,0,0.12);
  position: relative;
  overflow: hidden;
}
.fl-airline-logo::after {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 50%);
  pointer-events: none;
}
.fl-airline-name {
  font-size: 12px;
  font-weight: 600;
  color: var(--text);
  text-align: center;
  line-height: 1.2;
}
.fl-airline-code {
  font-size: 10px;
  color: var(--text3);
  font-weight: 500;
}
.fl-aircraft {
  font-size: 10px;
  color: var(--text3);
  display: flex;
  align-items: center;
  gap: 3px;
}

/* Route Section */
.fl-route {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 16px;
  min-width: 0;
}
.fl-endpoint {
  text-align: center;
  min-width: 72px;
}
.fl-time {
  font-size: 36px;
  font-weight: 800;
  font-family: 'Inter', sans-serif;
  color: var(--text);
  line-height: 1;
  letter-spacing: -1px;
}
.fl-city-code {
  font-size: 13px;
  font-weight: 600;
  color: var(--text2);
  margin-top: 4px;
}
.fl-city-name {
  font-size: 10px;
  color: var(--text3);
  margin-top: 1px;
}

/* Flight Line */
.fl-line-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  min-width: 100px;
  position: relative;
}
.fl-duration {
  font-size: 12px;
  font-weight: 600;
  color: var(--text2);
}
.fl-line-track {
  width: 100%;
  height: 2px;
  background: #e2e8f0;
  position: relative;
  border-radius: 2px;
}
.fl-line-track::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid #0d9488;
}
.fl-line-track::after {
  content: '';
  position: absolute;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #0d9488;
  border: 2px solid #0d9488;
}
.fl-plane-icon {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 24px;
  height: 24px;
  background: #fff;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1;
}
.fl-plane-icon .material-symbols-outlined {
  font-size: 15px;
  color: #0d9488;
  transform: rotate(90deg);
}
.fl-stops-label {
  font-size: 11px;
  font-weight: 500;
}
.fl-stops-label.direct {
  color: #16a34a;
}
.fl-stops-label.has-stops {
  color: #d97706;
}

/* Price Section */
.fl-price-section {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 4px;
  min-width: 160px;
  flex-shrink: 0;
}
.fl-price {
  font-size: 30px;
  font-weight: 800;
  font-family: 'Inter', sans-serif;
  letter-spacing: -0.5px;
  background: linear-gradient(135deg, #0d9488, #0891b2);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  line-height: 1;
}
.fl-class-seats {
  font-size: 12px;
  color: var(--text3);
  font-weight: 500;
}
.fl-urgency {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: 600;
  background: rgba(239,68,68,0.08);
  color: #ef4444;
  border: 1px solid rgba(239,68,68,0.15);
  animation: urgencyPulse 2s ease-in-out infinite;
}
.fl-actions {
  display: flex;
  gap: 8px;
  margin-top: 6px;
}
.fl-btn-book {
  padding: 10px 24px;
  background: linear-gradient(135deg, #0d9488, #0891b2);
  color: #fff;
  border: none;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  position: relative;
  overflow: hidden;
}
.fl-btn-book::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(120deg, rgba(255,255,255,0) 30%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0) 70%);
  transform: translateX(-100%);
  transition: transform 0.5s;
}
.fl-btn-book:hover::after {
  transform: translateX(100%);
}
.fl-btn-book:hover {
  box-shadow: 0 6px 20px rgba(13,148,136,0.35);
  transform: translateY(-1px);
}
.fl-btn-details {
  padding: 10px 16px;
  background: transparent;
  color: var(--text2);
  border: 1.5px solid var(--border);
  border-radius: 12px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}
.fl-btn-details:hover {
  border-color: #0d9488;
  color: #0d9488;
  background: rgba(13,148,136,0.04);
}

/* Wishlist Heart */
.fl-wishlist {
  position: absolute;
  top: 16px;
  right: 16px;
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  z-index: 3;
  padding: 4px;
  transition: transform 0.25s;
  line-height: 1;
}
.fl-wishlist:hover {
  transform: scale(1.2);
}

/* Bottom Amenity Strip */
.fl-amenity-strip {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 28px 12px;
  background: #f8fafb;
  border-top: 1px solid rgba(0,0,0,0.04);
  flex-wrap: wrap;
}
.fl-amenity {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  color: var(--text3);
  font-weight: 500;
}
.fl-amenity .material-symbols-outlined {
  font-size: 14px;
  color: #0d9488;
}
.fl-amenity-divider {
  width: 1px;
  height: 14px;
  background: #e2e8f0;
}

/* --- Filter Sidebar Override --- */
.fl-page-layout {
  display: grid;
  grid-template-columns: 260px 1fr;
  gap: 24px;
  align-items: start;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px 48px;
}
.fl-sidebar {
  position: sticky;
  top: 80px;
  background: #fff;
  border: 1px solid rgba(0,0,0,0.06);
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.fl-sidebar h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.fl-sidebar h3 .material-symbols-outlined {
  font-size: 20px;
  color: #0d9488;
}
.fl-sidebar .fg {
  margin-bottom: 18px;
}
.fl-sidebar label {
  font-size: 11px;
  color: var(--text3);
  margin-bottom: 6px;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  font-weight: 600;
  display: block;
}
.fl-sidebar input,
.fl-sidebar select {
  border-radius: 10px;
  border: 1.5px solid rgba(0,0,0,0.08);
  padding: 10px 14px;
  font-size: 13px;
  transition: all 0.25s;
}
.fl-sidebar input:focus,
.fl-sidebar select:focus {
  border-color: #0d9488;
  box-shadow: 0 0 0 3px rgba(13,148,136,0.1);
}
.fl-sidebar .btn-apply {
  width: 100%;
  padding: 11px 20px;
  background: linear-gradient(135deg, #0d9488, #0891b2);
  color: #fff;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  margin-top: 4px;
}
.fl-sidebar .btn-apply:hover {
  box-shadow: 0 6px 20px rgba(13,148,136,0.3);
  transform: translateY(-1px);
}

/* Results count badge */
.fl-results-count {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  background: rgba(13,148,136,0.06);
  border: 1px solid rgba(13,148,136,0.1);
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  color: #0d9488;
  margin-bottom: 20px;
}

/* Responsive */
@media (max-width: 960px) {
  .fl-page-layout {
    grid-template-columns: 1fr;
  }
  .fl-sidebar {
    position: static;
  }
  .fl-card-inner {
    flex-wrap: wrap;
    gap: 16px;
    padding: 20px;
  }
  .fl-route {
    width: 100%;
    order: -1;
  }
  .fl-time {
    font-size: 28px;
  }
  .fl-price-section {
    flex-direction: row;
    justify-content: space-between;
    width: 100%;
    align-items: center;
    min-width: auto;
  }
  .fl-amenity-strip {
    padding: 10px 20px 12px;
  }
}
@media (max-width: 600px) {
  .fl-card-inner {
    padding: 16px;
    gap: 12px;
  }
  .fl-airline {
    flex-direction: row;
    min-width: auto;
    gap: 10px;
  }
  .fl-time {
    font-size: 24px;
  }
  .fl-price {
    font-size: 22px;
  }
  .fl-sort-bar {
    overflow-x: auto;
    flex-wrap: nowrap;
    padding-bottom: 8px;
  }
}
</style>

<!-- Detail Modal -->
<div class="ov" id="det-modal"><div class="mod"><div class="mh"><h3 id="det-title"></h3><button class="mx" onclick="closeMod('det-modal')">✕</button></div><div class="mb" id="det-body"></div></div></div>

<!-- Page Header -->
<div class="fl-page-header">
  <h2>Flights</h2>
  <p class="fl-sub">Discover the best flights with real-time pricing, smart recommendations, and instant booking.</p>

  <!-- Sort Bar -->
  <div class="fl-sort-bar">
    <a href="<?=BASE?>/flights.php?<?=http_build_query(array_merge($_GET,['so'=>'price']))?>"
       class="fl-sort-chip <?=$so==='price'?'active':''?>">
      <span class="material-symbols-outlined">trending_down</span> Cheapest
    </a>
    <a href="<?=BASE?>/flights.php?<?=http_build_query(array_merge($_GET,['so'=>'dur']))?>"
       class="fl-sort-chip <?=$so==='dur'?'active':''?>">
      <span class="material-symbols-outlined">speed</span> Fastest
    </a>
    <a href="<?=BASE?>/flights.php?<?=http_build_query(array_merge($_GET,['so'=>'dep']))?>"
       class="fl-sort-chip <?=$so==='dep'?'active':''?>">
      <span class="material-symbols-outlined">schedule</span> Departure
    </a>
    <a href="<?=BASE?>/flights.php?<?=http_build_query(array_merge($_GET,['so'=>'price-d']))?>"
       class="fl-sort-chip <?=$so==='price-d'?'active':''?>">
      <span class="material-symbols-outlined">trending_up</span> Premium First
    </a>
    <div class="fl-sort-divider"></div>
    <span class="fl-results-count">
      <span class="material-symbols-outlined" style="font-size:14px">flight</span>
      <?=$res['total']?> flights found
    </span>
  </div>
</div>

<!-- Main Layout -->
<div class="fl-page-layout">
  <!-- Filter Sidebar -->
  <div class="fl-sidebar">
    <h3><span class="material-symbols-outlined">tune</span> Filters</h3>
    <form method="GET">
      <div class="fg">
        <label>Search</label>
        <input name="q" value="<?=clean($q)?>" placeholder="Airline, route, code...">
      </div>
      <div class="fg">
        <label>Airline</label>
        <select name="al" onchange="this.form.submit()">
          <option value="">All Airlines</option>
          <?php foreach($airlines as $a):?><option value="<?=clean($a['airline'])?>" <?=$al===$a['airline']?'selected':''?>><?=clean($a['airline'])?></option><?php endforeach;?>
        </select>
      </div>
      <div class="fg">
        <label>Stops</label>
        <select name="st" onchange="this.form.submit()">
          <option value="">Any Stops</option>
          <option value="Direct" <?=$st==='Direct'?'selected':''?>>Direct Only</option>
          <option value="1 Stop" <?=$st==='1 Stop'?'selected':''?>>1 Stop</option>
        </select>
      </div>
      <div class="fg">
        <label>Class</label>
        <select name="cl" onchange="this.form.submit()">
          <option value="">All Classes</option>
          <?php foreach(['Economy','Business','First Class'] as $c):?><option value="<?=$c?>" <?=$cl===$c?'selected':''?>><?=$c?></option><?php endforeach;?>
        </select>
      </div>
      <div class="fg">
        <label>Price Range</label>
        <select name="rng" onchange="this.form.submit()">
          <option value="">Any Price</option>
          <option value="0-5000" <?=$rng==='0-5000'?'selected':''?>>Under ₹5,000</option>
          <option value="5000-20000" <?=$rng==='5000-20000'?'selected':''?>>₹5,000 – ₹20,000</option>
          <option value="20000-999999" <?=$rng==='20000-999999'?'selected':''?>>Above ₹20,000</option>
        </select>
      </div>
      <input type="hidden" name="so" value="<?=clean($so)?>">
      <button type="submit" class="btn-apply">Apply Filters</button>
    </form>
  </div>

  <!-- Flight Cards -->
  <div>
    <?php if(empty($res['data'])):?>
      <div class="empty-state"><span class="empty-emoji">✈️</span><div class="empty-title">No flights found</div><div class="empty-desc">Try adjusting your filters or search for a different route.</div><a href="<?=BASE?>/flights.php" class="btn btn-primary">Clear Filters</a></div>
    <?php else:?>
    <?php foreach($res['data'] as $idx => $f):
      $alColor=$airlineColors[$f['airline']]??'#0d9488';
      $alCode=$airlineAbbr[$f['airline']]??substr($f['airline'],0,2);
      $aircraft=$aircraftMap[$f['airline']]??'Airbus A320';
      $isDirect=$f['stops']==='Direct';
      $isCheapest=$f['id']===$cheapestId && count($res['data'])>2;
      $isFastest=$f['id']===$fastestId && $fastestId!==$cheapestId && count($res['data'])>2;
      $seatsLow=$f['seats_available']<15;

      // Simulated amenities based on class
      $hasMeal=$f['class']==='Business'||$f['class']==='First Class'||rand(0,1);
      $hasLegroom=$f['class']==='Business'||$f['class']==='First Class';
      $hasUsb=true;
      $baggage=$f['class']==='Economy'?'15kg Cabin + 25kg Check-in':($f['class']==='Business'?'2× 23kg Check-in + Cabin':'2× 32kg + Cabin');
    ?>
    <div class="fl-card" onclick="showFlight(<?=$f['id']?>)">
      <!-- Recommendation Badges -->
      <?php if($isCheapest):?><span class="fl-rec-tag cheapest">✦ Cheapest</span><?php endif;?>
      <?php if($isFastest):?><span class="fl-rec-tag fastest">⚡ Fastest</span><?php endif;?>

      <!-- Wishlist -->
      <button class="fl-wishlist" onclick="event.stopPropagation();wlToggle('Flight',<?=$f['id']?>,this)" title="Save to Wishlist">🤍</button>

      <div class="fl-card-inner">
        <!-- Airline -->
        <div class="fl-airline">
          <div class="fl-airline-logo" style="background:<?=$alColor?>">
            <?=$alCode?>
          </div>
          <div class="fl-airline-name"><?=clean($f['airline'])?></div>
          <div class="fl-airline-code"><?=clean($f['flight_code'])?></div>
          <div class="fl-aircraft">
            <span class="material-symbols-outlined" style="font-size:11px;color:var(--text3)">flight</span>
            <?=$aircraft?>
          </div>
        </div>

        <!-- Route -->
        <div class="fl-route">
          <div class="fl-endpoint">
            <div class="fl-time"><?=$f['departure_time']?></div>
            <div class="fl-city-code"><?=clean($f['from_code'])?></div>
            <div class="fl-city-name"><?=clean($f['from_city'])?></div>
          </div>

          <div class="fl-line-container">
            <div class="fl-duration"><?=clean($f['duration'])?></div>
            <div class="fl-line-track">
              <div class="fl-plane-icon">
                <span class="material-symbols-outlined">flight</span>
              </div>
            </div>
            <span class="fl-stops-label <?=$isDirect?'direct':'has-stops'?>">
              <?=$isDirect?'Non-stop':clean($f['stops'])?>
            </span>
          </div>

          <div class="fl-endpoint">
            <div class="fl-time"><?=$f['arrival_time']?></div>
            <div class="fl-city-code"><?=clean($f['to_code'])?></div>
            <div class="fl-city-name"><?=clean($f['to_city'])?></div>
          </div>
        </div>

        <!-- Price & Actions -->
        <div class="fl-price-section">
          <div class="fl-price"><?=rupee($f['price'])?></div>
          <div class="fl-class-seats"><?=clean($f['class'])?> · <?=$f['seats_available']?> seats</div>
          <?php if($seatsLow):?><div class="fl-urgency">🔥 Selling fast</div><?php endif;?>
          <div class="fl-actions">
            <button class="fl-btn-details" onclick="event.stopPropagation();showFlight(<?=$f['id']?>)">Details</button>
            <a href="<?=BASE?>/book.php?type=flight&id=<?=$f['id']?>" class="fl-btn-book" onclick="event.stopPropagation()">
              Book Now
            </a>
          </div>
        </div>
      </div>

      <!-- Amenity Strip -->
      <div class="fl-amenity-strip">
        <?php if($hasMeal):?>
          <span class="fl-amenity"><span class="material-symbols-outlined">restaurant</span> Free Meal</span>
          <span class="fl-amenity-divider"></span>
        <?php endif;?>
        <?php if($hasLegroom):?>
          <span class="fl-amenity"><span class="material-symbols-outlined">airline_seat_legroom_extra</span> Extra Legroom</span>
          <span class="fl-amenity-divider"></span>
        <?php endif;?>
        <?php if($hasUsb):?>
          <span class="fl-amenity"><span class="material-symbols-outlined">charging_station</span> USB Charging</span>
          <span class="fl-amenity-divider"></span>
        <?php endif;?>
        <span class="fl-amenity"><span class="material-symbols-outlined">luggage</span> <?=$baggage?></span>
        <span class="fl-amenity-divider"></span>
        <span class="fl-amenity"><span class="material-symbols-outlined">wifi</span> WiFi</span>
        <?php if($isDirect):?>
          <span class="fl-amenity-divider"></span>
          <span class="fl-amenity" style="color:#16a34a"><span class="material-symbols-outlined" style="color:#16a34a">verified</span> Direct Flight</span>
        <?php endif;?>
      </div>
    </div>
    <?php endforeach;?>
    <?=pagLinks($res['page'],$res['last'],$base)?>
    <?php endif;?>
  </div>
</div>

<?php require_once __DIR__.'/includes/footer.php';?>
