<?php
require_once __DIR__.'/includes/bootstrap.php';
$a=$_GET['a']??$_POST['a']??'';

switch($a){
  // ─── Detail endpoints ───
  case 'flight':
    jsonOut(DB::one("SELECT * FROM flights WHERE id=? AND is_active=1",[(int)($_GET['id']??0)])?:['err'=>1]);
  case 'hotel':
    $h=DB::one("SELECT * FROM hotels WHERE id=? AND is_active=1",[(int)($_GET['id']??0)]);
    if($h)$h['reviews']=DB::all(
        "SELECT r.*,u.name user_name FROM reviews r JOIN users u ON r.user_id=u.id
         WHERE r.item_type='Hotel' AND r.item_id=? AND r.is_approved=1
         ORDER BY r.created_at DESC LIMIT 4",[(int)$_GET['id']]
    );
    jsonOut($h?:['err'=>1]);
  case 'pkg':    jsonOut(DB::one("SELECT * FROM packages WHERE id=? AND is_active=1",[(int)($_GET['id']??0)])?:['err'=>1]);
  case 'train':  jsonOut(DB::one("SELECT * FROM trains   WHERE id=? AND is_active=1",[(int)($_GET['id']??0)])?:['err'=>1]);
  case 'bus':    jsonOut(DB::one("SELECT * FROM buses    WHERE id=? AND is_active=1",[(int)($_GET['id']??0)])?:['err'=>1]);
  case 'cab':    jsonOut(DB::one("SELECT * FROM cabs     WHERE id=? AND is_active=1",[(int)($_GET['id']??0)])?:['err'=>1]);
  case 'cruise': jsonOut(DB::one("SELECT * FROM cruises  WHERE id=? AND is_active=1",[(int)($_GET['id']??0)])?:['err'=>1]);
  case 'user':
    mustAdmin();
    $u=DB::one("SELECT * FROM users WHERE id=?",[(int)($_GET['id']??0)]);
    if($u)unset($u['password']);
    jsonOut($u?:['err'=>1]);

  // ─── Wishlist toggle ───
  case 'wl':
    if(!loggedIn())jsonOut(['login'=>1]);
    if(!checkCsrf())jsonOut(['ok'=>false,'msg'=>'Invalid request token']);
    $uid=$_SESSION['uid'];
    $type=clean($_POST['type']??'');
    $id=(int)($_POST['id']??0);
    $ex=DB::val("SELECT id FROM wishlists WHERE user_id=? AND item_type=? AND item_id=?",[$uid,$type,$id]);
    if($ex){
        DB::q("DELETE FROM wishlists WHERE user_id=? AND item_type=? AND item_id=?",[$uid,$type,$id]);
        jsonOut(['saved'=>false]);
    }else{
        DB::insert('wishlists',['user_id'=>$uid,'item_type'=>$type,'item_id'=>$id]);
        jsonOut(['saved'=>true]);
    }

  // ─── Promo check ───
  case 'promo':
    $code=strtoupper(clean($_GET['code']??''));
    $amount=(float)($_GET['amount']??0);
    $p=DB::one(
        "SELECT * FROM promo_codes WHERE code=? AND status IN('Active','Expiring')
         AND valid_until>=CURDATE() AND used_count<usage_limit",[$code]
    );
    if(!$p) {
        // Loyalty discount check!
        if (loggedIn()) {
            $uid = $_SESSION['uid'];
            $loyalty_booking = DB::one("SELECT * FROM bookings WHERE booking_ref=? AND user_id=? AND booking_status IN('Confirmed','Completed')", [$code, $uid]);
            if ($loyalty_booking) {
                // Apply a flat 10% discount for loyalty
                $disc = round($amount * 0.10);
                jsonOut(['ok'=>true,'disc'=>$disc,'label'=>'Loyalty Discount 10%']);
            }
        }
        jsonOut(['ok'=>false,'msg'=>'Invalid or expired promo code']);
    }
    if($amount<$p['min_booking'])jsonOut(['ok'=>false,'msg'=>'Min booking '.rupee($p['min_booking']).' required']);
    $disc=$p['discount_type']==='percentage'
        ? min(round($amount*$p['discount_value']/100),(float)$p['max_discount'])
        : (float)$p['discount_value'];
    $label=$p['discount_type']==='percentage'
        ? $p['discount_value'].'% off'
        : '₹'.number_format($p['discount_value']).' off';
    jsonOut(['ok'=>true,'disc'=>$disc,'label'=>$label]);

  // ─── Cancel booking (user) ───
  case 'cancel':
    if(!loggedIn())jsonOut(['ok'=>false,'msg'=>'Not logged in']);
    if(!checkCsrf())jsonOut(['ok'=>false,'msg'=>'Invalid request token']);
    $ref=clean($_POST['ref']??'');
    $uid=$_SESSION['uid'];
    $b=DB::one(
        "SELECT * FROM bookings WHERE booking_ref=? AND user_id=? AND booking_status='Confirmed'",
        [$ref,$uid]
    );
    if(!$b)jsonOut(['ok'=>false,'msg'=>'Booking not found or already cancelled']);
    DB::q(
        "UPDATE bookings SET booking_status='Cancelled', payment_status='Refunded' WHERE booking_ref=? AND user_id=?",
        [$ref,$uid]
    );
    sendCancellationEmail([
        'ref' => $b['booking_ref'],
        'label' => $b['item_name'],
        'amount' => $b['total_amount'],
        'pemail' => $b['passenger_email'] ?? $me['email'] ?? ''
    ]);
    jsonOut(['ok'=>true,'msg'=>'Booking cancelled. Refund in 5-7 business days.']);

  // ─── Toggle status (admin) ───
  case 'toggle':
    mustAdmin();
    $map=['flight'=>'flights','hotel'=>'hotels','package'=>'packages','train'=>'trains',
          'bus'=>'buses','cab'=>'cabs','cruise'=>'cruises','user'=>'users'];
    $type=clean($_POST['type']??'');
    $id=(int)($_POST['id']??0);
    $status=(int)($_POST['status']??0);
    if(!isset($map[$type]))jsonOut(['ok'=>false,'msg'=>'Invalid type']);
    DB::q("UPDATE {$map[$type]} SET is_active=? WHERE id=?",[$status,$id]);
    jsonOut(['ok'=>true]);

  // ─── Admin: update booking status ───
  case 'admin_booking_status':
    mustAdmin();
    if(!checkCsrf())jsonOut(['ok'=>false,'msg'=>'Invalid request token']);
    $ref=clean($_POST['ref']??'');
    $newStatus=clean($_POST['status']??'');
    $allowed=['Confirmed','Pending','Cancelled','Completed'];
    if(!in_array($newStatus,$allowed))jsonOut(['ok'=>false,'msg'=>'Invalid status']);
    $b=DB::one("SELECT * FROM bookings WHERE booking_ref=?",[$ref]);
    if(!$b)jsonOut(['ok'=>false,'msg'=>'Booking not found']);
    $payStatus=$newStatus==='Cancelled'?'Refunded':($newStatus==='Confirmed'?'Paid':$b['payment_status']);
    DB::q("UPDATE bookings SET booking_status=?, payment_status=? WHERE booking_ref=?",[$newStatus,$payStatus,$ref]);
    jsonOut(['ok'=>true,'msg'=>'Booking status updated to '.$newStatus]);

  // ─── Delete/disable (admin) ───
  case 'del':
    mustAdmin();
    $map=['flight'=>'flights','hotel'=>'hotels','package'=>'packages','train'=>'trains',
          'bus'=>'buses','cab'=>'cabs','cruise'=>'cruises'];
    $type=clean($_POST['type']??'');
    $id=(int)($_POST['id']??0);
    if(!isset($map[$type]))jsonOut(['ok'=>false,'msg'=>'Invalid type']);
    DB::q("UPDATE {$map[$type]} SET is_active=0 WHERE id=?",[$id]);
    jsonOut(['ok'=>true]);

  // ─── Submit Review (User) ───
  case 'submit_review':
    if(!loggedIn())jsonOut(['ok'=>false,'msg'=>'Not logged in']);
    if(!checkCsrf())jsonOut(['ok'=>false,'msg'=>'Invalid request token']);
    $uid=$_SESSION['uid'];
    $r_type=clean($_POST['item_type']??'');
    $r_id=(int)($_POST['item_id']??0);
    $r_name=clean($_POST['item_name']??'');
    $r_rating=(int)($_POST['rating']??5);
    $r_comment=clean($_POST['comment']??'');
    
    // Check if already reviewed (prevents double submission)
    $exists = DB::val("SELECT id FROM reviews WHERE user_id=? AND item_type=? AND item_id=?", [$uid, $r_type, $r_id]);
    if ($exists) jsonOut(['ok'=>false, 'msg'=>'You have already reviewed this booking.']);
    
    if($r_rating < 1 || $r_rating > 5) jsonOut(['ok'=>false,'msg'=>'Invalid rating']);
    
    DB::insert('reviews', [
        'user_id' => $uid,
        'item_type' => $r_type,
        'item_id' => $r_id,
        'item_name' => $r_name,
        'rating' => $r_rating,
        'comment' => $r_comment,
        'is_approved' => 1 // Auto-approve for demo, can be 0 for moderation
    ]);
    jsonOut(['ok'=>true]);

  default:
    jsonOut(['err'=>'unknown action'],400);
}
