<?php
require_once __DIR__.'/includes/bootstrap.php';
if(loggedIn()){
    header('Location: '.BASE.(isAdmin()?'/admin/index.php':'/index.php')); exit;
}
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!checkCsrf()){$err='Invalid request.';}
    else{
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';
        $ip    = $_SERVER['REMOTE_ADDR'];

        // Clean up old attempts (older than 15 mins)
        DB::q("DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 15 MINUTE)");

        // Check rate limit (max 5 attempts per 15 mins per IP)
        $attempts = DB::count("login_attempts", "ip_address=?", [$ip]);
        if ($attempts >= 5) {
            $err = 'Too many failed login attempts. Please try again after 15 minutes.';
        } else {
            $user  = DB::one("SELECT * FROM users WHERE email=? AND is_active=1", [$email]);
        if($user && password_verify($pass, $user['password'])){
            $_SESSION['uid']  = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            flash('ok','Welcome back, '.explode(' ',$user['name'])[0].'!');
            $dest = $user['role']==='admin' ? BASE.'/admin/index.php' : BASE.'/index.php';
            header('Location: '.$dest); exit;
        }
            $err='Invalid email or password.';
            // Record failed attempt
            DB::insert('login_attempts', ['ip_address' => $ip, 'email' => $email]);
        }
    }
}
$pageTitle='Login — TravelNest';
require_once __DIR__.'/includes/header.php';
?>
<div class="auth-wrap">
  <div class="auth-card">
    <div style="font-family:'Inter',sans-serif;font-size:30px;font-weight:700;color:var(--accent);text-align:center;margin-bottom:6px">TravelNest</div>
    <p class="sm tc mb20">Sign in to your account</p>

    <?php if($err): ?><div class="flash err">⚠️ <?= clean($err) ?></div><?php endif; ?>

    <form method="POST" action="">
      <input type="hidden" name="csrf" value="<?=csrf()?>">
      <div class="fg"><label>Email Address</label><input type="email" name="email" placeholder="you@example.com" required autocomplete="email"></div>
      <div class="fg"><label>Password</label><input type="password" name="password" placeholder="••••••" required autocomplete="current-password"></div>
      <button type="submit" class="btn btn-primary w100 btn-lg mt8">Sign In →</button>
    </form>
    <p class="sm tc mt12"><a href="<?= BASE ?>/forgot_password.php" style="color:var(--text2)">Forgot your password?</a></p>
    <p class="sm tc mt8">No account? <a href="<?= BASE ?>/register.php" style="color:var(--accent)">Sign Up Free</a></p>

  </div>
</div>
<?php require_once __DIR__.'/includes/footer.php'; ?>
