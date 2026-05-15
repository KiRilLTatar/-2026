<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once APP_ROOT . '/includes/api.php';

api_require_method('GET');
api_success([
    'session' => api_session_resource(),
]);
