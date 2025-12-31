document.addEventListener('DOMContentLoaded', () => {
    if (!requireLogin()) return;

    const user = getCurrentUser();
    const contactForm = document.getElementById('contactForm');
    if (!contactForm) return;

    // Prefill user info
    document.getElementById('name').value = user.fullname;
    document.getElementById('email').value = user.email;
    document.getElementById('phone').value = user.phone;

    contactForm.addEventListener('submit', e => {
        e.preventDefault();
        alert('Message sent successfully');
        contactForm.reset();
    });
});