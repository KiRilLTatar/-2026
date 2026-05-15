<?php
declare(strict_types=1);

function get_categories(): array
{
    $statement = db()->query('SELECT id, name, slug, image_path, sort_order FROM categories ORDER BY sort_order ASC, id ASC');

    return $statement->fetchAll();
}

function get_colors(): array
{
    $statement = db()->query('SELECT id, name, slug, code, sort_order FROM colors ORDER BY sort_order ASC, id ASC');

    return $statement->fetchAll();
}

function get_sizes(): array
{
    $statement = db()->query('SELECT id, name, sort_order FROM sizes ORDER BY sort_order ASC, id ASC');

    return $statement->fetchAll();
}

function get_catalog_products(array $filters = []): array
{
    $sql = <<<SQL
        SELECT
            p.id,
            p.category_id,
            p.color_id,
            p.title,
            p.slug,
            p.short_description,
            p.description,
            p.price,
            p.image_path,
            p.is_new,
            p.is_bestseller,
            p.is_sale,
            p.created_at,
            c.name AS category_name,
            c.slug AS category_slug,
            co.name AS color_name,
            co.code AS color_code,
            (
                SELECT GROUP_CONCAT(s2.name ORDER BY s2.sort_order SEPARATOR ' ')
                FROM product_sizes ps2
                INNER JOIN sizes s2 ON s2.id = ps2.size_id
                WHERE ps2.product_id = p.id
            ) AS sizes_text
        FROM products p
        INNER JOIN categories c ON c.id = p.category_id
        LEFT JOIN colors co ON co.id = p.color_id
        WHERE 1 = 1
    SQL;

    $params = [];

    if (!empty($filters['category'])) {
        $sql .= ' AND c.slug = :category_slug';
        $params['category_slug'] = (string) $filters['category'];
    }

    if (!empty($filters['size_id'])) {
        $sql .= ' AND EXISTS (
            SELECT 1
            FROM product_sizes ps_filter
            WHERE ps_filter.product_id = p.id
              AND ps_filter.size_id = :size_id
        )';
        $params['size_id'] = (int) $filters['size_id'];
    }

    if (!empty($filters['color_id'])) {
        $sql .= ' AND p.color_id = :color_id';
        $params['color_id'] = (int) $filters['color_id'];
    }

    $minPrice = $filters['min_price'] ?? null;
    $maxPrice = $filters['max_price'] ?? null;

    if ($minPrice !== '' && $minPrice !== null) {
        $sql .= ' AND p.price >= :min_price';
        $params['min_price'] = (float) $minPrice;
    }

    if ($maxPrice !== '' && $maxPrice !== null) {
        $sql .= ' AND p.price <= :max_price';
        $params['max_price'] = (float) $maxPrice;
    }

    $flag = (string) ($filters['flag'] ?? '');

    if ($flag === 'new') {
        $sql .= ' AND p.is_new = 1';
    } elseif ($flag === 'bestsellers') {
        $sql .= ' AND p.is_bestseller = 1';
    } elseif ($flag === 'sale') {
        $sql .= ' AND p.is_sale = 1';
    }

    $sort = (string) ($filters['sort'] ?? 'newest');

    $orderBy = 'p.created_at DESC, p.id DESC';

    if ($sort === 'price_asc') {
        $orderBy = 'p.price ASC, p.id ASC';
    } elseif ($sort === 'price_desc') {
        $orderBy = 'p.price DESC, p.id DESC';
    } elseif ($sort === 'title') {
        $orderBy = 'p.title ASC, p.id ASC';
    }

    $sql .= ' ORDER BY ' . $orderBy;

    $statement = db()->prepare($sql);

    foreach ($params as $name => $value) {
        $type = PDO::PARAM_STR;

        if (is_int($value)) {
            $type = PDO::PARAM_INT;
        }

        $statement->bindValue(':' . $name, $value, $type);
    }

    $statement->execute();

    return $statement->fetchAll();
}

