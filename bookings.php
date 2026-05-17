<?php
require_once __DIR__.'/includes/bootstrap.php';
mustLogin();
$me=me();
$pg=max(1,(int)($_GET['pg']??1));
$res=DB::paginate("SELECT * FROM bookings WHERE user_id=? ORDER BY created_at DESC",[$me['id']],$pg,10);
$icons=['Flight'=>'✈','Hotel'=>'🏨','Package'=>'📦','Train'=>'🚆','Bus'=>'🚌','Cab'=>'🚕','Cruise'=>'🚢'];
$pageTitle='My Bookings — TravelNest';
require_once __DIR__.'/includes/header.php';
?>
<div class="ov" id="det-modal"><div class="mod"><div class="mh"><h3 id="det-title"></h3><button class="mx" onclick="closeMod('det-modal')">✕</button></div><div class="mb" id="det-body"></div></div></div>
<div class="sec">
  <h2 class="stitle">My Bookings</h2>
  <p class="ssub"><?= $res['total'] ?> total booking<?= $res['total']!=1?'s':'' ?></p>

  <?php if(empty($res['data'])): ?>
  <div class="empty-state">
    <span class="empty-emoji">📋</span>
    <div class="empty-title">No bookings yet</div>
    <div class="empty-desc">Start exploring and book your first trip!</div>
    <a href="<?= BASE ?>/index.php" class="btn btn-primary">Explore Deals</a>
  </div>
  <?php else: ?>

  <div class="bk-timeline">
  <?php foreach($res['data'] as $b):
    $travelDate = $b['travel_date'] ? fmtDate($b['travel_date']) : fmtDate($b['created_at']);
    $statusCls  = ['Confirmed'=>'t-green','Pending'=>'t-amber','Cancelled'=>'t-red','Completed'=>'t-blue'];
    $sCls       = $statusCls[$b['booking_status']] ?? 't-gray';
    $bkCls      = 'bk-card status-'.strtolower($b['booking_status']);
  ?>
  <div class="<?=$bkCls?>">
    <div class="card mb10">
      <div class="flex g16">
        <div style="font-size:30px;flex-shrink:0;width:44px;text-align:center"><?= $icons[$b['booking_type']] ?? '📋' ?></div>
        <div style="flex:1;min-width:0">
          <div class="fw5" style="font-size:15px"><?= clean($b['item_name']) ?></div>
          <div class="flex g12 mt6 wrap-x">
            <span class="xs">🗓 <?= $travelDate ?></span>
            <span class="xs">ID: <span class="acc fw5"><?= clean($b['booking_ref']) ?></span></span>
            <span class="xs">PNR: <?= clean($b['pnr_number'] ?? 'N/A') ?></span>
            <span class="xs"><?= (int)$b['passengers'] ?> pax · <?= clean($b['payment_method'] ?? '') ?></span>
          </div>
        </div>
        <div class="tr" style="flex-shrink:0">
          <div class="acc fw6" style="font-size:18px"><?= rupee($b['total_amount']) ?></div>
          <span class="tag <?= $sCls ?> mt4"><?= $b['booking_status'] ?></span>
          <div class="flex g6 mt8" style="justify-content:flex-end">
            <a href="<?= BASE ?>/invoice.php?ref=<?= clean($b['booking_ref']) ?>"
               class="btn btn-blue btn-xs">📄 Invoice</a>
            <?php if($b['booking_status'] === 'Confirmed'): ?>
            <button class="btn btn-danger btn-xs"
                    onclick="cancelBk('<?= clean($b['booking_ref']) ?>')">Cancel</button>
            <?php endif; ?>
            <?php if($b['booking_status'] === 'Completed'): ?>
            <?php
            // check if review already exists
            $hasReviewed = DB::val("SELECT id FROM reviews WHERE user_id=? AND item_type=? AND item_id=?", [$me['id'], $b['booking_type'], $b['item_id']]);
            if(!$hasReviewed):
            ?>
            <button class="btn btn-primary btn-xs"
                    onclick="openReviewModal('<?= clean($b['booking_type']) ?>', <?= $b['item_id'] ?>, '<?= htmlspecialchars(clean($b['item_name']), ENT_QUOTES) ?>')">⭐ Review</button>
            <?php else: ?>
            <span class="btn btn-ghost btn-xs" style="color:var(--green); cursor:default">Reviewed ✓</span>
            <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  </div>
  <?= pagLinks($res['page'], $res['last'], BASE.'/bookings.php?') ?>
  <?php endif; ?>
