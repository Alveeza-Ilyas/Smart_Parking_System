<?php
require_once __DIR__ . '/config.php';
requireLogin();

$user    = currentUser();
$error   = '';
$subject = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfVerify();

    $subject = postData('subject');
    $message = postData('message');

    $allowedSubjects = ['general', 'booking', 'payment', 'complaint', 'feedback'];

    if (!in_array($subject, $allowedSubjects, true)) {
        $error = 'Please choose a subject';
    } elseif (mb_strlen($message) < 5) {
        $error = 'Please write a message of at least 5 characters';
    } elseif (mb_strlen($message) > 2000) {
        $error = 'Message is too long (max 2000 characters)';
    } else {
        $stmt = db()->prepare('INSERT INTO contact_messages (user_id, subject, message) VALUES (?, ?, ?)');
        $stmt->execute([(int)$user['id'], $subject, $message]);

        flash('Message sent successfully - our team replies within 24 hours.', 'success');
        header('Location: contact.php');
        exit;
    }
}

pageHead('Contact', 'contact');
?>

    <div class="contact-container">
        <div class="contact-wrapper">
            <div class="contact-form-section">
                <h1>Get In Touch</h1>
                <p class="subtitle">We'd love to hear from you</p>

                <?= flashOutput() ?>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="contact.php">
                    <?= csrfField() ?>

                    <div class="input-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?= e($user['fullname']) ?>" readonly>
                    </div>

                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?= e($user['email']) ?>" readonly>
                    </div>

                    <div class="input-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?= e($user['phone']) ?>" readonly>
                        <p class="form-note">Contact details come from your account, so support can reach you.</p>
                    </div>

                    <div class="input-group">
                        <label for="subject">Subject</label>
                        <select id="subject" name="subject" required>
                            <option value="">Select a subject</option>
                            <option value="general" <?= $subject === 'general' ? 'selected' : '' ?>>General Inquiry</option>
                            <option value="booking" <?= $subject === 'booking' ? 'selected' : '' ?>>Booking Issue</option>
                            <option value="payment" <?= $subject === 'payment' ? 'selected' : '' ?>>Payment Problem</option>
                            <option value="complaint" <?= $subject === 'complaint' ? 'selected' : '' ?>>Complaint</option>
                            <option value="feedback" <?= $subject === 'feedback' ? 'selected' : '' ?>>Feedback</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" maxlength="2000" required
                                  placeholder="Write your message here..."><?= e($message) ?></textarea>
                    </div>

                    <button type="submit" class="btn-primary">Send Message</button>
                </form>
            </div>

            <div class="contact-info-section">
                <h2>Contact Information</h2>

                <div class="info-card">
                    <div class="info-icon">
                        <img src="../assets/img/location-pin.svg" width="50" alt="">
                    </div>
                    <h3>Address</h3>
                    <p>123 Parking Street<br>Downtown City, ST 12345</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <img src="../assets/img/telephone.svg" width="50" alt="">
                    </div>
                    <h3>Phone</h3>
                    <p>+1 (555) 123-4567<br>+1 (555) 987-6543</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <img src="../assets/img/email.svg" width="50" alt="">
                    </div>
                    <h3>Email</h3>
                    <p>info@smartparking.com<br>support@smartparking.com</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <img src="../assets/img/timetable.svg" width="50" alt="">
                    </div>
                    <h3>Business Hours</h3>
                    <p>Monday - Friday: 8AM - 8PM<br>Saturday - Sunday: 9AM - 6PM</p>
                </div>
            </div>
        </div>
    </div>

<?php
pageFooter();
