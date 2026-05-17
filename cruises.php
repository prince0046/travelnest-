<?php
require_once __DIR__.'/includes/bootstrap.php';
$q=clean($_GET['q']??'');$pg=max(1,(int)($_GET['pg']??1));
$w=['is_active=1'];$p=[];
if($q){$w[]='(name LIKE ? OR city LIKE ? OR destination LIKE ? OR from_city LIKE ? OR vehicle_name LIKE ? OR cruise_name LIKE ? OR train_name LIKE ? OR operator_name LIKE ?)';$lk="%$q%";for($i=0;$i<8;$i++)$p[]=$lk;}
$res=DB::paginate("SELECT * FROM cruises WHERE ".implode(' AND ',$w)." ORDER BY id ASC",$p,$pg);
$pageTitle='Cruises — TravelNest';
require_once __DIR__.'/includes/header.php';?>
<div class="ov" id="det-modal"><div class="mod" style="max-width:700px"><div class="mh"><h3 id="det-title"></h3><button class="mx" onclick="closeMod('det-modal')">✕</button></div><div class="mb" id="det-body"></div></div></div>
<meta name="csrf" content="<?=csrf()?>">
<div class="sec">
  <h2 class="stitle">Cruises</h2><p class="ssub"><?=$res['total']?> available</p>
  <form method="GET" class="fbar mb16">
    <input name="q" value="<?=clean($q)?>" placeholder="Search...">
    <button type="submit" class="btn btn-primary btn-sm">Search</button>
  </form>
  <?php foreach($res['data'] as $item):
    $nm=$item['name']??($item['cruise_name']??($item['train_name']??($item['operator_name']??($item['vehicle_name']??''))));
    $pr=$item['price']??($item['price_per_night']??($item['base_fare']??($item['price_2a']??0)));
    $em=$item['emoji']??'📋';
    $det=$item['destination']??($item['from_station']??($item['from_city']??($item['from_port']??($item['cab_type']??''))));
    $fn='show'.ucfirst(rtrim('cruises','s'));
  ?>
  <div class="card mb10 flex g16" style="cursor:pointer" onclick="<?=$fn?>(<?=$item['id']?>)">
    <div style="font-size:38px;flex:0 0 56px;display:flex;align-items:center;justify-content:center"><?=$em?></div>
    <div style="flex:1">
      <div class="fw5 mb4" style="font-size:15px"><?=clean($nm)?></div>
      <div class="sm mb8"><?=clean($det)?></div>
      <?php if(isset($item['inclusions'])):?><div class="flex wrap-x g4r"><?php foreach(incArr($item['inclusions']) as $inc):?><span class="chip"><?=clean($inc)?></span><?php endforeach;?></div><?php endif;?>
      <?php if(isset($item['amenities'])):?><div class="flex wrap-x g4r"><?php foreach(amenArr($item['amenities']) as $am):?><span class="chip"><?=clean($am)?></span><?php endforeach;?></div><?php endif;?>
      <?php if(isset($item['running_days'])):?><div class="xs mt4">🗓 <?=clean($item['running_days'])?></div><?php endif;?>
      <?php if(isset($item['departure_time'])):?>
      <div class="flex g8 mt6">
        <div class="tc"><div style="font-size:17px;font-weight:700"><?=$item['departure_time']?></div><div class="xs"><?=clean($item['from_station']??($item['from_city']??($item['from_port']??'')))?></div></div>
        <div style="flex:1;border-top:1px dashed rgba(255,255,255,.1);margin:10px 8px;position:relative"><span style="position:absolute;top:-9px;left:50%;transform:translateX(-50%);background:var(--card);padding:0 4px;font-size:11px;color:var(--text3)"><?=clean($item['duration']??'')?></span></div>
        <div class="tc"><div style="font-size:17px;font-weight:700"><?=$item['arrival_time']??''?></div><div class="xs"><?=clean($item['to_station']??($item['to_city']??($item['to_port']??'')))?></div></div>
      </div>
      <?php endif;?>
    </div>
    <div class="tr" style="min-width:150px;display:flex;flex-direction:column;justify-content:space-between;gap:10px">
      <div><div class="acc fw5" style="font-size:20px"><?=$pr>0?rupee($pr):'—'?></div><div class="xs"><?=isset($item['nights'])?$item['nights'].' nights':''?></div></div>
      <div class="flex g4r" style="justify-content:flex-end">
        <button class="btn btn-ghost btn-xs" onclick="event.stopPropagation();<?=$fn?>(<?=$item['id']?>)">Details</button>
        <a href="<?=BASE?>/book.php?type=<?=rtrim('cruises','s')?>&id=<?=$item['id']?>" class="btn btn-primary btn-xs" onclick="event.stopPropagation()">Book</a>
      </div>
    </div>
  </div>
  <?php endforeach;?>
  <?=pagLinks($res['page'],$res['last'],BASE."/cruises.php?q=".urlencode($q))?>
</div>
<?php require_once __DIR__.'/includes/footer.php';?>
