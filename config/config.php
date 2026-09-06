<?php
/**
 * Smart Parking System - central configuration + tiny helper layer.
 *
 * This is the ONLY file that holds database credentials.
 * api/config.php simply includes this file, so old require paths keep working.
 *
 * Credentials can be overridden with environment variables (useful when your
 * local MySQL/MariaDB root user has a password):
 *
 *   SMART_PARKING_DB_HOST, SMART_PARKING_DB_NAME,
 *   SMART_PARKING_DB_USER, SMART_PARKING_DB_PASS, SMART_PARKING_DEBUG
 */

// ---------------------------------------------------------------------------
// Pricing / layout rules (keep in sync with assets/js/price-calc.js)
// ---------------------------------------------------------------------------
if (!defined('PARKING_FIRST_HOUR_PRICE')) {
    define('PARKING_FIRST_HOUR_PRICE', 5.00);   // first hour
    define('PARKING_EXTRA_HOUR_PRICE', 3.00);   // every extra hour
    define('PARKING_DAILY_MAX_PRICE', 50.00);   // cap per started 24h block
    define('PARKING_MAX_HOURS', 24);
    define('PARKING_SLOTS_PER_FLOOR', 25);
    define('PARKING_FLOORS', ['A' => 'Ground Floor (A)', 'B' => 'First Floor (B)', 'C' => 'Second Floor (C)', 'D' => 'Third Floor (D)']);
    define('PARKING_CURRENCY', '$');
}

// ---------------------------------------------------------------------------
// Database connection (PDO)
// ---------------------------------------------------------------------------
// 'localhost' keeps the XAMPP default (unix socket); switch to '127.0.0.1'
// - or set SMART_PARKING_DB_HOST - if your MySQL only listens on TCP.
$dbHost = getenv('SMART_PARKING_DB_HOST') ?: 'localhost';
$dbName = getenv('SMART_PARKING_DB_NAME') ?: 'smart_parking';
$dbUser = getenv('SMART_PARKING_DB_USER') ?: 'root';
$dbPass = getenv('SMART_PARKING_DB_PASS') !== false ? getenv('SMART_PARKING_DB_PASS') : '';
$dbCharset = 'utf8mb4';

$dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$dbCharset}";

$pdoOptions = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $pdoOptions);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    $debug = getenv('SMART_PARKING_DEBUG') === '1';
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">'
        . '<title>Database connection failed</title></head><body style="font-family:system-ui,Segoe UI,Arial,sans-serif;margin:40px;line-height:1.6">'
        . '<h1 style="color:#c62828;margin-bottom:4px">Database connection failed</h1>'
        . '<p>Smart Parking could not talk to MySQL/MariaDB. Please check:</p><ol>'
        . '<li>Apache <strong>and</strong> MySQL are running in the XAMPP / WAMP control panel.</li>'
        . '<li>A database named <code>' . htmlspecialchars($dbName, ENT_QUOTES, 'UTF-8') . '</code> exists and '
        . '<code>database/smart_parking.sql</code> has been imported into it.</li>'
        . '<li>The credentials in <code>config/config.php</code> match your local MySQL setup '
        . '(the default XAMPP user is <code>root</code> with an empty password).</li>'
        . '</ol>';
    if ($debug) {
        echo '<p style="color:#555"><strong>Driver message:</strong> '
            . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
    }
    exit;
}

// ---------------------------------------------------------------------------
// Session (started once, with hardened cookie flags - see config/session.php)
// ---------------------------------------------------------------------------
require_once __DIR__ . '/session.php';
smart_parking_start_session();

// ===========================================================================
// Helpers
// ===========================================================================

