<?php
require_once __DIR__.'/includes/bootstrap.php';
$q=clean($_GET['q']??'');$pg=max(1,(int)($_GET['pg']??1));
$w=['is_active=1'];$p=[];
if($q){$w[]='(name LIKE ? OR destination LIKE ?)';$lk="%$q%";$p[]=$lk;$p[]=$lk;}
$res=DB::paginate("SELECT * FROM packages WHERE ".implode(' AND ',$w)." ORDER BY id ASC",$p,$pg);
$packageImages=[
  'Goa'=>'assets/images/packages/Goa.jpg',
  'Kerala'=>'assets/images/packages/Kerala.jpg',
  'Rajasthan'=>'assets/images/packages/Rajasthan.jpg',
  'Bali'=>'assets/images/packages/Bali.jpg',
  'Dubai'=>'assets/images/packages/Dubai.jpg',
  'Manali'=>'assets/images/packages/Manali.jpg',
  'Andaman'=>'assets/images/packages/Andaman.jpg',
  'Singapore'=>'assets/images/packages/Singapore.jpg',
  'Rishikesh'=>'assets/images/packages/Rishikesh.jpg',
  'Ladakh'=>'assets/images/packages/Ladakh.jpg',
  'Bangkok & Phuket'=>'assets/images/packages/Bangkok_and_Phuket.jpg',
  'Coorg'=>'assets/images/packages/Coorg.jpg',
  'Jim Corbett'=>'assets/images/packages/Jim_Corbett.jpg',
  'Maldives'=>'assets/images/packages/Maldives.jpg',
  'Nepal'=>'assets/images/packages/Nepal.jpg',
  'Srinagar'=>'assets/images/packages/Srinagar.jpg',
  'Switzerland'=>'assets/images/packages/Switzerland.jpg',
  'Vietnam'=>'assets/images/packages/Vietnam.jpg',
  'Meghalaya'=>'assets/images/packages/Meghalaya.jpg',
  'Bhutan'=>'assets/images/packages/Bhutan.jpg',
];
$defaultPkgImg='assets/images/packages/Default.jpg';
$tagColors=['Best Seller'=>'t-amber','Nature'=>'t-green','Heritage'=>'t-purple','Honeymoon'=>'t-red','International'=>'t-blue','Adventure'=>'t-teal','Island Paradise'=>'t-teal','Family'=>'t-blue','Spiritual'=>'t-purple','Extreme'=>'t-red','Popular'=>'t-amber','Weekend'=>'t-green','Wildlife'=>'t-green','Ultra Luxury'=>'t-gold','Bucket List'=>'t-red','Scenic'=>'t-blue','Premium'=>'t-gold','Cultural'=>'t-purple','Off-beat'=>'t-teal'];
$pageTitle='Holiday Packages — TravelNest';
require_once __DIR__.'/includes/header.php';?>
<div class="ov" id="det-modal"><div class="mod" style="max-width:700px"><div class="mh"><h3 id="det-title"></h3><button class="mx" onclick="closeMod('det-modal')">✕</button></div><div class="mb" id="det-body"></div></div></div>
<meta name="csrf" content="<?=csrf()?>">
<div class="sec">
  <h2 class="stitle">Holiday Packages</h2><p class="ssub"><?=$res['total']?> available</p>
  <form method="GET" class="fbar mb16">
    <input name="q" value="<?=clean($q)?>" placeholder="Search packages...">
    <button type="submit" class="btn btn-primary btn-sm">Search</button>
  </form>
  <div class="pkg-grid">
  <?php foreach($res['data'] as $item):
    $nm=$item['name']??'';
    $dest=$item['destination']??'';
    $pr=$item['price']??0;
    $pImg=$packageImages[$dest]??$defaultPkgImg;
    $tc=$tagColors[$item['tag']??'']??'t-amber';
  ?>
  <div class="pkg-card" onclick="showPackage(<?=$item['id']?>)">
    <div class="pkg-card-img" style="background-image:url('<?=$pImg?>')">
      <span class="pkg-nights">🌙 <?=$item['nights']?> Nights</span>
      <?php if(isset($item['tag'])):?><span class="pkg-tag tag <?=$tc?>"><?=clean($item['tag'])?></span><?php endif;?>
    </div>
    <div class="pkg-card-body">
      <div class="pkg-name"><?=clean($nm)?></div>
      <div class="pkg-dest">📍 <?=clean($dest)?><?php if(isset($item['category'])):?> · <?=clean($item['category'])?><?php endif;?></div>
      <?php if(isset($item['inclusions'])):?><div class="pkg-inclusions"><?php foreach(array_slice(incArr($item['inclusions']),0,4) as $inc):?><span class="chip"><?=clean($inc)?></span><?php endforeach;?></div><?php endif;?>
      <div class="pkg-card-footer">
        <div><span class="pkg-price"><?=$pr>0?rupee($pr):'—'?></span><div class="pkg-per">per person</div></div>
        <div class="flex g4r">
          <button class="btn btn-ghost btn-xs" onclick="event.stopPropagation();showPackage(<?=$item['id']?>)">Details</button>
          <a href="<?=BASE?>/book.php?type=package&id=<?=$item['id']?>" class="btn btn-primary btn-xs" onclick="event.stopPropagation()">Book</a>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach;?>
  </div>
  <?=pagLinks($res['page'],$res['last'],BASE."/packages.php?q=".urlencode($q))?>
</div>
<?php require_once __DIR__.'/includes/footer.php';?>
