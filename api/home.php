<?php
require_once __DIR__ . '/config.php';
requireLogin();

ensureSlotsExist();

$user = currentUser();

// Single query for all three counters - no need for three round trips.
$stats = db()->query(
    "SELECT COUNT(*) AS total,
            SUM(status = 'booked') AS booked,
            SUM(status = 'available') AS available
       FROM parking_slots"
)->fetch();

$totalSlots     = (int)($stats['total'] ?? 0);
$bookedSlots    = (int)($stats['booked'] ?? 0);
$availableSlots = (int)($stats['available'] ?? 0);

$occupancy = $totalSlots > 0 ? (int)round($bookedSlots / $totalSlots * 100) : 0;

pageHead('Home', 'home');
?>

    <div class="home-container">
        <?= flashOutput() ?>

        <section class="hero">
            <div class="hero-content">
                <h1>Welcome to Smart Parking System</h1>
                <p>Hi <?= e($user['fullname']) ?> - find and book your parking spot in seconds</p>
                <a href="booking.php" class="btn-primary">Book Now</a>
            </div>
        </section>

        <section class="stats">
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
            <div class="stat-card price">
                <h3><?= e(money(PARKING_FIRST_HOUR_PRICE)) ?>/hr</h3>
                <p>Starting Price (<?= $occupancy ?>% occupied)</p>
            </div>
        </section>

        <section class="features">
            <h2>Why Choose Us?</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="../assets/img/booking.svg" width="60" alt="">
                    </div>
                    <h3>Quick Booking</h3>
                    <p>Book your parking slot in just a few clicks</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="../assets/img/secure-parking.svg" width="60" alt="">
                    </div>
                    <h3>Secure Parking</h3>
                    <p>24/7 CCTV surveillance and security</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="../assets/img/realtime.svg" width="60" alt="">
                    </div>
                    <h3>Real-time Updates</h3>
                    <p>Get instant updates on slot availability</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="../assets/img/payment.svg" width="60" alt="">
                    </div>
                    <h3>Easy Payment</h3>
                    <p>Multiple payment options available</p>
                </div>
            </div>
        </section>
    </div>

<?php
pageFooter();
