<?php
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Offers & Deals — TravelNest';
require_once __DIR__ . '/includes/header.php';

$activePromos = DB::all("SELECT * FROM promo_codes WHERE status IN('Active','Expiring') AND valid_until>=CURDATE() ORDER BY discount_value DESC");

// Group promos by applicable type
$groupedPromos = [
    'All' => [],
    'Flight' => [],
    'Hotel' => [],
    'Package' => [],
    'Other' => []
];

foreach ($activePromos as $p) {
    if (isset($groupedPromos[$p['applicable_type']])) {
        $groupedPromos[$p['applicable_type']][] = $p;
    } else {
        $groupedPromos['Other'][] = $p;
    }
}

// Fetch user's past bookings for Loyalty Codes
$pastBookings = [];
if (loggedIn()) {
    $me = me();
    $pastBookings = DB::all("SELECT * FROM bookings WHERE user_id=? AND booking_status IN('Confirmed','Completed') ORDER BY created_at DESC", [$me['id']]);
}
?>

<style>
/* Filters and Layout */
.offers-sidebar {
    position: sticky;
    top: 80px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--rxl);
    padding: 24px;
    box-shadow: var(--shadow);
}
.offer-filter {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: var(--r);
    color: var(--text2);
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 4px;
}
.offer-filter:hover {
    background: var(--bg2);
    color: var(--text);
}
.offer-filter.active {
    background: rgba(0,140,255,0.06);
    color: var(--accent);
    font-weight: 600;
}
.offer-filter .material-symbols-outlined {
    font-size: 20px;
}

/* Loyalty Card Special Styling */
.loyalty-banner {
    background: linear-gradient(135deg, #1e1b4b, #312e81, #4338ca);
    border-radius: var(--rxl);
    padding: 32px;
    color: #fff;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(67, 56, 202, 0.3);
}
.loyalty-banner::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
    border-radius: 50%;
}
.loyalty-card {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: var(--rl);
    padding: 20px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s;
}
.loyalty-card:hover {
    background: rgba(255,255,255,0.15);
    transform: translateY(-2px);
}
.loyalty-code-box {
    background: #fff;
    color: #1e1b4b;
    padding: 10px 20px;
    border-radius: var(--r);
    font-family: 'DM Sans', monospace;
    font-size: 18px;
    font-weight: 800;
    letter-spacing: 1px;
    border: 2px dashed #4338ca;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 10px;
}
.loyalty-code-box:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

/* Standard Promo Card */
.promo-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--rxl);
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 24px;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow);
}
.promo-card:hover {
    border-color: rgba(0,140,255,0.4);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}
.promo-card-left {
    min-width: 140px;
    text-align: center;
    border-right: 1px dashed var(--border2);
    padding-right: 24px;
}
.promo-value {
    font-size: 32px;
    font-weight: 800;
    color: var(--accent);
    line-height: 1.1;
    font-family: 'Inter', sans-serif;
    letter-spacing: -1px;
}
.promo-type {
    font-size: 12px;
    color: var(--text3);
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
    margin-top: 4px;
}
.promo-card-main {
    flex: 1;
}
.promo-code-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(0,140,255,0.06);
    border: 1px dashed var(--accent);
    color: var(--accent);
    font-family: 'DM Sans', monospace;
    font-weight: 700;
    font-size: 15px;
    border-radius: var(--r);
    cursor: pointer;
    transition: all 0.2s;
}
.promo-code-btn:hover {
    background: rgba(0,140,255,0.1);
}

/* Category Headers */
.category-header {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 20px;
    font-weight: 700;
    margin: 40px 0 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border);
    color: var(--text);
}
.category-header .material-symbols-outlined {
    color: var(--accent);
}

