drop database if exists webtech_fall2024_madiba_quansah;
-- drop table if exists feedback;
-- drop table if exists payment;
-- drop table if exists order_details;
-- drop table if exists `order`;
-- drop table if exists menu_item_inventory;
-- drop table if exists inventory;
-- drop table if exists menu_item;
-- drop table if exists staff;
-- drop table if exists reservations;
-- drop table if exists customer;
-- drop table if exists `table`;
-- drop table if exists admin;
CREATE DATABASE if not exists webtech_fall2024_madiba_quansah;

USE webtech_fall2024_madiba_quansah;

CREATE TABLE if not exists `table` (
  table_id int AUTO_INCREMENT PRIMARY KEY,
  table_number int NOT NULL,
  seating_capacity int NOT NULL,
  location varchar(40),
  CHECK (seating_capacity > 0)
);

CREATE table if not exists customer (
  customer_id int AUTO_INCREMENT PRIMARY KEY,
  first_name varchar(40) NOT NULL,
  last_name varchar(40) NOT NULL,
  email varchar(40) NOT NULL,
  passhash varchar(255) not null,
  CHECK (
    email REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$'
  )
);

CREATE table if not exists reservations (
  reservation_id int AUTO_INCREMENT PRIMARY KEY,
  customer_id int NOT NULL,
  table_id int NOT NULL,
  reservation_datetime datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  number_of_guests int NOT NULL DEFAULT 1,
  special_requests varchar(255),
  FOREIGN KEY (customer_id) REFERENCES customer (customer_id),
  FOREIGN KEY (table_id) REFERENCES `table` (table_id),
  CHECK (number_of_guests > 0),
  UNIQUE (table_id, reservation_datetime)
);

CREATE table if not exists staff (
  staff_id int AUTO_INCREMENT PRIMARY KEY,
  first_name varchar(40) NOT NULL,
  last_name varchar(40) NOT NULL,
  `position` varchar(40) NOT NULL,
  email varchar(40) NOT NULL,
  hire_date date NOT NULL DEFAULT (CURRENT_DATE()),
  salary decimal(10, 4) NOT NULL,
  passhash varchar(255) not null,
  CHECK (
    email REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$'
  ),
  CHECK (salary > 0)
);

CREATE table if not exists menu_item (
  menu_item_id int AUTO_INCREMENT PRIMARY KEY,
  name varchar(40) NOT NULL,
  description varchar(40) NOT NULL,
  price double(10, 2) NOT NULL,
  category varchar(40),
  availability_status bool,
  CHECK (price > 0)
);

CREATE table if not exists inventory (
  inventory_id int AUTO_INCREMENT PRIMARY KEY,
  item_name varchar(40) NOT NULL,
  quantity int NOT NULL,
  reorder_level int NOT NULL,
  CHECK (
    quantity >= 0
    AND reorder_level >= 0
  )
);

CREATE table if not exists menu_item_inventory (
  menu_item_id int NOT NULL,
  inventory_id int NOT NULL,
  quantity_used int NOT NULL,
  PRIMARY KEY (menu_item_id, inventory_id),
  FOREIGN KEY (menu_item_id) REFERENCES menu_item (menu_item_id) on delete cascade,
  FOREIGN KEY (inventory_id) REFERENCES inventory (inventory_id) on delete cascade,
  CHECK (quantity_used >= 0)
);

CREATE table if not exists `order` (
  order_id int AUTO_INCREMENT PRIMARY KEY,
  customer_id int NOT NULL,
  staff_id int,
  order_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  total_amount decimal(10, 2),
  status varchar(20) NOT NULL DEFAULT 'Pending',
  FOREIGN KEY (customer_id) REFERENCES customer (customer_id),
  FOREIGN KEY (staff_id) REFERENCES staff (staff_id),
  CHECK (total_amount >= 0),
  CHECK (status IN ('Completed', 'Pending', 'Cancelled'))
);

CREATE table if not exists order_details (
  order_detail_id int AUTO_INCREMENT PRIMARY KEY,
  order_id int NOT NULL,
  menu_item_id int NOT NULL,
  quantity int NOT NULL,
  FOREIGN KEY (order_id) REFERENCES `order` (order_id) on delete cascade,
  FOREIGN KEY (menu_item_id) REFERENCES menu_item (menu_item_id)
);

