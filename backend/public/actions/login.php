<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';

require_guest();

if (!is_post()) {
    redirect('login.php');
}

verify_csrf();

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
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
    remember_input(['email' => $email]);
    set_form_errors($errors);
    redirect('login.php');
}

login_user((int) $user['id']);
add_flash('success', 'Вы успешно вошли в аккаунт.');
redirect_local(pull_intended_url(url('profile.php')), url('profile.php'));
