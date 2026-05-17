<?php
$edit=isset($_GET['edit'])?DB::one("SELECT * FROM flights WHERE id=?",[(int)$_GET['edit']]):null;
$q=clean($_GET['q']??'');$pg=max(1,(int)($_GET['pg']??1));
$w=['is_active=1'];$p=[];
if($q){$w[]='(airline LIKE ? OR flight_code LIKE ? OR from_city LIKE ? OR to_city LIKE ?)';$lk="%$q%";array_push($p,$lk,$lk,$lk,$lk);}
$res=DB::paginate("SELECT * FROM flights WHERE ".implode(' AND ',$w)." ORDER BY price ASC",$p,$pg,15);
$base=BASE."/admin/index.php?sec=flights&q=".urlencode($q);
?>
<div class="flex sb wrap-x g12 mb20">
  <h2 style="font-size:22px">Flights <span class="bdg t-blue"><?=$res['total']?></span></h2>
  <button class="btn btn-primary btn-sm" onclick="document.getElementById('addForm').classList.toggle('hidden')">+ Add Flight</button>
</div>
<div id="addForm" class="card mb20 <?=$edit?'':'hidden'?>">
  <h3 style="font-size:15px;margin-bottom:16px"><?=$edit?'Edit Flight':'Add New Flight'?></h3>
  <form method="POST" action="<?=BASE?>/admin/index.php?sec=flights">
    <input type="hidden" name="csrf" value="<?=csrf()?>">
    <input type="hidden" name="action" value="<?=BASE?>/admin/index.php">
    <input type="hidden" name="act" value="<?=$edit?'edit_flight':'add_flight'?>">
    <?php if($edit):?><input type="hidden" name="id" value="<?=$edit['id']?>"><?php endif;?>
    <div class="g4 mb12">
      <div class="fg"><label>Code</label><input name="flight_code" value="<?=clean($edit['flight_code']??'')?>" placeholder="6E-204" required></div>
      <div class="fg"><label>Airline</label><input name="airline" value="<?=clean($edit['airline']??'')?>" placeholder="IndiGo" required></div>
      <div class="fg"><label>Emoji</label><input name="emoji" value="<?=clean($edit['emoji']??'✈️')?>"></div>
      <div class="fg"><label>Class</label><select name="class"><?php foreach(['Economy','Business','First Class'] as $c):?><option value="<?=$c?>" <?=($edit['class']??'')===$c?'selected':''?>><?=$c?></option><?php endforeach;?></select></div>
    </div>
    <div class="g4 mb12">
      <div class="fg"><label>From City</label><input name="from_city" value="<?=clean($edit['from_city']??'')?>" required></div>
      <div class="fg"><label>From Code</label><input name="from_code" value="<?=clean($edit['from_code']??'')?>" placeholder="BOM" required></div>
      <div class="fg"><label>To City</label><input name="to_city" value="<?=clean($edit['to_city']??'')?>" required></div>
      <div class="fg"><label>To Code</label><input name="to_code" value="<?=clean($edit['to_code']??'')?>" placeholder="DEL" required></div>
    </div>
    <div class="g4 mb12">
      <div class="fg"><label>Departure</label><input name="dep" value="<?=clean($edit['departure_time']??'')?>" placeholder="06:00" required></div>
      <div class="fg"><label>Arrival</label><input name="arr" value="<?=clean($edit['arrival_time']??'')?>" placeholder="08:20" required></div>
      <div class="fg"><label>Duration</label><input name="dur" value="<?=clean($edit['duration']??'')?>" placeholder="2h 20m" required></div>
      <div class="fg"><label>Stops</label><select name="stops"><option value="Direct" <?=($edit['stops']??'')==='Direct'?'selected':''?>>Direct</option><option value="1 Stop" <?=($edit['stops']??'')==='1 Stop'?'selected':''?>>1 Stop</option></select></div>
    </div>
    <div class="g4 mb12">
      <div class="fg"><label>Price (₹)</label><input type="number" name="price" value="<?=$edit['price']??''?>" required></div>
      <div class="fg"><label>Seats</label><input type="number" name="seats" value="<?=$edit['seats_available']??50?>"></div>
      <div class="fg"><label>Aircraft</label><input name="aircraft" value="<?=clean($edit['aircraft']??'')?>" placeholder="A320neo"></div>
      <div class="fg"><label>Baggage</label><input name="baggage" value="<?=clean($edit['baggage']??'')?>" placeholder="15kg"></div>
    </div>
    <div class="flex g8">
      <button type="submit" class="btn btn-primary"><?=$edit?'Update':'Add Flight'?></button>
      <a href="<?=BASE?>/admin/index.php?sec=flights" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>
<div class="card">
  <form method="GET" class="fbar mb12"><input type="hidden" name="sec" value="flights">
    <input name="q" value="<?=clean($q)?>" placeholder="Airline / code / route...">
    <button class="btn btn-primary btn-sm">Search</button>
  </form>
  <div class="ox"><table class="dt"><thead><tr><th>Code</th><th>Airline</th><th>Route</th><th>Time</th><th>Class</th><th>Price</th><th>Seats</th><th>Stops</th><th>Actions</th></tr></thead><tbody>
  <?php foreach($res['data'] as $f):?>
  <tr data-type="flight" data-id="<?=$f['id']?>">
    <td class="fw5"><?=clean($f['flight_code'])?></td>
    <td><?=$f['emoji']?> <?=clean($f['airline'])?></td>
    <td class="xs"><?=clean($f['from_code'])?>→<?=clean($f['to_code'])?></td>
    <td class="xs"><?=$f['departure_time']?>→<?=$f['arrival_time']?></td>
    <td><span class="bdg t-blue"><?=$f['class']?></span></td>
    <td class="fw5"><?=rupee($f['price'])?></td>
    <td style="color:<?=$f['seats_available']<10?'var(--accent2)':($f['seats_available']<20?'var(--accent)':'var(--green)')?>"><?=$f['seats_available']?></td>
    <td><span class="bdg <?=$f['stops']==='Direct'?'t-green':'t-amber'?>"><?=$f['stops']?></span></td>
    <td><div class="flex g4r">
      <a href="?sec=flights&edit=<?=$f['id']?>" class="btn btn-ghost btn-xs">✏️</a>
      <button class="btn btn-danger btn-xs" onclick="delItem('flight',<?=$f['id']?>,'<?=addslashes(clean($f['flight_code']))?>')">🗑</button>
    </div></td>
  </tr>
  <?php endforeach;?>
  </tbody></table></div>
  <?=pagLinks($res['page'],$res['last'],$base)?>
</div>
