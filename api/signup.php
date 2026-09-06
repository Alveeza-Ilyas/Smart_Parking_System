<?php
require_once __DIR__ . '/config.php';

if (isLoggedIn()) {
    header('Location: home.php');
    exit;
}

$error = '';
$old   = ['fullname' => '', 'email' => '', 'phone' => '', 'vehicle' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfVerify();

    $old['fullname'] = postData('fullname');
    $old['email']    = postData('email');
    $old['phone']    = postData('phone');
    $old['vehicle']  = postData('vehicle');
    $password        = (string)($_POST['password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    if ($old['fullname'] === '' || mb_strlen($old['fullname']) > 100) {
        $error = 'Please enter your full name (max 100 characters)';
    } elseif (!validEmail($old['email'])) {
        $error = 'Please enter a valid email address (max 100 characters)';
    } elseif (!validPhone($old['phone'])) {
        $error = 'Phone number should be 7-20 digits (spaces, +, - and brackets allowed)';
    } elseif (!validPlate($old['vehicle'])) {
        $error = 'Vehicle number should be 2-20 letters/digits, e.g. ABC-1234';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        $stmt = db()->prepare('SELECT 1 FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([mb_strtolower($old['email'])]);

        if ($stmt->fetch()) {
            $error = 'That email is already registered - you can log in instead';
        } else {
            $stmt = db()->prepare(
                'INSERT INTO users (fullname, email, phone, vehicle, password) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $old['fullname'],
                mb_strtolower($old['email']),
                $old['phone'],
                strtoupper($old['vehicle']),
                password_hash($password, PASSWORD_DEFAULT),
            ]);

            // Post/Redirect/Get: a successful signup never renders markup, it
            // redirects so refreshing the page cannot insert the user twice.
            flash('Signup successful! Please log in.', 'success');
            header('Location: index.php');
            exit;
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
    <link rel="icon" type="image/svg+xml" href="../assets/img/favicon.svg">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <div class="signup-box">
            <div class="logo">
                <h1>Smart Parking</h1>
                <p>Join us today</p>
            </div>

            <form method="POST" action="signup.php" novalidate>
                <?= csrfField() ?>
                <h2>Create Account</h2>
                <p class="subtitle">Sign up to get started</p>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= e($error) ?></div>
                <?php endif; ?>

                <div class="input-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" value="<?= e($old['fullname']) ?>"
                           required maxlength="100" placeholder="Enter your full name" autocomplete="name">
                </div>

                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= e($old['email']) ?>"
                           required maxlength="100" placeholder="Enter your email" autocomplete="email">
                </div>

                <div class="input-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?= e($old['phone']) ?>"
                           required maxlength="20" placeholder="Enter your phone number" autocomplete="tel">
                </div>

                <div class="input-group">
                    <label for="vehicle">Vehicle Number</label>
                    <input type="text" id="vehicle" name="vehicle" value="<?= e($old['vehicle']) ?>"
                           required maxlength="50" placeholder="Enter your vehicle number">
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required minlength="8"
                           placeholder="Create a password (min 8 characters)" autocomplete="new-password">
                </div>

                <div class="input-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8"
                           placeholder="Confirm your password" autocomplete="new-password">
                </div>

                <div class="options">
                    <label class="remember">
                        <input type="checkbox" name="terms" required> I agree to Terms &amp; Conditions
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
