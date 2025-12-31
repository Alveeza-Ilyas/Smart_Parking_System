document.addEventListener('DOMContentLoaded', () => {
    if (!requireLogin()) return;

    const slots = JSON.parse(localStorage.getItem('parkingSlots')) || [];

    function renderFloor(floor, containerId) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';

        slots.filter(s => s.floor === floor).forEach(slot => {
            const div = document.createElement('div');
            div.className = 'slot ' + slot.status;
            div.innerText = slot.id;

            if (slot.status === 'available') {
                div.onclick = () => window.location.href = 'booking.html?slot=' + slot.id;
            }

            container.appendChild(div);
        });
    }

    function loadSlots() {
        renderFloor('A','floorA');
        renderFloor('B','floorB');
        renderFloor('C','floorC');
        renderFloor('D','floorD');

        const slots = JSON.parse(localStorage.getItem('parkingSlots'));
        document.getElementById('availableCount').innerText = slots.filter(s => s.status==='available').length;
        document.getElementById('bookedCount').innerText = slots.filter(s => s.status==='booked').length;
        document.getElementById('totalCount').innerText = slots.length;
    }

    loadSlots();

    const refreshBtn = document.querySelector('.btn-refresh');
    if (refreshBtn) refreshBtn.addEventListener('click', loadSlots);
});