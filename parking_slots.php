<?php
require 'config.php';
requireLogin();

// Initialize slots if empty
$stmt = $pdo->query("SELECT COUNT(*) as count FROM parking_slots");
$count = $stmt->fetch()['count'];
if($count == 0){
    foreach(['A','B','C','D'] as $floor){
        for($i=1;$i<=25;$i++){
            $slot_id = $floor.$i;
            $stmt = $pdo->prepare("INSERT INTO parking_slots (slot_id,floor) VALUES (?,?)");
            $stmt->execute([$slot_id,$floor]);
        }
    }
}

// Fetch slots
$slots = $pdo->query("SELECT * FROM parking_slots ORDER BY floor, slot_id")->fetchAll();

// Group by floor
$floorA = array_filter($slots, fn($s) => $s['floor'] === 'A');
$floorB = array_filter($slots, fn($s) => $s['floor'] === 'B');
$floorC = array_filter($slots, fn($s) => $s['floor'] === 'C');
$floorD = array_filter($slots, fn($s) => $s['floor'] === 'D');

$availableCount = count(array_filter($slots, fn($s) => $s['status'] === 'available'));
$bookedCount = count(array_filter($slots, fn($s) => $s['status'] === 'booked'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Parking System - Parking Slots</title>
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
                <li><a href="parking_slots.php" class="active">Parking Slots</a></li>
                <li><a href="booking.php">Book Slot</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="slots-container">
        <div class="slots-header">
            <h1>Parking Slots Overview</h1>
            <div class="slots-summary">
                <div class="summary-item">
                    <span class="dot available-dot"></span>
                    <span>Available: <strong><?= $availableCount ?></strong></span>
                </div>
                <div class="summary-item">
                    <span class="dot booked-dot"></span>
                    <span>Booked: <strong><?= $bookedCount ?></strong></span>
                </div>
                <div class="summary-item">
                    <span class="dot total-dot"></span>
                    <span>Total: <strong><?= count($slots) ?></strong></span>
                </div>
            </div>
            <button class="btn-refresh" onclick="location.reload()">Refresh</button>
        </div>
        
        <div class="parking-lot">
            <div class="floor-section">
                <h2>Ground Floor (A)</h2>
                <div class="slots-grid">
                    <?php foreach($floorA as $slot): ?>
                        <div class="slot <?= $slot['status'] ?>" 
                             onclick="<?= $slot['status']==='available' ? "location.href='booking.php?slot={$slot['slot_id']}'" : '' ?>">
                            <?= $slot['slot_id'] ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="floor-section">
                <h2>First Floor (B)</h2>
                <div class="slots-grid">
                    <?php foreach($floorB as $slot): ?>
                        <div class="slot <?= $slot['status'] ?>" 
                             onclick="<?= $slot['status']==='available' ? "location.href='booking.php?slot={$slot['slot_id']}'" : '' ?>">
                            <?= $slot['slot_id'] ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="floor-section">
                <h2>Second Floor (C)</h2>
                <div class="slots-grid">
                    <?php foreach($floorC as $slot): ?>
                        <div class="slot <?= $slot['status'] ?>" 
                             onclick="<?= $slot['status']==='available' ? "location.href='booking.php?slot={$slot['slot_id']}'" : '' ?>">
                            <?= $slot['slot_id'] ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="floor-section">
                <h2>Third Floor (D)</h2>
                <div class="slots-grid">
                    <?php foreach($floorD as $slot): ?>
                        <div class="slot <?= $slot['status'] ?>" 
                             onclick="<?= $slot['status']==='available' ? "location.href='booking.php?slot={$slot['slot_id']}'" : '' ?>">
                            <?= $slot['slot_id'] ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="legend">
            <h3>Legend:</h3>
            <div class="legend-items">
                <div class="legend-item">
                    <div class="slot-demo available">A1</div>
                    <span>Available Slot</span>
                </div>
                <div class="legend-item">
                    <div class="slot-demo booked">A1</div>
                    <span>Booked Slot</span>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p>&copy; 2025 Smart Parking System. All rights reserved.</p>
    </footer>
</body>
</html>