<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';

require_auth();

if (!is_post()) {
    redirect('profile.php');
}

verify_csrf();

$name = trim((string) ($_POST['name'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$errors = [];

if ($name === '' || str_length($name) < 2) {
    $errors['name'] = 'Введите имя минимум из 2 символов.';
}

if ($phone !== '' && str_length($phone) > 30) {
    $errors['phone'] = 'Телефон не должен быть длиннее 30 символов.';
}

if ($errors) {
    remember_input([
        'name' => $name,
        'phone' => $phone,
    ]);
    set_form_errors($errors);
    redirect('profile.php');
}

update_user_profile((int) current_user()['id'], [
    'name' => $name,
    'phone' => $phone !== '' ? $phone : null,
]);

add_flash('success', 'Профиль обновлён.');
redirect('profile.php');