CREATE table if not exists payment (
  payment_id int AUTO_INCREMENT PRIMARY KEY,
  order_id int NOT NULL,
  payment_method varchar(40),
  payment_time datetime DEFAULT (CURRENT_TIMESTAMP()),
  amount decimal(10, 2) NOT NULL,
  `status` varchar(40),
  FOREIGN KEY (order_id) REFERENCES `order` (order_id),
  CHECK (`status` IN ('Completed', 'Pending', 'Cancelled'))
);

CREATE table if not exists feedback (
  feedback_id int AUTO_INCREMENT PRIMARY KEY,
  customer_id int NOT NULL,
  order_id int NOT NULL,
  rating int NOT NULL,
  comments varchar(255),
  feedback_date date NOT NULL DEFAULT (current_date()),
  FOREIGN KEY (customer_id) REFERENCES customer (customer_id),
  FOREIGN KEY (order_id) REFERENCES `order` (order_id),
  CHECK (
    rating >= 1
    AND rating <= 5
  )
);

create table if not exists admin (
  admin_id int AUTO_INCREMENT PRIMARY KEY,
  username varchar(100) not null,
  passhash varchar(255) not null
);


DELIMITER //

-- inventory update procedure
CREATE PROCEDURE update_inventory(
    IN p_order_detail_id INT,
    IN p_menu_item_id INT,
    IN p_order_quantity INT,
    IN p_action VARCHAR(10),
    OUT p_error BOOLEAN
)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE inv_id INT;
    DECLARE qty_used INT;
    DECLARE cur CURSOR FOR 
        SELECT inventory_id, quantity_used
        FROM menu_item_inventory
        WHERE menu_item_id = p_menu_item_id;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        SET p_error = TRUE;
    END;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO inv_id, qty_used;
        IF done THEN
            LEAVE read_loop;
        END IF;

        IF p_action = 'REDUCE' THEN
            UPDATE inventory
            SET quantity = quantity - (qty_used * p_order_quantity)
            WHERE inventory_id = inv_id;

            -- Check if inventory goes below zero
            IF (SELECT quantity FROM inventory WHERE inventory_id = inv_id) < 0 THEN
                SET p_error = TRUE;
                LEAVE read_loop;
            END IF;
        ELSEIF p_action = 'INCREASE' THEN
            UPDATE inventory
            SET quantity = quantity + (qty_used * p_order_quantity)
            WHERE inventory_id = inv_id;
        END IF;
    END LOOP;

    CLOSE cur;

END //

-- Trigger for order_details INSERT operations
CREATE TRIGGER reduce_inventory_on_order_insert
AFTER INSERT ON order_details
FOR EACH ROW
BEGIN
    DECLARE error_occurred BOOLEAN DEFAULT FALSE;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET error_occurred = TRUE;
    
    SAVEPOINT before_inventory_update;
    
    CALL update_inventory(NEW.order_detail_id, NEW.menu_item_id, NEW.quantity, 'REDUCE', error_occurred);
    
    IF error_occurred THEN
        ROLLBACK TO SAVEPOINT before_inventory_update;
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Inventory update failed';
    END IF;
    
    RELEASE SAVEPOINT before_inventory_update;
END //

-- Trigger for order_details DELETE operations
CREATE TRIGGER increase_inventory_on_order_delete
AFTER DELETE ON order_details
FOR EACH ROW
BEGIN
    DECLARE error_occurred BOOLEAN DEFAULT FALSE;
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET error_occurred = TRUE;
    
    SAVEPOINT before_inventory_update;
    
    CALL update_inventory(OLD.order_detail_id, OLD.menu_item_id, OLD.quantity, 'INCREASE', error_occurred);
    
    IF error_occurred THEN
        ROLLBACK TO SAVEPOINT before_inventory_update;
    END IF;
    
    RELEASE SAVEPOINT before_inventory_update;
END //

DELIMITER ;

DELIMITER //

