-- Automatyczne tworzenie bazy przez Docker, ale możesz dodać:
CREATE DATABASE IF NOT EXISTS sklep_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sklep_laravel;

-- Widoki dla raportów
CREATE OR REPLACE VIEW vw_sales_summary AS
SELECT 
    DATE(created_at) as sale_date,
    COUNT(*) as total_orders,
    SUM(total_amount) as total_revenue,
    AVG(total_amount) as avg_order_value
FROM orders
WHERE status != 'cancelled'
GROUP BY DATE(created_at);

-- Trigger dla automatycznej aktualizacji stanu magazynowego
DELIMITER $$
CREATE TRIGGER tr_order_stock_update 
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status = 'completed' AND OLD.status != 'completed' THEN
        -- Logika aktualizacji już jest w modelu Order
        -- Ten trigger może służyć jako backup
        INSERT INTO activity_logs (user_id, action, description, created_at)
        VALUES (NEW.user_id, 'order_completed', CONCAT('Order #', NEW.id, ' completed'), NOW());
    END IF;
END$$
DELIMITER ;