<?php
require_once __DIR__ . '/includes/bootstrap.php';
mustLogin();

$me = me();
$err = '';
$msg = '';

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    if (!checkCsrf()) {
        $err = 'Invalid request. Please try again.';
    } else {
        $newName = clean($_POST['name'] ?? '');
        $newPhone = clean($_POST['phone'] ?? '');
        
        if (empty($newName)) {
            $err = 'Name is required.';
        } else {
            DB::q("UPDATE users SET name=?, phone=? WHERE id=?", [$newName, $newPhone, $me['id']]);
            $msg = 'Profile updated successfully!';
            $_SESSION['name'] = $newName; // update session
            $me = me(); // refresh local user var
        }
    }
}

// Calculate Loyalty Tier
$totalSpent = (float)$me['total_spent'];
$tierName = 'Bronze';
$tierColor = '#d97706'; // Amber-esque
$nextTierName = 'Silver';
$nextTierThreshold = 20000;
$tierProgress = 0;

if ($totalSpent >= 100000) {
    $tierName = 'Platinum';
    $tierColor = '#0f172a'; // Slate-900
    $nextTierName = 'Max Tier';
    $nextTierThreshold = 100000;
    $tierProgress = 100;
} elseif ($totalSpent >= 50000) {
    $tierName = 'Gold';
    $tierColor = '#eab308'; // Yellow-500
    $nextTierName = 'Platinum';
    $nextTierThreshold = 100000;
    $tierProgress = ($totalSpent / $nextTierThreshold) * 100;
} elseif ($totalSpent >= 20000) {
    $tierName = 'Silver';
    $tierColor = '#94a3b8'; // Slate-400
    $nextTierName = 'Gold';
    $nextTierThreshold = 50000;
    $tierProgress = ($totalSpent / $nextTierThreshold) * 100;
} else {
    $tierProgress = ($totalSpent / $nextTierThreshold) * 100;
}

$pageTitle = 'My Profile — TravelNest';
require_once __DIR__ . '/includes/header.php';
?>

<div class="sec" style="max-width:860px">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
        <h2 class="stitle" style="margin:0">My Profile</h2>
        <span class="tag" style="background:<?= $tierColor ?>; color:#fff; padding:6px 16px; font-size:14px">
            <?= $tierName ?> Member
        </span>
    </div>

    <?php if($err): ?><div class="flash err" style="margin-bottom:20px"><?= $err ?></div><?php endif; ?>
    <?php if($msg): ?><div class="flash" style="background:rgba(22,163,74,0.1); color:#16a34a; border-color:rgba(22,163,74,0.2)"><?= $msg ?></div><?php endif; ?>

    <div style="display:grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        
        <!-- Profile Form -->
        <div class="card">
            <h3 style="font-size:16px; margin-bottom:16px">Personal Information</h3>
            <form method="POST">
                <input type="hidden" name="csrf" value="<?= csrf() ?>">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="g2 mb16">
                    <div class="fg">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?= clean($me['name']) ?>" required>
                    </div>
                    <div class="fg">
                        <label>Email Address</label>
                        <input type="email" value="<?= clean($me['email']) ?>" readonly style="background:var(--bg2); color:var(--text3); cursor:not-allowed">
                        <div class="xs" style="margin-top:4px; color:var(--text3)">Email cannot be changed after registration.</div>
                    </div>
                </div>

                <div class="fg mb20">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?= clean($me['phone'] ?? '') ?>" placeholder="e.g. +91 9876543210">
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>

        <!-- Loyalty & Stats -->
        <div>
            <!-- Stats -->
            <div class="card" style="margin-bottom:20px">
                <h3 style="font-size:16px; margin-bottom:16px">Lifetime Statistics</h3>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px; border-bottom: 1px dashed var(--border2); padding-bottom: 12px;">
                    <span class="sm" style="color:var(--text2)">Total Bookings</span>
                    <span class="fw6" style="font-size:18px"><?= number_format($me['total_bookings']) ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span class="sm" style="color:var(--text2)">Total Spent</span>
                    <span class="fw6 acc" style="font-size:18px"><?= rupee($me['total_spent']) ?></span>
                </div>
            </div>

            <!-- Tier info -->
            <div class="card" style="background:linear-gradient(135deg, var(--bg2), #fff);">
                <div style="display:flex; justify-content:space-between; margin-bottom:8px">
                    <span class="fw6" style="font-size:13px; color:var(--text)"><?= $tierName ?> Tier</span>
                    <?php if($tierName !== 'Platinum'): ?>
                        <span class="xs" style="color:var(--text3)"><?= rupee((int)$nextTierThreshold - $totalSpent) ?> to <?= $nextTierName ?></span>
                    <?php endif; ?>
                </div>
                <div style="background:var(--border); height:6px; border-radius:3px; overflow:hidden; margin-bottom:12px">
                    <div style="background:<?= $tierColor ?>; height:100%; width:<?= min(100, $tierProgress) ?>%"></div>
                </div>
                <p class="xs" style="color:var(--text3); line-height:1.4">
                    <?= $tierName === 'Platinum' ? 'You have reached the highest tier! Enjoy exclusive platinum services.' : 'Book more to reach '.$nextTierName.' and unlock exclusive priority support and special offers.' ?>
                </p>
            </div>
        </div>
        
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
