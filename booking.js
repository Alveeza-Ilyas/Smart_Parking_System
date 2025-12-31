document.addEventListener('DOMContentLoaded', () => {
    if (!requireLogin()) return;

    const user = getCurrentUser();
    const bookingForm = document.getElementById('bookingForm');
    if (!bookingForm) return;

    // Prefill user info
    document.getElementById('vehicle_number').value = user.vehicle;
    document.getElementById('phone').value = user.phone;

    // Populate floor dropdown
    const floorSelect = document.getElementById('floor');
    const slotSelect = document.getElementById('slot');
    const slots = JSON.parse(localStorage.getItem('parkingSlots')) || [];

    floorSelect.addEventListener('change', () => {
        const floor = floorSelect.value;
        slotSelect.innerHTML = '';
        if (!floor) {
            slotSelect.disabled = true;
            slotSelect.innerHTML = '<option value="">First select a floor</option>';
            return;
        }

        const availableSlots = slots.filter(s => s.floor === floor && s.status==='available');
        if (!availableSlots.length) {
            slotSelect.innerHTML = '<option value="">No slots available</option>';
        } else {
            slotSelect.innerHTML = '<option value="">Select a slot</option>';
            availableSlots.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.text = s.id;
                slotSelect.appendChild(opt);
            });
        }
        slotSelect.disabled = false;
    });

    // Booking form submission
    bookingForm.addEventListener('submit', e => {
        e.preventDefault();
        const slotId = slotSelect.value;
        if (!slotId) {
            alert('Please select a slot');
            return;
        }

        const slots = JSON.parse(localStorage.getItem('parkingSlots'));
        const slot = slots.find(s => s.id === slotId);
        if (!slot || slot.status==='booked') {
            alert('Slot unavailable');
            return;
        }

        slot.status = 'booked';
        slot.bookedBy = user.email;
        localStorage.setItem('parkingSlots', JSON.stringify(slots));

        alert('Booking successful!');
        window.location.href = 'parking_slots.html';
    });
});