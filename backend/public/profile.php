<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_auth();

$pageTitle = 'Профиль';
$user = current_user();

require_once __DIR__ . '/../includes/header.php';
?>

<main class="section">
    <div class="container profile-layout">
        <section class="panel">
            <div class="panel__body">
                <h1 class="panel-title">Профиль пользователя</h1>
                <p class="muted-text">
                    Закрытая страница доступна только после входа. Здесь хранится текущий пользователь и обновляются
                    поля, которые действительно нужны минимальной версии сайта.
                </p>

                <form action="<?= e(url('actions/profile.php')) ?>" method="post" style="margin-top: 20px;">
                    <?= csrf_field() ?>

                    <div class="field">
                        <label for="name">Имя</label>
                        <input id="name" type="text" name="name" value="<?= e(old('name', $user['name'])) ?>" required>
                        <?php if ($message = error_for('name')): ?>
                            <div class="field-error"><?= e($message) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="field" style="margin-top: 16px;">
                        <label for="phone">Телефон</label>
                        <input id="phone" type="text" name="phone" value="<?= e(old('phone', (string) $user['phone'])) ?>">
                        <?php if ($message = error_for('phone')): ?>
                            <div class="field-error"><?= e($message) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="field" style="margin-top: 16px;">
                        <label for="email">E-mail</label>
                        <input id="email" type="email" value="<?= e($user['email']) ?>" disabled>
                    </div>

                    <div class="panel-actions">
                        <button class="btn btn--primary" type="submit">Сохранить изменения</button>
                        <a class="btn btn--ghost" href="<?= e(url('favorites.php')) ?>">Моё избранное</a>
                    </div>
                </form>
            </div>
        </section>

        <section class="panel">
            <div class="panel__body">
                <h2 class="panel-title">Информация об аккаунте</h2>
                <p class="muted-text">Пользователь создан: <?= e(format_date($user['created_at'])) ?></p>
                <p class="muted-text">Последнее обновление: <?= e(format_date($user['updated_at'])) ?></p>

                <div class="panel-actions">
                    <a class="btn btn--ghost" href="<?= e(url('catalog.php')) ?>">Перейти в каталог</a>
                    <a class="btn btn--ghost" href="<?= e(url('delivery.php')) ?>">Оплата и доставка</a>
                </div>
            </div>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
