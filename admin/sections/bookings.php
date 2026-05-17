<?php
$q=clean($_GET['q']??'');$bt=clean($_GET['bt']??'');$st=clean($_GET['st']??'');$pg=max(1,(int)($_GET['pg']??1));
$w=['1=1'];$p=[];
if($q){$w[]='(b.booking_ref LIKE ? OR b.item_name LIKE ? OR u.name LIKE ?)';$lk="%$q%";array_push($p,$lk,$lk,$lk);}
if($bt){$w[]='b.booking_type=?';$p[]=$bt;}
if($st){$w[]='b.booking_status=?';$p[]=$st;}
$sql="SELECT b.*,u.name un,u.email ue FROM bookings b LEFT JOIN users u ON b.user_id=u.id WHERE ".implode(' AND ',$w)." ORDER BY b.created_at DESC";
$res=DB::paginate($sql,$p,$pg,15);$bks=$res['data'];
$base=BASE."/admin/index.php?sec=bookings&q=".urlencode($q)."&bt=".urlencode($bt)."&st=".urlencode($st);
?>
<div class="flex sb wrap-x g12 mb20"><h2 style="font-size:22px">Bookings <span class="bdg t-blue"><?=$res['total']?></span></h2></div>
<div class="card">
<form method="GET" action="" class="fbar mb16"><input type="hidden" name="sec" value="bookings">
  <input name="q" value="<?=clean($q)?>" placeholder="Ref / item / user...">
  <select name="bt" onchange="this.form.submit()"><option value="">All Types</option><?php foreach(['Flight','Hotel','Package','Train','Bus','Cab','Cruise'] as $t):?><option value="<?=$t?>" <?=$bt===$t?'selected':''?>><?=$t?></option><?php endforeach;?></select>
  <select name="st" onchange="this.form.submit()"><option value="">All Status</option><?php foreach(['Confirmed','Pending','Cancelled','Completed'] as $s):?><option value="<?=$s?>" <?=$st===$s?'selected':''?>><?=$s?></option><?php endforeach;?></select>
  <button class="btn btn-primary btn-sm">Search</button>
</form>
<div class="ox"><table class="dt"><thead><tr><th>Ref</th><th>User</th><th>Type</th><th>Item</th><th>Date</th><th>Amount</th><th>Payment</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach($bks as $b):?>
<tr>
  <td class="acc fw5 xs"><?=clean($b['booking_ref'])?></td>
  <td><div style="font-size:13px"><?=clean($b['un']??'N/A')?></div><div class="xs"><?=clean($b['ue']??'')?></div></td>
  <td><span class="bdg t-blue"><?=$b['booking_type']?></span></td>
  <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px"><?=clean($b['item_name'])?></td>
  <td class="xs"><?=fmtDate($b['travel_date']?:$b['created_at'])?></td>
  <td class="fw5"><?=rupee($b['total_amount'])?></td>
  <td class="xs"><?=clean($b['payment_method'])?></td>
  <td><?=statusBadge($b['booking_status'])?></td>
  <td><div class="flex g4r">
    <a href="<?=BASE?>/invoice.php?ref=<?=clean($b['booking_ref'])?>&admin=1" target="_blank" class="btn btn-ghost btn-xs">📄</a>
    <?php if($b['booking_status']==='Confirmed'):?>
    <button class="btn btn-danger btn-xs" onclick="adminCancelBk('<?=clean($b['booking_ref'])?>')">Cancel</button>
    <?php endif;?>
    <select class="admin-status-sel" data-ref="<?=clean($b['booking_ref'])?>" onchange="adminUpdateBkStatus(this)" style="font-size:11px;padding:3px 6px;border-radius:6px;background:var(--bg3);color:var(--text);border:1px solid var(--border);cursor:pointer">
      <?php foreach(['Confirmed','Pending','Cancelled','Completed'] as $st2):?>
      <option value="<?=$st2?>" <?=$b['booking_status']===$st2?'selected':''?>><?=$st2?></option>
      <?php endforeach;?>
    </select>
  </div></td>
</tr>
<?php endforeach;?>
</tbody></table></div>
<?=pagLinks($res['page'],$res['last'],$base)?>
</div>
