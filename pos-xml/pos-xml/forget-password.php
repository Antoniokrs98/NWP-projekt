<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POS System - Forget Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel="stylesheet" href="auth.css">
</head>
<?php
$userFound = false;
$passwordChanged = false;
$usernameAttempted = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    $username = $_POST['username'];
    $usernameAttempted = true;

    $xml = simplexml_load_file('users.xml');
    foreach ($xml->user as $user) {
        if ($user->username == $username) {
            $userFound = true;
            break;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'])) {
    $username = $_POST['user'];
    $newPassword = $_POST['new_password'];

    $xml = simplexml_load_file('users.xml');
    foreach ($xml->user as $user) {
        if ($user->username == $username) {
            $user->password = md5($newPassword); 
            $xml->asXML('users.xml');
            $passwordChanged = true;
            break;
        }
    }
}
?>
<body>
    <div class="wrapper">
        <div class="logo text-center">
            <h1>POS System</h1>
        </div>
        <div class="inner-wrapper text-center">
            <h2 class="title">Reset your Password</h2>

            <?php if (!$userFound && !isset($_POST['new_password'])): ?>
            <form method="post">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Enter your username" required>
                    <?php if (!$userFound && $usernameAttempted && !isset($_POST['new_password'])): ?>
                    <span class="validate-tooltip">Username doesn't exist!</span>
                    <?php endif; ?>
                </div>
                <button type="submit" value="Check">Check Username</button>
            </form>
            <?php endif; ?>

            <?php if ($userFound && !isset($_POST['new_password'])): ?>
            <form method="post">
                <div class="input-group">
                    <input type="hidden" name="user" value="<?php echo htmlspecialchars($username); ?>">
                    <input type="password" name="new_password" placeholder="Enter new password" required>
                </div>
                <button type="submit" value="Reset Password">Reset Password</button>
            </form>
            <?php endif; ?>
            
            <?php if ($passwordChanged): ?>
            <span>Password changed successfully!</span>
            <a href="login.php">Login with new password</a>
            <?php endif; ?>
        </div>
        <div class="signup-wrapper text-center">
            <a href="login.php">Already have an account? <span class="text-primary">Login</span></a>
        </div>
    </div>
    <script src='https://code.jquery.com/jquery-2.2.4.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.15.0/jquery.validate.min.js'></script>
    <script src="./script.js"></script>
</body>
</html>
