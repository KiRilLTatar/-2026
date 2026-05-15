<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!is_post()) {
    redirect('index.php');
}

verify_csrf();
logout_user();
add_flash('success', 'Вы вышли из аккаунта.');
redirect('index.php');
