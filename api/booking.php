<?php
require_once __DIR__ . '/config.php';
requireLogin();

$user = currentUser();
$error = '';
$slots = [];

ensureSlotsExist();

/** Extra columns added by the current database/Smart_parking.sql dump. */
$storesBookingDetail = tableHasColumn('bookings', 'total_price');

// Available slots for the dropdown, numbered naturally (A2 before A10).
$slots = db()->query(
    "SELECT slot_id, floor FROM parking_slots WHERE status = 'available' " . slotOrderSql()
)->fetchAll();

$selectedSlot     = getData('slot');   // ?slot=B7 deep link from the grid
$selectedDuration = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfVerify();

    $userId         = (int)$user['id'];
    $slotId         = postData('slot');
    $duration       = (int)postData('duration', '1');
    $vehicleNumber  = postData('vehicle_number');
    $phone          = postData('phone');

    $selectedSlot     = $slotId;
    $selectedDuration = $duration;

    if ($slotId === '') {
        $error = 'Please choose a slot';
    } elseif (!validPlate($vehicleNumber)) {
        $error = 'Vehicle number should be 2-20 letters/digits, e.g. ABC-1234';
    } elseif (!validPhone($phone)) {
        $error = 'Contact number should be 7-20 digits (spaces, +, - and brackets allowed)';
    } elseif ($duration < 1 || $duration > PARKING_MAX_HOURS) {
        $error = 'Duration must be between 1 and ' . PARKING_MAX_HOURS . ' hours';
    } else {
        // Lock the slot row so two people cannot grab it at the same time.
        db()->beginTransaction();
        try {
            $stmt = db()->prepare(
                "SELECT slot_id FROM parking_slots WHERE slot_id = ? AND status = 'available' FOR UPDATE"
            );
            $stmt->execute([$slotId]);
            $slot = $stmt->fetch();

            if (!$slot) {
                db()->rollBack();
                $error = 'Sorry, that slot was just booked by someone else. Please pick another one.';
            } else {
                $price = parkingPrice($duration);

                $update = db()->prepare(
                    "UPDATE parking_slots
                        SET status = 'booked', booked_by = ?, booked_at = NOW()
                      WHERE slot_id = ? AND status = 'available'"
                );
                $update->execute([$userId, $slotId]);

                if ($update->rowCount() !== 1) {
                    db()->rollBack();
                    $error = 'Sorry, that slot was just booked by someone else. Please pick another one.';
                } else {
                    if ($storesBookingDetail) {
                        $insert = db()->prepare(
                            'INSERT INTO bookings (user_id, slot_id, duration, vehicle_number, phone, total_price)
                             VALUES (?, ?, ?, ?, ?, ?)'
                        );
                        $insert->execute([$userId, $slotId, $duration, $vehicleNumber, $phone, $price]);
                    } else {
                        $insert = db()->prepare('INSERT INTO bookings (user_id, slot_id, duration) VALUES (?, ?, ?)');
                        $insert->execute([$userId, $slotId, $duration]);
                    }

                    db()->commit();

                    flash(
                        'Slot ' . $slotId . ' booked for ' . $duration . ' hour(s) - ' . money($price) . '. Happy parking!',
                        'success'
                    );
                    header('Location: parking_slots.php');
                    exit;
                }
            }
        } catch (PDOException $e) {
            if (db()->inTransaction()) {
                db()->rollBack();
            }
            // Keep the driver detail in the server log, never in the browser.
            error_log('Smart Parking booking failed: ' . $e->getMessage());
            $error = 'Booking could not be saved. Please try again - if it keeps failing, check the database setup.';
        }
    }

    // Re-read availability so the dropdown never offers a slot we just took.
    $slots = db()->query(
        "SELECT slot_id, floor FROM parking_slots WHERE status = 'available' " . slotOrderSql()
    )->fetchAll();
}

pageHead('Book Slot', 'booking');
?>

    <div class="booking-container">
        <div class="booking-wrapper">
            <div class="booking-form-section">
                <h1>Book Your Parking Slot</h1>
                <p class="subtitle">Select an available slot and book instantly</p>

                <?= flashOutput() ?>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="booking.php" id="bookingForm">
                    <?= csrfField() ?>

                    <div class="input-group">
                        <label for="slot">Select Slot</label>
                        <select id="slot" name="slot" required>
                            <option value="">Choose a slot</option>
                            <?php foreach ($slots as $slot): ?>
                                <option value="<?= e($slot['slot_id']) ?>"
                                    <?= $selectedSlot === $slot['slot_id'] ? 'selected' : '' ?>>
                                    <?= e($slot['slot_id']) ?> - <?= e(PARKING_FLOORS[$slot['floor']] ?? 'Floor ' . $slot['floor']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!$slots): ?>
                            <p class="form-note">Every slot is currently booked - check back in a moment.</p>
                        <?php endif; ?>
                    </div>

                    <div class="input-group">
                        <label for="vehicle_number">Vehicle Number</label>
                        <input type="text" id="vehicle_number" name="vehicle_number"
                               value="<?= e(postData('vehicle_number') ?: $user['vehicle']) ?>"
                               required maxlength="50" placeholder="Enter vehicle number">
                    </div>

                    <div class="input-group">
                        <label for="duration">Duration (hours)</label>
                        <input type="number" id="duration" name="duration"
                               min="1" max="<?= (int)PARKING_MAX_HOURS ?>"
                               value="<?= (int)max(1, min(PARKING_MAX_HOURS, $selectedDuration)) ?>" required>
                    </div>

                    <div class="input-group">
                        <label for="phone">Contact Number</label>
                        <input type="tel" id="phone" name="phone"
                               value="<?= e(postData('phone') ?: $user['phone']) ?>"
                               required maxlength="20" placeholder="Enter contact number">
                    </div>

                    <div class="price-display" data-price-calculator
                         data-first="<?= (float)PARKING_FIRST_HOUR_PRICE ?>"
                         data-extra="<?= (float)PARKING_EXTRA_HOUR_PRICE ?>"
                         data-daily-max="<?= (float)PARKING_DAILY_MAX_PRICE ?>"
                         data-currency="<?= e(PARKING_CURRENCY) ?>"
                         data-duration-input="duration">
                        <span>Total Price:</span>
                        <span class="price" data-price-output><?= e(money(parkingPrice($selectedDuration))) ?></span>
                    </div>

                    <button type="submit" class="btn-primary">Book Now</button>
                </form>
            </div>

            <div class="booking-info-section">
                <h2>Available Slots: <?= count($slots) ?></h2>

                <div class="pricing-info">
                    <h3>Pricing Information</h3>
                    <ul>
                        <li>First hour: <strong><?= e(money(PARKING_FIRST_HOUR_PRICE)) ?></strong></li>
                        <li>Additional hours: <strong><?= e(money(PARKING_EXTRA_HOUR_PRICE)) ?>/hr</strong></li>
                        <li>Daily max: <strong><?= e(money(PARKING_DAILY_MAX_PRICE)) ?></strong></li>
                    </ul>
                </div>

                <div class="booking-rules">
                    <h3>Booking Rules</h3>
                    <ul>
                        <li>&#10003; Maximum booking: <?= (int)PARKING_MAX_HOURS ?> hours</li>
                        <li>&#10003; Free cancellation within 1 hour</li>
                        <li>&#10003; Late arrival grace period: 30 min</li>
                        <li>&#10003; Valid vehicle documents required</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/price-calc.js"></script>

<?php
pageFooter();
