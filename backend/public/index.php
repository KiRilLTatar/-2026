<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$pageTitle = 'Главная';
$categories = get_categories();
$newProducts = array_slice(get_catalog_products([
    'flag' => 'new',
    'sort' => 'newest',
]), 0, 3);

require_once __DIR__ . '/../includes/header.php';
?>

<main>
    <section class="hero">
        <div class="container">
            <div class="hero-card">
                <div class="hero-content">
                    <div class="eyebrow">Новая коллекция</div>
                    <h1 class="hero-title">Каталог верхней одежды</h1>
                    <p class="hero-text">
                        Главная страница в вашем фронтенде показывает новую коллекцию, категории и форму подписки,
                        поэтому именно эти сущности и подключены к базе в минимальной версии backend.
                    </p>
                    <div class="button-row">
                        <a class="btn btn--primary" href="<?= e(url('catalog.php?flag=new')) ?>">Смотреть новинки</a>
                        <a class="btn btn--ghost" href="<?= e(url('catalog.php')) ?>">Открыть каталог</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <h2 class="section-title">Категории</h2>
                    <p class="section-subtitle">Категории берутся из таблицы `categories` и ведут в каталог.</p>
                </div>
            </div>

            <div class="grid-4">
                <?php foreach ($categories as $category): ?>
                    <article class="category-card">
                        <img src="<?= e(asset($category['image_path'])) ?>" alt="<?= e($category['name']) ?>">
                        <div class="category-card__body">
                            <h3 class="category-card__title"><?= e($category['name']) ?></h3>
                            <div class="panel-actions">
                                <a class="btn btn--ghost" href="<?= e(url('catalog.php?category=' . urlencode($category['slug']))) ?>">
                                    Открыть категорию
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <h2 class="section-title">Новинки из базы</h2>
                    <p class="section-subtitle">Этот блок помогает быстро проверить чтение товаров из MySQL.</p>
                </div>
                <a class="btn btn--ghost" href="<?= e(url('catalog.php?flag=new')) ?>">Все новинки</a>
            </div>

            <div class="cards-grid">
                <?php foreach ($newProducts as $product): ?>
                    <article class="product-card">
                        <a href="<?= e(url('product.php?slug=' . urlencode($product['slug']))) ?>">
                            <img src="<?= e(asset($product['image_path'])) ?>" alt="<?= e($product['title']) ?>">
                        </a>
                        <div class="product-card__body">
                            <div class="badge-row">
                                <?php if ((int) $product['is_new'] === 1): ?>
                                    <span class="badge">New</span>
                                <?php endif; ?>
                                <?php if ((int) $product['is_bestseller'] === 1): ?>
                                    <span class="badge">Bestseller</span>
                                <?php endif; ?>
                            </div>
                            <h3 class="product-card__title">
                                <a href="<?= e(url('product.php?slug=' . urlencode($product['slug']))) ?>">
                                    <?= e($product['title']) ?>
                                </a>
                            </h3>
                            <div class="product-card__meta">
                                <div class="price"><?= e(format_price((float) $product['price'])) ?></div>
                                <div class="helper-text"><?= e($product['color_name'] ?? 'Без цвета') ?></div>
                            </div>
                            <div class="sizes">
                                <?php foreach (array_filter(explode(' ', (string) $product['sizes_text'])) as $sizeName): ?>
                                    <span class="chip"><?= e($sizeName) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="panel">
                <div class="panel__body">
                    <div class="section-head">
                        <div>
                            <h2 class="section-title">Подписка на новости</h2>
                            <p class="section-subtitle">
                                На фронтенде есть только e-mail поле и кнопка, поэтому в минимальной БД хранится одна таблица
                                `newsletter_subscribers` без лишних сущностей.
                            </p>
                        </div>
                    </div>

                    <form action="<?= e(url('actions/subscribe.php')) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="form-grid">
                            <div class="field">
                                <label for="subscribe-email">Ваш e-mail</label>
                                <input
                                    id="subscribe-email"
                                    type="email"
                                    name="email"
                                    value="<?= e(old('email')) ?>"
                                    placeholder="example@mail.com"
                                    required
                                >
                                <?php if ($message = error_for('email')): ?>
                                    <div class="field-error"><?= e($message) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="field">
                                <label>&nbsp;</label>
                                <button class="btn btn--primary" type="submit">Подписаться</button>
                            </div>
                        </div>
                    </form>

                    <p class="helper-text">
                        Нажимая на кнопку, пользователь создаёт или повторно активирует запись подписки в MySQL.
                    </p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
