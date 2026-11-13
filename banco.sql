CREATE USER IF NOT EXISTS 'restaurante'@'localhost'
IDENTIFIED BY 'Restaurante123!';

CREATE DATABASE IF NOT EXISTS restaurante;
USE restaurante;

GRANT ALL PRIVILEGES ON restaurante.*
TO 'restaurante'@'localhost';

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE pratos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    CHECK (valor > 0)
) ENGINE=InnoDB;

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    prato_id INT NOT NULL,
    quantidade INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (prato_id) REFERENCES pratos(id) ON DELETE RESTRICT,
    CHECK (quantidade > 0)
) ENGINE=InnoDB;
