document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;

    if (getCurrentUser()) {
        window.location.href = 'home.html';
        return;
    }

    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();

        const users = JSON.parse(localStorage.getItem('users')) || [];
        const user = users.find(u => u.email === email && u.password === password);

        if (user) {
            localStorage.setItem('currentUser', JSON.stringify(user));
            alert('Login successful!');
            window.location.href = 'home.html';
        } else {
            alert('Invalid email or password');
        }
    });
});

const forgotLink = document.querySelector('.forgot');
if (forgotLink) {
    forgotLink.addEventListener('click', function(e) {
        e.preventDefault();
        
        const email = prompt('Please enter your registered email:');
        if (!email) return;

        const users = JSON.parse(localStorage.getItem('users')) || [];
        const user = users.find(u => u.email === email);

        if (user) {
            alert(Your password is: ${user.password});
        } else {
            alert('Email not found. Please check and try again.');
        }
    });
}