<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!is_post()) {
    redirect('index.php');
}

verify_csrf();

$email = trim((string) ($_POST['email'] ?? ''));

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    remember_input(['email' => $email]);
    set_form_errors(['email' => 'Введите корректный e-mail.']);
    redirect('index.php');
}

save_newsletter_subscription($email);
add_flash('success', 'Подписка сохранена. Теперь e-mail есть в базе.');
redirect('index.php');
