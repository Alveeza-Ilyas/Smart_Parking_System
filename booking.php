<?php
require 'config.php';
requireLogin();

$user = $_SESSION;
$error = '';
$success = '';
$preselected_slot = $_GET['slot'] ?? '';

// Get available slots
$slots = $pdo->query("SELECT * FROM parking_slots WHERE status='available' ORDER BY floor, slot_id")->fetchAll();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $slot_id = $_POST['slot'];
    $duration = $_POST['duration'];
    $vehicle_number = $_POST['vehicle_number'];
    $phone = $_POST['phone'];

    // Check if slot is available
    $stmt = $pdo->prepare("SELECT * FROM parking_slots WHERE slot_id=? AND status='available'");
    $stmt->execute([$slot_id]);
    $slot = $stmt->fetch();

    if(!$slot){
        $error = "Slot unavailable";
    } else {
        // Update slot
        $stmt = $pdo->prepare("UPDATE parking_slots SET status='booked', booked_by=?, booked_at=NOW() WHERE slot_id=?");
        $stmt->execute([$user_id, $slot_id]);

        // Insert booking record
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, slot_id, duration) VALUES (?,?,?)");
        $stmt->execute([$user_id, $slot_id, $duration]);

        $success = "Booking successful!";
        header('Location: parking_slots.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Parking System - Book Slot</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <h1><img src="parking-car.png" width="40">Smart Parking</h1>
            </div>
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="parking_slots.php">Parking Slots</a></li>
                <li><a href="booking.php" class="active">Book Slot</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="booking-container">
        <div class="booking-wrapper">
            <div class="booking-form-section">
                <h1>Book Your Parking Slot</h1>
                <p class="subtitle">Select an available slot and book instantly</p>
                
                <?php if($error): ?>
                    <div style="background:#f44336;color:white;padding:10px;border-radius:5px;margin-bottom:15px;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="booking.php">
                    <div class="input-group">
                        <label for="slot">Select Slot</label>
                        <select name="slot" required>
                            <option value="">Choose a slot</option>
                            <?php foreach($slots as $slot): ?>
                                <option value="<?= $slot['slot_id'] ?>" <?= $preselected_slot === $slot['slot_id'] ? 'selected' : '' ?>>
                                    <?= $slot['slot_id'] ?> - Floor <?= $slot['floor'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="input-group">
                        <label for="vehicle_number">Vehicle Number</label>
                        <input type="text" name="vehicle_number" value="<?= htmlspecialchars($user['vehicle']) ?>" required placeholder="Enter vehicle number">
                    </div>
                    
                    <div class="input-group">
                        <label for="duration">Duration (hours)</label>
                        <input type="number" name="duration" min="1" max="24" value="1" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="phone">Contact Number</label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required placeholder="Enter contact number">
                    </div>
                    
                    <div class="price-display">
                        <span>Total Price:</span>
                        <span class="price">$5.00</span>
                    </div>
                    
                    <button type="submit" class="btn-primary">Book Now</button>
                </form>
            </div>
            
            <div class="booking-info-section">
                <h2>Available Slots: <?= count($slots) ?></h2>
                
                <div class="pricing-info">
                    <h3>Pricing Information</h3>
                    <ul>
                        <li>First hour: <strong>$5.00</strong></li>
                        <li>Additional hours: <strong>$3.00/hr</strong></li>
                        <li>Daily max: <strong>$50.00</strong></li>
                    </ul>
                </div>
                
                <div class="booking-rules">
                    <h3>Booking Rules</h3>
                    <ul>
                        <li>✓ Maximum booking: 24 hours</li>
                        <li>✓ Free cancellation within 1 hour</li>
                        <li>✓ Late arrival grace period: 30 min</li>
                        <li>✓ Valid vehicle documents required</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p>&copy; 2025 Smart Parking System. All rights reserved.</p>
    </footer>
</body>
</html>