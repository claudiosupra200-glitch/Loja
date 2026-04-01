<?php

class Banco
{
    private $host = "localhost:3306"; // Ajuste a porta se necessário (ex: 3306 ou 3316)
    private $usuario = "root";
    private $senha = ""; // Insira sua senha do banco aqui se houver
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

/*
SCRIPTOR SQL PARA CRIAÇÃO DO BANCO (REFERÊNCIA):

create DATABASE loja;
use loja;

create table produtos(
    id int primary key auto_increment,
    nome varchar(30) not null,
    descricao varchar(100) not null,
    quantidade int not null,
    preco decimal(10,2) not null
);

INSERT INTO produtos (nome, descricao, quantidade, preco) VALUES
('Paracetamol', 'Analgésico e antitérmico 500mg', 150, 4.50),
('Dipirona', 'Analgésico e antitérmico 1g', 200, 3.80),
('Ibuprofeno', 'Anti-inflamatório 400mg', 120, 6.90),
('Amoxicilina', 'Antibiótico 500mg', 80, 12.50),
('Omeprazol', 'Redutor de acidez gástrica 20mg', 100, 7.40);

create table funcionarios(
    id int primary key auto_increment,
    nome varchar(100) not null,
    cargo varchar(100) not null,
    departamento varchar(100) not null,
    pis varchar(20) not null,
    emailCorporativo varchar(100) not null,
    formacao varchar(100) not null,
    imagem varchar(255),
    senha varchar(255) not null
);

INSERT INTO funcionarios (nome, cargo, departamento, pis, emailCorporativo, formacao, imagem, senha) VALUES
('Fábio Oliveira', 'Vendedor', 'Vendas', '678.90123.45-6', 'fabio@loja.com', 'Ensino Médio', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Giovanna Mendes', 'Vendedor', 'Vendas', '789.01234.56-7', 'giovanna@loja.com', 'Gestão Comercial', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Henrique Rocha', 'Gerente', 'Gerência', '890.12345.67-8', 'henrique@loja.com', 'Administração', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
*/