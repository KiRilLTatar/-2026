<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once APP_ROOT . '/includes/api.php';

api_require_method('POST');
api_verify_csrf();
$user = api_require_auth_user();

$payload = api_input();
$name = trim((string) ($payload['name'] ?? ''));
$phone = trim((string) ($payload['phone'] ?? ''));
$errors = [];

if ($name === '' || str_length($name) < 2) {
    $errors['name'] = 'Введите имя минимум из 2 символов.';
}

if ($phone !== '' && str_length($phone) > 30) {
    $errors['phone'] = 'Телефон не должен быть длиннее 30 символов.';
}

if ($errors) {
    api_error('Не удалось обновить профиль.', 422, $errors);
}

update_user_profile((int) $user['id'], [
    'name' => $name,
    'phone' => $phone !== '' ? $phone : null,
]);

$freshUser = get_user_by_id((int) $user['id']);

api_success([
    'message' => 'Профиль обновлён.',
    'session' => api_session_resource($freshUser ?: $user),
]);
