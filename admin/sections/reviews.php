<?php $revs=DB::all("SELECT r.*,u.name un FROM reviews r LEFT JOIN users u ON r.user_id=u.id ORDER BY r.created_at DESC LIMIT 50");?>
<h2 style="font-size:22px;margin-bottom:20px">Reviews <span class="bdg t-blue"><?=count($revs)?></span></h2>
<div class="card"><div class="ox"><table class="dt"><thead><tr><th>User</th><th>Item</th><th>Type</th><th>Rating</th><th>Comment</th><th>Date</th></tr></thead><tbody>
<?php foreach($revs as $r):?>
<tr><td class="fw5"><?=clean($r['un']??'N/A')?></td><td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px"><?=clean($r['item_name']??'')?></td><td><span class="bdg t-blue"><?=clean($r['item_type'])?></span></td><td><span style="color:var(--gold)"><?=str_repeat('★',(int)$r['rating'])?></span></td><td style="max-width:200px;font-size:12px;color:var(--text2)"><?=clean($r['comment']??'')?></td><td class="xs"><?=fmtDate($r['created_at'])?></td></tr>
<?php endforeach;?>
</tbody></table></div></div>
