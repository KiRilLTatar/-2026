<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('APP_ROOT', dirname(__DIR__));
define('PUBLIC_ROOT', APP_ROOT . '/public');

$config = require APP_ROOT . '/config/config.php';

date_default_timezone_set((string) ($config['app']['timezone'] ?? 'Europe/Moscow'));

if (session_status() === PHP_SESSION_NONE) {
    session_name((string) ($config['app']['session_name'] ?? 'cyberteka_session'));
    session_start();
}

$GLOBALS['flash_messages'] = $_SESSION['flash_messages'] ?? [];
$GLOBALS['old_input'] = $_SESSION['old_input'] ?? [];
$GLOBALS['form_errors'] = $_SESSION['form_errors'] ?? [];

unset($_SESSION['flash_messages'], $_SESSION['old_input'], $_SESSION['form_errors']);

require_once APP_ROOT . '/includes/functions.php';
require_once APP_ROOT . '/config/database.php';
require_once APP_ROOT . '/includes/repositories.php';

ensure_runtime_schema();
