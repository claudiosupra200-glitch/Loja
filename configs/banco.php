<?php

class Banco
{
    private $host = "localhost:3316"; // Ajuste a porta se necessário (ex: 3306 ou 3316)
    private $usuario = "root";
    private $senha = "123456"; // Insira sua senha do banco aqui se houver
    private $banco = "loja";
    private $con;

    public function getConexao()
    {
        $this->con = null;

        try {
            $this->con = new PDO("mysql:host=$this->host;dbname=$this->banco", $this->usuario, $this->senha);
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Falha ao conectar: " . $e->getMessage();
        }
        return $this->con;
    }
}

create database loja;
use loja;

-- Tabela de produtos
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    quantidade INT NOT NULL,
    imagem VARCHAR(255)
);

-- Tabela de funcionarios
CREATE TABLE funcionarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cargo VARCHAR(100) NOT NULL,
    departamento VARCHAR(100) NOT NULL,
    pis VARCHAR(20) NOT NULL,
    emailCorporativo VARCHAR(100) NOT NULL,
    formacao VARCHAR(100) NOT NULL,
    imagem VARCHAR(255),
    senha VARCHAR(255) NOT NULL
);

-- Tabela de clientes
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    login VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    imagem VARCHAR(255)
);

-- Tabela de carrinho (itens temporarios antes de finalizar)
CREATE TABLE itens_compra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id),
    FOREIGN KEY (id_produto) REFERENCES produtos(id)
);

-- Tabela de pedidos (cabecalho da compra finalizada)
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pendente', 'aprovado', 'cancelado') DEFAULT 'pendente',
    FOREIGN KEY (id_cliente) REFERENCES clientes(id)
);

-- Tabela de itens do pedido finalizado
CREATE TABLE pedido_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT NOT NULL,
    id_produto INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id),
    FOREIGN KEY (id_produto) REFERENCES produtos(id)
);

-- Inserts de funcionarios
INSERT INTO funcionarios (nome, cargo, departamento, pis, emailCorporativo, formacao, imagem, senha) VALUES
('Fábio Oliveira', 'Vendedor', 'Vendas', '678.90123.45-6', 'fabio@loja.com', 'Ensino Médio', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Giovanna Mendes', 'Vendedor', 'Vendas', '789.01234.56-7', 'giovanna@loja.com', 'Gestão Comercial', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Henrique Rocha', 'Gerente', 'Gerência', '890.12345.67-8', 'henrique@loja.com', 'Administração', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Isabela Fernandes', 'Vendedor', 'Vendas', '901.23456.78-9', 'isabela@loja.com', 'Publicidade', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('João Pedro', 'Vendedor', 'Vendas', '012.34567.89-0', 'joao@loja.com', 'Ensino Médio', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO produtos (nome, descricao, preco, quantidade, imagem) VALUES
('Paracetamol 500mg', 'Analgésico e antitérmico', 4.80, 150, NULL),
('Dipirona 1g', 'Analgésico e antitérmico', 3.80, 200, NULL),
('Ibuprofeno 400mg', 'Anti-inflamatório', 6.90, 120, NULL),
('Amoxicilina 500mg', 'Antibiótico', 12.50, 80, NULL),
('Omeprazol 20mg', 'Redutor de acidez gástrica', 7.40, 100, NULL),
('Loratadina 10mg', 'Antialérgico', 5.20, 130, NULL),
('Vitamina C 1g', 'Suplemento vitamínico', 8.90, 180, NULL),
('Rivotril 2mg', 'Anticonvulsivante e ansiolítico', 18.90, 60, NULL),
('Metformina 850mg', 'Antidiabético oral', 9.50, 90, NULL),
('Atenolol 25mg', 'Anti-hipertensivo', 6.30, 110, NULL),
('Fluconazol 150mg', 'Antifúngico', 14.20, 70, NULL),
('Azitromicina 500mg', 'Antibiótico', 22.90, 50, NULL),
('Captopril 25mg', 'Anti-hipertensivo', 5.70, 120, NULL),
('Dorflex', 'Analgésico e relaxante muscular', 11.40, 140, NULL),
('Buscopan Composto', 'Analgésico e antiespasmódico', 13.80, 100, NULL),
('Protetor Solar FPS50', 'Proteção solar para pele', 34.90, 60, NULL),
('Álcool Gel 70%', 'Antisséptico para mãos', 6.50, 200, NULL),
('Pomada Bepantol', 'Cicatrizante e hidratante', 19.90, 80, NULL),
('Colagenase', 'Pomada cicatrizante', 24.50, 50, NULL),
('Termômetro Digital', 'Medição de temperatura corporal', 29.90, 40, NULL);