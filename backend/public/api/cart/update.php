<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once APP_ROOT . '/includes/api.php';

api_require_method('POST');
api_verify_csrf();
$user = api_require_auth_user();

$payload = api_input();
$productId = isset($payload['productId']) ? (int) $payload['productId'] : 0;
$quantity = isset($payload['quantity']) ? (int) $payload['quantity'] : 0;
$product = $productId > 0 ? find_product_by_id($productId) : null;

if ($product === null) {
    api_error('Товар не найден.', 404);
}

update_cart_item_quantity((int) $user['id'], $productId, $quantity);

api_success([
    'message' => $quantity > 0 ? 'Количество обновлено.' : 'Товар удалён из корзины.',
    'session' => api_session_resource(),
]);
