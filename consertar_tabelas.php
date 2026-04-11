<?php
include_once "configs/banco.php";
$banco = new Banco();
$db = $banco->getConexao();

try {
    echo "Recriando/Atualizando tabelas...\n";

    // 1. Criar compras (com venda_id e status finalizada)
    $sqlCompras = "CREATE TABLE IF NOT EXISTS compras (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_cliente INT NOT NULL,
        venda_id VARCHAR(50),
        id_produto INT NOT NULL,
        quantidade INT NOT NULL,
        preco_unitario DECIMAL(10,2) NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        data DATETIME DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pendente', 'aprovado', 'enviado', 'entregue', 'cancelado', 'finalizada') DEFAULT 'pendente',
        FOREIGN KEY (id_cliente) REFERENCES clientes(id),
        FOREIGN KEY (id_produto) REFERENCES produtos(id)
    )";
    $db->exec($sqlCompras);
    echo "- Tabela 'compras' verificada/criada.\n";

    // 2. Criar itens_compra (com venda_id)
    $sqlItens = "CREATE TABLE IF NOT EXISTS itens_compra (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_cliente INT NOT NULL,
        venda_id VARCHAR(50),
        id_produto INT NOT NULL,
        quantidade INT NOT NULL,
        preco_unitario DECIMAL(10,2) NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        data DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_cliente) REFERENCES clientes(id),
        FOREIGN KEY (id_produto) REFERENCES produtos(id)
    )";
    $db->exec($sqlItens);
    echo "- Tabela 'itens_compra' verificada/criada.\n";

    // 3. Garantir que as colunas existem (caso as tabelas já existissem sem elas)
    try { $db->exec("ALTER TABLE compras ADD COLUMN venda_id VARCHAR(50) AFTER id_cliente"); } catch(Exception $e){}
    try { $db->exec("ALTER TABLE itens_compra ADD COLUMN venda_id VARCHAR(50) AFTER id_cliente"); } catch(Exception $e){}
    try { $db->exec("ALTER TABLE compras MODIFY COLUMN status ENUM('pendente', 'aprovado', 'enviado', 'entregue', 'cancelado', 'finalizada') DEFAULT 'pendente'"); } catch(Exception $e){}

    echo "Processo concluído!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>
