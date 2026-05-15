<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$pageTitle = 'Каталог';
$filters = [
    'flag' => trim((string) ($_GET['flag'] ?? '')),
    'category' => trim((string) ($_GET['category'] ?? '')),
    'size_id' => isset($_GET['size_id']) && $_GET['size_id'] !== '' ? (int) $_GET['size_id'] : null,
    'color_id' => isset($_GET['color_id']) && $_GET['color_id'] !== '' ? (int) $_GET['color_id'] : null,
    'min_price' => trim((string) ($_GET['min_price'] ?? '')),
    'max_price' => trim((string) ($_GET['max_price'] ?? '')),
    'sort' => trim((string) ($_GET['sort'] ?? 'newest')),
];

$categories = get_categories();
$colors = get_colors();
$sizes = get_sizes();
$products = get_catalog_products($filters);
$favoriteProductIds = is_logged_in() ? get_favorite_product_ids((int) current_user()['id']) : [];

$catalogUrl = static function (array $overrides = []) use ($filters): string {
    $params = array_merge($filters, $overrides);

    foreach ($params as $key => $value) {
        if ($value === '' || $value === null) {
            unset($params[$key]);
        }
    }

    $query = http_build_query($params);

    return url('catalog.php' . ($query !== '' ? '?' . $query : ''));
};

require_once __DIR__ . '/../includes/header.php';
?>

