<?php
declare(strict_types=1);

$currentUser = current_user();
$favoriteCount = $currentUser ? count_user_favorites((int) $currentUser['id']) : 0;
$pageTitle = $pageTitle ?? 'Cyberteka';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e((string) config('app.name')) ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body>
<header class="site-header">
    <div class="container header-row">
        <a class="logo" href="<?= e(url('index.php')) ?>">YANKI</a>

        <nav class="main-nav">
            <a class="<?= is_active_page(['index.php']) ? 'active' : '' ?>" href="<?= e(url('index.php')) ?>">NEW</a>
            <a class="<?= is_active_page(['catalog.php', 'product.php', 'favorites.php']) ? 'active' : '' ?>" href="<?= e(url('catalog.php')) ?>">Каталог</a>
            <a class="<?= is_active_page(['delivery.php']) ? 'active' : '' ?>" href="<?= e(url('delivery.php')) ?>">Оплата</a>
            <a class="<?= is_active_page(['returns.php']) ? 'active' : '' ?>" href="<?= e(url('returns.php')) ?>">Возврат</a>
        </nav>

        <div class="header-actions">
            <div class="header-meta">RU • RUB</div>

            <?php if ($currentUser): ?>
                <a class="<?= is_active_page(['favorites.php']) ? 'active' : '' ?>" href="<?= e(url('favorites.php')) ?>">
                    Избранное (<?= $favoriteCount ?>)
                </a>
                <a class="<?= is_active_page(['profile.php']) ? 'active' : '' ?>" href="<?= e(url('profile.php')) ?>">
                    Профиль
                </a>
                <form method="post" action="<?= e(url('actions/logout.php')) ?>">
                    <?= csrf_field() ?>
                    <button class="link-button" type="submit">Выйти</button>
                </form>
            <?php else: ?>
                <a class="<?= is_active_page(['login.php']) ? 'active' : '' ?>" href="<?= e(url('login.php')) ?>">Войти</a>
                <a class="<?= is_active_page(['register.php']) ? 'active' : '' ?>" href="<?= e(url('register.php')) ?>">Регистрация</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php $flashMessages = flash_messages(); ?>
<?php if ($flashMessages): ?>
    <div class="container alerts">
        <?php foreach ($flashMessages as $message): ?>
            <div class="alert alert--<?= e($message['type']) ?>">
                <?= e($message['message']) ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
