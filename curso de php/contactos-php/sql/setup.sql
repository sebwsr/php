-- Eliminar y recrear la base de datos
DROP DATABASE IF EXISTS contacts_App;
CREATE DATABASE contacts_App;
USE contacts_App;

-- Tabla de usuarios
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255)
);

-- Tabla de contactos
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    user_id INT NOT NULL,
    phone_number VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- NUEVA TABLA: Direcciones
CREATE TABLE addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contact_id INT NOT NULL,
    user_id INT NOT NULL,
    address VARCHAR(255) NOT NULL,
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Datos de prueba
UPDATE INTO users (name, email, password) VALUES 
('John Doe', 'john@example.com', 'password123');

INSERT INTO contacts (user_id, name, phone_number) VALUES 
(1, 'Jane Smith', '1234567890'),
(1, 'Bob Johnson', '1234567891');

INSERT INTO addresses (contact_id, user_id, address) VALUES 
(1, 1, '123 Main St, New York, NY 10001'),
(1, 1, '456 Oak Ave, Brooklyn, NY 11201'),
(2, 1, '789 Pine Rd, Los Angeles, CA 90001');
