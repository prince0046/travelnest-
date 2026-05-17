<?php
require_once __DIR__.'/includes/bootstrap.php';mustLogin();$me=me();
$wl=DB::all("SELECT * FROM wishlists WHERE user_id=? ORDER BY created_at DESC",[$me['id']]);
$pageTitle='My Wishlist — TravelNest';require_once __DIR__.'/includes/header.php';?>
<div class="ov" id="det-modal"><div class="mod" style="max-width:700px"><div class="mh"><h3 id="det-title"></h3><button class="mx" onclick="closeMod('det-modal')">✕</button></div><div class="mb" id="det-body"></div></div></div>
<meta name="csrf" content="<?=csrf()?>">
<div class="sec">
  <h2 class="stitle">❤️ My Wishlist</h2><p class="ssub"><?=count($wl)?> saved items</p>
  <?php if(empty($wl)):?><div class="card tc" style="padding:56px"><div style="font-size:56px;margin-bottom:16px">🤍</div><h3>Wishlist is empty</h3><p class="sm mt8">Browse deals and tap 🤍 to save.</p><a href="<?=BASE?>/index.php" class="btn btn-primary mt16">Explore Deals</a></div>
  <?php else:?>
    <?php
    $fns=['Flight'=>'showFlight','Hotel'=>'showHotel','Package'=>'showPackage','Train'=>'showTrain','Bus'=>'showBus','Cruise'=>'showCruise'];
    foreach($wl as $w):
      $tbl=['Flight'=>'flights','Hotel'=>'hotels','Package'=>'packages','Train'=>'trains','Bus'=>'buses','Cruise'=>'cruises'];
      if(!isset($tbl[$w['item_type']]))continue;
      $it=DB::one("SELECT * FROM {$tbl[$w['item_type']]} WHERE id=?",[$w['item_id']]);
      if(!$it)continue;
      $nm=$it['name']??($it['cruise_name']??($it['train_name']??($it['operator_name']??($it['vehicle_name']??''))));
      if($w['item_type']==='Flight')$nm=$it['airline'].' '.$it['flight_code'].' '.$it['from_city'].'→'.$it['to_city'];
      if($w['item_type']==='Hotel')$nm=$it['name'].', '.$it['city'];
      $pr=$it['price']??($it['price_per_night']??($it['base_fare']??($it['price_2a']??0)));
    ?>
    <div class="card mb10 flex g16">
      <div style="font-size:30px;flex-shrink:0"><?=$it['emoji']??'📋'?></div>
      <div style="flex:1"><div class="fw5"><?=clean($nm)?></div><div class="xs mt4"><?=$w['item_type']?> · Saved <?=fmtDate($w['created_at'])?></div></div>
      <div class="tr">
        <?php if($pr>0):?><div class="acc fw5" style="font-size:17px"><?=rupee($pr)?></div><?php endif;?>
        <div class="flex g6 mt8" style="justify-content:flex-end">
          <?php if(isset($fns[$w['item_type']])):?><button class="btn btn-ghost btn-xs" onclick="<?=$fns[$w['item_type']]?>(<?=$w['item_id']?>)">Details</button><?php endif;?>
          <a href="<?=BASE?>/book.php?type=<?=strtolower($w['item_type'])?>&id=<?=$w['item_id']?>" class="btn btn-primary btn-xs">Book</a>
          <button class="btn btn-danger btn-xs" onclick="wlToggle('<?=$w['item_type']?>',<?=$w['item_id']?>,this);this.closest('.card').remove()">✕</button>
        </div>
      </div>
    </div>
    <?php endforeach;?>
  <?php endif;?>
</div>
<?php require_once __DIR__.'/includes/footer.php';?>
