<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>POS System - Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <link rel="stylesheet" href="auth.css">
</head>
<?php
session_start();
$loginError = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $xml = simplexml_load_file('users.xml');
    foreach ($xml->user as $user) {
        if ($user->username == $username && $user->password == md5($password)) {
            $_SESSION['username'] = $username;
            header("Location: manage-receipts.php");
            exit;
        }
    }
    $loginError = true;  
}
?>
<body>
<div class="wrapper">
    <div class="logo text-center">
        <h1>POS System</h1>
    </div>
    <div class="inner-wrapper text-center">
        <h2 class="title">Login to your account</h2>
        <form method="post">
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <input type="password" placeholder="Password" name="password" required>
                <?php if ($loginError): ?>
                <span class="validate-tooltip">Incorrect login or password.</span>
                <?php endif; ?>
            </div>
            <button type="submit" value="login">Login</button>
        </form>
        <div class="clearfix supporter">
            <a class="forgot pull-right" href="forget-password.php">Forgot Password?</a>
        </div>
    </div>
    <div class="signup-wrapper text-center">
        <a href="register.php">Don't have an account? <span class="text-primary">Create One</span></a>
    </div>
</div>
<script src='https://code.jquery.com/jquery-2.2.4.min.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.0/jquery.validate.min.js'></script>
<script src="./script.js"></script>
</body>
</html>
