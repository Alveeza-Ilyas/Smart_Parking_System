<?php
require 'config.php';

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if($user && password_verify($password, $user['password'])){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['phone'] = $user['phone'];
        $_SESSION['vehicle'] = $user['vehicle'];
        header('Location: home.php');
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Parking System - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <div class="logo">
                <h1>Smart Parking</h1>
                <p>Park Smart, Live Easy</p>
            </div>
            
            <form method="POST" action="index.php">
                <h2>Welcome Back</h2>
                <p class="subtitle">Login to your account</p>
                
                <?php if($error): ?>
                    <div style="background:#f44336;color:white;padding:10px;border-radius:5px;margin-bottom:15px;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" required placeholder="Enter your email">
                </div>
                
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" required placeholder="Enter your password">
                </div>
                
                <div class="options">
                    <label class="remember">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="#" class="forgot">Forgot Password?</a>
                </div>
                
                <button type="submit" class="btn-primary">Login</button>
                
                <p class="signup-link">
                    Don't have an account? <a href="signup.php">Sign Up</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>