@media (max-width: 768px) {
    .promo-card {
        flex-direction: column;
        text-align: center;
        gap: 16px;
    }
    .promo-card-left {
        border-right: none;
        border-bottom: 1px dashed var(--border2);
        padding-right: 0;
        padding-bottom: 16px;
    }
    .loyalty-card {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<div class="hero" style="padding:48px 24px 36px">
    <h1>Exclusive Deals &amp; <em>Offers</em></h1>
    <p class="hero-sub" style="margin-bottom:0">Save big on your next adventure with TravelNest</p>
</div>

<div class="page-with-sidebar sec" style="padding-top:32px">
    <!-- Sidebar Filters -->
    <div class="filter-sidebar">
        <h3 style="margin-bottom: 20px; color: var(--text); font-size: 16px;">Filter by Category</h3>
        
        <?php if(loggedIn() && count($pastBookings) > 0): ?>
        <div class="offer-filter active" onclick="scrollToSection('loyalty')">
            <span class="material-symbols-outlined">workspace_premium</span>
            My Rewards
        </div>
        <?php endif; ?>

        <?php if(count($groupedPromos['All']) > 0): ?>
        <div class="offer-filter" onclick="scrollToSection('cat-all')">
            <span class="material-symbols-outlined">local_activity</span>
            Sitewide Offers
        </div>
        <?php endif; ?>

        <?php if(count($groupedPromos['Flight']) > 0): ?>
        <div class="offer-filter" onclick="scrollToSection('cat-flight')">
            <span class="material-symbols-outlined">flight</span>
            Flights
        </div>
        <?php endif; ?>

        <?php if(count($groupedPromos['Hotel']) > 0): ?>
        <div class="offer-filter" onclick="scrollToSection('cat-hotel')">
            <span class="material-symbols-outlined">hotel</span>
            Hotels
        </div>
        <?php endif; ?>

        <?php if(count($groupedPromos['Package']) > 0): ?>
        <div class="offer-filter" onclick="scrollToSection('cat-package')">
            <span class="material-symbols-outlined">inventory_2</span>
            Holiday Packages
        </div>
        <?php endif; ?>
        
        <?php if(count($groupedPromos['Other']) > 0): ?>
        <div class="offer-filter" onclick="scrollToSection('cat-other')">
            <span class="material-symbols-outlined">directions_bus</span>
            Other Transport
        </div>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div>
        <?php if(loggedIn() && count($pastBookings) > 0): ?>
        <!-- Loyalty Rewards Banner -->
        <div id="loyalty" class="loyalty-banner">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 24px;">
                <div>
                    <h2 style="font-size:24px; margin-bottom:8px">Return Customer Loyalty Rewards</h2>
                    <p style="color:rgba(255,255,255,0.8); font-size:14px">Thank you for traveling with us! Use any of your past booking Reference IDs as a promo code to get a flat 10% discount on your next booking.</p>
                </div>
                <span class="material-symbols-outlined" style="font-size:48px; color:rgba(255,255,255,0.2)">workspace_premium</span>
            </div>
            
            <?php foreach(array_slice($pastBookings, 0, 3) as $bk): ?>
            <div class="loyalty-card">
                <div style="flex:1">
                    <div style="font-weight:600; font-size:16px; margin-bottom:4px"><?= clean($bk['item_name']) ?></div>
                    <div style="font-size:12px; color:rgba(255,255,255,0.7)">
                        Booked on <?= fmtDate($bk['created_at']) ?> · <?= clean($bk['booking_type']) ?>
                    </div>
                </div>
                <div class="loyalty-code-box" onclick="copyCode('<?= clean($bk['booking_ref']) ?>', this)">
                    <?= clean($bk['booking_ref']) ?>
                    <span class="material-symbols-outlined" style="font-size:18px">content_copy</span>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if(count($pastBookings) > 3): ?>
            <div style="text-align:right; margin-top:16px">
                <a href="<?= BASE ?>/bookings.php" style="color:rgba(255,255,255,0.8); font-size:13px; text-decoration:underline">View all past bookings</a>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>


        <!-- Promo Codes Render Loop -->
        <?php
        $categoryMap = [
            'All' => ['icon' => 'local_activity', 'title' => 'Sitewide Offers (All Bookings)'],
            'Flight' => ['icon' => 'flight', 'title' => 'Flight Deals'],
            'Hotel' => ['icon' => 'hotel', 'title' => 'Hotel Discounts'],
            'Package' => ['icon' => 'inventory_2', 'title' => 'Holiday Package Offers'],
            'Other' => ['icon' => 'directions_bus', 'title' => 'Other Travel Deals']
        ];

        foreach (['All', 'Flight', 'Hotel', 'Package', 'Other'] as $cat):
            if(count($groupedPromos[$cat]) === 0) continue;
        ?>
        
        <div id="cat-<?= strtolower($cat) ?>" class="category-section" style="margin-bottom: 40px">
            <h3 class="category-header">
                <span class="material-symbols-outlined"><?= $categoryMap[$cat]['icon'] ?></span>
                <?= $categoryMap[$cat]['title'] ?>
            </h3>

            <div style="display:grid; gap: 20px;">
                <?php foreach($groupedPromos[$cat] as $promo): 
                    $isPercent = $promo['discount_type'] === 'percentage';
                    $valText = $isPercent ? $promo['discount_value'] . '%' : '₹' . number_format($promo['discount_value']);
                ?>
                <div class="promo-card">
                    <?php if($promo['status'] === 'Expiring'): ?>
                        <span class="urgency-badge" style="position:absolute; top: 12px; left: 12px; z-index:1">
                            <span class="material-symbols-outlined" style="font-size:12px">timer</span> Expiring Soon
                        </span>
                    <?php endif; ?>

                    <div class="promo-card-left">
                        <div class="promo-value"><?= $valText ?> <span style="font-size:18px">OFF</span></div>
                        <div class="promo-type"><?= $promo['discount_type'] ?> discount</div>
                    </div>
                    
                    <div class="promo-card-main">
                        <h4 style="font-size:18px; margin-bottom:8px; color:var(--text)"><?= clean($promo['description']) ?></h4>
                        <div style="display:flex; gap: 16px; margin-bottom: 12px;">
                            <span class="sm"><span class="fw5">Min. Booking:</span> <?= rupee($promo['min_booking']) ?></span>
                            <?php if($isPercent): ?>
                                <span class="sm"><span class="fw5">Max Discount:</span> <?= rupee($promo['max_discount']) ?></span>
                            <?php endif; ?>
                            <span class="sm"><span class="fw5">Valid till:</span> <?= fmtDate($promo['valid_until']) ?></span>
                        </div>
                    </div>

                    <div style="flex-shrink:0; text-align:right">
                        <div class="promo-code-btn" onclick="copyCode('<?= clean($promo['code']) ?>', this)">
                            <?= clean($promo['code']) ?>
                            <span class="material-symbols-outlined" style="font-size:18px">content_copy</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php endforeach; ?>

    </div>
</div>

<script>
// Filter Scroll
function scrollToSection(id) {
    document.querySelectorAll('.offer-filter').forEach(el => el.classList.remove('active'));
    event.currentTarget.classList.add('active');
    
    const sec = document.getElementById(id);
    if(sec) {
        window.scrollTo({
            top: sec.offsetTop - 100,
            behavior: 'smooth'
        });
    }
}

// Copy Code
function copyCode(code, btnElement) {
    navigator.clipboard.writeText(code).then(() => {
        const originalText = btnElement.innerHTML;
        btnElement.innerHTML = `<span class="material-symbols-outlined" style="font-size:18px">check</span> Copied!`;
        btnElement.style.background = 'rgba(22,163,74,0.1)';
        btnElement.style.color = '#16a34a';
        btnElement.style.borderColor = '#16a34a';
        
        setTimeout(() => {
            btnElement.innerHTML = originalText;
            btnElement.style.background = '';
            btnElement.style.color = '';
            btnElement.style.borderColor = '';
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy', err);
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
