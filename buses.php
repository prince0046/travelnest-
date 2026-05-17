<?php
require_once __DIR__.'/includes/bootstrap.php';
$q=clean($_GET['q']??'');$pg=max(1,(int)($_GET['pg']??1));
$w=['is_active=1'];$p=[];
if($q){$w[]='(name LIKE ? OR city LIKE ? OR destination LIKE ? OR from_city LIKE ? OR vehicle_name LIKE ? OR cruise_name LIKE ? OR train_name LIKE ? OR operator_name LIKE ?)';$lk="%$q%";for($i=0;$i<8;$i++)$p[]=$lk;}
$res=DB::paginate("SELECT * FROM buses WHERE ".implode(' AND ',$w)." ORDER BY id ASC",$p,$pg);
$busImages=[
  'Neeta Travels'=>'assets/images/buses/Neeta_Travels.jpg',
  'VRL Travels'=>'assets/images/buses/VRL_Travels.jpg',
  'Sharma Transports'=>'assets/images/buses/Sharma_Transports.jpg',
  'Karnataka SRTC'=>'assets/images/buses/Karnataka_SRTC.jpg',
  'Orange Travels'=>'assets/images/buses/Orange_Travels.jpg',
  'Paulo Travels'=>'assets/images/buses/Paulo_Travels.jpg',
  'MSRTC Shivneri'=>'assets/images/buses/MSRTC_Shivneri.jpg',
  'Raj National Express'=>'assets/images/buses/Raj_National_Express.jpg',
  'SRS Travels'=>'assets/images/buses/SRS_Travels.jpg',
  'Parveen Travels'=>'assets/images/buses/Parveen_Travels.jpg',
  'Green Line Travels'=>'assets/images/buses/Green_Line_Travels.jpg',
  'Kallada Travels'=>'assets/images/buses/Kallada_Travels.jpg',
  'RSRTC Volvo'=>'assets/images/buses/RSRTC_Volvo.jpg',
  'Hans Travels'=>'assets/images/buses/Hans_Travels.jpg',
];
$defaultBusImg='assets/images/buses/Default.jpg';
$pageTitle='Bus Tickets — TravelNest';
require_once __DIR__.'/includes/header.php';?>
<div class="ov" id="det-modal"><div class="mod" style="max-width:700px"><div class="mh"><h3 id="det-title"></h3><button class="mx" onclick="closeMod('det-modal')">✕</button></div><div class="mb" id="det-body"></div></div></div>
<meta name="csrf" content="<?=csrf()?>">
<div class="sec">
  <h2 class="stitle">Bus Tickets</h2><p class="ssub"><?=$res['total']?> available</p>
  <form method="GET" class="fbar mb16">
    <input name="q" value="<?=clean($q)?>" placeholder="Search...">
    <button type="submit" class="btn btn-primary btn-sm">Search</button>
  </form>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:16px">
  <?php foreach($res['data'] as $item):
    $nm=$item['operator_name']??'';
    $pr=$item['price']??0;
    $busImg=$busImages[$nm]??$defaultBusImg;
  ?>
  <div class="hc" style="cursor:pointer" onclick="showBus(<?=$item['id']?>)">
    <div class="hc-img" style="background:url('<?=$busImg?>') center/cover no-repeat;height:170px">
      <span class="rb" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);color:#fff;font-size:11px"><?=$item['bus_type']?></span>
    </div>
    <div class="hc-body">
      <div class="fw5 mb4" style="font-size:15px"><?=clean($nm)?></div>
      <?php if(isset($item['departure_time'])):?>
      <div class="flex g8 mt6 mb6" style="align-items:center">
        <div><div style="font-size:15px;font-weight:700"><?=$item['departure_time']?></div><div class="xs"><?=clean($item['from_city']??'')?></div></div>
        <div style="flex:1;border-top:1px dashed rgba(255,255,255,.15);margin:0 6px;position:relative"><span style="position:absolute;top:-9px;left:50%;transform:translateX(-50%);background:var(--card);padding:0 4px;font-size:10px;color:var(--text3)"><?=clean($item['duration']??'')?></span></div>
        <div class="tr"><div style="font-size:15px;font-weight:700"><?=$item['arrival_time']??''?></div><div class="xs"><?=clean($item['to_city']??'')?></div></div>
      </div>
      <?php endif;?>
      <?php if(isset($item['amenities'])):?><div class="flex wrap-x g4r mb6"><?php foreach(amenArr($item['amenities']) as $am):?><span class="chip"><?=clean($am)?></span><?php endforeach;?></div><?php endif;?>
      <div class="flex sb mt6">
        <div><span class="acc fw5" style="font-size:18px"><?=rupee($pr)?></span></div>
        <div class="flex g4r">
          <button class="btn btn-ghost btn-xs" onclick="event.stopPropagation();showBus(<?=$item['id']?>)">Details</button>
          <a href="<?=BASE?>/book.php?type=bus&id=<?=$item['id']?>" class="btn btn-primary btn-xs" onclick="event.stopPropagation()">Book</a>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach;?>
  </div>
  <?=pagLinks($res['page'],$res['last'],BASE."/buses.php?q=".urlencode($q))?>
</div>
<?php require_once __DIR__.'/includes/footer.php';?>
