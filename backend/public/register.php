<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_guest();

$pageTitle = 'Регистрация';

require_once __DIR__ . '/../includes/header.php';
?>

<main class="section">
    <div class="container auth-layout">
        <section class="panel">
            <div class="panel__body">
                <h1 class="panel-title">Регистрация</h1>
                <p class="muted-text">
                    Пользовательская сущность вытекает из кнопки «профиль» и сценария избранного. В минимальной версии
                    сохраняем имя, e-mail, телефон, пароль и подтверждение пароля.
                </p>

                <form action="<?= e(url('actions/register.php')) ?>" method="post" style="margin-top: 20px;">
                    <?= csrf_field() ?>

                    <div class="field">
                        <label for="name">Имя</label>
                        <input id="name" type="text" name="name" value="<?= e(old('name')) ?>" required>
                        <?php if ($message = error_for('name')): ?>
                            <div class="field-error"><?= e($message) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="field" style="margin-top: 16px;">
                        <label for="email">E-mail</label>
                        <input id="email" type="email" name="email" value="<?= e(old('email')) ?>" required>
                        <?php if ($message = error_for('email')): ?>
                            <div class="field-error"><?= e($message) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="field" style="margin-top: 16px;">
                        <label for="phone">Телефон</label>
                        <input id="phone" type="text" name="phone" value="<?= e(old('phone')) ?>" placeholder="+7 (900) 000-00-00">
                        <?php if ($message = error_for('phone')): ?>
                            <div class="field-error"><?= e($message) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="field" style="margin-top: 16px;">
                        <label for="password">Пароль</label>
                        <input id="password" type="password" name="password" required>
                        <?php if ($message = error_for('password')): ?>
                            <div class="field-error"><?= e($message) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="field" style="margin-top: 16px;">
                        <label for="password_confirmation">Повторите пароль</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required>
                        <?php if ($message = error_for('password_confirmation')): ?>
                            <div class="field-error"><?= e($message) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="panel-actions">
                        <button class="btn btn--primary" type="submit">Зарегистрироваться</button>
                        <a class="btn btn--ghost" href="<?= e(url('login.php')) ?>">Уже есть аккаунт</a>
                    </div>
                </form>
            </div>
        </section>

        <section class="panel">
            <div class="panel__body">
                <h2 class="panel-title">Что даёт регистрация</h2>
                <ul class="list">
                    <li>Доступ к личному профилю.</li>
                    <li>Сохранение товаров в избранное.</li>
                    <li>Основа для дальнейшего расширения проекта корзиной или заказами.</li>
                </ul>
            </div>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
