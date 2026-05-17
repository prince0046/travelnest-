<?php
$pg=max(1,(int)($_GET['pg']??1));
$q=clean($_GET['q']??'');
$w=['is_active=1'];$p=[];
if($q){$w[]='(name LIKE ? OR emoji LIKE ?)';$lk="%$q%";$p[]=$lk;$p[]=$lk;}
$res=DB::paginate("SELECT * FROM hotels WHERE ".implode(' AND ',$w)." ORDER BY id ASC",$p,$pg,15);
$base=BASE."/admin/index.php?sec=hotels&q=".urlencode($q);
?>
<div class="flex sb wrap-x g12 mb20"><h2 style="font-size:22px"><?=ucfirst('hotels')?> <span class="bdg t-blue"><?=$res['total']?></span></h2></div>
<div class="card">
<form method="GET" class="fbar mb12"><input type="hidden" name="sec" value="hotels">
  <input name="q" value="<?=clean($q)?>" placeholder="Search...">
  <button class="btn btn-primary btn-sm">Search</button>
</form>
<div class="ox"><table class="dt"><thead><tr><th>ID</th><th>Name</th><th>Details</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach($res['data'] as $item):
  $nm=$item['name']??($item['cruise_name']??($item['train_name']??($item['operator_name']??($item['vehicle_name']??'—'))));
  $pr=$item['price']??($item['price_per_night']??($item['base_fare']??($item['price_2a']??0)));
  $det=$item['city']??($item['destination']??($item['from_station']??($item['from_city']??($item['from_port']??($item['cab_type']??'')))));
?>
<tr data-type="<?=rtrim('hotels','s')?>" data-id="<?=$item['id']?>">
  <td class="xs"><?=$item['id']?></td>
  <td><?=isset($item['emoji'])?$item['emoji'].' ':''?><span class="fw5"><?=clean($nm)?></span></td>
  <td class="sm"><?=clean($det)?></td>
  <td class="fw5 acc"><?=$pr>0?rupee($pr):'—'?></td>
  <td><?=statusBadge($item['is_active']?'Active':'Suspended')?></td>
  <td><button class="btn btn-<?=$item['is_active']?'danger':'green'?> btn-xs" onclick="toggleStatus('<?=rtrim('hotels','s')?>',<?=$item['id']?>,<?=$item['is_active']?>)"><?=$item['is_active']?'Disable':'Enable'?></button></td>
</tr>
<?php endforeach;?>
</tbody></table></div>
<?=pagLinks($res['page'],$res['last'],$base)?>
</div>
