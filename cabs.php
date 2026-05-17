<?php
require_once __DIR__.'/includes/bootstrap.php';
$q=clean($_GET['q']??'');$pg=max(1,(int)($_GET['pg']??1));
$w=['is_active=1'];$p=[];
if($q){$w[]='(vehicle_name LIKE ? OR cab_type LIKE ?)';$lk="%$q%";$p[]=$lk;$p[]=$lk;}
$res=DB::paginate("SELECT * FROM cabs WHERE ".implode(' AND ',$w)." ORDER BY id ASC",$p,$pg);
$cabImages=[
  'Maruti Suzuki Swift'=>'assets/images/cabs/1images.jpeg',
  'Hyundai i20'=>'assets/images/cabs/2images.jpeg',
  'Tata Tiago'=>'assets/images/cabs/3images.jpeg',
  'Maruti Suzuki Baleno'=>'assets/images/cabs/4images.jpeg',
  'Renault Kwid'=>'assets/images/cabs/5images.jpeg',
  'Maruti Suzuki Dzire'=>'assets/images/cabs/6images.jpeg',
  'Honda City'=>'assets/images/cabs/7images.jpeg',
  'Hyundai Verna'=>'assets/images/cabs/8images.jpeg',
  'Volkswagen Virtus'=>'assets/images/cabs/9images.jpeg',
  'Hyundai Creta'=>'assets/images/cabs/10images.jpeg',
  'Tata Nexon'=>'assets/images/cabs/11images.jpeg',
  'Kia Seltos'=>'assets/images/cabs/12images.jpeg',
  'Toyota Innova Crysta'=>'assets/images/cabs/13images.jpeg',
  'Maruti Suzuki Ertiga'=>'assets/images/cabs/14images.jpeg',
  'Mahindra XUV700'=>'assets/images/cabs/15images.jpeg',
  'Toyota Fortuner'=>'assets/images/cabs/16images.jpeg',
  'Jeep Compass'=>'assets/images/cabs/17images.jpeg',
  'Mercedes-Benz E-Class'=>'assets/images/cabs/18images.jpeg',
  'BMW 5 Series'=>'assets/images/cabs/19images.jpeg',
  'Audi A6'=>'assets/images/cabs/20images.jpeg',
];
$defaultCabImg='https://images.unsplash.com/photo-1502877338535-766e1452684a?w=400&h=300&fit=crop';
$pageTitle='Cab Rentals — TravelNest';
require_once __DIR__.'/includes/header.php';?>
<div class="ov" id="det-modal"><div class="mod" style="max-width:700px"><div class="mh"><h3 id="det-title"></h3><button class="mx" onclick="closeMod('det-modal')">✕</button></div><div class="mb" id="det-body"></div></div></div>
<meta name="csrf" content="<?=csrf()?>">
<div class="sec">
  <h2 class="stitle">Cab Rentals</h2><p class="ssub"><?=$res['total']?> available</p>
  <form method="GET" class="fbar mb16">
    <input name="q" value="<?=clean($q)?>" placeholder="Search...">
    <button type="submit" class="btn btn-primary btn-sm">Search</button>
  </form>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
  <?php foreach($res['data'] as $item):
    $nm=$item['vehicle_name']??'';
    $pr=$item['base_fare']??0;
    $cabImg=$cabImages[$nm]??$defaultCabImg;
    $fn='showCab';
  ?>
  <div class="hc" style="cursor:pointer" onclick="<?=$fn?>(<?=$item['id']?>)">
    <div class="hc-img" style="background:url('<?=$cabImg?>') center/cover no-repeat;height:180px">
      <span class="rb" style="background:linear-gradient(135deg,var(--accent),#d4900a);color:#000"><?=$item['cab_type']?></span>
    </div>
    <div class="hc-body">
      <div class="fw5 mb4" style="font-size:15px"><?=clean($nm)?></div>
      <div class="sm mb6">👥 <?=$item['capacity']?> passengers · 📏 Min <?=$item['min_km']?> km</div>
      <?php if(isset($item['amenities'])):?><div class="flex wrap-x g4r mb8"><?php foreach(amenArr($item['amenities']) as $am):?><span class="chip"><?=clean($am)?></span><?php endforeach;?></div><?php endif;?>
      <div class="flex sb mt8">
        <div><span class="acc fw5" style="font-size:18px"><?=rupee($pr)?></span><span class="xs"> base + ₹<?=$item['price_per_km']?>/km</span></div>
        <div class="flex g4r">
          <button class="btn btn-ghost btn-xs" onclick="event.stopPropagation();<?=$fn?>(<?=$item['id']?>)">Details</button>
          <a href="<?=BASE?>/book.php?type=cab&id=<?=$item['id']?>" class="btn btn-primary btn-xs" onclick="event.stopPropagation()">Book</a>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach;?>
  </div>
  <?=pagLinks($res['page'],$res['last'],BASE."/cabs.php?q=".urlencode($q))?>
</div>
<?php require_once __DIR__.'/includes/footer.php';?>
