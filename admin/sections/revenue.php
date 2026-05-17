<?php
$tot=DB::val("SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE booking_status!='Cancelled'");
$tbk=DB::val("SELECT COUNT(*) FROM bookings WHERE booking_status!='Cancelled'");
$byType=DB::all("SELECT booking_type,COUNT(*) cnt,COALESCE(SUM(total_amount),0) rev FROM bookings WHERE booking_status!='Cancelled' GROUP BY booking_type ORDER BY rev DESC");
$monthly=DB::all("SELECT DATE_FORMAT(created_at,'%b %Y') m,MONTH(created_at) mo,YEAR(created_at) yr,COUNT(*) bk,COALESCE(SUM(total_amount),0) rev,COALESCE(SUM(discount_amount),0) disc FROM bookings WHERE booking_status!='Cancelled' GROUP BY yr,mo ORDER BY yr DESC,mo DESC LIMIT 12");
?>
<h2 style="font-size:22px;margin-bottom:20px">Revenue Report</h2>
<div class="g4 mb20">
  <div class="stat"><div class="stat-v acc"><?=rupee($tot)?></div><div class="stat-l">Total Revenue</div></div>
  <div class="stat"><div class="stat-v blu"><?=number_format($tbk)?></div><div class="stat-l">Successful Bookings</div></div>
  <div class="stat"><div class="stat-v grn"><?=$tbk>0?rupee($tot/$tbk):'₹0'?></div><div class="stat-l">Avg Booking Value</div></div>
  <div class="stat"><div class="stat-v pur"><?=rupee(DB::val("SELECT COALESCE(SUM(discount_amount),0) FROM bookings"))?></div><div class="stat-l">Total Discounts</div></div>
</div>
<div class="g2 mb16">
  <div class="card">
    <h3 style="font-size:15px;margin-bottom:14px">Revenue by Category</h3>
    <?php $mx=max(1,$byType[0]['rev']??1);$tc=['Flight'=>'var(--accent)','Hotel'=>'var(--blue)','Package'=>'var(--green)','Train'=>'var(--purple)','Bus'=>'var(--teal)','Cab'=>'#f472b6','Cruise'=>'#33a3ff'];
    foreach($byType as $t):?>
    <div class="mb12"><div class="flex sb mb4"><span class="fw5" style="font-size:13px"><?=$t['booking_type']?></span><span class="fw5 acc"><?=rupee($t['rev'])?></span></div>
    <div class="flex g8 mb4"><div class="pb"><div class="pf" style="width:<?=round($t['rev']/$mx*100)?>%;background:<?=$tc[$t['booking_type']]??'var(--text3)'?>"></div></div><span class="xs"><?=$t['cnt']?> bk</span></div></div>
    <?php endforeach;?>
  </div>
  <div class="card">
    <h3 style="font-size:15px;margin-bottom:14px">Monthly Breakdown</h3>
    <div class="ox"><table class="dt"><thead><tr><th>Month</th><th>Bookings</th><th>Revenue</th><th>Discounts</th><th>Net</th></tr></thead><tbody>
    <?php foreach($monthly as $m):?>
    <tr><td class="fw5"><?=$m['m']?></td><td><?=number_format($m['bk'])?></td><td class="acc fw5"><?=rupee($m['rev'])?></td><td class="red xs">-<?=rupee($m['disc'])?></td><td class="grn fw5"><?=rupee($m['rev']-$m['disc'])?></td></tr>
    <?php endforeach;?>
    </tbody></table></div>
  </div>
</div>
