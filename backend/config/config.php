<?php
declare(strict_types=1);

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => (int) (getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_NAME') ?: 'cyberteka',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
    'app' => [
        'name' => getenv('APP_NAME') ?: 'Cyberteka',
        'session_name' => getenv('APP_SESSION_NAME') ?: 'cyberteka_session',
        'timezone' => getenv('APP_TIMEZONE') ?: 'Europe/Moscow',
    ],
];
