CREATE DATABASE IF NOT EXISTS oceanit_db;
USE oceanit_db;

-- Portfolio Table
CREATE TABLE IF NOT EXISTS portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_name VARCHAR(255) NOT NULL,
    description TEXT,
    link VARCHAR(255) NOT NULL,
    image_path VARCHAR(255)
);

-- Reviews Table (rating removed)
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255) NOT NULL,
    business_name VARCHAR(255),
    comment TEXT
);