</div>

<!-- Review Modal -->
<div id="review-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:999; align-items:center; justify-content:center">
    <div class="card" style="width:100%; max-width:450px">
        <div style="display:flex; justify-content:space-between; margin-bottom:20px">
            <h3 style="margin:0">Rate your experience</h3>
            <span class="material-symbols-outlined" style="cursor:pointer; color:var(--text3)" onclick="document.getElementById('review-modal').style.display='none'">close</span>
        </div>
        <p class="sm" id="review-item-name" style="margin-bottom:16px; font-weight:600; color:var(--text2)"></p>
        
        <form id="review-form" onsubmit="submitReview(event)">
            <input type="hidden" id="rev-type">
            <input type="hidden" id="rev-id">
            <input type="hidden" id="rev-name">
            <div style="display:flex; gap:8px; justify-content:center; margin-bottom:20px; font-size:32px; color:var(--border2); cursor:pointer" id="star-rating">
                <span data-val="1">★</span><span data-val="2">★</span><span data-val="3">★</span><span data-val="4">★</span><span data-val="5">★</span>
            </div>
            <input type="hidden" id="rev-rating" value="0">
            
            <div class="fg mb20">
                <label>Your Review</label>
                <textarea id="rev-comment" rows="4" placeholder="Tell us what you liked (or didn't like)..." required style="resize:vertical"></textarea>
            </div>
            
            <div style="display:flex; gap:12px; justify-content:flex-end">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('review-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary" id="rev-submit">Submit Review</button>
            </div>
        </form>
    </div>
</div>

<script>
// Review Star Logic
const stars = document.querySelectorAll('#star-rating span');
let ratingInput = document.getElementById('rev-rating');

stars.forEach(s => {
    s.addEventListener('click', () => {
        let val = parseInt(s.getAttribute('data-val'));
        ratingInput.value = val;
        stars.forEach(st => {
            if(parseInt(st.getAttribute('data-val')) <= val) {
                st.style.color = '#eab308'; // yellow
            } else {
                st.style.color = 'var(--border2)'; // gray
            }
        });
    });
});

function openReviewModal(type, id, name) {
    document.getElementById('rev-type').value = type;
    document.getElementById('rev-id').value = id;
    document.getElementById('rev-name').value = name;
    document.getElementById('review-item-name').innerText = "Booking: " + name;
    
    // reset form
    ratingInput.value = 0;
    stars.forEach(st => st.style.color = 'var(--border2)');
    document.getElementById('rev-comment').value = '';
    
    document.getElementById('review-modal').style.display = 'flex';
}

function submitReview(e) {
    e.preventDefault();
    if(ratingInput.value == 0) {
        alert("Please select a star rating first!");
        return;
    }
    document.getElementById('rev-submit').disabled = true;
    document.getElementById('rev-submit').textContent = 'Submitting...';
    
    const data = new FormData();
    data.append('a', 'submit_review');
    data.append('csrf', document.querySelector('meta[name="csrf"]').content);
    data.append('item_type', document.getElementById('rev-type').value);
    data.append('item_id', document.getElementById('rev-id').value);
    data.append('item_name', document.getElementById('rev-name').value);
    data.append('rating', ratingInput.value);
    data.append('comment', document.getElementById('rev-comment').value);
    
    fetch(_BASE + '/api.php', {
        method: 'POST',
        body: data
    }).then(r => r.json()).then(d => {
        if(d.ok) {
            alert('Review submitted successfully! Thank you for your feedback.');
            window.location.reload();
        } else {
            alert(d.msg || 'An error occurred.');
            document.getElementById('rev-submit').disabled = false;
            document.getElementById('rev-submit').textContent = 'Submit Review';
        }
    });
}
</script>

<?php require_once __DIR__.'/includes/footer.php'; ?>
