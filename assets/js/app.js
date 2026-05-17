// TravelNest JS v5 — Premium UI

/* ─── Dark Mode Toggle ─── */
function toggleTheme() {
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  document.documentElement.setAttribute('data-theme', isDark ? 'light' : 'dark');
  localStorage.setItem('tn-theme', isDark ? 'light' : 'dark');
  const btn = document.getElementById('theme-toggle');
  if (btn) {
    btn.textContent = isDark ? '🌙' : '☀️';
    btn.setAttribute('aria-pressed', isDark ? 'false' : 'true');
  }
}
// Auto-apply saved theme on load
(function() {
  const saved = localStorage.getItem('tn-theme');
  if (saved === 'dark') {
    document.documentElement.setAttribute('data-theme', 'dark');
    const btn = document.getElementById('theme-toggle');
    if (btn) btn.textContent = '☀️';
  }
})();

/* ─── Toast with icon + border ─── */
function toast(msg, type = 'ok') {
  const t = document.getElementById('toast');
  if (!t) return;
  const icon = type === 'ok' ? '✓' : '✕';
  t.className = 'toast ' + type;
  t.innerHTML = `<span style="font-size:16px;font-weight:700">${icon}</span> ${msg}`;
  t.classList.add('show');
  clearTimeout(t._timer);
  t._timer = setTimeout(() => t.classList.remove('show'), 3800);
}

/* ─── Modals — Slide in ─── */
function openMod(id) {
  const m = document.getElementById(id);
  if (!m) return;
  m.classList.add('show');
  document.body.style.overflow = 'hidden';
}
function closeMod(id) {
  const m = document.getElementById(id);
  if (!m) return;
  m.classList.remove('show');
  document.body.style.overflow = '';
}
document.addEventListener('click', e => {
  if (e.target.classList.contains('ov')) {
    e.target.classList.remove('show');
    document.body.style.overflow = '';
  }
});
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.ov.show').forEach(m => {
      m.classList.remove('show');
    });
    document.body.style.overflow = '';
  }
});

/* ─── Search tabs ─── */
function sTab(t, el) {
  document.querySelectorAll('.stab').forEach(x => x.classList.remove('on'));
  el.classList.add('on');
  document.querySelectorAll('.spane').forEach(x => x.classList.remove('on'));
  const p = document.getElementById('s-' + t);
  if (p) p.classList.add('on');
}

/* ─── API helpers ─── */
const _BASE = (document.querySelector('meta[name=base]')?.content) || '';
const _API = _BASE + '/api.php';

async function apiFetch(qs) {
  try {
    const r = await fetch(_API + '?' + qs);
    if (!r.ok) throw new Error('HTTP ' + r.status);
    return await r.json();
  } catch (e) {
    console.error('API error:', e);
    return { err: 'Network error' };
  }
}
async function apiPost(data) {
  try {
    const csrf = document.querySelector('meta[name=csrf]')?.content || '';
    data.csrf = csrf;
    const r = await fetch(_API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams(data)
    });
    return await r.json();
  } catch (e) {
    return { err: 'Network error' };
  }
}

/* ─── Currency formatter ─── */
function rupee(n) {
  return '₹' + Number(n).toLocaleString('en-IN', { maximumFractionDigits: 0 });
}

/* ─── Date Input Formatter ─── */
function formatDateInput(e) {
  let v = e.target.value.replace(/\D/g, '').substring(0, 8);
  if (v.length >= 5) {
    e.target.value = v.substring(0, 2) + '/' + v.substring(2, 4) + '/' + v.substring(4, 8);
  } else if (v.length >= 3) {
    e.target.value = v.substring(0, 2) + '/' + v.substring(2, 4);
  } else {
    e.target.value = v;
  }
}
window.formatDateInput = formatDateInput;

/* ─── Set modal content ─── */
function setMod(title, body) {
  const t = document.getElementById('det-title');
  const b = document.getElementById('det-body');
  if (t) t.textContent = title;
  if (b) b.innerHTML = body;
}

