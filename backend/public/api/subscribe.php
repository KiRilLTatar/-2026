<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once APP_ROOT . '/includes/api.php';

api_require_method('POST');
api_verify_csrf();

$payload = api_input();
$email = trim((string) ($payload['email'] ?? ''));

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    api_error('Введите корректный e-mail.', 422, [
        'email' => 'Введите корректный e-mail.',
    ]);
}

save_newsletter_subscription($email);

api_success([
    'message' => 'Подписка сохранена. Теперь e-mail есть в базе.',
]);
