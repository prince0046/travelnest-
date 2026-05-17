<?php
require_once __DIR__.'/includes/bootstrap.php';
if(loggedIn()){header('Location: '.BASE.'/index.php');exit;}

$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!checkCsrf()){$msg='Invalid request.';}
    else{
        $email=trim($_POST['email']??'');
        if(!$email||!filter_var($email,FILTER_VALIDATE_EMAIL)){
            $msg='Please enter a valid email address.';
        }else{
            $user=DB::one("SELECT id,name FROM users WHERE email=? AND is_active=1",[$email]);
            if($user){
                // Delete old tokens for this email
                DB::q("DELETE FROM password_resets WHERE email=?",[$email]);
                // Generate token
                $token=bin2hex(random_bytes(32));
                $expires=date('Y-m-d H:i:s',strtotime('+1 hour'));
                DB::insert('password_resets',['email'=>$email,'token'=>$token,'expires_at'=>$expires]);
                $fullResetUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . BASE . '/reset_password.php?token=' . $token;

                // Send the password reset email
                sendPasswordResetEmail($email, $user['name'], $fullResetUrl);
                $msg='✅ A password reset link has been sent to your email address.';
            }else{
                // Don't reveal whether the email exists
                $msg='✅ If an account with that email exists, a reset link has been sent.';
            }
        }
    }
}
$pageTitle='Forgot Password — TravelNest';
require_once __DIR__.'/includes/header.php';
?>
<div class="auth-wrap">
  <div class="auth-card">
    <div style="font-family:'Inter',sans-serif;font-size:30px;font-weight:700;color:var(--accent);text-align:center;margin-bottom:6px">TravelNest</div>
    <p class="sm tc mb20">Reset your password</p>

    <?php if($msg): ?><div class="flash <?= str_starts_with($msg,'✅')?'ok':'err' ?>"><?= clean($msg) ?></div><?php endif; ?>

    <?php if(!str_starts_with($msg,'✅')): ?>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?=csrf()?>">
      <div class="fg"><label>Email Address</label><input type="email" name="email" placeholder="you@example.com" required autocomplete="email"></div>
      <button type="submit" class="btn btn-primary w100 btn-lg mt8">Send Reset Link →</button>
    </form>
    <?php endif; ?>

    <p class="sm tc mt16"><a href="<?= BASE ?>/login.php" style="color:var(--accent)">← Back to Sign In</a></p>
  </div>
</div>
<?php require_once __DIR__.'/includes/footer.php'; ?>
