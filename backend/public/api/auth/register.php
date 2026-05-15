<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once APP_ROOT . '/includes/api.php';

api_require_method('POST');
api_verify_csrf();

$payload = api_input();
$name = trim((string) ($payload['name'] ?? ''));
$email = trim((string) ($payload['email'] ?? ''));
$phone = trim((string) ($payload['phone'] ?? ''));
$password = (string) ($payload['password'] ?? '');
$passwordConfirmation = (string) ($payload['passwordConfirmation'] ?? '');
$errors = [];

if ($name === '' || str_length($name) < 2) {
    $errors['name'] = 'Введите имя минимум из 2 символов.';
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Введите корректный e-mail.';
} elseif (find_user_by_email($email) !== null) {
    $errors['email'] = 'Пользователь с таким e-mail уже зарегистрирован.';
}

if ($phone !== '' && str_length($phone) > 30) {
    $errors['phone'] = 'Телефон не должен быть длиннее 30 символов.';
}

if (str_length($password) < 6) {
    $errors['password'] = 'Пароль должен содержать минимум 6 символов.';
}

if ($passwordConfirmation === '') {
    $errors['passwordConfirmation'] = 'Повторите пароль.';
} elseif ($password !== $passwordConfirmation) {
    $errors['passwordConfirmation'] = 'Пароли не совпадают.';
}

if ($errors) {
    api_error('Не удалось создать аккаунт.', 422, $errors);
}

$userId = create_user([
    'name' => $name,
    'email' => $email,
    'phone' => $phone !== '' ? $phone : null,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
]);

login_user($userId);

api_success([
    'message' => 'Регистрация прошла успешно.',
    'session' => api_session_resource(),
], 201);
