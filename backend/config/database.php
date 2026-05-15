<?php
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        (string) config('db.host'),
        (int) config('db.port'),
        (string) config('db.name'),
        (string) config('db.charset')
    );

    try {
        $pdo = new PDO(
            $dsn,
            (string) config('db.user'),
            (string) config('db.pass'),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $exception) {
        http_response_code(500);
        exit('Не удалось подключиться к MySQL. Проверьте настройки в backend/config/config.php и импорт SQL-скрипта.');
    }

    return $pdo;
}
