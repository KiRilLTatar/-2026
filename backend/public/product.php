<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$product = $slug !== '' ? find_product_by_slug($slug) : null;

if ($product === null) {
    http_response_code(404);
}

$pageTitle = $product['title'] ?? 'Товар не найден';
$favoriteProductIds = is_logged_in() ? get_favorite_product_ids((int) current_user()['id']) : [];
$isFavorite = $product ? in_array((int) $product['id'], $favoriteProductIds, true) : false;

require_once __DIR__ . '/../includes/header.php';
?>

<main class="section">
    <div class="container">
        <?php if ($product === null): ?>
            <div class="panel empty-state">
                <h1 class="panel-title">Товар не найден</h1>
                <p class="muted-text">Проверьте ссылку или вернитесь в каталог.</p>
                <div class="panel-actions">
                    <a class="btn btn--primary" href="<?= e(url('catalog.php')) ?>">Перейти в каталог</a>
                </div>
            </div>
        <?php else: ?>
            <div class="page-header">
                <div class="breadcrumbs">
                    Главная › <a href="<?= e(url('catalog.php')) ?>">Каталог</a> › <?= e($product['title']) ?>
                </div>
                <h1 class="page-title"><?= e($product['title']) ?></h1>
            </div>

            <div class="product-layout">
                <div class="product-image">
                    <img src="<?= e(asset($product['image_path'])) ?>" alt="<?= e($product['title']) ?>">
                </div>

                <section class="panel product-panel">
                    <div class="badge-row">
                        <?php if ((int) $product['is_new'] === 1): ?>
                            <span class="badge">New</span>
                        <?php endif; ?>
                        <?php if ((int) $product['is_bestseller'] === 1): ?>
                            <span class="badge">Bestseller</span>
                        <?php endif; ?>
                        <?php if ((int) $product['is_sale'] === 1): ?>
                            <span class="badge">Sale</span>
                        <?php endif; ?>
                    </div>

                    <p class="muted-text"><?= e($product['category_name']) ?> · <?= e($product['color_name'] ?? 'Без цвета') ?></p>
                    <div class="price" style="margin-top: 14px;"><?= e(format_price((float) $product['price'])) ?></div>

                    <p class="muted-text" style="margin-top: 18px;">
                        <?= e($product['short_description']) ?>
                    </p>

                    <p class="muted-text">
                        <?= e($product['description']) ?>
                    </p>

                    <div class="field" style="margin-top: 22px;">
                        <label>Доступные размеры</label>
                        <div class="chips">
                            <?php foreach (array_filter(explode(' ', (string) $product['sizes_text'])) as $sizeName): ?>
                                <span class="chip"><?= e($sizeName) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="panel-actions">
                        <?php if (is_logged_in()): ?>
                            <form class="favorite-form" action="<?= e(url('actions/toggle_favorite.php')) ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                <input type="hidden" name="redirect_to" value="<?= e(current_path_with_query()) ?>">
                                <button class="btn <?= $isFavorite ? 'btn--ghost' : 'btn--primary' ?>" type="submit">
                                    <?= $isFavorite ? 'Убрать из избранного' : 'Добавить в избранное' ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <a class="btn btn--primary" href="<?= e(url('login.php')) ?>">Войти, чтобы сохранить</a>
                        <?php endif; ?>

                        <a class="btn btn--ghost" href="<?= e(url('catalog.php?category=' . urlencode($product['category_slug']))) ?>">
                            Ещё товары категории
                        </a>
                    </div>
                </section>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
