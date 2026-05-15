CREATE DATABASE IF NOT EXISTS cyberteka
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE cyberteka;

SET NAMES utf8mb4;

DROP TABLE IF EXISTS favorites;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS product_sizes;
DROP TABLE IF EXISTS newsletter_subscribers;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS sizes;
DROP TABLE IF EXISTS colors;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB;

CREATE TABLE categories (
    id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY uq_categories_slug (slug)
) ENGINE=InnoDB;

CREATE TABLE colors (
    id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) NOT NULL,
    code CHAR(7) DEFAULT NULL,
    sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY uq_colors_slug (slug)
) ENGINE=InnoDB;

CREATE TABLE sizes (
    id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(10) NOT NULL,
    sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY uq_sizes_name (name)
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id TINYINT UNSIGNED NOT NULL,
    color_id TINYINT UNSIGNED DEFAULT NULL,
    title VARCHAR(150) NOT NULL,
    slug VARCHAR(160) NOT NULL,
    short_description VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_new TINYINT(1) NOT NULL DEFAULT 0,
    is_bestseller TINYINT(1) NOT NULL DEFAULT 0,
    is_sale TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_products_slug (slug),
    KEY idx_products_category_id (category_id),
    KEY idx_products_color_id (color_id),
    KEY idx_products_price (price),
    KEY idx_products_is_new (is_new),
    KEY idx_products_is_bestseller (is_bestseller),
    KEY idx_products_is_sale (is_sale),
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories (id)
        ON UPDATE CASCADE,
    CONSTRAINT fk_products_color
        FOREIGN KEY (color_id) REFERENCES colors (id)
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE product_sizes (
    product_id INT UNSIGNED NOT NULL,
    size_id TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (product_id, size_id),
    KEY idx_product_sizes_size_id (size_id),
    CONSTRAINT fk_product_sizes_product
        FOREIGN KEY (product_id) REFERENCES products (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_product_sizes_size
        FOREIGN KEY (size_id) REFERENCES sizes (id)
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE favorites (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_favorites_user_product (user_id, product_id),
    KEY idx_favorites_product_id (product_id),
    CONSTRAINT fk_favorites_user
        FOREIGN KEY (user_id) REFERENCES users (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_favorites_product
        FOREIGN KEY (product_id) REFERENCES products (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE cart_items (
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
) ENGINE=InnoDB;

CREATE TABLE newsletter_subscribers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_newsletter_email (email)
) ENGINE=InnoDB;

INSERT INTO users (name, email, phone, password_hash, created_at, updated_at) VALUES
    (
        'Марина Лебедева',
        'marina.lebedeva@yanki-demo.ru',
        '+7 (925) 410-22-18',
        '$2y$10$mMUnjpt0TqGHhfnpJ9yBn.zYU2Uepl8s6xLr7HMyki2VldSS6pGRq',
        '2026-04-12 12:15:00',
        '2026-04-19 09:40:00'
    );

INSERT INTO categories (name, slug, image_path, sort_order) VALUES
    ('Куртки', 'jackets', 'images/category-jackets.png', 1),
    ('Пальто', 'coats', 'images/category-coats.png', 2),
    ('Шубы', 'fur-coats', 'images/category-fur-coats.png', 3),
    ('Парки', 'parkas', 'images/category-parkas.png', 4);

INSERT INTO colors (name, slug, code, sort_order) VALUES
    ('Белый', 'white', '#f5f5f3', 1),
    ('Синий', 'blue', '#6e7d97', 2),
    ('Бежевый', 'beige', '#ddc7b1', 3),
    ('Молочный', 'milk', '#efe6de', 4);

INSERT INTO sizes (name, sort_order) VALUES
    ('XXS', 1),
    ('XS', 2),
    ('S', 3),
    ('M', 4),
    ('L', 5);

INSERT INTO products (
    category_id,
    color_id,
    title,
    slug,
    short_description,
    description,
    price,
    image_path,
    is_new,
    is_bestseller,
    is_sale,
    created_at
) VALUES
    (
        1,
        1,
        'Белая куртка',
        'white-jacket',
        'Утеплённая куртка с объёмным капюшоном и мягкой подкладкой.',
        'Повседневная модель для прохладной погоды: высокий воротник, регулируемая талия и вместительные карманы делают её удобной для города и поездок.',
        2900.00,
        'images/product-white-jacket.png',
        0,
        1,
        0,
        '2026-03-01 10:00:00'
    ),
    (
        2,
        2,
        'Синее пальто',
        'blue-coat',
        'Прямое пальто из фактурной ткани с акцентной фурнитурой.',
        'Лаконичный силуэт, плотная посадка по плечам и мягкая подкладка делают модель универсальной для делового и повседневного гардероба.',
        3150.00,
        'images/product-blue-coat.png',
        1,
        1,
        0,
        '2026-03-24 12:00:00'
    ),
    (
        3,
        3,
        'Бежевая шуба',
        'beige-fur-coat',
        'Шуба светлого оттенка с мягкой фактурой и свободным кроем.',
        'Модель держит форму, красиво садится поверх трикотажа и подходит для зимних образов с платьями, брюками и сапогами.',
        4200.00,
        'images/product-beige-fur-coat.png',
        0,
        1,
        0,
        '2026-02-20 09:30:00'
    ),
    (
        4,
        2,
        'Синяя парка',
        'blue-parka',
        'Практичная парка с капюшоном и контрастной внутренней отделкой.',
        'Удобная модель для активного дня: свободный силуэт, глубокие карманы и защита от ветра подходят для города и поездок.',
        2900.00,
        'images/product-blue-parka.png',
        0,
        0,
        1,
        '2026-02-15 15:00:00'
    ),
    (
        4,
        1,
        'Белая парка',
        'white-parka',
        'Светлая парка с акцентом на тёплый воротник и чистый силуэт.',
        'Модель подойдёт для прохладного межсезонья: лёгкий утеплитель, функциональная посадка и спокойный оттенок легко комбинируются с базовым гардеробом.',
        3050.00,
        'images/product-white-parka.png',
        1,
        0,
        0,
        '2026-03-27 11:00:00'
    ),
    (
        3,
        4,
        'Молочная шуба',
        'milk-fur-coat',
        'Молочная шуба с объёмным воротником и мягким ворсом.',
        'Нежный оттенок и прямой крой делают эту модель универсальной для вечерних выходов и повседневных комплектов в холодный сезон.',
        4300.00,
        'images/product-milk-fur-coat.png',
        1,
        0,
        0,
        '2026-03-29 13:00:00'
    );

INSERT INTO product_sizes (product_id, size_id) VALUES
    (1, 1), (1, 2), (1, 3), (1, 4), (1, 5),
    (2, 2), (2, 3), (2, 4), (2, 5),
    (3, 2), (3, 3), (3, 4),
    (4, 1), (4, 2), (4, 3),
    (5, 2), (5, 3), (5, 4),
    (6, 3), (6, 4), (6, 5);

INSERT INTO favorites (user_id, product_id, created_at) VALUES
    (1, 2, '2026-04-19 18:20:00'),
    (1, 5, '2026-04-20 10:05:00');

INSERT INTO cart_items (user_id, product_id, quantity, created_at, updated_at) VALUES
    (1, 1, 1, '2026-04-20 10:15:00', '2026-04-20 10:15:00'),
    (1, 4, 2, '2026-04-20 10:18:00', '2026-04-20 10:18:00');

INSERT INTO newsletter_subscribers (email, is_active, created_at, updated_at) VALUES
    ('m.belova@yanki-demo.ru', 1, '2026-04-10 09:00:00', '2026-04-10 09:00:00'),
    ('shopper.olga@yanki-demo.ru', 1, '2026-04-14 14:30:00', '2026-04-14 14:30:00'),
    ('client.alina@yanki-demo.ru', 1, '2026-04-18 18:45:00', '2026-04-18 18:45:00');
