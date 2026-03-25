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

select*from produtos;
drop table produtos;
