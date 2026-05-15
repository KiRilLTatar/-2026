<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once APP_ROOT . '/includes/api.php';

api_require_method('POST');
api_verify_csrf();

$payload = api_input();
$email = trim((string) ($payload['email'] ?? ''));
$password = (string) ($payload['password'] ?? '');
$errors = [];

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Введите корректный e-mail.';
}

if ($password === '') {
    $errors['password'] = 'Введите пароль.';
}

$user = null;

if (!$errors) {
    $user = find_user_by_email($email);

    if ($user === null || !password_verify($password, (string) $user['password_hash'])) {
        $errors['credentials'] = 'Неверный e-mail или пароль.';
    }
}

if ($errors) {
    api_error('Не удалось войти в аккаунт.', 422, $errors);
}

login_user((int) $user['id']);

api_success([
    'message' => 'Вы успешно вошли в аккаунт.',
    'session' => api_session_resource(),
]);
