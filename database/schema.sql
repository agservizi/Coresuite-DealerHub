CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) UNIQUE NOT NULL,
    email VARCHAR(120) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('superadmin','affiliato') NOT NULL DEFAULT 'affiliato',
    status ENUM('active','suspended') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE contracts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contract_code CHAR(8) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    type ENUM('telefonia','luce','gas','bundle') NOT NULL,
    subtype VARCHAR(50) DEFAULT NULL,
    customer_name VARCHAR(80) NOT NULL,
    customer_surname VARCHAR(80) NOT NULL,
    customer_cf VARCHAR(16) NOT NULL,
    customer_email VARCHAR(120) NOT NULL,
    customer_phone VARCHAR(30) NOT NULL,
    customer_address VARCHAR(255) NOT NULL,
    birth_date DATE NULL,
    birth_place VARCHAR(120) NULL,
    address_street VARCHAR(120) NOT NULL,
    address_number VARCHAR(10) NOT NULL,
    address_city VARCHAR(120) NOT NULL,
    address_zip CHAR(5) NOT NULL,
    address_province CHAR(2) NOT NULL,
    document_type VARCHAR(50) NOT NULL,
    document_number VARCHAR(50) NOT NULL,
    document_issue_date DATE NOT NULL,
    document_expiry DATE NOT NULL,
    status ENUM('in_attesa','in_lavorazione','in_verifica','attivato','annullato','rigettato') DEFAULT 'in_attesa',
    document_front VARCHAR(255) DEFAULT NULL,
    document_back VARCHAR(255) DEFAULT NULL,
    bill VARCHAR(255) DEFAULT NULL,
    adhesion_form VARCHAR(255) DEFAULT NULL,
    voice_recording VARCHAR(255) DEFAULT NULL,
    payment_method ENUM('rid','carta','bollettino') DEFAULT NULL,
    payment_details JSON NULL,
    technical_details JSON NULL,
    attachments JSON NULL,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    message VARCHAR(255) NOT NULL,
    type VARCHAR(50) DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(120) NOT NULL,
    context JSON NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
