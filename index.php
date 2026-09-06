<?php
/**
 * Root entry point for the PHP + MySQL version.
 *
 * The README (and every tutorial that points at this repo) uses
 *   http://localhost/smart-parking-system/index.php
 * but the app pages live in api/ - this file closes that gap without
 * duplicating the login logic.
 */

$target = 'api/index.php';

// Preserve a query string (e.g. ?signedup=1) when forwarding.
$query = isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== ''
    ? '?' . $_SERVER['QUERY_STRING']
    : '';

if (!is_file(__DIR__ . '/api/index.php')) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<h1>Smart Parking System</h1>'
        . '<p>Could not find <code>api/index.php</code>. Make sure the whole project folder '
        . 'was copied into your web root (e.g. <code>C:\\xampp\\htdocs\\smart-parking-system</code>).</p>';
    exit;
}

header('Location: ' . $target . $query, true, 302);
exit;
