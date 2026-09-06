/* Parking slots grid (static / LocalStorage version). */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    if (!requireLogin()) {
        return;
    }

    var user = getCurrentUser() || {};
    var container = document.querySelector('.slots-container');
    var refreshBtn = document.querySelector('.btn-refresh');

    function slotNumber(id) {
        return parseInt(String(id).replace(/[^0-9]/g, ''), 10) || 0;
    }

    function markReleased(slotId, email) {
        var bookings = ParkingStore.getBookings();
        bookings.forEach(function (booking) {
            if (booking.slotId === slotId && booking.email === email && !booking.releasedAt) {
                booking.releasedAt = new Date().toISOString();
            }
        });
        localStorage.setItem(ParkingStore.KEYS.bookings, JSON.stringify(bookings));
    }

    function releaseOwnSlot(slot) {
        var confirmed = window.confirm(
            'Release ' + slot.id + '?\n\nThis frees the slot for other users (demo cancellation).'
        );
        if (!confirmed) {
            return;
        }

        // Re-read: the click handler holds a parsed copy, and saving that would
        // clobber changes made elsewhere (e.g. in another tab).
        var slots = ParkingStore.getSlots();
        var fresh = ParkingStore.findSlot(slots, slot.id);
        if (!fresh) {
            return;
        }

        fresh.status = 'available';
        fresh.bookedBy = null;
        fresh.bookedAt = null;
        ParkingStore.saveSlots(slots);

        markReleased(slot.id, user.email);

        loadSlots();
        showNotice(container, 'Slot ' + slot.id + ' is available again.', 'success');
    }

    function renderFloor(floor, containerId, slots) {
        var grid = document.getElementById(containerId);
        if (!grid) {
            return;
        }
        grid.innerHTML = '';

        slots.filter(function (s) {
            return s.floor === floor;
        }).sort(function (a, b) {
            return slotNumber(a.id) - slotNumber(b.id);
        }).forEach(function (slot) {
            var cell = document.createElement('div');
            var mine = slot.status === 'booked' && slot.bookedBy && slot.bookedBy === user.email;

            cell.className = 'slot ' + (slot.status === 'available' ? 'available' : 'booked') + (mine ? ' own' : '');
            cell.textContent = slot.id;

            if (slot.status === 'available') {
                cell.title = slot.id + ' - click to book';
                cell.setAttribute('role', 'button');
                cell.tabIndex = 0;
                cell.addEventListener('click', function () {
                    window.location.href = 'booking.html?slot=' + encodeURIComponent(slot.id);
                });
                cell.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        window.location.href = 'booking.html?slot=' + encodeURIComponent(slot.id);
                    }
                });
            } else {
                cell.title = slot.id + ' - booked' + (mine ? ' by you (click to release)' : '');
                if (mine) {
                    cell.style.cursor = 'pointer';
                    cell.addEventListener('click', function () {
                        releaseOwnSlot(slot);
                    });
                }
            }

            grid.appendChild(cell);
        });
    }

    function loadSlots() {
        var slots = ParkingStore.getSlots();

        ParkingStore.FLOORS.forEach(function (floor) {
            renderFloor(floor, 'floor' + floor, slots);
        });

        var available = slots.filter(function (s) {
            return s.status === 'available';
        }).length;

        setText('availableCount', available);
        setText('bookedCount', slots.length - available);
        setText('totalCount', slots.length);
    }

    function setText(id, value) {
        var node = document.getElementById(id);
        if (node) {
            node.textContent = value;
        }
    }

    loadSlots();

    // The markup used to call loadSlots() from an inline onclick, but the
    // function only exists inside this closure -> "loadSlots is not defined".
    if (refreshBtn) {
        refreshBtn.addEventListener('click', loadSlots);
    }

    // Confirmation banner after arriving from booking.html?booked=A2
    var justBooked = getQueryParam('booked');
    if (justBooked) {
        var booking = ParkingStore.getBookings().filter(function (b) {
            return b.slotId === justBooked && b.email === user.email && !b.releasedAt;
        })[0];

        showNotice(
            container,
            'Slot ' + justBooked + ' booked' +
            (booking ? ' for ' + booking.duration + ' hour(s) - ' + ParkingPrice.format(booking.price) : '') +
            '. Park safely!',
            'success'
        );

        // Drop the query string so a refresh does not repeat the message.
        if (window.history && window.history.replaceState) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }
});