<main class="section">
    <div class="container">
        <div class="page-header">
            <div class="breadcrumbs">Главная › Каталог</div>
            <h1 class="page-title">Каталог</h1>
            <p class="page-subtitle">
                По текущему интерфейсу здесь нужны категории, фильтры по размеру, цвету, цене, сортировка и избранное.
            </p>
        </div>

        <div class="catalog-layout">
            <aside class="panel sidebar">
                <h2 class="sidebar-title">Разделы каталога</h2>
                <div class="sidebar-list">
                    <a class="sidebar-link <?= $filters['flag'] === 'new' ? 'active' : '' ?>" href="<?= e($catalogUrl(['flag' => 'new'])) ?>">
                        New
                    </a>
                    <a class="sidebar-link <?= $filters['flag'] === 'bestsellers' ? 'active' : '' ?>" href="<?= e($catalogUrl(['flag' => 'bestsellers'])) ?>">
                        Bestsellers
                    </a>
                    <a class="sidebar-link <?= $filters['flag'] === 'sale' ? 'active' : '' ?>" href="<?= e($catalogUrl(['flag' => 'sale'])) ?>">
                        Sale
                    </a>
                    <?php foreach ($categories as $category): ?>
                        <a
                            class="sidebar-link <?= $filters['category'] === $category['slug'] ? 'active' : '' ?>"
                            href="<?= e($catalogUrl(['category' => $category['slug']])) ?>"
                        >
                            <?= e($category['name']) ?>
                        </a>
                    <?php endforeach; ?>
                    <a class="sidebar-link" href="<?= e(url('catalog.php')) ?>">Сбросить всё</a>
                </div>
            </aside>

            <div>
                <section class="panel filter-panel">
                    <form action="<?= e(url('catalog.php')) ?>" method="get">
                        <div class="filter-grid">
                            <div class="field">
                                <label for="size_id">Размер</label>
                                <select id="size_id" name="size_id">
                                    <option value="">Любой</option>
                                    <?php foreach ($sizes as $size): ?>
                                        <option value="<?= (int) $size['id'] ?>"<?= selected_if((string) $filters['size_id'], (string) $size['id']) ?>>
                                            <?= e($size['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="field">
                                <label for="color_id">Цвет</label>
                                <select id="color_id" name="color_id">
                                    <option value="">Любой</option>
                                    <?php foreach ($colors as $color): ?>
                                        <option value="<?= (int) $color['id'] ?>"<?= selected_if((string) $filters['color_id'], (string) $color['id']) ?>>
                                            <?= e($color['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="field">
                                <label for="min_price">Цена от</label>
                                <input id="min_price" type="number" name="min_price" min="0" step="1" value="<?= e($filters['min_price']) ?>">
                            </div>

                            <div class="field">
                                <label for="max_price">Цена до</label>
                                <input id="max_price" type="number" name="max_price" min="0" step="1" value="<?= e($filters['max_price']) ?>">
                            </div>
                        </div>

                        <div class="filter-grid" style="margin-top: 16px;">
                            <div class="field">
                                <label for="sort">Сортировка</label>
                                <select id="sort" name="sort">
                                    <option value="newest"<?= selected_if($filters['sort'], 'newest') ?>>Сначала новые</option>
                                    <option value="price_asc"<?= selected_if($filters['sort'], 'price_asc') ?>>Цена по возрастанию</option>
                                    <option value="price_desc"<?= selected_if($filters['sort'], 'price_desc') ?>>Цена по убыванию</option>
                                    <option value="title"<?= selected_if($filters['sort'], 'title') ?>>По названию</option>
                                </select>
                            </div>

                            <div class="field">
                                <label for="flag">Спецподборка</label>
                                <select id="flag" name="flag">
                                    <option value="">Без спецподборки</option>
                                    <option value="new"<?= selected_if($filters['flag'], 'new') ?>>New</option>
                                    <option value="bestsellers"<?= selected_if($filters['flag'], 'bestsellers') ?>>Bestsellers</option>
                                    <option value="sale"<?= selected_if($filters['flag'], 'sale') ?>>Sale</option>
                                </select>
                            </div>

                            <div class="field">
                                <label for="category">Категория</label>
                                <select id="category" name="category">
                                    <option value="">Все категории</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= e($category['slug']) ?>"<?= selected_if($filters['category'], $category['slug']) ?>>
                                            <?= e($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="field">
                                <label>&nbsp;</label>
                                <div class="button-row">
                                    <button class="btn btn--primary" type="submit">Применить</button>
                                    <a class="btn btn--ghost" href="<?= e(url('catalog.php')) ?>">Сбросить</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </section>

                <?php if (!$products): ?>
                    <div class="panel empty-state">
                        <h2 class="panel-title">Ничего не найдено</h2>
                        <p class="muted-text">Попробуйте сбросить часть фильтров или открыть весь каталог.</p>
                    </div>
                <?php else: ?>
                    <div class="catalog-grid">
                        <?php foreach ($products as $product): ?>
                            <?php $isFavorite = in_array((int) $product['id'], $favoriteProductIds, true); ?>
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
                                        <?php if ((int) $product['is_sale'] === 1): ?>
                                            <span class="badge">Sale</span>
                                        <?php endif; ?>
                                    </div>

                                    <h2 class="product-card__title">
                                        <a href="<?= e(url('product.php?slug=' . urlencode($product['slug']))) ?>">
                                            <?= e($product['title']) ?>
                                        </a>
                                    </h2>

                                    <p class="helper-text"><?= e($product['short_description']) ?></p>

                                    <div class="product-card__meta">
                                        <div>
                                            <div class="price"><?= e(format_price((float) $product['price'])) ?></div>
                                            <div class="helper-text">
                                                <?= e($product['category_name']) ?> · <?= e($product['color_name'] ?? 'Без цвета') ?>
                                            </div>
                                        </div>

                                        <?php if (is_logged_in()): ?>
                                            <form class="favorite-form" action="<?= e(url('actions/toggle_favorite.php')) ?>" method="post">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                                <input type="hidden" name="redirect_to" value="<?= e(current_path_with_query()) ?>">
                                                <button class="favorite-button <?= $isFavorite ? 'favorite-button--active' : '' ?>" type="submit">
                                                    <?= $isFavorite ? '♥' : '♡' ?>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <a class="btn btn--ghost" href="<?= e(url('login.php')) ?>">Войти</a>
                                        <?php endif; ?>
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
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
