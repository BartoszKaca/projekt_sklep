-- ============================================================
-- Database Initialization Script for Rap Shop
-- ============================================================
-- This script creates views, triggers, procedures, and indexes
-- for the shop database to improve reporting and performance.
-- ============================================================

-- ============================================================
-- VIEWS
-- ============================================================

-- View: Sales Summary
-- Provides a summary of sales by date, including order count and total revenue
DROP VIEW IF EXISTS vw_sales_summary;
CREATE VIEW vw_sales_summary AS
SELECT 
    DATE(orders.created_at) AS order_date,
    COUNT(orders.id) AS order_count,
    SUM(orders.subtotal) AS subtotal_sum,
    SUM(orders.shipping_cost) AS shipping_sum,
    SUM(orders.discount) AS discount_sum,
    SUM(orders.total) AS total_revenue,
    AVG(orders.total) AS avg_order_value
FROM orders
WHERE orders.deleted_at IS NULL
    AND orders.payment_status = 'paid'
GROUP BY DATE(orders.created_at)
ORDER BY order_date DESC;

-- View: Product Stock Status
-- Shows current stock levels and identifies low stock products
DROP VIEW IF EXISTS vw_product_stock_status;
CREATE VIEW vw_product_stock_status AS
SELECT 
    p.id,
    p.name,
    p.sku,
    p.stock_quantity,
    p.low_stock_threshold,
    CASE 
        WHEN p.stock_quantity = 0 THEN 'out_of_stock'
        WHEN p.stock_quantity <= p.low_stock_threshold THEN 'low_stock'
        ELSE 'in_stock'
    END AS stock_status,
    c.name AS category_name,
    p.price,
    p.discount_price,
    (SELECT SUM(oi.quantity) 
     FROM order_items oi 
     JOIN orders o ON oi.order_id = o.id 
     WHERE oi.product_id = p.id 
       AND o.deleted_at IS NULL 
       AND o.payment_status = 'paid'
       AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ) AS sold_last_30_days
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.deleted_at IS NULL
ORDER BY p.stock_quantity ASC;

