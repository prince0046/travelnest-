<?php
require_once __DIR__.'/includes/bootstrap.php';
if(loggedIn()){header('Location: '.BASE.'/index.php');exit;}

$token=trim($_GET['token']??'');
$err=''; $ok=false;

// Validate token
$reset=null;
if($token){
    $reset=DB::one("SELECT * FROM password_resets WHERE token=? AND expires_at>NOW()",[$token]);
}
if(!$reset){
    $err='This reset link is invalid or has expired. Please request a new one.';
}

if($_SERVER['REQUEST_METHOD']==='POST' && $reset){
    if(!checkCsrf()){$err='Invalid request.';}
    else{
        $pass=$_POST['password']??'';
        $confirm=$_POST['confirm']??'';
        if(strlen($pass)<6){
            $err='Password must be at least 6 characters.';
        }elseif($pass!==$confirm){
            $err='Passwords do not match.';
        }else{
            $hash=password_hash($pass,PASSWORD_BCRYPT);
            DB::q("UPDATE users SET password=? WHERE email=?",[$hash,$reset['email']]);
            DB::q("DELETE FROM password_resets WHERE email=?",[$reset['email']]);
            // Get user name for email
            $userForEmail = DB::one("SELECT name FROM users WHERE email=?", [$reset['email']]);
            if($userForEmail) {
                sendPasswordChangedEmail($reset['email'], $userForEmail['name']);
            }
            flash('ok','Password reset successfully! Please sign in.');
            header('Location: '.BASE.'/login.php');exit;
        }
    }
}

$pageTitle='Reset Password — TravelNest';
require_once __DIR__.'/includes/header.php';
?>
<div class="auth-wrap">
  <div class="auth-card">
    <div style="font-family:'Inter',sans-serif;font-size:30px;font-weight:700;color:var(--accent);text-align:center;margin-bottom:6px">TravelNest</div>
    <p class="sm tc mb20">Set a new password</p>

    <?php if($err): ?>
      <div class="flash err">⚠️ <?= clean($err) ?></div>
      <?php if(!$reset): ?>
        <p class="sm tc mt16"><a href="<?= BASE ?>/forgot_password.php" class="btn btn-primary btn-sm">Request New Link →</a></p>
      <?php endif; ?>
    <?php elseif($reset): ?>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?=csrf()?>">
      <input type="hidden" name="token" value="<?= clean($token) ?>">
      <div class="fg"><label>New Password (min 6 chars)</label><input type="password" name="password" required></div>
      <div class="fg"><label>Confirm Password</label><input type="password" name="confirm" required></div>
      <button type="submit" class="btn btn-primary w100 btn-lg mt8">Reset Password →</button>
    </form>
    <?php endif; ?>

    <p class="sm tc mt16"><a href="<?= BASE ?>/login.php" style="color:var(--accent)">← Back to Sign In</a></p>
  </div>
</div>
<?php require_once __DIR__.'/includes/footer.php'; ?>
