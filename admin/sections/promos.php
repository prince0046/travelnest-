<?php
$edit=isset($_GET['edit'])?DB::one("SELECT * FROM promo_codes WHERE id=?",[(int)$_GET['edit']]):null;
$promos=DB::all("SELECT * FROM promo_codes ORDER BY status ASC,created_at DESC");
?>
<div class="flex sb wrap-x g12 mb20">
  <h2 style="font-size:22px">Promo Codes <span class="bdg t-blue"><?=count($promos)?></span></h2>
  <button class="btn btn-primary btn-sm" onclick="document.getElementById('addPromo').classList.toggle('hidden')">+ Add Promo</button>
</div>
<div id="addPromo" class="card mb20 <?=$edit?'':'hidden'?>">
  <h3 style="font-size:15px;margin-bottom:16px"><?=$edit?'Edit':'Create'?> Promo Code</h3>
  <form method="POST" action="<?=BASE?>/admin/index.php?sec=promos">
    <input type="hidden" name="csrf" value="<?=csrf()?>">
    <input type="hidden" name="act" value="<?=$edit?'edit_promo':'add_promo'?>">
    <?php if($edit):?><input type="hidden" name="id" value="<?=$edit['id']?>"><?php endif;?>
    <div class="g4 mb12">
      <div class="fg"><label>Code</label><input name="code" value="<?=clean($edit['code']??'')?>" required style="text-transform:uppercase" placeholder="SUMMER25"></div>
      <div class="fg"><label>Type</label><select name="dtype"><option value="percentage" <?=($edit['discount_type']??'')==='percentage'?'selected':''?>>Percentage %</option><option value="fixed" <?=($edit['discount_type']??'')==='fixed'?'selected':''?>>Fixed ₹</option></select></div>
      <div class="fg"><label>Value</label><input type="number" step="0.01" name="dval" value="<?=$edit['discount_value']??''?>" required placeholder="25"></div>
      <div class="fg"><label>Max Discount (₹)</label><input type="number" name="maxd" value="<?=$edit['max_discount']??500?>"></div>
    </div>
    <div class="g4 mb12">
      <div class="fg"><label>Min Booking (₹)</label><input type="number" name="minb" value="<?=$edit['min_booking']??0?>"></div>
      <div class="fg"><label>Applies To</label><select name="apptype"><?php foreach(['All','Flight','Hotel','Package','Train','Bus','Cruise'] as $t):?><option value="<?=$t?>" <?=($edit['applicable_type']??'All')===$t?'selected':''?>><?=$t?></option><?php endforeach;?></select></div>
      <div class="fg"><label>Usage Limit</label><input type="number" name="ulimit" value="<?=$edit['usage_limit']??1000?>"></div>
      <div class="fg"><label>Status</label><select name="status"><?php foreach(['Active','Expiring','Expired','Scheduled'] as $s):?><option value="<?=$s?>" <?=($edit['status']??'Active')===$s?'selected':''?>><?=$s?></option><?php endforeach;?></select></div>
    </div>
    <div class="g3 mb12">
      <div class="fg"><label>Description</label><input name="desc" value="<?=clean($edit['description']??'')?>" placeholder="25% off summer travel"></div>
      <div class="fg"><label>Valid From</label><input type="text" name="vfrom" value="<?=$edit?date('d/m/Y',strtotime($edit['valid_from'])):date('d/m/Y')?>" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}" maxlength="10" title="Format: dd/mm/yyyy" oninput="formatDateInput(event)"></div>
      <div class="fg"><label>Valid Until</label><input type="text" name="vuntil" value="<?=$edit?date('d/m/Y',strtotime($edit['valid_until'])):''?>" required placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}" maxlength="10" title="Format: dd/mm/yyyy" oninput="formatDateInput(event)"></div>
    </div>
    <div class="flex g8">
      <button type="submit" class="btn btn-primary"><?=$edit?'Update':'Create'?> Promo</button>
      <a href="<?=BASE?>/admin/index.php?sec=promos" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>
<div class="card">
<div class="ox"><table class="dt"><thead><tr><th>Code</th><th>Discount</th><th>Max</th><th>Type</th><th>Used/Limit</th><th>Valid Until</th><th>Status</th><th>Actions</th></tr></thead><tbody>
<?php foreach($promos as $pr):?>
<tr>
  <td class="acc fw5"><?=clean($pr['code'])?></td>
  <td><?=$pr['discount_type']==='percentage'?$pr['discount_value'].'%':'₹'.number_format($pr['discount_value'])?></td>
  <td><?=rupee($pr['max_discount'])?></td>
  <td class="xs"><?=clean($pr['applicable_type'])?></td>
  <td><div class="xs mb4"><?=number_format($pr['used_count']).' / '.number_format($pr['usage_limit'])?></div>
    <div class="pb"><div class="pf" style="width:<?=min(100,round($pr['used_count']/max(1,$pr['usage_limit'])*100))?>%;background:<?=$pr['used_count']/$pr['usage_limit']>.9?'var(--accent2)':'var(--accent)'?>"></div></div>
  </td>
  <td class="xs"><?=$pr['valid_until']?></td>
  <td><?=statusBadge($pr['status'])?></td>
  <td><div class="flex g4r">
    <a href="?sec=promos&edit=<?=$pr['id']?>" class="btn btn-ghost btn-xs">✏️</a>
    <button class="btn btn-danger btn-xs" onclick="delItem('promo_codes',<?=$pr['id']?>,'<?=addslashes(clean($pr['code']))?>')">🗑</button>
  </div></td>
</tr>
<?php endforeach;?>
</tbody></table></div>
</div>
