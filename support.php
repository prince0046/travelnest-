<?php
require_once __DIR__ . '/includes/bootstrap.php';
mustLogin();

$me = me();
$uid = $me['id'];
$err = '';
$msg = '';

// Handle new ticket submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'new_ticket') {
    if (!checkCsrf()) {
        $err = 'Invalid request. Please try again.';
    } else {
        $subject = clean($_POST['subject'] ?? '');
        $message = clean($_POST['message'] ?? '');
        $priority = clean($_POST['priority'] ?? 'Medium');
        $bookingRef = clean($_POST['booking_ref'] ?? '');
        
        if (empty($bookingRef)) $bookingRef = null;

        if (empty($subject) || empty($message)) {
            $err = 'Subject and Message are required.';
        } else {
            DB::insert('support_tickets', [
                'user_id' => $uid,
                'booking_ref' => $bookingRef,
                'subject' => $subject,
                'message' => $message,
                'priority' => $priority,
                'status' => 'Open'
            ]);
            $msg = 'Support ticket submitted successfully. Our team will respond shortly.';
        }
    }
}

// Fetch all user tickets
$tickets = DB::all("SELECT * FROM support_tickets WHERE user_id=? ORDER BY created_at DESC", [$uid]);

// Fetch user's bookings for the dropdown
$bookings = DB::all("SELECT booking_ref, item_name, booking_type FROM bookings WHERE user_id=? ORDER BY created_at DESC", [$uid]);

$pageTitle = 'Support Center — TravelNest';
require_once __DIR__ . '/includes/header.php';
?>

<div class="sec" style="max-width:900px">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 24px;">
        <div>
            <h2 class="stitle" style="margin:0">Support Center</h2>
            <p class="sm" style="color:var(--text3); margin-top:4px">Manage your support tickets and get help.</p>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('new-ticket-modal').style.display='flex'">
            + New Ticket
        </button>
    </div>

    <?php if($err): ?><div class="flash err" style="margin-bottom:20px"><?= $err ?></div><?php endif; ?>
    <?php if($msg): ?><div class="flash" style="background:rgba(22,163,74,0.1); color:#16a34a; border-color:rgba(22,163,74,0.2)"><?= $msg ?></div><?php endif; ?>

    <?php if (empty($tickets)): ?>
        <div class="card tc" style="padding:48px 24px">
            <div style="font-size:48px; margin-bottom:16px">🎧</div>
            <h3 style="margin-bottom:8px">No Support Tickets Yet</h3>
            <p style="color:var(--text3)">If you need help with a booking, you can create a new ticket here.</p>
        </div>
    <?php else: ?>
        <div style="display:grid; gap: 16px;">
            <?php foreach($tickets as $t): 
                $statusColor = match($t['status']) {
                    'Open' => 't-blue',
                    'In Progress' => 't-amber',
                    'Resolved' => 't-green',
                    'Closed' => 't-gray',
                    default => 't-gray'
                };
                $priorityColor = match($t['priority']) {
                    'Urgent' => '#ef4444',
                    'High' => '#f97316',
                    'Medium' => '#3b82f6',
                    'Low' => '#94a3b8',
                    default => '#94a3b8'
                };
            ?>
            <div class="card" style="display:flex; gap: 20px; align-items:flex-start; cursor:pointer; transition:all 0.2s" onclick="toggleDetails(<?= $t['id'] ?>)">
                <div style="flex:1">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px">
                        <span class="tag <?= $statusColor ?>"><?= $t['status'] ?></span>
                        <span style="font-size:12px; color:<?= $priorityColor ?>; font-weight:600">• <?= $t['priority'] ?></span>
                        <span class="sm" style="color:var(--text3)">Ticket #<?= $t['id'] ?></span>
                    </div>
                    <div class="fw6" style="font-size:16px; margin-bottom:4px; color:var(--text)"><?= clean($t['subject']) ?></div>
                    <div class="sm" style="color:var(--text2)">
                        Created on <?= fmtDate($t['created_at']) ?> 
                        <?php if($t['booking_ref']): ?>
                            · Booking Ref: <strong><?= clean($t['booking_ref']) ?></strong>
                        <?php endif; ?>
                    </div>
                </div>
                <div style="color:var(--text3)">
                    <span class="material-symbols-outlined" id="icon-<?= $t['id'] ?>">expand_more</span>
                </div>
            </div>

            <!-- Expanded Details -->
            <div id="details-<?= $t['id'] ?>" class="card" style="display:none; background:var(--bg2); margin-top:-8px; margin-bottom: 8px; border-top:none; border-top-left-radius:0; border-top-right-radius:0">
                <div style="margin-bottom:16px">
                    <div class="fw6 sm" style="margin-bottom:6px">Your Message:</div>
                    <div style="padding:12px; background:#fff; border:1px solid var(--border); border-radius:var(--r); font-size:14px; color:var(--text2); white-space:pre-wrap"><?= clean($t['message']) ?></div>
                </div>
                
                <?php if($t['admin_reply']): ?>
                <div>
                    <div class="fw6 sm" style="margin-bottom:6px; color:var(--accent)">Admin Reply:</div>
                    <div style="padding:12px; background:rgba(0,140,255,0.05); border:1px solid rgba(0,140,255,0.2); border-radius:var(--r); font-size:14px; color:var(--text); white-space:pre-wrap"><?= clean($t['admin_reply']) ?></div>
                    <div class="xs mt4" style="text-align:right; color:var(--text3)">Replied on <?= fmtDate($t['updated_at']) ?></div>
                </div>
                <?php else: ?>
                <div class="sm" style="color:var(--text3); font-style:italic">Awaiting response from our support team...</div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- New Ticket Modal -->
<div id="new-ticket-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:999; align-items:center; justify-content:center">
    <div class="card" style="width:100%; max-width:500px">
        <div style="display:flex; justify-content:space-between; margin-bottom:20px">
            <h3 style="margin:0">Create Support Ticket</h3>
            <span class="material-symbols-outlined" style="cursor:pointer; color:var(--text3)" onclick="document.getElementById('new-ticket-modal').style.display='none'">close</span>
        </div>
        
        <form method="POST">
            <input type="hidden" name="csrf" value="<?= csrf() ?>">
            <input type="hidden" name="action" value="new_ticket">
            
            <div class="fg mb16">
                <label>Subject</label>
                <input type="text" name="subject" placeholder="Brief description of the issue" required>
            </div>
            
            <div class="g2 mb16">
                <div class="fg">
                    <label>Related Booking (Optional)</label>
                    <select name="booking_ref">
                        <option value="">-- Select a booking --</option>
                        <?php foreach($bookings as $b): ?>
                            <option value="<?= clean($b['booking_ref']) ?>"><?= clean($b['booking_ref']) ?> - <?= clean($b['item_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="fg">
                    <label>Priority</label>
                    <select name="priority">
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                        <option value="Urgent">Urgent</option>
                    </select>
                </div>
            </div>
            
            <div class="fg mb20">
                <label>Message</label>
                <textarea name="message" rows="5" placeholder="Please provide details about your issue..." required style="resize:vertical"></textarea>
            </div>
            
            <div style="display:flex; gap:12px; justify-content:flex-end">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('new-ticket-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Ticket</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleDetails(id) {
    const el = document.getElementById('details-' + id);
    const icon = document.getElementById('icon-' + id);
    if (el.style.display === 'none') {
        el.style.display = 'block';
        icon.textContent = 'expand_less';
    } else {
        el.style.display = 'none';
        icon.textContent = 'expand_more';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
