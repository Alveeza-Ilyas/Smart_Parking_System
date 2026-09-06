/* Home page (static / LocalStorage version). */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    if (!requireLogin()) {
        return;
    }

    var user = getCurrentUser() || {};
    var userNameEl = document.getElementById('userName');
    if (userNameEl) {
        userNameEl.textContent = user.fullname ? 'Hi ' + user.fullname : 'Guest';
    }

    function setText(id, value) {
        var node = document.getElementById(id);
        if (node) {
            node.textContent = value;
        }
    }

    function loadStats() {
        var slots = ParkingStore.getSlots();
        var booked = slots.filter(function (s) {
            return s.status === 'booked';
        }).length;

        setText('totalSlots', slots.length);
        setText('bookedSlots', booked);
        setText('availableSlots', slots.length - booked);

        var occupancy = slots.length ? Math.round(booked / slots.length * 100) : 0;
        setText('occupancy', occupancy + '% occupied');

        var myList = document.getElementById('myBookings');
        if (myList) {
            var mine = ParkingStore.getBookings().filter(function (b) {
                return b.email === user.email && !b.releasedAt;
            });

            myList.innerHTML = '';
            if (!mine.length) {
                var empty = document.createElement('li');
                empty.className = 'info-text';
                empty.textContent = 'No bookings yet - grab a slot while one is free.';
                myList.appendChild(empty);
                return;
            }

            mine.slice(0, 5).forEach(function (booking) {
                var li = document.createElement('li');
                li.className = 'booking-row';
                li.innerHTML =
                    '<strong></strong><span></span><em></em>';
                li.children[0].textContent = booking.slotId;
                li.children[1].textContent = booking.duration + ' hour(s)';
                li.children[2].textContent = ParkingPrice.format(booking.price);
                myList.appendChild(li);
            });
        }
    }

    // Seed + render once, then keep the numbers honest (another tab may book).
    loadStats();
    setInterval(loadStats, 5000);
});
