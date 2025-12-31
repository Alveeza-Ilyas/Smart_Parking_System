document.addEventListener('DOMContentLoaded', () => {
    if (!requireLogin()) return;

    const user = getCurrentUser();
    const userNameEl = document.getElementById('userName');
    if (userNameEl) userNameEl.innerText = 'Welcome, ' + user.fullname;

    function initSlots() {
        if (!localStorage.getItem('parkingSlots')) {
            const slots = [];
            ['A','B','C','D'].forEach(f => {
                Array.from({ length: 25 }, (_, i) => {
                    slots.push({ id: f + (i+1), floor: f, status: 'available' });
                });
            });
            localStorage.setItem('parkingSlots', JSON.stringify(slots));
        }
    }

    function loadStats() {
        initSlots();
        const slots = JSON.parse(localStorage.getItem('parkingSlots'));
        const booked = slots.filter(s => s.status === 'booked').length;

        document.getElementById('totalSlots').innerText = slots.length;
        document.getElementById('bookedSlots').innerText = booked;
        document.getElementById('availableSlots').innerText = slots.length - booked;
    }

    loadStats();
    setInterval(loadStats, 5000);
});