<?php
require_once __DIR__ . '/config.php';

// Already signed in? Straight to the dashboard.
if (isLoggedIn()) {
    header('Location: home.php');
    exit;
}

$error   = '';
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfVerify();

    $email    = postData('email');
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password';
    } elseif (!validEmail($email)) {
        $error = 'Please enter a valid email address';
    } else {
        $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([mb_strtolower($email)]);
        $account = $stmt->fetch();

        if ($account && password_verify($password, $account['password'])) {
            // New session id on privilege change (protects against session fixation).
            session_regenerate_id(true);

            $_SESSION['user_id']  = $account['id'];
            $_SESSION['fullname'] = $account['fullname'];
            $_SESSION['email']    = $account['email'];
            $_SESSION['phone']    = $account['phone'];
            $_SESSION['vehicle']  = $account['vehicle'];

            header('Location: home.php');
            exit;
        }

        $error = 'Invalid email or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Parking System - Login</title>
    <link rel="icon" type="image/svg+xml" href="../assets/img/favicon.svg">
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <div class="logo">
                <h1>Smart Parking</h1>
                <p>Park Smart, Live Easy</p>
            </div>

            <form method="POST" action="index.php" novalidate>
                <?= csrfField() ?>
                <h2>Welcome Back</h2>
                <p class="subtitle">Login to your account</p>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= e($error) ?></div>
                <?php endif; ?>

                <?= flashOutput() ?>

                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= e($email) ?>"
                           required placeholder="Enter your email" autocomplete="email">
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required
                           placeholder="Enter your password" autocomplete="current-password">
                </div>

                <div class="options">
                    <label class="remember">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="#" class="forgot" title="Password reset is not wired up in this demo">Forgot Password?</a>
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
