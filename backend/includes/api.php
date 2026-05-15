<?php
declare(strict_types=1);

function api_input(): array
{
    static $payload = null;

    if (is_array($payload)) {
        return $payload;
    }

    $raw = trim((string) file_get_contents('php://input'));

    if ($raw === '') {
        $payload = $_POST;
        return $payload;
    }

    $decoded = json_decode($raw, true);
    $payload = is_array($decoded) ? $decoded : $_POST;

    return $payload;
}

function api_success(array $payload = [], int $status = 200): void
{
    header('Cache-Control: no-store');
    json_response(array_merge(['success' => true], $payload), $status);
}

function api_error(string $message, int $status = 422, array $errors = []): void
{
    $payload = [
        'success' => false,
        'message' => $message,
    ];

    if ($errors) {
        $payload['errors'] = $errors;
    }

    header('Cache-Control: no-store');
    json_response($payload, $status);
}

function api_require_method(string $method): void
{
    if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== strtoupper($method)) {
        api_error('Метод не поддерживается.', 405);
    }
}

function api_csrf_token_from_request(): string
{
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $payload = api_input();

    if (!empty($headers['X-CSRF-Token'])) {
        return (string) $headers['X-CSRF-Token'];
    }

    if (!empty($headers['x-csrf-token'])) {
        return (string) $headers['x-csrf-token'];
    }

    if (!empty($payload['csrfToken'])) {
        return (string) $payload['csrfToken'];
    }

    return (string) ($payload['csrf_token'] ?? '');
}

function api_verify_csrf(): void
{
    $token = api_csrf_token_from_request();
    $sessionToken = (string) ($_SESSION['csrf_token'] ?? '');

    if ($token === '' || $sessionToken === '' || !hash_equals($sessionToken, $token)) {
        api_error('Не найден CSRF-токен.', 419);
    }
}

function api_require_auth_user(): array
{
    $user = current_user();

    if ($user === null) {
        api_error('Сначала войдите в аккаунт.', 401);
    }

    return $user;
}

function api_frontend_image_url(string $path): string
{
    return '/images/' . basename($path);
}

function api_user_resource(?array $user): ?array
{
    if ($user === null) {
        return null;
    }

    return [
        'id' => (int) $user['id'],
        'name' => (string) $user['name'],
        'email' => (string) $user['email'],
        'phone' => (string) ($user['phone'] ?? ''),
        'createdAt' => (string) $user['created_at'],
        'updatedAt' => (string) $user['updated_at'],
    ];
}

function api_category_resource(array $category): array
{
    return [
        'id' => (int) $category['id'],
        'name' => (string) $category['name'],
        'slug' => (string) $category['slug'],
        'imageUrl' => api_frontend_image_url((string) $category['image_path']),
        'sortOrder' => (int) $category['sort_order'],
    ];
}

function api_product_resource(array $product): array
{
    return [
        'id' => (int) $product['id'],
        'title' => (string) $product['title'],
        'slug' => (string) $product['slug'],
        'shortDescription' => (string) ($product['short_description'] ?? ''),
        'description' => (string) ($product['description'] ?? ''),
        'price' => (float) $product['price'],
        'imageUrl' => api_frontend_image_url((string) $product['image_path']),
        'isNew' => (int) $product['is_new'] === 1,
        'isBestseller' => (int) $product['is_bestseller'] === 1,
        'isSale' => (int) $product['is_sale'] === 1,
        'category' => [
            'id' => (int) $product['category_id'],
            'name' => (string) $product['category_name'],
            'slug' => (string) $product['category_slug'],
        ],
        'color' => [
            'id' => isset($product['color_id']) ? (int) $product['color_id'] : null,
            'name' => (string) ($product['color_name'] ?? ''),
            'code' => (string) ($product['color_code'] ?? ''),
        ],
        'sizes' => array_values(
            array_filter(
                explode(' ', trim((string) ($product['sizes_text'] ?? '')))
            )
        ),
    ];
}

function api_cart_resource(int $userId): array
{
    return array_map(
        static function (array $item): array {
            return [
                'productId' => (int) $item['product_id'],
                'quantity' => (int) $item['quantity'],
            ];
        },
        get_cart_items($userId)
    );
}

function api_session_resource(?array $explicitUser = null): array
{
    $user = $explicitUser ?? current_user();
    $favoriteIds = [];
    $cart = [];
    $favoriteCount = 0;
    $cartCount = 0;

    if ($user !== null) {
        $userId = (int) $user['id'];
        $favoriteIds = get_favorite_product_ids($userId);
        $cart = api_cart_resource($userId);
        $favoriteCount = count($favoriteIds);
        $cartCount = count_cart_items($userId);
    }

    return [
        'csrfToken' => csrf_token(),
        'user' => api_user_resource($user),
        'favoriteIds' => $favoriteIds,
        'cart' => $cart,
        'counts' => [
            'favorites' => $favoriteCount,
            'cart' => $cartCount,
        ],
    ];
}

function api_catalog_resource(array $filters = []): array
{
    return [
        'categories' => array_map('api_category_resource', get_categories()),
        'colors' => array_map(
            static function (array $color): array {
                return [
                    'id' => (int) $color['id'],
                    'name' => (string) $color['name'],
                    'slug' => (string) $color['slug'],
                    'code' => (string) ($color['code'] ?? ''),
                ];
            },
            get_colors()
        ),
        'sizes' => array_map(
            static function (array $size): array {
                return [
                    'id' => (int) $size['id'],
                    'name' => (string) $size['name'],
                ];
            },
            get_sizes()
        ),
        'products' => array_map('api_product_resource', get_catalog_products($filters)),
    ];
}
