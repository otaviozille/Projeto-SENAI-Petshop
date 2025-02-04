CREATE DATABASE IF NOT EXISTS petshop;

USE petshop;

-- Tabela de Clientes
CREATE TABLE clientes (
    nomeCompleto VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    telefone VARCHAR(15),
    endereco TEXT
);

-- Tabela de Pets
CREATE TABLE pets (
    idPet INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    tipo VARCHAR(50),
    idade VARCHAR(50),
    dataNascimento DATE,
    observacoes TEXT,
    cpf VARCHAR(14) NOT NULL,
    FOREIGN KEY (cpf) REFERENCES clientes(cpf)
);

-- Tabela de Produtos
CREATE TABLE produtos (
    idProduto INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    quantidade INT NOT NULL,
    imagem VARCHAR(255)
);

INSERT INTO produtos (nome, tipo, preco, quantidade, imagem)
VALUES 
('Ração para Cachorro', 'racao', 120.00, 10, 'img/racao-cachorro.jpg'),
('Brinquedo para Gato', 'brinquedo', 35.00, 5, 'img/brinquedo-gato.jpg'),
('Coleira para Cachorro', 'acessorio', 60.00, 8, 'img/coleira-cachorro.jpg');