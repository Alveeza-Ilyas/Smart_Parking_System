<?php
require 'config.php';
requireLogin();

// Fetch stats
$totalSlots = $pdo->query("SELECT COUNT(*) as total FROM parking_slots")->fetch()['total'];
$bookedSlots = $pdo->query("SELECT COUNT(*) as booked FROM parking_slots WHERE status='booked'")->fetch()['booked'];
$availableSlots = $totalSlots - $bookedSlots;

$user = $_SESSION;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Parking System - Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <h1><img src="parking-car.png" width="40">Smart Parking</h1>
            </div>
            <ul class="nav-links">
                <li><a href="home.php" class="active">Home</a></li>
                <li><a href="parking_slots.php">Parking Slots</a></li>
                <li><a href="booking.php">Book Slot</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
    </nav>

    <div class="home-container">
        <section class="hero">
            <div class="hero-content" >
                <h1>Welcome to Smart Parking System</h1>
                <p>Find and book your parking spot in seconds</p>
                <a href="booking.php" class="btn-primary">Book Now</a>
            </div>
        </section>

    <div class="stats">
        <div class="stat-card total">
            <h3><?= $totalSlots ?></h3>
            <p>Total Slots</p>
        </div>
        <div class="stat-card booked">
            <h3><?= $bookedSlots ?></h3>
            <p>Booked Slots</p>
        </div>
        <div class="stat-card available">
            <h3><?= $availableSlots ?></h3>
            <p>Available Slots</p>
        </div>

        <div class="stat-card">
                <h3>$5/hr</h3>
                <p>Starting Price</p>
        </div>
    </div>

    <section class="features">
            <h2>Why Choose Us?</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon"><img src="booking.png" width="60"></div>
                    <h3>Quick Booking</h3>
                    <p>Book your parking slot in just a few clicks</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon"><img src="secure-parking.png" width="60"></div>
                    <h3>Secure Parking</h3>
                    <p>24/7 CCTV surveillance and security</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon"><img src="changes.png" width="60"></div>
                    <h3>Real-time Updates</h3>
                    <p>Get instant updates on slot availability</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon"><img src="payment.png" width="60"></div>
                    <h3>Easy Payment</h3>
                    <p>Multiple payment options available</p>
                </div>
            </div>
        </section>

    <footer class="footer">
        <p>&copy; 2025 Smart Parking System. All rights reserved.</p>
    </footer>
</body>
</html>
