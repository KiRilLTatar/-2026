<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once APP_ROOT . '/includes/api.php';

api_require_method('POST');
api_verify_csrf();
$user = api_require_auth_user();

clear_cart((int) $user['id']);

api_success([
    'message' => 'Корзина очищена.',
    'session' => api_session_resource(),
]);
