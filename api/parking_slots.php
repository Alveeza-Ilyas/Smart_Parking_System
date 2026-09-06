<?php
require_once __DIR__ . '/config.php';
requireLogin();

ensureSlotsExist();

$user = currentUser();

$slots = db()->query('SELECT * FROM parking_slots ' . slotOrderSql())->fetchAll();

// Group the flat result set by floor once - cheaper than four filtered queries.
$byFloor = ['A' => [], 'B' => [], 'C' => [], 'D' => []];
$availableCount = 0;
$bookedCount    = 0;
foreach ($slots as $slot) {
    $floor = $slot['floor'];
    if (!isset($byFloor[$floor])) {
        $byFloor[$floor] = [];
    }
    $byFloor[$floor][] = $slot;

    if ($slot['status'] === 'available') {
        $availableCount++;
    } else {
        $bookedCount++;
    }
}

pageHead('Parking Slots', 'parking_slots');
?>

    <div class="slots-container">
        <?= flashOutput() ?>

        <div class="slots-header">
            <h1>Parking Slots Overview</h1>
            <div class="slots-summary">
                <div class="summary-item">
                    <span class="dot available-dot"></span>
                    <span>Available: <strong id="availableCount"><?= (int)$availableCount ?></strong></span>
                </div>
                <div class="summary-item">
                    <span class="dot booked-dot"></span>
                    <span>Booked: <strong id="bookedCount"><?= (int)$bookedCount ?></strong></span>
                </div>
                <div class="summary-item">
                    <span class="dot total-dot"></span>
                    <span>Total: <strong id="totalCount"><?= count($slots) ?></strong></span>
                </div>
            </div>
            <button class="btn-refresh" type="button" onclick="location.reload()">Refresh</button>
        </div>

        <div class="parking-lot">
            <?php foreach (PARKING_FLOORS as $floorCode => $floorLabel): ?>
                <div class="floor-section">
                    <h2><?= e($floorLabel) ?></h2>
                    <div class="slots-grid">
                        <?php foreach ($byFloor[$floorCode] as $slot):
                            $isAvailable = $slot['status'] === 'available';
                            $isMine      = (int)($slot['booked_by'] ?? 0) === (int)$user['id'];
                            $title       = $isAvailable
                                ? $slot['slot_id'] . ' - click to book'
                                : $slot['slot_id'] . ' - booked'
                                  . ($isMine ? ' by you' : '')
                                  . (empty($slot['booked_at']) ? '' : ' at ' . $slot['booked_at']);
                        ?>
                            <div class="slot <?= $isAvailable ? 'available' : 'booked' ?><?= $isMine ? ' own' : '' ?>"
                                 title="<?= e($title) ?>"
                                <?= $isAvailable ? 'role="button" tabindex="0" data-slot="' . e($slot['slot_id']) . '"' : '' ?>>
                                <?= e($slot['slot_id']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
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
                <div class="legend-item">
                    <div class="slot-demo booked own">A1</div>
                    <span>Booked by you</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Available slots are links - clicking (or pressing Enter on) one opens
        // the booking form with that slot already selected.
        document.querySelectorAll('[data-slot]').forEach(function (el) {
            var open = function () {
                window.location.href = 'booking.php?slot=' + encodeURIComponent(el.dataset.slot);
            };
            el.addEventListener('click', open);
            el.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    open();
                }
            });
        });
    </script>

<?php
pageFooter();
