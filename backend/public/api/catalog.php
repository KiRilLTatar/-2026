<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once APP_ROOT . '/includes/api.php';

api_require_method('GET');

$filters = [
    'flag' => trim((string) ($_GET['flag'] ?? '')),
    'category' => trim((string) ($_GET['category'] ?? '')),
    'size_id' => isset($_GET['size_id']) && $_GET['size_id'] !== '' ? (int) $_GET['size_id'] : null,
    'color_id' => isset($_GET['color_id']) && $_GET['color_id'] !== '' ? (int) $_GET['color_id'] : null,
    'min_price' => trim((string) ($_GET['min_price'] ?? '')),
    'max_price' => trim((string) ($_GET['max_price'] ?? '')),
    'sort' => trim((string) ($_GET['sort'] ?? 'newest')),
];

api_success([
    'catalog' => api_catalog_resource($filters),
]);
