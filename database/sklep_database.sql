-- =====================================================
-- SKRYPT TWORZENIA BAZY DANYCH SKLEPU RAP SHOP
-- =====================================================
-- Uruchom ten skrypt w MySQL aby utworzyć bazę danych
-- i wprowadzić przykładowe dane
-- =====================================================

-- Tworzenie bazy danych
CREATE DATABASE IF NOT EXISTS sklep_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sklep_laravel;

-- =====================================================
-- TABELE
-- =====================================================

-- Tabela użytkowników
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    phone VARCHAR(20) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

-- Tabela resetowania hasła
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
) ENGINE=InnoDB;

-- Tabela sesji
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX sessions_user_id_index (user_id),
    INDEX sessions_last_activity_index (last_activity)
) ENGINE=InnoDB;

-- Tabela kategorii
CREATE TABLE IF NOT EXISTS categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

-- Tabela produktów
CREATE TABLE IF NOT EXISTS products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    type ENUM('album', 'merch') DEFAULT 'album',
    price DECIMAL(10, 2) NOT NULL,
    discount_price DECIMAL(10, 2) NULL,
    artist VARCHAR(255) NULL,
    release_year INT NULL,
    format VARCHAR(50) NULL,
    label VARCHAR(255) NULL,
    stock_quantity INT DEFAULT 0,
    low_stock_threshold INT DEFAULT 5,
    sku VARCHAR(50) UNIQUE NOT NULL,
    barcode VARCHAR(50) NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    views_count INT DEFAULT 0,
    weight DECIMAL(8, 2) NULL,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela zdjęć produktów
CREATE TABLE IF NOT EXISTS product_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255) NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela wariantów produktów
CREATE TABLE IF NOT EXISTS product_variants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    size VARCHAR(10) NULL,
    color VARCHAR(50) NULL,
    price_modifier DECIMAL(10, 2) DEFAULT 0,
    stock_quantity INT DEFAULT 0,
    sku VARCHAR(50) UNIQUE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela adresów
