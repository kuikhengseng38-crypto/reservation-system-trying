-- 创建数据库
CREATE DATABASE IF NOT EXISTS restaurant;
USE restaurant;

-- ================== tables 表 ==================
CREATE TABLE IF NOT EXISTS tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number INT UNIQUE,
    status ENUM('available','reserved') DEFAULT 'available'
);

-- 初始化 20 张桌子（只插入一次，如果表为空）
INSERT INTO tables(table_number)
SELECT n FROM (
    SELECT 1 n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
    UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
    UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15
    UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20
) t
WHERE NOT EXISTS (SELECT 1 FROM tables);

-- ================== reservations 表 ==================
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_id INT,
    customer_name VARCHAR(100),
    reserve_date DATE,
    start_time TIME,
    end_time TIME,
    status ENUM('active','cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================== orders 表 ==================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT,
    total DECIMAL(10,2) DEFAULT 0,
    status ENUM('pending','paid') DEFAULT 'pending'
);

-- ================== menu 表（原 order_items 改成菜单表） ==================
CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,        -- 库存数量，可选
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 示例插入菜单
INSERT INTO menu(name, description, price, stock) VALUES
('Fried Rice', 'Delicious fried rice with vegetables', 12.50, 50),
('Chicken Burger', 'Grilled chicken burger with cheese', 15.00, 30),
('Spaghetti Bolognese', 'Classic Italian pasta', 18.00, 25),
('Coke', 'Soft drink 330ml', 4.50, 100),
('Orange Juice', 'Freshly squeezed orange juice', 6.00, 50);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    qty INT NOT NULL
);

