<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_guest();

$pageTitle = 'Вход';

require_once __DIR__ . '/../includes/header.php';
?>

<main class="section">
    <div class="container auth-layout">
        <section class="panel">
            <div class="panel__body">
                <h1 class="panel-title">Вход в аккаунт</h1>
                <p class="muted-text">
                    На исходном фронтенде нет отдельной страницы входа, но есть кнопка «профиль», поэтому для backend
                    добавлена минимальная форма авторизации через сессию.
                </p>

                <?php if ($message = error_for('credentials')): ?>
                    <div class="alert alert--error" style="margin-top: 16px;"><?= e($message) ?></div>
                <?php endif; ?>

                <form action="<?= e(url('actions/login.php')) ?>" method="post" style="margin-top: 20px;">
                    <?= csrf_field() ?>

                    <div class="field">
                        <label for="email">E-mail</label>
                        <input id="email" type="email" name="email" value="<?= e(old('email')) ?>" required>
                        <?php if ($message = error_for('email')): ?>
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

                    <div class="panel-actions">
                        <button class="btn btn--primary" type="submit">Войти</button>
                        <a class="btn btn--ghost" href="<?= e(url('register.php')) ?>">Создать аккаунт</a>
                    </div>
                </form>
            </div>
        </section>

        <section class="panel">
            <div class="panel__body">
                <h2 class="panel-title">Что реализовано</h2>
                <ul class="list">
                    <li>Проверка пользователя по e-mail и `password_verify`.</li>
                    <li>Хранение авторизации в `$_SESSION['user_id']`.</li>
                    <li>Защита закрытых страниц `profile.php` и `favorites.php`.</li>
                    <li>Без фреймворков, ORM и сторонних библиотек.</li>
                </ul>
            </div>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
