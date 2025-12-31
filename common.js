// =======================
// COMMON UTILITIES
// =======================
function getCurrentUser() {
    return JSON.parse(localStorage.getItem('currentUser'));
}

function requireLogin() {
    if (!getCurrentUser()) {
        window.location.href = 'index.html';
        return false;
    }
    return true;
}

function logoutHandler() {
    localStorage.removeItem('currentUser');
    window.location.href = 'index.html';
}

document.addEventListener('DOMContentLoaded', () => {
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', e => {
            e.preventDefault();
            logoutHandler();
        });
    }
});