-- View: Customer Orders
-- Provides detailed view of customer orders with shipping information
DROP VIEW IF EXISTS vw_customer_orders;
CREATE VIEW vw_customer_orders AS
SELECT 
    o.id AS order_id,
    o.order_number,
    o.created_at AS order_date,
    o.status,
    o.payment_status,
    o.payment_method,
    o.subtotal,
    o.shipping_cost,
    o.discount,
    o.total,
    u.id AS customer_id,
    u.name AS customer_name,
    u.email AS customer_email,
    os.first_name AS shipping_first_name,
    os.last_name AS shipping_last_name,
    os.street_address AS shipping_address,
    os.city AS shipping_city,
    os.postal_code AS shipping_postal_code,
    os.country AS shipping_country,
    os.phone AS shipping_phone,
    (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count
FROM orders o
LEFT JOIN users u ON o.user_id = u.id
LEFT JOIN order_shipping os ON o.id = os.order_id
WHERE o.deleted_at IS NULL
ORDER BY o.created_at DESC;

-- View: Best Selling Products
DROP VIEW IF EXISTS vw_best_selling_products;
CREATE VIEW vw_best_selling_products AS
SELECT 
    p.id,
    p.name,
    p.slug,
    p.price,
    p.discount_price,
    c.name AS category_name,
    SUM(oi.quantity) AS total_sold,
    SUM(oi.total) AS total_revenue,
    COUNT(DISTINCT o.id) AS order_count
FROM products p
JOIN order_items oi ON p.id = oi.product_id
JOIN orders o ON oi.order_id = o.id
LEFT JOIN categories c ON p.category_id = c.id
WHERE o.deleted_at IS NULL
    AND o.payment_status = 'paid'
    AND p.deleted_at IS NULL
GROUP BY p.id, p.name, p.slug, p.price, p.discount_price, c.name
ORDER BY total_sold DESC;

-- ============================================================
-- TRIGGERS
-- ============================================================

-- Note: Triggers are commented out as they may require specific MySQL version
-- and permissions. Uncomment and adjust as needed for your environment.

-- Trigger: Auto-update stock on order completion
-- This trigger decreases stock when order status changes to 'processing'
/*
DROP TRIGGER IF EXISTS tr_order_stock_update;
DELIMITER //
CREATE TRIGGER tr_order_stock_update
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status = 'processing' AND OLD.status = 'pending' THEN
        UPDATE products p
        JOIN order_items oi ON p.id = oi.product_id
        SET p.stock_quantity = p.stock_quantity - oi.quantity
        WHERE oi.order_id = NEW.id;
    END IF;
    
    IF NEW.status = 'cancelled' AND OLD.status != 'cancelled' THEN
        UPDATE products p
        JOIN order_items oi ON p.id = oi.product_id
        SET p.stock_quantity = p.stock_quantity + oi.quantity
        WHERE oi.order_id = NEW.id;
    END IF;
END //
DELIMITER ;
*/

-- Trigger: Log price changes
-- Creates a log entry when product prices are modified
/*
DROP TRIGGER IF EXISTS tr_log_price_changes;
DELIMITER //
CREATE TRIGGER tr_log_price_changes
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    IF OLD.price != NEW.price OR OLD.discount_price != NEW.discount_price THEN
        INSERT INTO activity_logs (
            log_type, 
            description, 
            model_type, 
            model_id, 
            created_at, 
            updated_at
        ) VALUES (
            'price_change',
            CONCAT('Price changed from ', OLD.price, ' to ', NEW.price, 
                   '. Discount price from ', IFNULL(OLD.discount_price, 'NULL'), 
                   ' to ', IFNULL(NEW.discount_price, 'NULL')),
            'Product',
            NEW.id,
            NOW(),
            NOW()
        );
    END IF;
END //
DELIMITER ;
*/

-- ============================================================
-- STORED PROCEDURES
-- ============================================================

-- Procedure: Calculate Order Total
-- Recalculates and updates the total for a given order
DROP PROCEDURE IF EXISTS sp_calculate_order_total;
DELIMITER //
CREATE PROCEDURE sp_calculate_order_total(IN p_order_id INT)
BEGIN
    DECLARE v_subtotal DECIMAL(10,2);
    DECLARE v_shipping DECIMAL(10,2);
    DECLARE v_discount DECIMAL(10,2);
    DECLARE v_tax DECIMAL(10,2);
    DECLARE v_total DECIMAL(10,2);
    
    -- Calculate subtotal from order items
    SELECT COALESCE(SUM(total), 0) INTO v_subtotal
    FROM order_items
    WHERE order_id = p_order_id;
    
    -- Get shipping and discount from order
    SELECT shipping_cost, discount, tax 
    INTO v_shipping, v_discount, v_tax
    FROM orders
    WHERE id = p_order_id;
    
    -- Calculate total
    SET v_total = v_subtotal + COALESCE(v_shipping, 0) + COALESCE(v_tax, 0) - COALESCE(v_discount, 0);
    
    -- Update order
    UPDATE orders
    SET subtotal = v_subtotal,
        total = v_total
    WHERE id = p_order_id;
    
    SELECT v_subtotal AS subtotal, v_shipping AS shipping, v_discount AS discount, v_total AS total;
END //
DELIMITER ;

-- Procedure: Get Sales Report for Date Range
DROP PROCEDURE IF EXISTS sp_sales_report;
DELIMITER //
CREATE PROCEDURE sp_sales_report(
    IN p_start_date DATE,
    IN p_end_date DATE
)
BEGIN
    SELECT 
        DATE(created_at) AS sale_date,
        COUNT(*) AS order_count,
        SUM(total) AS total_sales,
        SUM(discount) AS total_discounts,
        AVG(total) AS avg_order_value
    FROM orders
    WHERE deleted_at IS NULL
        AND payment_status = 'paid'
        AND DATE(created_at) BETWEEN p_start_date AND p_end_date
    GROUP BY DATE(created_at)
    ORDER BY sale_date;
END //
DELIMITER ;

-- ============================================================
-- INDEXES
-- ============================================================

-- Note: Some indexes may already exist. Errors will be ignored.

-- Products indexes for better search and filtering performance
CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_products_active ON products(is_active);
CREATE INDEX IF NOT EXISTS idx_products_featured ON products(is_featured);
CREATE INDEX IF NOT EXISTS idx_products_price ON products(price);
CREATE INDEX IF NOT EXISTS idx_products_type ON products(type);
CREATE INDEX IF NOT EXISTS idx_products_slug ON products(slug);

-- Orders indexes
CREATE INDEX IF NOT EXISTS idx_orders_user ON orders(user_id);
CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status);
CREATE INDEX IF NOT EXISTS idx_orders_payment_status ON orders(payment_status);
CREATE INDEX IF NOT EXISTS idx_orders_created_at ON orders(created_at);
CREATE INDEX IF NOT EXISTS idx_orders_order_number ON orders(order_number);

-- Order items indexes
CREATE INDEX IF NOT EXISTS idx_order_items_order ON order_items(order_id);
CREATE INDEX IF NOT EXISTS idx_order_items_product ON order_items(product_id);

-- Users indexes
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);

-- Categories indexes
CREATE INDEX IF NOT EXISTS idx_categories_slug ON categories(slug);
CREATE INDEX IF NOT EXISTS idx_categories_active ON categories(is_active);

-- Stock movements indexes
CREATE INDEX IF NOT EXISTS idx_stock_movements_product ON stock_movements(product_id);
CREATE INDEX IF NOT EXISTS idx_stock_movements_type ON stock_movements(type);

-- Wishlist indexes
CREATE INDEX IF NOT EXISTS idx_wishlist_user ON wishlists(user_id);
CREATE INDEX IF NOT EXISTS idx_wishlist_product ON wishlists(product_id);

-- Newsletter subscribers indexes
CREATE INDEX IF NOT EXISTS idx_newsletter_email ON newsletter_subscribers(email);
CREATE INDEX IF NOT EXISTS idx_newsletter_active ON newsletter_subscribers(is_active);
