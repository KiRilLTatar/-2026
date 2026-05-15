<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';

if (!is_post()) {
    redirect('catalog.php');
}

verify_csrf();

$redirectTo = sanitize_local_path((string) ($_POST['redirect_to'] ?? ''), url('catalog.php'));

if (!is_logged_in()) {
    $_SESSION['intended_url'] = $redirectTo;
    add_flash('error', 'Чтобы работать с избранным, сначала войдите в аккаунт.');
    redirect('login.php');
}

$productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
$product = $productId > 0 ? find_product_by_id($productId) : null;

if ($product === null) {
    add_flash('error', 'Товар не найден.');
    redirect_local($redirectTo, url('catalog.php'));
}

$added = toggle_favorite((int) current_user()['id'], $productId);

add_flash(
    'success',
    $added
        ? 'Товар "' . (string) $product['title'] . '" добавлен в избранное.'
        : 'Товар "' . (string) $product['title'] . '" удалён из избранного.'
);

redirect_local($redirectTo, url('catalog.php'));
