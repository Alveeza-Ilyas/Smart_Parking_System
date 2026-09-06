/* Contact page (static / LocalStorage version). */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    if (!requireLogin()) {
        return;
    }

    var user = getCurrentUser() || {};
    var contactForm = document.getElementById('contactForm');
    if (!contactForm) {
        return;
    }

    var box = contactForm.closest('.contact-form-section') || contactForm;

    // Prefill from the profile without tripping over a half-filled account.
    [['name', user.fullname], ['email', user.email], ['phone', user.phone]].forEach(function (pair) {
        var field = document.getElementById(pair[0]);
        if (field && !field.value && pair[1]) {
            field.value = pair[1];
        }
    });

    contactForm.addEventListener('submit', function (e) {
        e.preventDefault();

        var messageField = document.getElementById('message');
        var subjectField = document.getElementById('subject');
        var message = messageField ? messageField.value.trim() : '';
        var subject = subjectField ? subjectField.value : '';

        if (!subject) {
            showNotice(box, 'Please choose a subject.', 'error');
            return;
        }
        if (message.length < 5) {
            showNotice(box, 'Please write a message of at least 5 characters.', 'error');
            return;
        }

        // The static version has no server, so tickets are kept locally and can
        // be inspected with: JSON.parse(localStorage.contactMessages)
        ParkingStore.addMessage({
            email: user.email || '',
            fullname: user.fullname || '',
            phone: document.getElementById('phone') ? document.getElementById('phone').value.trim() : '',
            subject: subject,
            message: message,
            sentAt: new Date().toISOString()
        });

        contactForm.reset();
        showNotice(box, 'Message sent - our team replies within 24 hours.', 'success');
    });
});
