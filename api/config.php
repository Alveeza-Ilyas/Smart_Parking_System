<?php
/**
 * Backwards-compatible shim.
 *
 * Every page in api/ used to `require 'config.php'` and each of them carried a
 * copy of the connection code. Configuration now lives once, in
 * /config/config.php - this file just forwards to it so both old and new
 * require statements keep working.
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/partials.php';
