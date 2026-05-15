<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_auth();

$pageTitle = 'Избранное';
$products = get_favorite_products((int) current_user()['id']);

require_once __DIR__ . '/../includes/header.php';
?>

<main class="section">
    <div class="container">
        <div class="page-header">
            <div class="breadcrumbs">Главная › Избранное</div>
            <h1 class="page-title">Избранное</h1>
            <p class="page-subtitle">
                Эта страница появилась из кнопки сердца на карточке товара и ссылки «избранное» в шапке.
            </p>
        </div>

        <?php if (!$products): ?>
            <div class="panel empty-state">
                <h2 class="panel-title">Пока пусто</h2>
                <p class="muted-text">Добавьте товары из каталога, и они появятся здесь.</p>
                <div class="panel-actions">
                    <a class="btn btn--primary" href="<?= e(url('catalog.php')) ?>">Открыть каталог</a>
                </div>
            </div>
        <?php else: ?>
            <div class="catalog-grid">
                <?php foreach ($products as $product): ?>
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

                            <h2 class="product-card__title">
                                <a href="<?= e(url('product.php?slug=' . urlencode($product['slug']))) ?>">
                                    <?= e($product['title']) ?>
                                </a>
                            </h2>

                            <div class="product-card__meta">
                                <div>
                                    <div class="price"><?= e(format_price((float) $product['price'])) ?></div>
                                    <div class="helper-text"><?= e($product['category_name']) ?></div>
                                </div>

                                <form class="favorite-form" action="<?= e(url('actions/toggle_favorite.php')) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                    <input type="hidden" name="redirect_to" value="<?= e(current_path_with_query()) ?>">
                                    <button class="favorite-button favorite-button--active" type="submit">♥</button>
                                </form>
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
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
