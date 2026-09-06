/* Booking page (static / LocalStorage version). */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    if (!requireLogin()) {
        return;
    }

    var bookingForm = document.getElementById('bookingForm');
    if (!bookingForm) {
        return;
    }

    var user = getCurrentUser() || {};
    var box = bookingForm.closest('.booking-form-section') || bookingForm;

    var floorSelect = document.getElementById('floor');
    var slotSelect = document.getElementById('slot');
    var durationInput = document.getElementById('duration');
    var vehicleInput = document.getElementById('vehicle_number');
    var phoneInput = document.getElementById('phone');
    var badges = document.getElementById('availableSlotsDisplay');

    // Prefill from the profile, but never crash on a partially filled account.
    if (vehicleInput && !vehicleInput.value) {
        vehicleInput.value = user.vehicle || '';
    }
    if (phoneInput && !phoneInput.value) {
        phoneInput.value = user.phone || '';
    }

    var slots = ParkingStore.getSlots();

    function slotNumber(id) {
        return parseInt(String(id).replace(/[^0-9]/g, ''), 10) || 0;
    }

    function renderBadges(floor) {
        if (!badges) {
            return;
        }
        badges.innerHTML = '';

        var free = ParkingStore.availableSlots(slots, floor).sort(function (a, b) {
            return slotNumber(a.id) - slotNumber(b.id);
        });

        if (!free.length) {
            var empty = document.createElement('p');
            empty.className = 'info-text';
            empty.textContent = 'No free slots on this floor right now.';
            badges.appendChild(empty);
            return;
        }

        var wrap = document.createElement('div');
        wrap.className = 'slots-badges';

        free.forEach(function (slot) {
            var badge = document.createElement('button');
            badge.type = 'button';
            badge.className = 'slot-badge';
            badge.textContent = slot.id;
            badge.dataset.slot = slot.id;
            badge.addEventListener('click', function () {
                slotSelect.value = slot.id;
                Array.prototype.forEach.call(wrap.children, function (child) {
                    child.classList.toggle('selected', child === badge);
                });
            });
            if (slotSelect.value === slot.id) {
                badge.classList.add('selected');
            }
            wrap.appendChild(badge);
        });

        badges.appendChild(wrap);
    }

    function fillSlots(floor) {
        slotSelect.innerHTML = '';

        if (!floor) {
            slotSelect.disabled = true;
            slotSelect.innerHTML = '<option value="">First select a floor</option>';
            renderBadges(null);
            return;
        }

        var free = ParkingStore.availableSlots(slots, floor).sort(function (a, b) {
            return slotNumber(a.id) - slotNumber(b.id);
        });

        if (!free.length) {
            slotSelect.disabled = true;
            slotSelect.innerHTML = '<option value="">No slots available</option>';
            renderBadges(floor);
            return;
        }

        var placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Select a slot';
        slotSelect.appendChild(placeholder);

        free.forEach(function (slot) {
            var option = document.createElement('option');
            option.value = slot.id;
            option.textContent = slot.id + ' - ' + floorLabel(slot.floor);
            slotSelect.appendChild(option);
        });

        slotSelect.disabled = false;
        renderBadges(floor);
    }

    function floorLabel(floor) {
        return (ParkingStore.FLOORS.indexOf(floor) >= 0 ? 'Floor ' + floor : floor);
    }

    floorSelect.addEventListener('change', function () {
        fillSlots(floorSelect.value);
    });

    /** deep-link support: parking_slots.html?slot=B7 -> preselect floor B, slot B7 */
    var wantedSlot = getQueryParam('slot');
    if (wantedSlot) {
        var match = ParkingStore.findSlot(slots, wantedSlot);
        if (match && match.status === 'available') {
            floorSelect.value = match.floor;
            fillSlots(match.floor);
            slotSelect.value = match.id;
            renderBadges(match.floor);
        } else {
            showNotice(box, match ? 'Slot ' + wantedSlot + ' is already booked - pick another one.' : 'Unknown slot: ' + wantedSlot, 'error');
        }
    } else if (floorSelect.value) {
        fillSlots(floorSelect.value);
    }

    if (durationInput) {
        durationInput.addEventListener('change', function () {
            // Keep the shared price widget (price-calc.js) in sync.
            durationInput.dispatchEvent(new Event('input', { bubbles: true }));
        });
    }

    bookingForm.addEventListener('submit', function (e) {
        e.preventDefault();

        var slotId = slotSelect.value;
        if (!slotId) {
            showNotice(box, 'Please select a slot.', 'error');
            return;
        }

        var vehicleNumber = (vehicleInput ? vehicleInput.value.trim() : '');
        var phone = (phoneInput ? phoneInput.value.trim() : '');
        var duration = durationInput ? parseInt(durationInput.value, 10) : 1;

        if (!/^[A-Z0-9][A-Z0-9\- ]{1,19}$/i.test(vehicleNumber)) {
            showNotice(box, 'Vehicle number should be 2-20 letters/digits, e.g. ABC-1234.', 'error');
            return;
        }
        if (!/^[0-9+\-\s()]{7,20}$/.test(phone)) {
            showNotice(box, 'Contact number should be 7-20 digits.', 'error');
            return;
        }
        if (isNaN(duration) || duration < 1 || duration > 24) {
            showNotice(box, 'Duration must be between 1 and 24 hours.', 'error');
            return;
        }

        // Reload the slot list: another tab may have booked it in the meantime.
        slots = ParkingStore.getSlots();

        var booked = ParkingStore.bookSlot(slotId, {
            slotId: slotId,
            floor: slotId.charAt(0),
            email: user.email || '',
            fullname: user.fullname || '',
            vehicleNumber: vehicleNumber.toUpperCase(),
            phone: phone,
            duration: duration,
            price: ParkingPrice.calculate(duration),
            bookedAt: new Date().toISOString()
        });

        if (!booked) {
            showNotice(box, 'Sorry, that slot was just taken. Please pick another one.', 'error');
            fillSlots(floorSelect.value);
            return;
        }

        window.location.href = 'parking_slots.html?booked=' + encodeURIComponent(slotId);
    });
});
