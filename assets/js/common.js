/* ============================================================
 * Common helpers for the static (no-backend) version.
 *
 * Everything lives in the browser, so a single module owns the
 * storage keys. Previously each page parsed localStorage on its
 * own, which meant: slots were only created on the home page
 * (direct links to booking.html crashed), and reading a missing
 * key threw on `null.filter(...)`.
 * ============================================================ */
var ParkingStore = (function () {
    'use strict';

    var KEYS = {
        users: 'users',
        currentUser: 'currentUser',
        slots: 'parkingSlots',
        bookings: 'bookings',
        messages: 'contactMessages'
    };

    var FLOORS = ['A', 'B', 'C', 'D'];
    var SLOTS_PER_FLOOR = 25;

    function read(store, key, fallback) {
        try {
            var raw = store.getItem(key);
            if (raw === null || raw === '') {
                return fallback;
            }
            var parsed = JSON.parse(raw);
            return parsed === null ? fallback : parsed;
        } catch (err) {
            return fallback;
        }
    }

    function write(store, key, value) {
        try {
            store.setItem(key, JSON.stringify(value));
        } catch (err) {
            /* private mode / quota exceeded - nothing else we can do here */
        }
    }

    function local(key, fallback) {
        return read(localStorage, key, fallback);
    }

    function save(key, value) {
        write(localStorage, key, value);
    }

    // ------------------------------------------------------------------
    // Slots
    // ------------------------------------------------------------------
    function seedSlots() {
        var existing = local(KEYS.slots, null);
        if (Array.isArray(existing) && existing.length) {
            return existing;
        }

        var slots = [];
        FLOORS.forEach(function (floor) {
            for (var i = 1; i <= SLOTS_PER_FLOOR; i++) {
                slots.push({ id: floor + i, floor: floor, status: 'available', bookedBy: null, bookedAt: null });
            }
        });
        save(KEYS.slots, slots);
        return slots;
    }

    function getSlots() {
        return seedSlots();
    }

    function saveSlots(slots) {
        save(KEYS.slots, slots);
    }

    function findSlot(slots, slotId) {
        for (var i = 0; i < slots.length; i++) {
            if (slots[i].id === slotId) {
                return slots[i];
            }
        }
        return null;
    }

    function availableSlots(slots, floor) {
        return slots.filter(function (s) {
            return s.status === 'available' && (!floor || s.floor === floor);
        });
    }

    /** Returns true when the slot was free and is now booked for this user. */
    function bookSlot(slotId, booking) {
        var slots = getSlots();
        var slot = findSlot(slots, slotId);

        if (!slot || slot.status !== 'available') {
            return false;
        }

        slot.status = 'booked';
        slot.bookedBy = booking.email || null;
        slot.bookedAt = booking.bookedAt || new Date().toISOString();
        saveSlots(slots);

        addBooking(booking);
        return true;
    }

    // ------------------------------------------------------------------
    // Bookings / messages
    // ------------------------------------------------------------------
    function getBookings() {
        var bookings = local(KEYS.bookings, []);
        return Array.isArray(bookings) ? bookings : [];
    }

    function addBooking(booking) {
        var bookings = getBookings();
        bookings.unshift(booking);
        save(KEYS.bookings, bookings);
    }

    function getMessages() {
        var messages = local(KEYS.messages, []);
        return Array.isArray(messages) ? messages : [];
    }

    function addMessage(message) {
        var messages = getMessages();
        messages.unshift(message);
        save(KEYS.messages, messages);
    }

    // ------------------------------------------------------------------
    // Users / session
    // ------------------------------------------------------------------
    function getUsers() {
        var users = local(KEYS.users, []);
        return Array.isArray(users) ? users : [];
    }

    function addUser(user) {
        var users = getUsers();
        users.push(user);
        save(KEYS.users, users);
        return users;
    }

    function findUserByEmail(email) {
        var wanted = String(email || '').trim().toLowerCase();
        return getUsers().filter(function (u) {
            return String(u.email || '').toLowerCase() === wanted;
        })[0] || null;
    }

    /**
     * "Remember me" keeps the user in localStorage; without it the login only
     * survives the current tab (sessionStorage).
     */
    function setCurrentUser(user, remember) {
        var payload = JSON.stringify(user);
        clearCurrentUser();
        if (remember) {
            localStorage.setItem(KEYS.currentUser, payload);
        } else {
            sessionStorage.setItem(KEYS.currentUser, payload);
        }
    }

    function getCurrentUser() {
        var user = read(localStorage, KEYS.currentUser, null) || read(sessionStorage, KEYS.currentUser, null);
        return user && user.email ? user : null;
    }

    function clearCurrentUser() {
        localStorage.removeItem(KEYS.currentUser);
        sessionStorage.removeItem(KEYS.currentUser);
    }

    return {
        KEYS: KEYS,
        FLOORS: FLOORS,
        SLOTS_PER_FLOOR: SLOTS_PER_FLOOR,
        seedSlots: seedSlots,
        getSlots: getSlots,
        saveSlots: saveSlots,
        findSlot: findSlot,
        availableSlots: availableSlots,
        bookSlot: bookSlot,
        getBookings: getBookings,
        addBooking: addBooking,
        getMessages: getMessages,
        addMessage: addMessage,
        getUsers: getUsers,
        addUser: addUser,
        findUserByEmail: findUserByEmail,
        setCurrentUser: setCurrentUser,
        getCurrentUser: getCurrentUser,
        clearCurrentUser: clearCurrentUser
    };
})();

// ---------------------------------------------------------------------------
// Small page-level helpers (kept as globals because every view calls them)
// ---------------------------------------------------------------------------
function getCurrentUser() {
    return ParkingStore.getCurrentUser();
}

function requireLogin() {
    if (!ParkingStore.getCurrentUser()) {
        window.location.href = 'index.html';
        return false;
    }
    return true;
}

function logoutHandler() {
    ParkingStore.clearCurrentUser();
    window.location.href = 'index.html';
}

/** ?slot=A5 style query params (used by the parking-grid -> booking handoff). */
function getQueryParam(name) {
    var params = new URLSearchParams(window.location.search);
    var value = params.get(name);
    return value ? decodeURIComponent(value) : '';
}

/**
 * Inline feedback instead of a wall of alert() popups.
 * Inserts (or reuses) a .alert box at the top of the given container.
 */
function showNotice(container, message, type) {
    if (typeof container === 'string') {
        container = document.getElementById(container);
    }
    if (!container) {
        return;
    }

    var node = container.querySelector('.alert');
    if (!node) {
        node = document.createElement('div');
        container.insertBefore(node, container.firstChild);
    }
    node.className = 'alert ' + (type === 'error' ? 'alert-error' : 'alert-success');
    node.textContent = message;

    if (!node.dataset.autohide || node.dataset.autohide === '1') {
        setTimeout(function () {
            if (node.parentNode) {
                node.classList.add('alert-hide');
            }
        }, 4000);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    var logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function (e) {
            e.preventDefault();
            logoutHandler();
        });
    }
});
