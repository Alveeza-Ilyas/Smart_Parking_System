<?php
/**
 * Shared page chrome for the PHP pages.
 *
 * The header, navbar and footer used to be copy-pasted into every file - and
 * one of those copies was missing its closing </ul> tag, which broke the whole
 * layout. Keeping them here means the bug cannot come back.
 */

if (!function_exists('pageHead')) {

    /**
     * <!DOCTYPE html> … <body> + navbar.
     *
     * @param string $title  Browser tab title suffix.
     * @param string $active One of: home, parking_slots, booking, contact, ''.
     */
    function pageHead(string $title, string $active = ''): void
    {
        $pages = [
            'home'          => ['home.php', 'Home'],
            'parking_slots' => ['parking_slots.php', 'Parking Slots'],
            'booking'       => ['booking.php', 'Book Slot'],
            'contact'       => ['contact.php', 'Contact'],
        ];
        $user = currentUser();

        echo '<!DOCTYPE html>' . "\n"
            . '<html lang="en">' . "\n"
            . '<head>' . "\n"
            . '    <meta charset="UTF-8">' . "\n"
            . '    <meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n"
            . '    <title>Smart Parking System - ' . e($title) . '</title>' . "\n"
            . '    <link rel="icon" type="image/svg+xml" href="../assets/img/favicon.svg">' . "\n"
            . '    <link rel="stylesheet" href="../assets/css/styles.css">' . "\n"
            . '</head>' . "\n"
            . '<body>' . "\n"
            . '    <nav class="navbar">' . "\n"
            . '        <div class="nav-container">' . "\n"
            . '            <div class="logo">' . "\n"
            . '                <h1><img src="../assets/img/parking-car.svg" width="40" alt="">Smart Parking</h1>' . "\n"
            . '            </div>' . "\n"
            . '            <ul class="nav-links">' . "\n";

        foreach ($pages as $key => [$href, $label]) {
            $class = $active === $key ? ' class="active"' : '';
            echo '                <li><a href="' . e($href) . '"' . $class . '>' . e($label) . '</a></li>' . "\n";
        }

        echo '                <li><span class="user-info">Hi, ' . e($user['fullname']) . '</span></li>' . "\n"
            . '                <li><a href="logout.php" class="btn-logout">Logout</a></li>' . "\n"
            . '            </ul>' . "\n"
            . '        </div>' . "\n"
            . '    </nav>' . "\n"
            . "\n";
    }

    /** Footer + closing tags. */
    function pageFooter(): void
    {
        echo "\n"
            . '    <footer class="footer">' . "\n"
            . '        <p>&copy; ' . date('Y') . ' Smart Parking System. All rights reserved.</p>' . "\n"
            . '    </footer>' . "\n"
            . '</body>' . "\n"
            . '</html>' . "\n";
    }
}