/** Escape a value for safe HTML output. */
function e($value): string
{
    if ($value === null) {
        return '';
    }
    if (is_array($value) || is_object($value)) {
        $value = (string)json_encode($value);
    }
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** PDO handle for use inside helper functions. */
function db(): PDO
{
    global $pdo;
    return $pdo;
}

/** True when a user is logged in. */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/** The logged-in user's session values (name, email, phone, vehicle). */
function currentUser(): array
{
    return [
        'id'       => $_SESSION['user_id'] ?? null,
        'fullname' => $_SESSION['fullname'] ?? '',
        'email'    => $_SESSION['email'] ?? '',
        'phone'    => $_SESSION['phone'] ?? '',
        'vehicle'  => $_SESSION['vehicle'] ?? '',
    ];
}

/** Guard clause used by every authenticated page. */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

/** Trimmed, always-array POST accessor (no "undefined index" notices). */
function postData(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}

/** Trimmed GET accessor. */
function getData(string $key, string $default = ''): string
{
    $value = $_GET[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}

// ---------------------------------------------------------------------------
// CSRF protection
// ---------------------------------------------------------------------------
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

function csrfValid(): bool
{
    $sent = $_POST['csrf_token'] ?? '';
    return is_string($sent) && $sent !== '' && hash_equals(csrfToken(), $sent);
}

/** Call at the top of every POST handler. */
function csrfVerify(): void
{
    if (!csrfValid()) {
        http_response_code(400);
        exit('Your form session expired. Please go back, reload the page and try again.');
    }
}

// ---------------------------------------------------------------------------
// One-shot messages (survive a redirect, so POST requests can use PRG)
// ---------------------------------------------------------------------------
function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'][] = ['message' => $message, 'type' => $type];
}

/**
 * Render (and consume) pending flash messages.
 * Returns HTML, so pages can simply do: <?= flashOutput() ?>
 */
function flashOutput(): string
{
    if (empty($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        return '';
    }
    $html = '';
    foreach ($_SESSION['flash'] as $item) {
        $type  = (($item['type'] ?? 'success') === 'error') ? 'alert alert-error' : 'alert alert-success';
        $html .= '<div class="' . $type . '">' . e($item['message'] ?? '') . '</div>';
    }
    unset($_SESSION['flash']);
    return $html;
}

// ---------------------------------------------------------------------------
// Pricing
// ---------------------------------------------------------------------------

/**
 * First hour $5.00, every extra hour $3.00, capped per started 24h block
 * at $50.00. Mirrored 1:1 by ParkingPrice.calculate() in price-calc.js.
 */
function parkingPrice(int $hours): float
{
    $hours = max(1, min(PARKING_MAX_HOURS, $hours));
    $price = PARKING_FIRST_HOUR_PRICE + ($hours - 1) * PARKING_EXTRA_HOUR_PRICE;
    $days  = (int)ceil($hours / 24);
    $price = min($price, $days * PARKING_DAILY_MAX_PRICE);

    return round($price, 2);
}

function money(float $amount): string
{
    return PARKING_CURRENCY . number_format($amount, 2);
}

// ---------------------------------------------------------------------------
// Slots
// ---------------------------------------------------------------------------

/**
 * "A2" before "A10" - plain ORDER BY slot_id sorts alphabetically, which
 * scrambles the grid. Length-first ordering keeps the numbering readable and
 * works on MySQL and MariaDB alike.
 */
function slotOrderSql(string $alias = ''): string
{
    $p = $alias === '' ? '' : $alias . '.';

    return "ORDER BY {$p}floor ASC, LENGTH({$p}slot_id) ASC, {$p}slot_id ASC";
}

/** Make sure the 100 slots exist (fresh install / empty table). */
function ensureSlotsExist(): int
{
    $count = (int)db()->query('SELECT COUNT(*) FROM parking_slots')->fetchColumn();
    if ($count > 0) {
        return $count;
    }

    $insert = db()->prepare('INSERT INTO parking_slots (slot_id, floor, status) VALUES (?, ?, \'available\')');
    db()->beginTransaction();
    try {
        foreach (array_keys(PARKING_FLOORS) as $floor) {
            for ($i = 1; $i <= PARKING_SLOTS_PER_FLOOR; $i++) {
                $insert->execute([$floor . $i, $floor]);
            }
        }
        db()->commit();
    } catch (PDOException $e) {
        db()->rollBack();
        throw $e;
    }

    return (int)db()->query('SELECT COUNT(*) FROM parking_slots')->fetchColumn();
}

/** Does a column exist? Lets new code run on databases imported from older dumps. */
function tableHasColumn(string $table, string $column): bool
{
    static $cache = [];
    $key = $table . '|' . $column;
    if (!isset($cache[$key])) {
        $stmt = db()->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
        );
        $stmt->execute([$table, $column]);
        $cache[$key] = (int)$stmt->fetchColumn() > 0;
    }
    return $cache[$key];
}

// ---------------------------------------------------------------------------
// Input validation shared by signup / contact / booking
// ---------------------------------------------------------------------------
function validEmail(string $email): bool
{
    return strlen($email) <= 100 && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validPhone(string $phone): bool
{
    return (bool)preg_match('/^[0-9+\-\s()]{7,20}$/', $phone);
}

function validPlate(string $plate): bool
{
    return (bool)preg_match('/^[A-Z0-9][A-Z0-9\- ]{1,19}$/i', $plate);
}