/* ─── Detail Modals ─── */
function showFlight(id) {
  setMod('Loading...', '<div class="skeleton skel-block mb12"></div><div class="skeleton skel-line med mb8"></div><div class="skeleton skel-line short"></div>');
  openMod('det-modal');
  apiFetch('a=flight&id=' + id).then(f => {
    if (!f || f.err) return toast('Could not load details', 'err');
    const seatPct = Math.min(100, Math.round((f.seats_available / 50) * 100));
    setMod(f.airline + ' — ' + f.flight_code, `
      <div class="g2 mb16">
        <div class="card2"><div class="xs mb4">DEPARTURE</div>
          <div style="font-size:22px;font-weight:700;font-family:'Inter',sans-serif">${f.departure_time}</div>
          <div class="fw5">${f.from_city} (${f.from_code})</div></div>
        <div class="card2"><div class="xs mb4">ARRIVAL</div>
          <div style="font-size:22px;font-weight:700;font-family:'Inter',sans-serif">${f.arrival_time}</div>
          <div class="fw5">${f.to_city} (${f.to_code})</div></div>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:16px">
        ${[['Duration', f.duration], ['Aircraft', f.aircraft || '—'], ['Terminal', f.terminal || '—'],
      ['Class', f.class], ['Baggage', f.baggage || '—'], ['Seats Left', f.seats_available]]
        .map(([l, v]) => `<div class="card2 tc p12"><div class="xs">${l}</div>
            <div class="fw5 mt4" style="color:${l === 'Seats Left' && v < 10 ? 'var(--a2)' : 'inherit'}">${v}</div></div>`)
        .join('')}
      </div>
      <div class="card2 mb16">
        <div class="xs mb8">Seat Availability</div>
        <div class="pb" style="height:8px"><div class="pf" style="width:${seatPct}%;background:${seatPct < 30 ? 'var(--a2)' : seatPct < 60 ? 'var(--gold)' : 'var(--green)'}"></div></div>
        <div class="flex sb mt4"><span class="xs">${f.seats_available} seats left</span><span class="xs">${seatPct}% filled</span></div>
      </div>
      <div class="card2 flex sb mb16">
        <span class="fw5">Fare per person</span>
        <span style="font-size:24px;font-weight:700;color:var(--accent);font-family:'Inter',sans-serif">${rupee(f.price)}</span>
      </div>
      <div class="flex g8">
        <button class="btn btn-ghost w100" onclick="wlToggle('Flight',${f.id},this)">🤍 Save</button>
        <a href="${_BASE}/book.php?type=flight&id=${f.id}" class="btn btn-primary w100">Book This Flight →</a>
      </div>`);
  });
}

/* ─── Hotel & Cab Image Maps ─── */
const _hotelImgs = {
  'Four Seasons Mumbai': 'assets/images/hotels/hotel_1.jpg',
  'Atlantis The Palm': 'assets/images/hotels/hotel_2.jpg',
  'The Plaza Hotel': 'assets/images/hotels/hotel_3.jpg',
  'Marina Bay Sands': 'assets/images/hotels/hotel_4.jpg',
  'Ritz Paris': 'assets/images/hotels/hotel_5.jpg',
  'The Savoy': 'assets/images/hotels/hotel_6.jpg',
  'Bellagio': 'assets/images/hotels/hotel_7.jpg',
  'Aman Tokyo': 'assets/images/hotels/hotel_8.jpg',
  'Giraffe Manor': 'assets/images/hotels/hotel_9.jpg',
  'Royal Mansour': 'assets/images/hotels/hotel_10.jpg',
  'Soneva Jani': 'assets/images/hotels/hotel_11.jpg',
  'Fairmont Banff Springs': 'assets/images/hotels/hotel_12.jpg',
  'Park Hyatt Sydney': 'assets/images/hotels/hotel_13.jpg',
  'Mandarin Oriental': 'assets/images/hotels/hotel_14.jpg',
  'Shangri-La Paris': 'assets/images/hotels/hotel_15.jpg',
  'Hotel Danieli': 'assets/images/hotels/hotel_16.jpg',
  'La Mamounia': 'assets/images/hotels/hotel_17.jpg',
  'Copacabana Palace': 'assets/images/hotels/hotel_18.jpg',
  'Burj Al Arab Jumeirah': 'assets/images/hotels/hotel_19.jpg',
  'The Taj Mahal Palace': 'assets/images/hotels/hotel_20.jpg',
};
const _defaultHotelImg = 'assets/images/hotels/hotel_1.jpg';
const _hotelGallery = Array.from({ length: 60 }, (_, i) => `assets/images/rooms/room_${i + 1}.jpg`);

