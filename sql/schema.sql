CREATE TABLE clients (
    client_id INT AUTO_INCREMENT PRIMARY KEY,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    patronymic VARCHAR(50),
    phone VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    birth_date DATE NOT NULL,
    registration_date DATE NOT NULL DEFAULT (CURRENT_DATE),
    CHECK (birth_date <= CURRENT_DATE - INTERVAL 18 YEAR)
);

CREATE TABLE workspaces (
    workspace_id INT AUTO_INCREMENT PRIMARY KEY,
    workspace_name VARCHAR(50) NOT NULL,
    workspace_type ENUM('открытое', 'переговорная', 'офис') NOT NULL,
    price_per_hour DECIMAL(10,2) NOT NULL CHECK (price_per_hour > 0)
);

CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    workspace_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    total_price DECIMAL(10,2) NOT NULL CHECK (total_price > 0),
    status ENUM('активно', 'завершено', 'отменено') DEFAULT 'активно',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (workspace_id) REFERENCES workspaces(workspace_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE KEY unique_booking_slot (workspace_id, start_time),
    CHECK (end_time > start_time)
);

CREATE TABLE discounts (
    discount_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    discount_percent INT NOT NULL CHECK (discount_percent BETWEEN 5 AND 50),
    is_active BOOLEAN DEFAULT TRUE,
    granted_date DATE NOT NULL DEFAULT (CURRENT_DATE),
    FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_active_discount (client_id)
);

CREATE INDEX idx_bookings_start_time ON bookings(start_time);
CREATE INDEX idx_workspace_type ON workspaces(workspace_type);
CREATE INDEX idx_discounts_active ON discounts(is_active);
