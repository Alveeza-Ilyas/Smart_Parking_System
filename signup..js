document.addEventListener('DOMContentLoaded', () => {
    const signupForm = document.getElementById('signupForm');
    if (!signupForm) return;

    signupForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const fullname = document.getElementById('fullname').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const vehicle = document.getElementById('vehicle').value.trim();
        const password = document.getElementById('password').value.trim();
        const confirmPassword = document.getElementById('confirm_password').value.trim();

        if (password !== confirmPassword) {
            alert('Passwords do not match');
            return;
        }

        let users = JSON.parse(localStorage.getItem('users')) || [];
        if (users.find(u => u.email === email)) {
            alert('Email already exists');
            return;
        }

        users.push({ fullname, email, phone, vehicle, password });
        localStorage.setItem('users', JSON.stringify(users));

        alert('Signup successful! Please login.');
        window.location.href = 'index.html';
    });
});