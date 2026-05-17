<?php
require_once __DIR__.'/../includes/bootstrap.php';

// Already logged in as admin
if(loggedIn() && isAdmin()){
    header('Location: '.BASE.'/admin/index.php'); exit;
}
// Logged in as regular user — clear session
if(loggedIn() && !isAdmin()){
    session_destroy(); session_start();
}

$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $user = DB::one("SELECT * FROM users WHERE email=? AND role='admin' AND is_active=1", [$email]);

    if($user && password_verify($pass, $user['password'])){
            $_SESSION['uid']  = $user['id'];
            $_SESSION['role'] = 'admin';
            $_SESSION['name'] = $user['name'];
            flash('ok', 'Welcome back, Admin!');
            header('Location: '.BASE.'/admin/index.php'); exit;
    }
    $err = 'Invalid admin credentials. Check email and password.';
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Admin Login — TravelNest</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE ?>/assets/css/style.css">
<style>
.auth-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;background:radial-gradient(ellipse 60% 40% at 50% 0%,rgba(240,165,0,.06),transparent 70%)}
.auth-card{background:var(--card);border:1px solid var(--border);border-radius:var(--rxl,16px);padding:38px;max-width:440px;width:100%}
.flash{padding:12px 18px;border-radius:8px;margin-bottom:16px;font-size:14px}
.flash.err{background:#3b0012;color:#fb7185;border:1px solid #7f1d1d}
</style>
</head>
<body>
<div class="toast" id="toast"></div>
<div class="auth-wrap">
  <div class="auth-card">
    <!-- Logo -->
    <div style="text-align:center;margin-bottom:6px">
      <div style="font-family:'Inter',sans-serif;font-size:30px;font-weight:700;color:#008cff">TravelNest</div>
      <div style="font-size:13px;color:#f43f5e;font-weight:600;margin-top:2px;letter-spacing:.5px">ADMIN PANEL</div>
    </div>
    <p style="font-size:13px;color:#8899b4;text-align:center;margin-bottom:24px">Sign in with your administrator credentials</p>

    <?php if($err): ?>
    <div class="flash err">⚠️ <?= clean($err) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="fg">
        <label>Admin Email</label>
        <input type="email" name="email" placeholder="admin@travelnest.com" required autocomplete="email" autofocus>
      </div>
      <div class="fg" style="margin-bottom:20px">
        <label>Password</label>
        <input type="password" name="password" placeholder="••••••" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-primary w100 btn-lg">Login to Admin Panel →</button>
    </form>

    <p style="font-size:13px;color:#4a5e7a;text-align:center;margin-top:20px">
      <a href="<?= BASE ?>/login.php" style="color:#8899b4">← Back to User Login</a>
    </p>


  </div>
</div>
<script src="<?= BASE ?>/assets/js/app.js"></script>
</body>
</html>
