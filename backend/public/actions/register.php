<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';

require_guest();

if (!is_post()) {
    redirect('register.php');
}

verify_csrf();

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$passwordConfirmation = (string) ($_POST['password_confirmation'] ?? '');

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
    $errors['password_confirmation'] = 'Повторите пароль.';
} elseif ($password !== $passwordConfirmation) {
    $errors['password_confirmation'] = 'Пароли не совпадают.';
}

if ($errors) {
    remember_input([
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
    ]);
    set_form_errors($errors);
    redirect('register.php');
}

$userId = create_user([
    'name' => $name,
    'email' => $email,
    'phone' => $phone !== '' ? $phone : null,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
]);

login_user($userId);
add_flash('success', 'Регистрация прошла успешно. Добро пожаловать!');
redirect('profile.php');
