<?php
/**
 * Session bootstrap for the PHP version.
 *
 * config/config.php needs it for every page, and logout has to clear the very
 * same cookie - so it lives here instead of being copy-pasted twice. It is
 * deliberately free of any database dependency: signing out must work even
 * when MySQL is down.
 */

if (!defined('SMART_PARKING_SESSION_NAME')) {
    define('SMART_PARKING_SESSION_NAME', 'SMARTPARKINGSESSID');
}

if (!function_exists('smart_parking_start_session')) {
    function smart_parking_start_session(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'httponly' => true,
            'secure'   => $secure,
            'samesite' => 'Lax',
        ]);
        session_name(SMART_PARKING_SESSION_NAME);
        session_start();
    }

    /** Wipe session data and expire the browser cookie. */
    function smart_parking_destroy_session(): void
    {
        smart_parking_start_session();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
