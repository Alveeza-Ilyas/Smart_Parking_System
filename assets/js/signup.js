/* Sign-up page (static / LocalStorage version). */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    var signupForm = document.getElementById('signupForm');
    if (!signupForm) {
        return;
    }

    var box = signupForm.closest('.signup-box') || signupForm;

    function value(id) {
        var field = document.getElementById(id);
        return field ? field.value.trim() : '';
    }

    signupForm.addEventListener('submit', function (e) {
        e.preventDefault();

        var fullname = value('fullname');
        var email = value('email');
        var phone = value('phone');
        var vehicle = value('vehicle');
        var password = document.getElementById('password').value;
        var confirmPassword = document.getElementById('confirm_password').value;

        // Same rules as the PHP signup, so both versions accept the same input.
        if (!fullname || fullname.length > 100) {
            showNotice(box, 'Please enter your full name (max 100 characters).', 'error');
            return;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) {
            showNotice(box, 'Please enter a valid email address.', 'error');
            return;
        }
        if (!/^[0-9+\-\s()]{7,20}$/.test(phone)) {
            showNotice(box, 'Phone number should be 7-20 digits (spaces, +, - and brackets allowed).', 'error');
            return;
        }
        if (!/^[A-Z0-9][A-Z0-9\- ]{1,19}$/i.test(vehicle)) {
            showNotice(box, 'Vehicle number should be 2-20 letters/digits, e.g. ABC-1234.', 'error');
            return;
        }
        if (password.length < 8) {
            showNotice(box, 'Password must be at least 8 characters long.', 'error');
            return;
        }
        if (password !== confirmPassword) {
            showNotice(box, 'Passwords do not match.', 'error');
            return;
        }
        // The form uses novalidate (JS does the checking), so the terms box
        // has to be verified here too.
        var terms = signupForm.querySelector('input[name="terms"]');
        if (terms && !terms.checked) {
            showNotice(box, 'Please accept the Terms & Conditions first.', 'error');
            return;
        }
        if (ParkingStore.findUserByEmail(email)) {
            showNotice(box, 'That email is already registered - you can log in instead.', 'error');
            return;
        }

        ParkingStore.addUser({
            fullname: fullname,
            // Lower-cased so the (case-insensitive) login lookup stays predictable.
            email: email.toLowerCase(),
            phone: phone,
            vehicle: vehicle.toUpperCase(),
            password: password,
            createdAt: new Date().toISOString()
        });

        window.location.href = 'index.html?signedup=1';
    });
});
