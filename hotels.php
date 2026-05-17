<?php
require_once __DIR__.'/includes/bootstrap.php';
$q=clean($_GET['q']??'');$city=clean($_GET['city']??'');$star=(int)($_GET['star']??0);$fc=$_GET['fc']??'';$so=clean($_GET['so']??'rating');$pg=max(1,(int)($_GET['pg']??1));
$w=['is_active=1'];$p=[];
if($q){$w[]='(name LIKE ? OR city LIKE ?)';$lk="%$q%";$p[]=$lk;$p[]=$lk;}
if($city){$w[]='city=?';$p[]=$city;}if($star){$w[]='stars=?';$p[]=$star;}
if($fc==='1'){$w[]='free_cancellation=1';}elseif($fc==='0'){$w[]='free_cancellation=0';}
$om=['rating'=>'rating DESC','price'=>'price_per_night ASC','price-d'=>'price_per_night DESC'];
$res=DB::paginate("SELECT * FROM hotels WHERE ".implode(' AND ',$w)." ORDER BY ".($om[$so]??'rating DESC'),$p,$pg);
$cities=DB::all("SELECT DISTINCT city FROM hotels WHERE is_active=1 ORDER BY city");
$base=BASE."/hotels.php?q=".urlencode($q)."&city=".urlencode($city)."&star=$star&fc=".urlencode($fc)."&so=$so";
require_once __DIR__.'/includes/image_maps.php';

$pageTitle='Hotels — TravelNest';require_once __DIR__.'/includes/header.php';?>
<div class="ov" id="det-modal"><div class="mod"><div class="mh"><h3 id="det-title"></h3><button class="mx" onclick="closeMod('det-modal')">✕</button></div><div class="mb" id="det-body"></div></div></div>
<div class="sec">
  <h2 class="stitle">Hotels & Resorts</h2><p class="ssub"><?=$res['total']?> properties worldwide</p>
  <div class="page-with-sidebar">
    <div class="filter-sidebar">
      <h3>🔍 Filters</h3>
      <form method="GET">
        <div class="fg"><label>Search</label><input name="q" value="<?=clean($q)?>" placeholder="City / hotel name..."></div>
        <div class="fg"><label>City</label><select name="city" onchange="this.form.submit()"><option value="">All Cities</option><?php foreach($cities as $c):?><option value="<?=clean($c['city'])?>" <?=$city===$c['city']?'selected':''?>><?=clean($c['city'])?></option><?php endforeach;?></select></div>
        <div class="fg"><label>Stars</label><select name="star" onchange="this.form.submit()"><option value="">All Stars</option><?php for($i=5;$i>=3;$i--):?><option value="<?=$i?>" <?=$star===$i?'selected':''?>><?=$i?> Star</option><?php endfor;?></select></div>
        <div class="fg"><label>Cancellation</label><select name="fc" onchange="this.form.submit()"><option value="">Any</option><option value="1" <?=$fc==='1'?'selected':''?>>Free Cancel</option><option value="0" <?=$fc==='0'?'selected':''?>>Non-Refundable</option></select></div>
        <div class="fg"><label>Sort By</label><select name="so" onchange="this.form.submit()"><option value="rating" <?=$so==='rating'?'selected':''?>>Top Rated</option><option value="price" <?=$so==='price'?'selected':''?>>Price ↑</option><option value="price-d" <?=$so==='price-d'?'selected':''?>>Price ↓</option></select></div>
        <button type="submit" class="btn btn-primary">Apply</button>
      </form>
    </div>
    <div>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px">
        <?php if(empty($res['data'])):?>
          <div class="empty-state" style="grid-column:1/-1"><span class="empty-emoji">🏨</span><div class="empty-title">No hotels found</div><div class="empty-desc">Try different filters or search location.</div><a href="<?=BASE?>/hotels.php" class="btn btn-primary">Clear Filters</a></div>
        <?php endif;?>
        <?php foreach($res['data'] as $h):
          $hImg=$hotelImages[$h['name']]??$defaultHotelImg;
        ?>
        <div class="hc">
          <div class="hc-img" onclick="showHotel(<?=$h['id']?>)" style="background:url('<?=$hImg?>') center/cover no-repeat">
            <span class="rb"><?=$h['rating']?></span>
          </div>
          <div class="hc-body">
            <div class="fw5 mb4" style="font-size:14px;cursor:pointer" onclick="showHotel(<?=$h['id']?>)"><?=clean($h['name'])?></div>
            <div class="xs mb6">📍 <?=clean($h['city'])?>, <?=clean($h['country']??'India')?></div>
            <div class="stars mb6"><?=str_repeat('★',(int)$h['stars'])?></div>
            <?php if($h['free_cancellation']):?><span class="tag t-green" style="font-size:10px;display:block;margin-bottom:6px">Free Cancellation</span><?php endif;?>
            <div class="flex sb mt8"><div><span class="acc fw5" style="font-size:16px"><?=rupee($h['price_per_night'])?></span><span class="xs">/night</span></div>
              <div class="flex g4r">
                <button style="background:none;border:none;cursor:pointer;font-size:16px;padding:4px" onclick="wlToggle('Hotel',<?=$h['id']?>,this)" title="Wishlist">🤍</button>
                <button class="btn btn-ghost btn-xs" onclick="showHotel(<?=$h['id']?>)">Details</button>
                <a href="<?=BASE?>/book.php?type=hotel&id=<?=$h['id']?>" class="btn btn-primary btn-xs">Book</a>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach;?>
      </div>
      <?=pagLinks($res['page'],$res['last'],$base)?>
    </div>
  </div>
</div>
<?php require_once __DIR__.'/includes/footer.php';?>