-- Trigger for order INSERT operation to calculate total 
-- amount
CREATE TRIGGER calculate_order_total_insert
AFTER INSERT ON order_details
FOR EACH ROW
BEGIN
    CALL update_order_total(NEW.order_id);
END //

-- Trigger for UPDATE operations
CREATE TRIGGER calculate_order_total_update
AFTER UPDATE ON order_details
FOR EACH ROW
BEGIN
    CALL update_order_total(NEW.order_id);
END //

-- Stored procedure to update the order total
CREATE PROCEDURE update_order_total(IN p_order_id INT)
BEGIN
    DECLARE order_total DECIMAL(10, 2);

    -- Calculate the total for the entire order
    SELECT COALESCE(SUM(mi.price * od.quantity), 0) INTO order_total
    FROM order_details od
    JOIN menu_item mi ON od.menu_item_id = mi.menu_item_id
    WHERE od.order_id = p_order_id;

    -- Update the total_amount in the order table
    UPDATE `order`
    SET total_amount = order_total
    WHERE order_id = p_order_id;
END //

DELIMITER ;

-- -- 1. `table`
INSERT INTO
  `table` (table_number, seating_capacity, location)
VALUES
  (1, 4, 'Patio'),
  (2, 2, 'Near Window'),
  (3, 6, 'Main Hall'),
  (4, 8, 'VIP Lounge'),
  (5, 10, 'Banquet Hall'),
  (6, 4, 'Garden'),
  (7, 2, 'Bar Area'),
  (8, 6, 'Balcony'),
  (9, 8, 'Private Room'),
  (10, 12, 'Conference Hall');

-- 4. `menu_item`
INSERT INTO
  menu_item (
    name,
    description,
    price,
    category,
    availability_status
  )
VALUES
  (
    'Margherita Pizza',
    'Tomato and mozzarella',
    12.99,
    'Main Course',
    TRUE
  ),
  (
    'Caesar Salad',
    'Lettuce with Caesar dressing',
    8.50,
    'Appetizer',
    TRUE
  ),
  (
    'Chocolate Cake',
    'Rich dessert',
    6.99,
    'Dessert',
    TRUE
  ),
  (
    'Steak',
    'Grilled to perfection',
    25.99,
    'Main Course',
    TRUE
  ),
  (
    'Pasta Alfredo',
    'Creamy sauce with chicken',
    15.50,
    'Main Course',
    TRUE
  ),
  (
    'Garlic Bread',
    'Toasted with garlic butter',
    3.99,
    'Appetizer',
    TRUE
  ),
  (
    'Cheeseburger',
    'Served with fries',
    10.99,
    'Main Course',
    TRUE
  ),
  (
    'Tiramisu',
    'Coffee-flavored dessert',
    7.50,
    'Dessert',
    TRUE
  ),
  (
    'Wine Glass',
    'Premium red wine',
    8.99,
    'Beverage',
    TRUE
  ),
  (
    'Iced Tea',
    'Chilled and refreshing',
    2.99,
    'Beverage',
    TRUE
  );

-- 5. `inventory`
INSERT INTO
  inventory (item_name, quantity, reorder_level)
VALUES
  ('Tomatoes', 50,  10),
  ('Lettuce', 30,  5),
  ('Flour', 100,  20),
  ('Beef', 40,  10),
  ('Chicken', 60,  15),
  ('Butter', 80,  10),
  ('Cheese', 75,  20),
  ('Wine Bottles', 40,  10),
  ('Coffee Beans', 50,  10),
  ('Pasta', 90,  15);

-- 6. `menu_item_inventory`
INSERT INTO
  menu_item_inventory (menu_item_id, inventory_id, quantity_used)
VALUES
  (1, 1, 2),
  (2, 2, 1),
  (3, 3, 3),
  (4, 4, 2),
  (5, 5, 3),
  (6, 6, 1),
  (7, 7, 2),
  (8, 8, 1),
  (9, 9, 1),
  (10, 10, 1);

INSERT INTO admin (username, passhash) values ("madiba", "$2y$10$Wb6MPRNp0oO7/eM81Ma8HO4kVapyl0o.O1vkvSp3/IiWYW5oGsjyW");