const _cabImgs = {
  'Maruti Suzuki Swift': 'assets/images/cabs/1images.jpeg',
  'Hyundai i20': 'assets/images/cabs/2images.jpeg',
  'Tata Tiago': 'assets/images/cabs/3images.jpeg',
  'Maruti Suzuki Baleno': 'assets/images/cabs/4images.jpeg',
  'Renault Kwid': 'assets/images/cabs/5images.jpeg',
  'Maruti Suzuki Dzire': 'assets/images/cabs/6images.jpeg',
  'Honda City': 'assets/images/cabs/7images.jpeg',
  'Hyundai Verna': 'assets/images/cabs/8images.jpeg',
  'Volkswagen Virtus': 'assets/images/cabs/9images.jpeg',
  'Hyundai Creta': 'assets/images/cabs/10images.jpeg',
  'Tata Nexon': 'assets/images/cabs/11images.jpeg',
  'Kia Seltos': 'assets/images/cabs/12images.jpeg',
  'Toyota Innova Crysta': 'assets/images/cabs/13images.jpeg',
  'Maruti Suzuki Ertiga': 'assets/images/cabs/14images.jpeg',
  'Mahindra XUV700': 'assets/images/cabs/15images.jpeg',
  'Toyota Fortuner': 'assets/images/cabs/16images.jpeg',
  'Jeep Compass': 'assets/images/cabs/17images.jpeg',
  'Mercedes-Benz E-Class': 'assets/images/cabs/18images.jpeg',
  'BMW 5 Series': 'assets/images/cabs/19images.jpeg',
  'Audi A6': 'assets/images/cabs/20images.jpeg',
};
const _defaultCabImg = 'https://images.unsplash.com/photo-1502877338535-766e1452684a?w=400&h=300&fit=crop';

