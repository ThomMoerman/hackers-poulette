CREATE DATABASE IF NOT EXISTS hackerspoulette;

USE hackerspoulette;

CREATE TABLE contact_forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    firstname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    description VARCHAR(1000) NOT NULL,
    file MEDIUMBLOB
);
