<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once APP_ROOT . '/includes/api.php';

api_require_method('POST');
api_verify_csrf();

logout_user();

api_success([
    'message' => 'Вы вышли из аккаунта.',
    'session' => api_session_resource(),
]);
