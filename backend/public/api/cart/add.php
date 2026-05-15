<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once APP_ROOT . '/includes/api.php';

api_require_method('POST');
api_verify_csrf();
$user = api_require_auth_user();

$payload = api_input();
$productId = isset($payload['productId']) ? (int) $payload['productId'] : 0;
$product = $productId > 0 ? find_product_by_id($productId) : null;

if ($product === null) {
    api_error('Товар не найден.', 404);
}

add_to_cart((int) $user['id'], $productId, 1);

api_success([
    'message' => 'Товар добавлен в корзину.',
    'session' => api_session_resource(),
]);
