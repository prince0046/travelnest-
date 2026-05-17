<?php
require_once __DIR__.'/includes/bootstrap.php';
if(loggedIn()){header('Location: '.BASE.'/index.php');exit;}
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!checkCsrf()){$err='Invalid request. Please try again.';}
    else{
    $name=clean($_POST['name']??'');
    $email=trim($_POST['email']??'');
    $pass=$_POST['password']??'';
    $phone=clean($_POST['phone']??'');
    $city=clean($_POST['city']??'');
    if(strlen($pass)<6){$err='Password must be at least 6 characters.';}
    elseif(DB::count('users','email=?',[$email])){$err='Email already registered.';}
    else{
        $id=DB::insert('users',['name'=>$name,'email'=>$email,'password'=>password_hash($pass,PASSWORD_BCRYPT),'phone'=>$phone,'city'=>$city,'role'=>'user','tier'=>'Bronze']);
        $_SESSION['uid']=$id;$_SESSION['role']='user';$_SESSION['name']=$name;
        sendWelcomeEmail($email, $name);
        flash('ok',"Welcome to TravelNest, $name!");
        header('Location: '.BASE.'/index.php');exit;
    }
    }
}
$pageTitle='Sign Up — TravelNest';
require_once __DIR__.'/includes/header.php';
?>
<div class="auth-wrap">
  <div class="auth-card">
    <div style="font-family:'Inter',sans-serif;font-size:30px;font-weight:700;color:var(--accent);text-align:center;margin-bottom:6px">TravelNest</div>
    <p class="sm tc mb20">Create your free account</p>
    <?php if($err): ?><div class="flash err"><?= clean($err) ?></div><?php endif; ?>
    <form method="POST">
      <input type="hidden" name="csrf" value="<?=csrf()?>">
      <div class="fg"><label>Full Name</label><input name="name" placeholder="Your full name" required></div>
      <div class="fg"><label>Email</label><input type="email" name="email" placeholder="you@example.com" required></div>
      <div class="fg"><label>Password (min 6 chars)</label><input type="password" name="password" required></div>
      <div class="g2">
        <div class="fg"><label>Phone</label><input name="phone" placeholder="+91 98765 43210"></div>
        <div class="fg"><label>City</label><input name="city" placeholder="Mumbai"></div>
      </div>
      <button type="submit" class="btn btn-primary w100 btn-lg mt8">Create Account →</button>
    </form>
    <p class="sm tc mt16">Already have an account? <a href="<?= BASE ?>/login.php" style="color:var(--accent)">Sign In</a></p>
  </div>
</div>
<?php require_once __DIR__.'/includes/footer.php'; ?>
