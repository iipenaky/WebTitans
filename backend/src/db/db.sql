CREATE DATABASE if not exists restaurant;

USE restaurant;

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
  phone_number varchar(40) NOT NULL,
  email varchar(40) NOT NULL,
  address varchar(40),
  CHECK (
    email REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$'
  )
);

CREATE table if not exists reservations (
  reservation_id int AUTO_INCREMENT PRIMARY KEY,
  position_id int,
  customer_id int NOT NULL,
  table_id int NOT NULL,
  reservation_datetime datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  number_of_guests int NOT NULL DEFAULT 1,
  special_requests varchar(255),
  FOREIGN KEY (customer_id) REFERENCES customer (customer_id),
  FOREIGN KEY (table_id) REFERENCES `table` (table_id),
  CHECK (number_of_guests > 0),
  UNIQUE (table_id, reservation_datetime),
  UNIQUE (position_id, reservation_datetime)
);

CREATE table if not exists staff (
  staff_id int AUTO_INCREMENT PRIMARY KEY,
  first_name varchar(40) NOT NULL,
  last_name varchar(40) NOT NULL,
  `position` varchar(40) NOT NULL,
  phone_number varchar(40) NOT NULL,
  email varchar(40) NOT NULL,
  address varchar(40) NOT NULL,
  hire_date date NOT NULL DEFAULT (CURRENT_DATE()),
  salary decimal(10, 4) NOT NULL,
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
  unit int NOT NULL,
  reorder_level int NOT NULL,
  CHECK (
    quantity >= 0
    AND reorder_level >= 0
  ),
  CHECK (unit > 0)
);

