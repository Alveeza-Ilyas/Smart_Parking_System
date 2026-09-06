/* Login page (static / LocalStorage version). */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    var loginForm = document.getElementById('loginForm');
    if (!loginForm) {
        return;
    }

    // Already signed in? Skip the form.
    if (getCurrentUser()) {
        window.location.href = 'home.html';
        return;
    }

    var box = loginForm.closest('.login-box') || loginForm;

    if (getQueryParam('signedup') === '1') {
        showNotice(box, 'Sign-up complete - you can log in now.', 'success');
    }

    loginForm.addEventListener('submit', function (e) {
        e.preventDefault();

        var email = document.getElementById('email').value.trim();
        // Passwords are compared exactly as typed: trimming would silently
        // reject valid passwords that end with a space.
        var password = document.getElementById('password').value;
        var rememberField = document.querySelector('input[name="remember"]');
        var remember = rememberField ? rememberField.checked : false;

        if (!email || !password) {
            showNotice(box, 'Please enter both email and password.', 'error');
            return;
        }

        var user = ParkingStore.findUserByEmail(email);

        if (!user || user.password !== password) {
            showNotice(box, 'Invalid email or password.', 'error');
            return;
        }

        ParkingStore.setCurrentUser(user, remember);
        window.location.href = 'home.html';
    });

    // "Forgot password?" never reveals a stored password - it only confirms
    // whether the address is registered and points at the demo reset flow.
    var forgotLink = document.querySelector('.forgot');
    if (forgotLink) {
        forgotLink.addEventListener('click', function (e) {
            e.preventDefault();

            var email = window.prompt('Please enter your registered email:');
            if (!email) {
                return;
            }

            var found = ParkingStore.findUserByEmail(email);
            if (found) {
                showNotice(
                    box,
                    'A reset link for ' + found.email + ' would be emailed to you. ' +
                    'This offline demo has no mail server, so re-enter your password instead.'
                );
            } else {
                showNotice(box, 'Email not found. Please check and try again.', 'error');
            }
        });
    }
});