CREATE TABLE IF NOT EXISTS addresses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    street_address VARCHAR(255) NOT NULL,
    apartment VARCHAR(100) NULL,
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NULL,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela zamówień
CREATE TABLE IF NOT EXISTS orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
    subtotal DECIMAL(10, 2) NOT NULL,
    shipping_cost DECIMAL(10, 2) DEFAULT 0,
    tax DECIMAL(10, 2) DEFAULT 0,
    discount DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    coupon_code VARCHAR(50) NULL,
    payment_method VARCHAR(50) NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    customer_notes TEXT NULL,
    admin_notes TEXT NULL,
    tracking_number VARCHAR(100) NULL,
    carrier VARCHAR(100) NULL,
    paid_at TIMESTAMP NULL,
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabela wysyłki zamówienia
CREATE TABLE IF NOT EXISTS order_shipping (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    street_address VARCHAR(255) NOT NULL,
    apartment VARCHAR(100) NULL,
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela pozycji zamówienia
CREATE TABLE IF NOT EXISTS order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NULL,
    product_variant_id BIGINT UNSIGNED NULL,
    product_name VARCHAR(255) NOT NULL,
    variant_name VARCHAR(255) NULL,
    sku VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabela ruchów magazynowych
CREATE TABLE IF NOT EXISTS stock_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NULL,
    product_variant_id BIGINT UNSIGNED NULL,
    order_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NULL,
    type ENUM('in', 'out', 'adjustment') NOT NULL,
    quantity INT NOT NULL,
    stock_before INT NOT NULL,
    stock_after INT NOT NULL,
    reason TEXT NULL,
    reference VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON DELETE SET NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabela recenzji
CREATE TABLE IF NOT EXISTS reviews (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(255) NULL,
    content TEXT NULL,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela kuponów
CREATE TABLE IF NOT EXISTS coupons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    type ENUM('percentage', 'fixed') NOT NULL,
    value DECIMAL(10, 2) NOT NULL,
    min_order_value DECIMAL(10, 2) NULL,
    usage_limit INT NULL,
    usage_count INT DEFAULT 0,
    valid_from DATE NULL,
    valid_until DATE NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

-- Tabela ulubionych
CREATE TABLE IF NOT EXISTS wishlists (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_wishlist (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabela subskrypcji newslettera
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

-- Tabela logów aktywności
CREATE TABLE IF NOT EXISTS activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(255) NOT NULL,
    model_type VARCHAR(255) NULL,
    model_id BIGINT UNSIGNED NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================
-- WIDOKI
-- =====================================================

-- Widok produktów z niskim stanem magazynowym
CREATE OR REPLACE VIEW v_low_stock_products AS
SELECT 
    p.id,
    p.name,
    p.sku,
    p.stock_quantity,
    p.low_stock_threshold,
    c.name AS category_name
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.stock_quantity <= p.low_stock_threshold
AND p.deleted_at IS NULL
AND p.is_active = TRUE;

-- Widok podsumowania sprzedaży
CREATE OR REPLACE VIEW v_sales_summary AS
SELECT 
    DATE(o.created_at) AS date,
    COUNT(*) AS order_count,
    SUM(o.total) AS total_revenue,
    AVG(o.total) AS avg_order_value
FROM orders o
WHERE o.payment_status = 'paid'
AND o.deleted_at IS NULL
GROUP BY DATE(o.created_at);

-- Widok najlepiej sprzedających się produktów
CREATE OR REPLACE VIEW v_top_selling_products AS
SELECT 
    p.id,
    p.name,
    p.sku,
    p.artist,
    SUM(oi.quantity) AS total_sold,
    SUM(oi.total) AS total_revenue
FROM products p
JOIN order_items oi ON p.id = oi.product_id
JOIN orders o ON oi.order_id = o.id
WHERE o.payment_status = 'paid'
GROUP BY p.id, p.name, p.sku, p.artist
ORDER BY total_sold DESC;

-- =====================================================
-- WYZWALACZE
-- =====================================================

DELIMITER //

-- Wyzwalacz aktualizacji stanu magazynowego po zamówieniu
CREATE TRIGGER tr_after_order_item_insert
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    IF NEW.product_id IS NOT NULL THEN
        UPDATE products 
        SET stock_quantity = stock_quantity - NEW.quantity
        WHERE id = NEW.product_id;
        
        INSERT INTO stock_movements (
            product_id, 
            order_id, 
            type, 
            quantity, 
            stock_before, 
            stock_after, 
            reason, 
            created_at
        )
        SELECT 
            NEW.product_id,
            NEW.order_id,
            'out',
            NEW.quantity,
            stock_quantity + NEW.quantity,
            stock_quantity,
            CONCAT('Zamówienie #', (SELECT order_number FROM orders WHERE id = NEW.order_id)),
            NOW()
        FROM products WHERE id = NEW.product_id;
    END IF;
END//

-- Wyzwalacz logowania zmian statusu zamówienia
CREATE TRIGGER tr_after_order_update
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO activity_logs (
            action, 
            model_type, 
            model_id, 
            old_values, 
            new_values, 
            created_at
        )
        VALUES (
            'order_status_changed',
            'Order',
            NEW.id,
            JSON_OBJECT('status', OLD.status),
            JSON_OBJECT('status', NEW.status),
            NOW()
        );
    END IF;
END//

DELIMITER ;

-- =====================================================
-- DANE TESTOWE
-- =====================================================

-- Użytkownicy
INSERT INTO users (name, email, password, role, phone, is_active, created_at, updated_at) VALUES
('Admin', 'admin@rapshop.pl', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '+48123456789', TRUE, NOW(), NOW()),
('Jan Kowalski', 'jan@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '+48987654321', TRUE, NOW(), NOW()),
('Anna Nowak', 'anna@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '+48555666777', TRUE, NOW(), NOW());
-- Hasło dla wszystkich: password

-- Kategorie
INSERT INTO categories (name, slug, description, is_active, created_at, updated_at) VALUES
('Płyty CD', 'plyty-cd', 'Albumy rapowe w formacie CD', TRUE, NOW(), NOW()),
('Winyle', 'winyle', 'Klasyczne albumy hip-hopowe na winylu', TRUE, NOW(), NOW()),
('Kasety', 'kasety', 'Limitowane wydania na kasetach', TRUE, NOW(), NOW()),
('Koszulki', 'koszulki', 'Odzież rapowa - koszulki z nadrukami', TRUE, NOW(), NOW()),
('Bluzy', 'bluzy', 'Bluzy z kapturem i bez', TRUE, NOW(), NOW()),
('Czapki', 'czapki', 'Czapki snapback i beanie', TRUE, NOW(), NOW()),
('Akcesoria', 'akcesoria', 'Plecaki, torby, biżuteria', TRUE, NOW(), NOW());

-- Produkty - Płyty CD
INSERT INTO products (category_id, name, slug, description, type, price, discount_price, artist, release_year, format, label, stock_quantity, low_stock_threshold, sku, is_featured, is_active, created_at, updated_at) VALUES
(1, 'Taco Hemingway - Café Belga', 'taco-hemingway-cafe-belga', 'Kultowy album Taco Hemingwaya z 2015 roku. Zawiera hity takie jak "Białkoholicy" i "Wosk".', 'album', 39.99, NULL, 'Taco Hemingway', 2015, 'CD', 'Antena Krzyku', 50, 10, 'CD-TACO-001', TRUE, TRUE, NOW(), NOW()),
(1, 'Quebonafide - Romantic Psycho', 'quebonafide-romantic-psycho', 'Przełomowy album Quebonafide z 2017 roku. Platynowa płyta z hitami jak "Candy" i "Tamagotchi".', 'album', 44.99, 34.99, 'Quebonafide', 2017, 'CD', 'QueQuality', 3, 5, 'CD-QUEBO-001', TRUE, TRUE, NOW(), NOW()),
(1, 'Sokół - 100 Barów 2.0', 'sokol-100-barow-20', 'Legendarny album jednego z najważniejszych raperów w Polsce.', 'album', 42.99, NULL, 'Sokół', 2014, 'CD', 'Asfalt Records', 25, 10, 'CD-SOKOL-001', FALSE, TRUE, NOW(), NOW());

-- Produkty - Winyle
INSERT INTO products (category_id, name, slug, description, type, price, artist, release_year, format, label, stock_quantity, low_stock_threshold, sku, is_featured, is_active, created_at, updated_at) VALUES
(2, 'O.S.T.R. - Tylko Dla Dorosłych (Vinyl)', 'ostr-tylko-dla-doroslych-vinyl', 'Klasyczny album OSTR-a w limitowanej edycji winylowej.', 'album', 89.99, 'O.S.T.R.', 2010, 'Vinyl', 'Asfalt Records', 15, 5, 'VIN-OSTR-001', TRUE, TRUE, NOW(), NOW());

-- Produkty - Merch
INSERT INTO products (category_id, name, slug, description, type, price, format, stock_quantity, low_stock_threshold, sku, is_featured, is_active, weight, created_at, updated_at) VALUES
(4, 'Koszulka "Polish Hip-Hop"', 'koszulka-polish-hip-hop', 'Premium koszulka z nadrukiem Polish Hip-Hop. 100% bawełna, wysokiej jakości nadruk.', 'merch', 79.99, 'Clothing', 0, 5, 'TSH-PHH-001', TRUE, TRUE, 0.2, NOW(), NOW()),
(5, 'Bluza Oversize "Underground"', 'bluza-oversize-underground', 'Ciepła bluza z kapturem, oversize fit. Idealny streetwear.', 'merch', 159.99, 'Clothing', 0, 3, 'HDD-UND-001', TRUE, TRUE, 0.6, NOW(), NOW()),
(6, 'Czapka Snapback "Rap PL"', 'czapka-snapback-rap-pl', 'Klasyczna czapka snapback z haftowanym logo.', 'merch', 59.99, 'Accessories', 45, 10, 'CAP-RPL-001', FALSE, TRUE, 0.15, NOW(), NOW());

-- Warianty produktów (rozmiary koszulek i bluz)
INSERT INTO product_variants (product_id, name, size, color, price_modifier, stock_quantity, sku, is_active, created_at, updated_at) VALUES
(5, 'Rozmiar S', 'S', 'Czarny', 0, 8, 'TSH-PHH-001-S', TRUE, NOW(), NOW()),
(5, 'Rozmiar M', 'M', 'Czarny', 0, 15, 'TSH-PHH-001-M', TRUE, NOW(), NOW()),
(5, 'Rozmiar L', 'L', 'Czarny', 0, 20, 'TSH-PHH-001-L', TRUE, NOW(), NOW()),
(5, 'Rozmiar XL', 'XL', 'Czarny', 0, 12, 'TSH-PHH-001-XL', TRUE, NOW(), NOW()),
(5, 'Rozmiar XXL', 'XXL', 'Czarny', 0, 5, 'TSH-PHH-001-XXL', TRUE, NOW(), NOW()),
(6, 'Rozmiar S', 'S', 'Czarny', 0, 10, 'HDD-UND-001-S', TRUE, NOW(), NOW()),
(6, 'Rozmiar M', 'M', 'Czarny', 0, 12, 'HDD-UND-001-M', TRUE, NOW(), NOW()),
(6, 'Rozmiar L', 'L', 'Czarny', 0, 8, 'HDD-UND-001-L', TRUE, NOW(), NOW()),
(6, 'Rozmiar XL', 'XL', 'Czarny', 0, 6, 'HDD-UND-001-XL', TRUE, NOW(), NOW());

-- Kupony
INSERT INTO coupons (code, type, value, min_order_value, usage_limit, is_active, valid_from, valid_until, created_at, updated_at) VALUES
('RABAT10', 'percentage', 10.00, 50.00, 100, TRUE, '2024-01-01', '2025-12-31', NOW(), NOW()),
('PIERWSZYZAKUP', 'fixed', 15.00, 30.00, NULL, TRUE, '2024-01-01', '2025-12-31', NOW(), NOW()),
('DARMOWADOSTAWA', 'fixed', 14.99, 100.00, 50, TRUE, '2024-01-01', '2025-12-31', NOW(), NOW());

-- =====================================================
-- INFORMACJE
-- =====================================================
-- Dane dostępowe:
-- Admin: admin@rapshop.pl / password
-- Klient: jan@example.com / password
-- Klient: anna@example.com / password
-- =====================================================
