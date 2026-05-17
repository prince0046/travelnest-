<?php $tickets=DB::all("SELECT t.*,u.name un FROM support_tickets t LEFT JOIN users u ON t.user_id=u.id ORDER BY FIELD(t.status,'Open','In Progress','Resolved','Closed'),t.created_at DESC LIMIT 50");?>
<h2 style="font-size:22px;margin-bottom:20px">Support Tickets</h2>
<div class="card mb16"><div class="ox"><table class="dt"><thead><tr><th>ID</th><th>User</th><th>Subject</th><th>Priority</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead><tbody>
<?php foreach($tickets as $t):?>
<tr><td class="fw5">#<?=$t['id']?></td><td class="xs"><?=clean($t['un']??'Guest')?></td><td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px"><?=clean($t['subject'])?></td>
<td><span class="bdg <?=$t['priority']==='Urgent'?'t-red':($t['priority']==='High'?'t-amber':'t-blue')?>"><?=$t['priority']?></span></td>
<td><?=statusBadge($t['status'])?></td><td class="xs"><?=fmtDate($t['created_at'])?></td>
<td><button class="btn btn-ghost btn-xs" onclick="document.getElementById('rId').value=<?=$t['id']?>;document.getElementById('rSub').textContent='<?=addslashes(clean($t['subject']))?>';document.getElementById('rMsg').textContent='<?=addslashes(clean($t['message']))?>';document.getElementById('replyPanel').classList.remove('hidden')">💬 Reply</button></td>
</tr><?php endforeach;?>
</tbody></table></div></div>
<div id="replyPanel" class="card hidden">
  <h3 style="font-size:15px;margin-bottom:12px">Reply to Ticket</h3>
  <div class="card2 mb12"><div class="fw5 mb4" id="rSub"></div><div class="sm" id="rMsg"></div></div>
  <form method="POST" action="<?=BASE?>/admin/index.php?sec=support">
    <input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="act" value="reply_ticket"><input type="hidden" name="tid" id="rId">
    <div class="fg mb12"><label>Your Reply</label><textarea name="reply" rows="4" required></textarea></div>
    <div class="flex g8"><button type="submit" class="btn btn-primary">Send & Resolve</button><button type="button" class="btn btn-ghost" onclick="document.getElementById('replyPanel').classList.add('hidden')">Cancel</button></div>
  </form>
</div>