function find_product_by_slug(string $slug): ?array
{
    $sql = <<<SQL
        SELECT
            p.id,
            p.category_id,
            p.color_id,
            p.title,
            p.slug,
            p.short_description,
            p.description,
            p.price,
            p.image_path,
            p.is_new,
            p.is_bestseller,
            p.is_sale,
            p.created_at,
            c.name AS category_name,
            c.slug AS category_slug,
            co.name AS color_name,
            co.code AS color_code,
            (
                SELECT GROUP_CONCAT(s2.name ORDER BY s2.sort_order SEPARATOR ' ')
                FROM product_sizes ps2
                INNER JOIN sizes s2 ON s2.id = ps2.size_id
                WHERE ps2.product_id = p.id
            ) AS sizes_text
        FROM products p
        INNER JOIN categories c ON c.id = p.category_id
        LEFT JOIN colors co ON co.id = p.color_id
        WHERE p.slug = :slug
        LIMIT 1
    SQL;

    $statement = db()->prepare($sql);
    $statement->execute(['slug' => $slug]);
    $product = $statement->fetch();

    return $product ?: null;
}

function find_product_by_id(int $productId): ?array
{
    $statement = db()->prepare('SELECT id, title, slug FROM products WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $productId]);
    $product = $statement->fetch();

    return $product ?: null;
}

function get_user_by_id(int $userId): ?array
{
    $statement = db()->prepare('SELECT id, name, email, phone, created_at, updated_at FROM users WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $userId]);
    $user = $statement->fetch();

    return $user ?: null;
}

function find_user_by_email(string $email): ?array
{
    $statement = db()->prepare('SELECT id, name, email, phone, password_hash, created_at, updated_at FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();

    return $user ?: null;
}

function create_user(array $data): int
{
    $statement = db()->prepare(
        'INSERT INTO users (name, email, phone, password_hash) VALUES (:name, :email, :phone, :password_hash)'
    );

    $statement->execute([
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'password_hash' => $data['password_hash'],
    ]);

    return (int) db()->lastInsertId();
}

function update_user_profile(int $userId, array $data): void
{
    $statement = db()->prepare('UPDATE users SET name = :name, phone = :phone WHERE id = :id');
    $statement->execute([
        'id' => $userId,
        'name' => $data['name'],
        'phone' => $data['phone'],
    ]);
}

function save_newsletter_subscription(string $email): void
{
    $statement = db()->prepare(
        'INSERT INTO newsletter_subscribers (email, is_active) VALUES (:email, 1)
         ON DUPLICATE KEY UPDATE is_active = 1, updated_at = CURRENT_TIMESTAMP'
    );

    $statement->execute(['email' => $email]);
}

function get_favorite_product_ids(int $userId): array
{
    $statement = db()->prepare('SELECT product_id FROM favorites WHERE user_id = :user_id');
    $statement->execute(['user_id' => $userId]);

    return array_map('intval', array_column($statement->fetchAll(), 'product_id'));
}

function get_favorite_products(int $userId): array
{
    $sql = <<<SQL
        SELECT
            p.id,
            p.category_id,
            p.color_id,
            p.title,
            p.slug,
            p.short_description,
            p.description,
            p.price,
            p.image_path,
            p.is_new,
            p.is_bestseller,
            p.is_sale,
            p.created_at,
            c.name AS category_name,
            c.slug AS category_slug,
            co.name AS color_name,
            co.code AS color_code,
            (
                SELECT GROUP_CONCAT(s2.name ORDER BY s2.sort_order SEPARATOR ' ')
                FROM product_sizes ps2
                INNER JOIN sizes s2 ON s2.id = ps2.size_id
                WHERE ps2.product_id = p.id
            ) AS sizes_text
        FROM favorites f
        INNER JOIN products p ON p.id = f.product_id
        INNER JOIN categories c ON c.id = p.category_id
        LEFT JOIN colors co ON co.id = p.color_id
        WHERE f.user_id = :user_id
        ORDER BY f.created_at DESC, p.id DESC
    SQL;

    $statement = db()->prepare($sql);
    $statement->execute(['user_id' => $userId]);

    return $statement->fetchAll();
}

function count_user_favorites(int $userId): int
{
    $statement = db()->prepare('SELECT COUNT(*) FROM favorites WHERE user_id = :user_id');
    $statement->execute(['user_id' => $userId]);

    return (int) $statement->fetchColumn();
}

function get_cart_items(int $userId): array
{
    $statement = db()->prepare(
        'SELECT product_id, quantity
         FROM cart_items
         WHERE user_id = :user_id
         ORDER BY updated_at DESC, id DESC'
    );
    $statement->execute(['user_id' => $userId]);

    return array_map(
        static function (array $item): array {
            return [
                'product_id' => (int) $item['product_id'],
                'quantity' => (int) $item['quantity'],
            ];
        },
        $statement->fetchAll()
    );
}

function count_cart_items(int $userId): int
{
    $statement = db()->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE user_id = :user_id');
    $statement->execute(['user_id' => $userId]);

    return (int) $statement->fetchColumn();
}

function add_to_cart(int $userId, int $productId, int $quantity = 1): void
{
    $statement = db()->prepare(
        'INSERT INTO cart_items (user_id, product_id, quantity)
         VALUES (:user_id, :product_id, :quantity)
         ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity), updated_at = CURRENT_TIMESTAMP'
    );
    $statement->execute([
        'user_id' => $userId,
        'product_id' => $productId,
        'quantity' => $quantity,
    ]);
}

function update_cart_item_quantity(int $userId, int $productId, int $quantity): void
{
    if ($quantity <= 0) {
        remove_from_cart($userId, $productId);
        return;
    }

    $statement = db()->prepare(
        'UPDATE cart_items
         SET quantity = :quantity, updated_at = CURRENT_TIMESTAMP
         WHERE user_id = :user_id AND product_id = :product_id'
    );
    $statement->execute([
        'user_id' => $userId,
        'product_id' => $productId,
        'quantity' => $quantity,
    ]);
}

function remove_from_cart(int $userId, int $productId): void
{
    $statement = db()->prepare('DELETE FROM cart_items WHERE user_id = :user_id AND product_id = :product_id');
    $statement->execute([
        'user_id' => $userId,
        'product_id' => $productId,
    ]);
}

function clear_cart(int $userId): void
{
    $statement = db()->prepare('DELETE FROM cart_items WHERE user_id = :user_id');
    $statement->execute(['user_id' => $userId]);
}

function toggle_favorite(int $userId, int $productId): bool
{
    $checkStatement = db()->prepare('SELECT id FROM favorites WHERE user_id = :user_id AND product_id = :product_id LIMIT 1');
    $checkStatement->execute([
        'user_id' => $userId,
        'product_id' => $productId,
    ]);

    $favorite = $checkStatement->fetch();

    if ($favorite) {
        $deleteStatement = db()->prepare('DELETE FROM favorites WHERE id = :id');
        $deleteStatement->execute(['id' => $favorite['id']]);

        return false;
    }

    $insertStatement = db()->prepare('INSERT INTO favorites (user_id, product_id) VALUES (:user_id, :product_id)');
    $insertStatement->execute([
        'user_id' => $userId,
        'product_id' => $productId,
    ]);

    return true;
}

function ensure_runtime_schema(): void
{
    static $bootstrapped = false;

    if ($bootstrapped) {
        return;
    }

    db()->exec(
        'CREATE TABLE IF NOT EXISTS cart_items (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            product_id INT UNSIGNED NOT NULL,
            quantity SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_cart_items_user_product (user_id, product_id),
            KEY idx_cart_items_product_id (product_id),
            CONSTRAINT fk_cart_items_user
                FOREIGN KEY (user_id) REFERENCES users (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE,
            CONSTRAINT fk_cart_items_product
                FOREIGN KEY (product_id) REFERENCES products (id)
                ON DELETE CASCADE
                ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $bootstrapped = true;
}
