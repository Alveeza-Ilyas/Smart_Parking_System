<?php
require 'config.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $vehicle = $_POST['vehicle'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);
        if($stmt->rowCount() > 0) {
            $error = "Email already exists";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (fullname,email,phone,vehicle,password) VALUES (?,?,?,?,?)");
            $stmt->execute([$fullname,$email,$phone,$vehicle,$hashed_password]);
            
            $success = "Signup successful! Please login.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Parking System - Sign Up</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="signup-box">
            <div class="logo">
                <h1>Smart Parking</h1>
                <p>Join us today</p>
            </div>
            
            <form method="POST" action="signup.php">
                <h2>Create Account</h2>
                <p class="subtitle">Sign up to get started</p>
                
                <?php if($error): ?>
                    <div style="background:#f44336;color:white;padding:10px;border-radius:5px;margin-bottom:15px;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div style="background:#4caf50;color:white;padding:10px;border-radius:5px;margin-bottom:15px;">
                        <?= htmlspecialchars($success) ?> <a href="index.php" style="color:white;text-decoration:underline;">Click here to login</a>
                    </div>
                <?php endif; ?>
                
                <div class="input-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" name="fullname" required placeholder="Enter your full name">
                </div>
                
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" required placeholder="Enter your email">
                </div>
                
                <div class="input-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" required placeholder="Enter your phone number">
                </div>
                
                <div class="input-group">
                    <label for="vehicle">Vehicle Number</label>
                    <input type="text" name="vehicle" required placeholder="Enter your vehicle number">
                </div>
                
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" required placeholder="Create a password">
                </div>
                
                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" required placeholder="Confirm your password">
                </div>
                
                <div class="options">
                    <label class="remember">
                        <input type="checkbox" name="terms" required> I agree to Terms & Conditions
                    </label>
                </div>
                
                <button type="submit" class="btn-primary">Sign Up</button>
                
                <p class="signup-link">
                    Already have an account? <a href="index.php">Login</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>