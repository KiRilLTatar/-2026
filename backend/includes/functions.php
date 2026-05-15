<?php
declare(strict_types=1);

function config(string $key, $default = null)
{
    global $config;

    $value = $config ?? [];

    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }

        $value = $value[$segment];
    }

    return $value;
}

function app_base_url(): string
{
    static $baseUrl = null;

    if ($baseUrl !== null) {
        return $baseUrl;
    }

    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

    if ($scriptName === '') {
        $baseUrl = '';
        return $baseUrl;
    }

    if (preg_match('#/actions/[^/]+$#', $scriptName) === 1) {
        $scriptName = (string) preg_replace('#/actions/[^/]+$#', '', $scriptName);
    } else {
        $scriptName = (string) preg_replace('#/[^/]+\.php$#', '', $scriptName);
    }

    $baseUrl = rtrim($scriptName, '/');

    if ($baseUrl === '/') {
        $baseUrl = '';
    }

    return $baseUrl;
}

function url(string $path = ''): string
{
    $baseUrl = app_base_url();
    $path = ltrim($path, '/');

    if ($path === '') {
        return $baseUrl !== '' ? $baseUrl : '/';
    }

    if ($baseUrl === '') {
        return '/' . $path;
    }

    return $baseUrl . '/' . $path;
}

function asset(string $path = ''): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $path = ''): void
{
    header('Location: ' . url($path));
    exit;
}

function redirect_local(string $path, string $default = ''): void
{
    $fallback = $default !== '' ? $default : url();
    header('Location: ' . sanitize_local_path($path, $fallback));
    exit;
}

function sanitize_local_path(?string $path, string $default): string
{
    $path = trim((string) $path);

    if ($path === '') {
        return $default;
    }

    if (preg_match('#^https?://#i', $path) === 1) {
        return $default;
    }

    if (substr($path, 0, 1) !== '/') {
        return $default;
    }

    return $path;
}

function current_path_with_query(): string
{
    return (string) ($_SERVER['REQUEST_URI'] ?? url());
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function is_post(): bool
{
    return strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) === 'POST';
}

function str_length(string $value): int
{
    if (function_exists('mb_strlen')) {
        return mb_strlen($value);
    }

    return strlen($value);
}

function current_script(): string
{
    return basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''));
}

function is_active_page(array $scripts): bool
{
    return in_array(current_script(), $scripts, true);
}

function add_flash(string $type, string $message): void
{
    $_SESSION['flash_messages'][] = [
        'type' => $type,
        'message' => $message,
    ];
}

function flash_messages(): array
{
    return $GLOBALS['flash_messages'] ?? [];
}

function remember_input(array $input): void
{
    $_SESSION['old_input'] = $input;
}

function old(string $key, string $default = ''): string
{
    $oldInput = $GLOBALS['old_input'] ?? [];
    $value = $oldInput[$key] ?? $default;

    return is_scalar($value) ? (string) $value : $default;
}

function set_form_errors(array $errors): void
{
    $_SESSION['form_errors'] = $errors;
}

function error_for(string $key): ?string
{
    $errors = $GLOBALS['form_errors'] ?? [];

    return isset($errors[$key]) ? (string) $errors[$key] : null;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    verify_csrf_token((string) ($_POST['csrf_token'] ?? ''));
}

function verify_csrf_token(string $requestToken): void
{
    $sessionToken = (string) ($_SESSION['csrf_token'] ?? '');

    if ($sessionToken === '' || $requestToken === '' || !hash_equals($sessionToken, $requestToken)) {
        http_response_code(419);
        exit('CSRF-защита не прошла. Обновите страницу и попробуйте снова.');
    }
}

function login_user(int $userId): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
}

function logout_user(): void
{
    unset($_SESSION['user_id'], $_SESSION['intended_url']);
    session_regenerate_id(true);
}

function current_user(): ?array
{
    static $user = false;

    if ($user !== false) {
        return $user;
    }

    $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;

    if ($userId <= 0) {
        $user = null;
        return $user;
    }

    $user = get_user_by_id($userId);

    if ($user === null) {
        unset($_SESSION['user_id']);
    }

    return $user;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function require_auth(): void
{
    if (is_logged_in()) {
        return;
    }

    $_SESSION['intended_url'] = current_path_with_query();
    add_flash('error', 'Сначала войдите в аккаунт.');
    redirect('login.php');
}

function require_guest(): void
{
    if (!is_logged_in()) {
        return;
    }

    redirect('profile.php');
}

function pull_intended_url(string $default): string
{
    $path = sanitize_local_path($_SESSION['intended_url'] ?? '', $default);
    unset($_SESSION['intended_url']);

    return $path;
}

function format_price($price): string
{
    return number_format((float) $price, 0, ',', ' ') . ' руб.';
}

function format_date(string $datetime): string
{
    return date('d.m.Y H:i', strtotime($datetime));
}

function selected_if($left, $right): string
{
    return (string) $left === (string) $right ? ' selected' : '';
}

function checked_if(bool $condition): string
{
    return $condition ? ' checked' : '';
}

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