function showHotel(id) {
  setMod('Loading...', '<div class="skeleton skel-block mb12"></div><div class="skeleton skel-line med mb8"></div><div class="skeleton skel-line short"></div>');
  openMod('det-modal');
  apiFetch('a=hotel&id=' + id).then(h => {
    if (!h || h.err) return toast('Could not load details', 'err');
    const am = (h.amenities || '').split(',').filter(Boolean).map(a => `<span class="chip">${a.trim()}</span>`).join('');
    const revs = (h.reviews || []).map(r => `
      <div class="card-sm mb8">
        <div class="flex g8 mb4">
          <span style="color:var(--gold)">${'★'.repeat(Number(r.rating) || 0)}</span>
          <span class="fw5 sm">${r.user_name || 'Guest'}</span>
        </div>
        <div class="sm">${r.comment || ''}</div>
      </div>`).join('');
    const mainImg = _hotelImgs[h.name] || _defaultHotelImg;
    const hid = Number(h.id) || 1;
    const baseRoom = (hid - 1) * 3;
    const r1 = _hotelGallery[baseRoom % _hotelGallery.length] || _hotelGallery[0];
    const r2 = _hotelGallery[(baseRoom + 1) % _hotelGallery.length] || _hotelGallery[1];
    const r3 = _hotelGallery[(baseRoom + 2) % _hotelGallery.length] || _hotelGallery[2];
    const roomImgs = [r1, r2, r3];

    const gallery = `<div style="margin-bottom:16px">
      <div style="border-radius:var(--rl);overflow:hidden;height:220px;background:url('${mainImg}') center/cover no-repeat;position:relative;margin-bottom:12px">
        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.3),transparent 60%)"></div>
        <span class="rb" style="position:absolute;top:12px;right:12px">${h.rating}/10</span>
      </div>
      <div class="fw5 mb8" style="font-size:13px;color:rgba(0,0,0,0.6)">ROOMS & SUITES</div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px">
        ${roomImgs.map(img => `<div style="border-radius:var(--r);overflow:hidden;height:70px;background:url('${img}') center/cover no-repeat;cursor:pointer;transition:opacity .2s;box-shadow:inset 0 0 0 1px rgba(0,0,0,0.05)" onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'"></div>`).join('')}
      </div>
    </div>`;
    setMod(h.name, `
      ${gallery}
      <div class="flex g16 mb16">
        <div>
          <div class="sm mb4">📍 ${h.city}, ${h.country || 'India'} · <span style="color:var(--gold)">${'★'.repeat(Number(h.stars) || 0)}</span></div>
          <div class="flex g8 mb4 wrap-x">
            ${h.free_cancellation == '1' ? '<span class="tag t-green">Free Cancellation</span>' : ''}
            <span class="tag t-blue">${h.meal_plan || 'Room Only'}</span>
          </div>
        </div>
      </div>
      <p class="sm mb16" style="line-height:1.7">${h.description || ''}</p>
      <div class="mb16"><div class="fw5 mb8">Amenities</div><div>${am || '—'}</div></div>
      ${revs ? `<div class="mb16"><div class="fw5 mb8">Guest Reviews</div>${revs}</div>` : ''}
      <div class="card2 flex sb mb16">
        <span class="fw5">Price per night</span>
        <span style="font-size:24px;font-weight:700;color:var(--accent);font-family:'Inter',sans-serif">${rupee(h.price_per_night)}</span>
      </div>
      <div class="flex g8">
        <button class="btn btn-ghost w100" onclick="wlToggle('Hotel',${h.id},this)">🤍 Save</button>
        <a href="${_BASE}/book.php?type=hotel&id=${h.id}" class="btn btn-primary w100">Book This Hotel →</a>
      </div>`);
  });
}

function showPackage(id) {
  setMod('Loading...', '<div class="skeleton skel-block mb12"></div><div class="skeleton skel-line med mb8"></div><div class="skeleton skel-line short"></div>');
  openMod('det-modal');
  apiFetch('a=pkg&id=' + id).then(p => {
    if (!p || p.err) return toast('Could not load details', 'err');
    const inc = (p.inclusions || '').split('|').filter(Boolean).map(i => `<span class="chip">${i.trim()}</span>`).join('');
    const hl = (p.highlights || '').split('|').filter(Boolean).map(h => `
      <div class="flex g8 mb6">
        <span style="color:var(--accent);flex-shrink:0">◆</span>
        <span class="sm">${h.trim()}</span>
      </div>`).join('');
    // Tabbed content
    setMod(p.name, `
      <div class="flex g8 mb16" id="pkg-tabs">
        <button class="btn btn-sm btn-primary" onclick="pkgTab('overview',this)">Overview</button>
        <button class="btn btn-sm btn-ghost" onclick="pkgTab('itinerary',this)">Itinerary</button>
        <button class="btn btn-sm btn-ghost" onclick="pkgTab('inclusions',this)">Inclusions</button>
      </div>
      <div id="pkg-overview" class="pkg-tab-pane">
        <div class="flex g16 mb16">
          <div style="font-size:54px;flex-shrink:0">${p.emoji || '📦'}</div>
          <div>
            <div class="flex g8 wrap-x mb8">
              <span class="tag t-amber">${p.tag || ''}</span>
              <span class="tag t-blue">${p.category || ''}</span>
            </div>
            <div class="sm mb4">📍 ${p.destination || ''} · 🌙 ${p.nights || ''} Nights</div>
            <div class="sm">${p.description || ''}</div>
          </div>
        </div>
        ${hl ? `<div class="mb16"><div class="fw5 mb8">Highlights</div>${hl}</div>` : ''}
      </div>
      <div id="pkg-itinerary" class="pkg-tab-pane" style="display:none">
        <div class="card2 p16">
          <div class="fw5 mb12">Day-by-Day Itinerary</div>
          ${Array.from({ length: Number(p.nights) || 3 }, (_, i) => `
            <div class="flex g12 mb12">
              <div style="width:36px;height:36px;border-radius:50%;background:rgba(249,115,22,.08);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">D${i + 1}</div>
              <div class="sm">Day ${i + 1} — Explore ${p.destination || 'destination'}</div>
            </div>`).join('')}
        </div>
      </div>
      <div id="pkg-inclusions" class="pkg-tab-pane" style="display:none">
        <div class="mb16"><div class="fw5 mb8">What's Included</div><div>${inc || '—'}</div></div>
      </div>
      <div class="card2 flex sb mb16 mt16">
        <span class="fw5">Per person</span>
        <span style="font-size:24px;font-weight:700;color:var(--accent);font-family:'Inter',sans-serif">${rupee(p.price)}</span>
      </div>
      <div class="flex g8">
        <button class="btn btn-ghost w100" onclick="wlToggle('Package',${p.id},this)">🤍 Save</button>
        <a href="${_BASE}/book.php?type=package&id=${p.id}" class="btn btn-primary w100">Book Package →</a>
      </div>`);
  });
}

// Package tab switcher
function pkgTab(tab, btn) {
  document.querySelectorAll('.pkg-tab-pane').forEach(p => p.style.display = 'none');
  document.querySelectorAll('#pkg-tabs button').forEach(b => { b.className = 'btn btn-sm btn-ghost'; });
  btn.className = 'btn btn-sm btn-primary';
  const pane = document.getElementById('pkg-' + tab);
  if (pane) pane.style.display = 'block';
}

function showTrain(id) {
  setMod('Loading...', '<div class="skeleton skel-block mb12"></div><div class="skeleton skel-line med mb8"></div><div class="skeleton skel-line short"></div>');
  openMod('det-modal');
  apiFetch('a=train&id=' + id).then(t => {
    if (!t || t.err) return toast('Could not load', 'err');
    const fares = [
      [t.price_1a, '1A — AC First Class'],
      [t.price_2a, '2A — AC 2 Tier'],
      [t.price_3a, '3A — AC 3 Tier'],
      [t.price_sl, 'SL — Sleeper']
    ].filter(f => Number(f[0]) > 0);
    setMod(t.train_number + ' — ' + t.train_name, `
      <div class="g2 mb12">
        <div class="card2 p12">
          <div class="xs mb4">FROM</div>
          <div style="font-size:18px;font-weight:700">${t.departure_time}</div>
          <div class="sm">${t.from_station}</div>
        </div>
        <div class="card2 p12">
          <div class="xs mb4">TO</div>
          <div style="font-size:18px;font-weight:700">${t.arrival_time}</div>
          <div class="sm">${t.to_station}</div>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:14px">
        <div class="card2 tc p12"><div class="xs">Duration</div><div class="fw5 mt4">${t.duration}</div></div>
        <div class="card2 tc p12"><div class="xs">Type</div><div class="fw5 mt4">${t.train_type}</div></div>
        <div class="card2 tc p12"><div class="xs">Runs</div><div class="fw5 mt4" style="font-size:11px">${t.running_days}</div></div>
      </div>
      <div class="card2 mb16">
        <div class="fw5 mb12">Fare by Class</div>
        ${fares.map(([pr, lbl]) => `<div class="info-row"><span class="sm">${lbl}</span><span class="fw5 acc">${rupee(pr)}</span></div>`).join('')}
      </div>
      <a href="${_BASE}/book.php?type=train&id=${t.id}" class="btn btn-primary w100">Book Ticket →</a>`);
  });
}

function showBus(id) {
  setMod('Loading...', '<div class="skeleton skel-block mb12"></div><div class="skeleton skel-line med mb8"></div><div class="skeleton skel-line short"></div>');
  openMod('det-modal');
  apiFetch('a=bus&id=' + id).then(b => {
    if (!b || b.err) return toast('Could not load', 'err');
    const am = (b.amenities || '').split(',').filter(Boolean).map(a => `<span class="chip">${a.trim()}</span>`).join('');
    setMod(b.operator_name + ' — ' + b.bus_type, `
      <div class="g2 mb12">
        <div class="card2 tc p12">
          <div class="xs mb4">DEPARTURE</div>
          <div style="font-size:20px;font-weight:700">${b.departure_time}</div>
          <div class="sm">${b.from_city}</div>
        </div>
        <div class="card2 tc p12">
          <div class="xs mb4">ARRIVAL</div>
          <div style="font-size:20px;font-weight:700">${b.arrival_time}</div>
          <div class="sm">${b.to_city}</div>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:14px">
        <div class="card2 tc p12"><div class="xs">Duration</div><div class="fw5 mt4">${b.duration}</div></div>
        <div class="card2 tc p12"><div class="xs">Seats Left</div><div class="fw5 mt4 grn">${b.seats_available}</div></div>
        <div class="card2 tc p12"><div class="xs">Rating</div><div class="fw5 mt4">⭐ ${b.rating}</div></div>
      </div>
      <div class="mb14"><div class="fw5 mb8">Amenities</div><div>${am || '—'}</div></div>
      <div class="card2 flex sb mb16">
        <span class="fw5">Fare per seat</span>
        <span style="font-size:22px;font-weight:700;color:var(--accent);font-family:'Inter',sans-serif">${rupee(b.price)}</span>
      </div>
      <a href="${_BASE}/book.php?type=bus&id=${b.id}" class="btn btn-primary w100">Book Seat →</a>`);
  });
}

function showCab(id) {
  setMod('Loading...', '<div class="skeleton skel-block mb12"></div><div class="skeleton skel-line med mb8"></div><div class="skeleton skel-line short"></div>');
  openMod('det-modal');
  apiFetch('a=cab&id=' + id).then(c => {
    if (!c || c.err) return toast('Could not load', 'err');
    const am = (c.amenities || '').split(',').filter(Boolean).map(a => `<span class="chip">${a.trim()}</span>`).join('');
    const base = Number(c.base_fare); const pkm = Number(c.price_per_km); const mink = Number(c.min_km);
    const est = [20, 40, 60, 80, 100, 150].map(km => {
      const f = base + Math.max(0, km - mink) * pkm;
      return `<div class="info-row"><span class="sm">${km} km</span><span class="acc">${rupee(f)}</span></div>`;
    }).join('');
    const cabImg = _cabImgs[c.vehicle_name] || _defaultCabImg;
    setMod(c.vehicle_name + ' (' + c.cab_type + ')', `
      <div style="border-radius:var(--rl);overflow:hidden;height:220px;background:url('${cabImg}') center/cover no-repeat;position:relative;margin-bottom:16px">
        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.35),transparent 60%)"></div>
        <div style="position:absolute;bottom:16px;left:16px;z-index:2">
          <div class="fw5 lg" style="color:#fff;text-shadow:0 2px 8px rgba(0,0,0,.5)">${c.vehicle_name}</div>
          <span class="tag t-amber">${c.cab_type}</span>
          <span class="tag t-blue" style="margin-left:4px">Max ${c.capacity} passengers</span>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:16px">
        <div class="card2 tc p12"><div class="xs">Base Fare</div><div class="fw5 acc mt4">${rupee(c.base_fare)}</div></div>
        <div class="card2 tc p12"><div class="xs">Per KM</div><div class="fw5 acc mt4">₹${c.price_per_km}/km</div></div>
        <div class="card2 tc p12"><div class="xs">Min KM</div><div class="fw5 mt4">${c.min_km} km</div></div>
      </div>
      <div class="mb16"><div class="fw5 mb8">Features</div><div>${am || '—'}</div></div>
      <div class="card2 mb16">
        <div class="fw5 mb10">Fare Estimates</div>${est}
      </div>
      <a href="${_BASE}/book.php?type=cab&id=${c.id}" class="btn btn-primary w100">Book Cab →</a>`);
  });
}

function showCruise(id) {
  setMod('Loading...', '<div class="skeleton skel-block mb12"></div><div class="skeleton skel-line med mb8"></div><div class="skeleton skel-line short"></div>');
  openMod('det-modal');
  apiFetch('a=cruise&id=' + id).then(c => {
    if (!c || c.err) return toast('Could not load', 'err');
    const inc = (c.inclusions || '').split(',').filter(Boolean).map(i => `<span class="chip">${i.trim()}</span>`).join('');
    setMod(c.cruise_name, `
      <div class="flex g16 mb16">
        <div style="font-size:54px;flex-shrink:0">${c.emoji || '🚢'}</div>
        <div>
          <div class="flex g8 mb8 wrap-x">
            <span class="tag t-blue">${c.cruise_type}</span>
            <span class="tag t-teal">${c.category}</span>
          </div>
          <div class="sm mb4">🚢 ${c.ship_name}</div>
          <div class="sm">${c.from_port} → ${c.to_port}</div>
        </div>
      </div>
      <div class="g2 mb12">
        <div class="card2 p12"><div class="xs mb4">DEPARTS</div><div class="fw5">${c.departure_schedule}</div></div>
        <div class="card2 p12"><div class="xs mb4">ARRIVES</div><div class="fw5">${c.arrival_schedule}</div></div>
      </div>
      <div class="card2 tc p12 mb12">
        <div class="xs">Duration</div>
        <div class="fw5 mt4">${Number(c.nights) > 0 ? c.nights + ' Nights' : 'Day Cruise'}</div>
      </div>
      <div class="mb12"><div class="fw5 mb8">Included</div><div>${inc || '—'}</div></div>
      <div class="card2 flex sb mb16">
        <span class="fw5">Per person</span>
        <span style="font-size:22px;font-weight:700;color:var(--accent);font-family:'Inter',sans-serif">${rupee(c.price)}</span>
      </div>
      <div class="flex g8">
        <button class="btn btn-ghost w100" onclick="wlToggle('Cruise',${c.id},this)">🤍 Save</button>
        <a href="${_BASE}/book.php?type=cruise&id=${c.id}" class="btn btn-primary w100">Book Cruise →</a>
      </div>`);
  });
}

/* ─── Admin user view ─── */
async function viewUser(id) {
  const u = await apiFetch('a=user&id=' + id);
  if (!u || u.err) return toast('User not found', 'err');
  const initials = u.name.split(' ').map(w => w[0]).slice(0, 2).join('');
  const tierCls = { 'Platinum': 'tier-plat', 'Gold': 'tier-gold', 'Silver': 'tier-silver', 'Bronze': 'tier-bronze' };
  const tierColors = { Platinum: 'var(--purple)', Gold: 'var(--gold)', Silver: '#94a3b8', Bronze: '#d97706' };
  setMod('User Profile — ' + u.name, `
    <div class="flex g16 mb20">
      <div style="width:56px;height:56px;border-radius:50%;background:var(--bg3);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:var(--accent);flex-shrink:0">${initials}</div>
      <div>
        <div class="fw5 lg">${u.name}</div>
        <div class="sm">${u.email}</div>
        <div class="flex g8 mt4 wrap-x">
          <span class="tier-badge ${tierCls[u.tier] || ''}">${u.tier}</span>
          <span class="tag ${u.is_active == '1' ? 't-green' : 't-red'}">${u.is_active == '1' ? 'Active' : 'Suspended'}</span>
        </div>
      </div>
    </div>
    <div class="g2 mb16">
      ${[['Phone', u.phone || 'N/A'], ['City', u.city || 'N/A'], ['Joined', u.created_at?.split(' ')[0] || '—'],
    ['Bookings', u.total_bookings], ['Total Spent', rupee(u.total_spent)], ['Role', u.role]]
      .map(([l, v]) => `<div class="card-sm flex sb"><span class="xs">${l}</span><span class="fw5">${v}</span></div>`)
      .join('')}
    </div>
    <div class="flex g8">
      <button class="btn btn-${u.is_active == '1' ? 'danger' : 'green'} btn-sm" onclick="toggleStatus('user',${u.id},${u.is_active})">
        ${u.is_active == '1' ? 'Suspend User' : 'Activate User'}
      </button>
    </div>`);
  openMod('det-modal');
}

/* ─── Wishlist toggle ─── */
async function wlToggle(type, id, btn) {
  const d = await apiPost({ a: 'wl', type, id });
  if (d.login) { window.location = _BASE + '/login.php'; return; }
  if (d.err) { toast(d.err, 'err'); return; }
  if (btn) btn.innerHTML = d.saved ? '❤️ Saved' : '🤍 Save';
  toast(d.saved ? 'Added to wishlist ❤️' : 'Removed from wishlist');
}

/* ─── Promo code ─── */
let _discAmt = 0;
async function checkPromo() {
  const inp = document.getElementById('promo-in');
  if (!inp) return;
  const code = inp.value.trim().toUpperCase();
  const base = parseFloat(document.getElementById('base-amt')?.value || 0);
  const msg = document.getElementById('promo-msg');
  if (!code) { if (msg) msg.innerHTML = '<span class="tag t-amber">Enter a promo code</span>'; return; }
  const d = await apiFetch('a=promo&code=' + encodeURIComponent(code) + '&amount=' + base);
  if (d.ok) {
    _discAmt = d.disc;
    document.getElementById('disc-amt').value = d.disc;
    document.getElementById('disc-row')?.classList.remove('hidden');
    document.getElementById('disc-show').textContent = '-' + rupee(d.disc);
    if (msg) msg.innerHTML = `<span class="tag t-green">${d.label} — you save ${rupee(d.disc)}</span>`;
    recalcTotal();
    toast('Promo applied! Saving ' + rupee(d.disc));
  } else {
    _discAmt = 0;
    document.getElementById('disc-amt').value = 0;
    document.getElementById('disc-row')?.classList.add('hidden');
    if (msg) msg.innerHTML = `<span class="tag t-red">${d.msg || 'Invalid code'}</span>`;
    recalcTotal();
  }
}
function recalcTotal() {
  const base = parseFloat(document.getElementById('base-amt')?.value || 0);
  const tax = Math.round(base * .12);
  const total = Math.max(0, base + tax - _discAmt);
  const td = document.getElementById('total-show');
  if (td) td.textContent = rupee(total);
  const pb = document.getElementById('pay-btn');
  if (pb) pb.textContent = 'Confirm & Pay ' + rupee(total);
}

/* ─── Cancel booking ─── */
async function cancelBk(ref) {
  if (!confirm('Cancel booking ' + ref + '?\n\nRefund will be processed in 5–7 business days.')) return;
  const d = await apiPost({ a: 'cancel', ref });
  if (d.ok) {
    toast('Booking cancelled. Refund initiated. ✓');
    setTimeout(() => location.reload(), 1500);
  } else {
    toast(d.msg || 'Could not cancel booking', 'err');
  }
}

/* ─── Admin actions ─── */
async function toggleStatus(type, id, cur) {
  const newStatus = cur == '1' || cur === 1 ? 0 : 1;
  const d = await apiPost({ a: 'toggle', type, id, status: newStatus });
  if (d.ok) {
    toast('Status updated ✓');
    setTimeout(() => location.reload(), 1000);
  } else {
    toast('Failed to update', 'err');
  }
}
async function delItem(type, id, name) {
  if (!confirm('Disable "' + name + '"?\n\nThis will hide the item from users.')) return;
  const d = await apiPost({ a: 'del', type, id });
  if (d.ok) {
    toast('Item disabled ✓');
    setTimeout(() => location.reload(), 1000);
  } else {
    toast('Failed to disable', 'err');
  }
}

/* ─── Admin booking cancel ─── */
async function adminCancelBk(ref) {
  if (!confirm('Cancel booking ' + ref + '?\n\nThis will cancel the booking and initiate a refund.')) return;
  const d = await apiPost({ a: 'admin_booking_status', ref, status: 'Cancelled' });
  if (d.ok) {
    toast('Booking cancelled ✓');
    setTimeout(() => location.reload(), 1200);
  } else {
    toast(d.msg || 'Failed to cancel', 'err');
  }
}

/* ─── Admin booking status update ─── */
async function adminUpdateBkStatus(sel) {
  const ref = sel.dataset.ref;
  const status = sel.value;
  const d = await apiPost({ a: 'admin_booking_status', ref, status });
  if (d.ok) {
    toast('Status updated to ' + status + ' ✓');
    setTimeout(() => location.reload(), 1200);
  } else {
    toast(d.msg || 'Failed to update status', 'err');
    location.reload();
  }
}

/* ─── Booking Wizard ─── */
let currentStep = 1;
function wizardGo(step) {
  const panels = document.querySelectorAll('.step-panel');
  const steps = document.querySelectorAll('.wizard-step');
  const connectors = document.querySelectorAll('.wizard-connector');
  if (step < 1 || step > panels.length) return;
  currentStep = step;
  panels.forEach((p, i) => {
    p.classList.toggle('active', i + 1 === step);
  });
  steps.forEach((s, i) => {
    s.classList.remove('active', 'done');
    if (i + 1 === step) s.classList.add('active');
    else if (i + 1 < step) s.classList.add('done');
  });
  connectors.forEach((c, i) => {
    c.classList.toggle('done', i + 1 < step);
  });
}

/* ─── Mobile Nav Drawer ─── */
function openDrawer() {
  const drawer  = document.getElementById('nav-drawer');
  const overlay = document.getElementById('nav-overlay');
  const toggle  = document.querySelector('.nav-toggle');
  drawer?.classList.add('open');
  overlay?.classList.add('show');
  if (drawer)  drawer.setAttribute('aria-hidden', 'false');
  if (overlay) overlay.setAttribute('aria-hidden', 'false');
  if (toggle)  toggle.setAttribute('aria-expanded', 'true');
  document.body.style.overflow = 'hidden';
}
function closeDrawer() {
  const drawer  = document.getElementById('nav-drawer');
  const overlay = document.getElementById('nav-overlay');
  const toggle  = document.querySelector('.nav-toggle');
  drawer?.classList.remove('open');
  overlay?.classList.remove('show');
  if (drawer)  drawer.setAttribute('aria-hidden', 'true');
  if (overlay) overlay.setAttribute('aria-hidden', 'true');
  if (toggle)  toggle.setAttribute('aria-expanded', 'false');
  document.body.style.overflow = '';
}

/* ─── Scroll Reveal (Intersection Observer) ─── */
function initReveal() {
  const els = document.querySelectorAll('.reveal');
  if (!els.length) return;
  const obs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
  els.forEach((el, i) => {
    el.style.transitionDelay = `${i * 0.08}s`;
    obs.observe(el);
  });
}

/* ─── Skeleton Helper ─── */
function showSkeleton(container, count = 3) {
  let html = '';
  for (let i = 0; i < count; i++) {
    html += `<div class="card mb10" style="padding:20px">
      <div class="flex g12">
        <div class="skeleton skel-circle"></div>
        <div style="flex:1">
          <div class="skeleton skel-line med"></div>
          <div class="skeleton skel-line short"></div>
        </div>
        <div style="width:80px">
          <div class="skeleton skel-line"></div>
        </div>
      </div>
    </div>`;
  }
  if (container) container.innerHTML = html;
}

/* ─── DOMContentLoaded ─── */
document.addEventListener('DOMContentLoaded', () => {
  // Scroll reveal
  initReveal();

  // Auto-dismiss flash messages
  const fl = document.querySelector('.flash');
  if (fl) {
    // Add close button
    const closeBtn = document.createElement('button');
    closeBtn.className = 'flash-close';
    closeBtn.textContent = '✕';
    closeBtn.onclick = () => { fl.style.opacity = '0'; fl.style.transition = 'opacity .3s'; setTimeout(() => fl.remove(), 300); };
    fl.appendChild(closeBtn);
    setTimeout(() => { fl.style.opacity = '0'; fl.style.transition = 'opacity .5s'; setTimeout(() => fl.remove(), 500); }, 5000);
  }

  // Admin table: click row to show detail
  document.querySelectorAll('table.dt tbody tr[data-type][data-id]').forEach(row => {
    row.addEventListener('click', function (e) {
      if (e.target.closest('button') || e.target.closest('a') || e.target.closest('select') || e.target.closest('form')) return;
      const { type, id } = this.dataset;
      const fnMap = {
        user: viewUser, flight: showFlight, hotel: showHotel,
        package: showPackage, train: showTrain, bus: showBus,
        cab: showCab, cruise: showCruise
      };
      if (fnMap[type]) fnMap[type](id);
    });
  });

  // Auto-uppercase promo input
  const pi = document.getElementById('promo-in');
  if (pi) pi.addEventListener('input', () => pi.value = pi.value.toUpperCase());
  if (pi) pi.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); checkPromo(); } });

  // Mobile nav drawer overlay close
  document.getElementById('nav-overlay')?.addEventListener('click', closeDrawer);

  // Initialize wizard if present
  if (document.querySelector('.wizard-progress')) {
    wizardGo(1);
  }
});
