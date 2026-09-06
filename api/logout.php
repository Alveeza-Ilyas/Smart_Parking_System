<?php
/**
 * Signs the user out.
 *
 * Deliberately does NOT require config/config.php: the old version called
 * session_start() a second time (notice: "session already started") and only
 * destroyed the server-side data, leaving the cookie behind. Here the cookie is
 * expired too, and it works even when the database is unreachable.
 */

require_once __DIR__ . '/../config/session.php';

smart_parking_destroy_session();

header('Location: index.php');
exit;