CREATE table if not exists menu_item_inventory (
  menu_item_id int NOT NULL,
  inventory_id int NOT NULL,
  quantity_used int NOT NULL,
  PRIMARY KEY (menu_item_id, inventory_id),
  FOREIGN KEY (menu_item_id) REFERENCES menu_item (menu_item_id),
  FOREIGN KEY (inventory_id) REFERENCES inventory (inventory_id),
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
  FOREIGN KEY (order_id) REFERENCES `order` (order_id),
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

-- -- 1. `table`
-- INSERT INTO
--   `table` (table_number, seating_capacity, location)
-- VALUES
--   (1, 4, 'Patio'),
--   (2, 2, 'Near Window'),
--   (3, 6, 'Main Hall'),
--   (4, 8, 'VIP Lounge'),
--   (5, 10, 'Banquet Hall'),
--   (6, 4, 'Garden'),
--   (7, 2, 'Bar Area'),
--   (8, 6, 'Balcony'),
--   (9, 8, 'Private Room'),
--   (10, 12, 'Conference Hall');
-- 
-- -- 2. `customer`
-- INSERT INTO
--   customer (
--     first_name,
--     last_name,
--     phone_number,
--     email,
--     address
--   )
-- VALUES
--   (
--     'John',
--     'Doe',
--     '123-456-7890',
--     'john.doe@example.com',
--     '123 Elm St'
--   ),
--   (
--     'Jane',
--     'Smith',
--     '234-567-8901',
--     'jane.smith@example.com',
--     '456 Maple St'
--   ),
--   (
--     'Alice',
--     'Johnson',
--     '345-678-9012',
--     'alice.j@example.com',
--     '789 Oak St'
--   ),
--   (
--     'Bob',
--     'Williams',
--     '456-789-0123',
--     'bob.w@example.com',
--     '101 Pine St'
--   ),
--   (
--     'Charlie',
--     'Brown',
--     '567-890-1234',
--     'charlie.b@example.com',
--     '202 Cedar St'
--   ),
--   (
--     'David',
--     'Lee',
--     '678-901-2345',
--     'david.l@example.com',
--     '303 Birch St'
--   ),
--   (
--     'Eva',
--     'Green',
--     '789-012-3456',
--     'eva.g@example.com',
--     '404 Ash St'
--   ),
--   (
--     'Frank',
--     'Miller',
--     '890-123-4567',
--     'frank.m@example.com',
--     '505 Walnut St'
--   ),
--   (
--     'Grace',
--     'Hopper',
--     '901-234-5678',
--     'grace.h@example.com',
--     '606 Elm St'
--   ),
--   (
--     'Helen',
--     'Keller',
--     '012-345-6789',
--     'helen.k@example.com',
--     '707 Maple St'
--   );
-- 
-- -- 3. `staff`
-- INSERT INTO
--   staff (
--     first_name,
--     last_name,
--     position,
--     phone_number,
--     email,
--     address,
--     salary
--   )
-- VALUES
--   (
--     'Mark',
--     'Lee',
--     'Chef',
--     '111-222-3333',
--     'mark.lee@example.com',
--     '10 Baker St',
--     5000.00
--   ),
--   (
--     'Lucy',
--     'Brown',
--     'Waiter',
--     '222-333-4444',
--     'lucy.b@example.com',
--     '20 Hill St',
--     2500.00
--   ),
--   (
--     'James',
--     'Smith',
--     'Manager',
--     '333-444-5555',
--     'james.s@example.com',
--     '30 Oak St',
--     7000.00
--   ),
--   (
--     'Anna',
--     'Taylor',
--     'Bartender',
--     '444-555-6666',
--     'anna.t@example.com',
--     '40 Pine St',
--     3000.00
--   ),
--   (
--     'Chris',
--     'Evans',
--     'Host',
--     '555-666-7777',
--     'chris.e@example.com',
--     '50 Birch St',
--     3200.00
--   ),
--   (
--     'Mia',
--     'Wallace',
--     'Waiter',
--     '666-777-8888',
--     'mia.w@example.com',
--     '60 Ash St',
--     2500.00
--   ),
--   (
--     'Paul',
--     'Walker',
--     'Chef',
--     '777-888-9999',
--     'paul.w@example.com',
--     '70 Elm St',
--     5000.00
--   ),
--   (
--     'Sophia',
--     'Turner',
--     'Manager',
--     '888-999-0000',
--     'sophia.t@example.com',
--     '80 Maple St',
--     7200.00
--   ),
--   (
--     'Leo',
--     'King',
--     'Cleaner',
--     '999-000-1111',
--     'leo.k@example.com',
--     '90 Cedar St',
--     1800.00
--   ),
--   (
--     'Emma',
--     'Stone',
--     'Cashier',
--     '000-111-2222',
--     'emma.s@example.com',
--     '100 Oak St',
--     2600.00
--   );
-- 
-- -- 4. `menu_item`
-- INSERT INTO
--   menu_item (
--     name,
--     description,
--     price,
--     category,
--     availability_status
--   )
-- VALUES
--   (
--     'Margherita Pizza',
--     'Tomato and mozzarella',
--     12.99,
--     'Main Course',
--     TRUE
--   ),
--   (
--     'Caesar Salad',
--     'Lettuce with Caesar dressing',
--     8.50,
--     'Appetizer',
--     TRUE
--   ),
--   (
--     'Chocolate Cake',
--     'Rich dessert',
--     6.99,
--     'Dessert',
--     TRUE
--   ),
--   (
--     'Steak',
--     'Grilled to perfection',
--     25.99,
--     'Main Course',
--     TRUE
--   ),
--   (
--     'Pasta Alfredo',
--     'Creamy sauce with chicken',
--     15.50,
--     'Main Course',
--     TRUE
--   ),
--   (
--     'Garlic Bread',
--     'Toasted with garlic butter',
--     3.99,
--     'Appetizer',
--     TRUE
--   ),
--   (
--     'Cheeseburger',
--     'Served with fries',
--     10.99,
--     'Main Course',
--     TRUE
--   ),
--   (
--     'Tiramisu',
--     'Coffee-flavored dessert',
--     7.50,
--     'Dessert',
--     TRUE
--   ),
--   (
--     'Wine Glass',
--     'Premium red wine',
--     8.99,
--     'Beverage',
--     TRUE
--   ),
--   (
--     'Iced Tea',
--     'Chilled and refreshing',
--     2.99,
--     'Beverage',
--     TRUE
--   );
-- 
-- -- 5. `inventory`
-- INSERT INTO
--   inventory (item_name, quantity, unit, reorder_level)
-- VALUES
--   ('Tomatoes', 50, 1, 10),
--   ('Lettuce', 30, 1, 5),
--   ('Flour', 100, 1, 20),
--   ('Beef', 40, 1, 10),
--   ('Chicken', 60, 1, 15),
--   ('Butter', 80, 1, 10),
--   ('Cheese', 75, 1, 20),
--   ('Wine Bottles', 40, 1, 10),
--   ('Coffee Beans', 50, 1, 10),
--   ('Pasta', 90, 1, 15);
-- 
-- -- 6. `menu_item_inventory`
-- INSERT INTO
--   menu_item_inventory (menu_item_id, inventory_id, quantity_used)
-- VALUES
--   (1, 1, 2),
--   (2, 2, 1),
--   (3, 3, 3),
--   (4, 4, 2),
--   (5, 5, 3),
--   (6, 6, 1),
--   (7, 7, 2),
--   (8, 8, 1),
--   (9, 9, 1),
--   (10, 10, 1);
-- 
-- -- 7. `reservations`
-- INSERT INTO
--   reservations (
--     position_id,
--     customer_id,
--     table_id,
--     reservation_datetime,
--     number_of_guests,
--     special_requests
--   )
-- VALUES
--   (
--     1,
--     1,
--     1,
--     '2024-10-21 19:00:00',
--     2,
--     'Near the window'
--   ),
--   (
--     2,
--     2,
--     3,
--     '2024-10-22 18:30:00',
--     4,
--     'Birthday celebration'
--   ),
--   (3, 3, 5, '2024-10-23 20:00:00', 6, ''),
--   (4, 4, 7, '2024-10-24 12:00:00', 8, ''),
--   (5, 5, 2, '2024-10-25 13:00:00', 2, ''),
--   (6, 6, 4, '2024-10-26 18:00:00', 3, ''),
--   (7, 7, 6, '2024-10-27 19:00:00', 4, ''),
--   (8, 8, 8, '2024-10-28 20:00:00', 5, ''),
--   (9, 9, 9, '2024-10-29 21:00:00', 6, ''),
--   (10, 10, 10, '2024-10-30 22:00:00', 12, '');
-- 
-- -- 8. `order`
-- INSERT INTO
--   `order` (customer_id, staff_id, total_amount, status)
-- VALUES
--   (1, 1, 20.49, 'Completed'),
--   (2, 2, 15.50, 'Pending'),
--   (3, 3, 45.99, 'Completed'),
--   (4, 4, 30.99, 'Pending'),
--   (5, 5, 12.50, 'Cancelled'),
--   (6, 6, 18.99, 'Completed'),
--   (7, 7, 27.99, 'Pending'),
--   (8, 8, 35.49, 'Cancelled'),
--   (9, 9, 10.99, 'Completed'),
--   (10, 10, 8.99, 'Pending');
-- 
-- -- 9. `order_details`
-- INSERT INTO
--   order_details (order_id, menu_item_id, quantity)
-- VALUES
--   (1, 1, 1),
--   (2, 2, 2),
--   (3, 3, 1),
--   (4, 4, 2),
--   (5, 5, 1),
--   (6, 6, 2),
--   (7, 7, 1),
--   (8, 8, 1),
--   (9, 9, 1),
--   (10, 10, 2);
-- 
-- -- 10. `payment`
-- INSERT INTO
--   payment (order_id, payment_method, amount, status)
-- VALUES
--   (1, 'Credit Card', 20.49, 'Completed'),
--   (2, 'Cash', 15.50, 'Pending'),
--   (3, 'Credit Card', 45.99, 'Completed'),
--   (4, 'Cash', 30.99, 'Pending'),
--   (5, 'Debit Card', 12.50, 'Cancelled'),
--   (6, 'Credit Card', 18.99, 'Completed'),
--   (7, 'Cash', 27.99, 'Pending'),
--   (8, 'Debit Card', 35.49, 'Cancelled'),
--   (9, 'Credit Card', 10.99, 'Completed'),
--   (10, 'Cash', 8.99, 'Pending');
