<?php
$q=clean($_GET['q']??'');$tier=clean($_GET['tier']??'');$pg=max(1,(int)($_GET['pg']??1));
$w=["role='user'"];$p=[];
if($q){$w[]='(name LIKE ? OR email LIKE ? OR city LIKE ?)';$lk="%$q%";array_push($p,$lk,$lk,$lk);}
if($tier){$w[]='tier=?';$p[]=$tier;}
$res=DB::paginate("SELECT * FROM users WHERE ".implode(' AND ',$w)." ORDER BY total_spent DESC",$p,$pg,15);
$base=BASE."/admin/index.php?sec=users&q=".urlencode($q)."&tier=".urlencode($tier);
?>
<div class="flex sb wrap-x g12 mb20"><h2 style="font-size:22px">Users <span class="bdg t-blue"><?=$res['total']?></span></h2></div>
<div class="card">
<form method="GET" class="fbar mb16"><input type="hidden" name="sec" value="users">
  <input name="q" value="<?=clean($q)?>" placeholder="Name / email / city...">
  <select name="tier" onchange="this.form.submit()"><option value="">All Tiers</option><?php foreach(['Platinum','Gold','Silver','Bronze'] as $t):?><option value="<?=$t?>" <?=$tier===$t?'selected':''?>><?=$t?></option><?php endforeach;?></select>
  <button class="btn btn-primary btn-sm">Search</button>
</form>
<div class="ox"><table class="dt"><thead><tr><th>User</th><th>City</th><th>Joined</th><th>Bookings</th><th>Spent</th><th>Tier</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach($res['data'] as $u):?>
<tr data-type="user" data-id="<?=$u['id']?>">
  <td><div class="fw5"><?=clean($u['name'])?></div><div class="xs"><?=clean($u['email'])?></div><div class="xs"><?=clean($u['phone']??'')?></div></td>
  <td class="sm"><?=clean($u['city']??'—')?></td>
  <td class="xs"><?=fmtDate($u['created_at'])?></td>
  <td class="fw5 blu"><?=$u['total_bookings']?></td>
  <td class="fw5 grn"><?=rupee($u['total_spent'])?></td>
  <td><?=tierBadge($u['tier'])?></td>
  <td><?=statusBadge($u['is_active']?'Active':'Suspended')?></td>
  <td><div class="flex g4r">
    <button class="btn btn-ghost btn-xs" onclick="viewUser(<?=$u['id']?>)">👁 View</button>
    <button class="btn btn-<?=$u['is_active']?'danger':'green'?> btn-xs" onclick="toggleStatus('user',<?=$u['id']?>,<?=$u['is_active']?>)"><?=$u['is_active']?'Suspend':'Activate'?></button>
  </div></td>
</tr>
<?php endforeach;?>
</tbody></table></div>
<?=pagLinks($res['page'],$res['last'],$base)?>
</div>
