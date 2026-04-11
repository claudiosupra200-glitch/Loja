<?php
session_start();

include_once "configs/banco.php";
include_once "objetos/produtosController.php";

$banco = new Banco();
$db = $banco->getConexao();

// 1. Validar se usuário está logado
if (!isset($_SESSION['cliente'])) {
    header("location: login.php?msg=login_compra");
    exit();
}

$cliente = $_SESSION['cliente'];

// 2. Validar se o carrinho não está vazio
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    header("location: carrinho.php");
    exit();
}

$carrinho = $_SESSION['carrinho'];
$valor_total = 0;

// Calcular valor total prévio para a compra
foreach ($carrinho as $item) {
    $valor_total += ($item['preco'] * $item['quantidade']);
}

try {
    // Iniciar Transação
    $db->beginTransaction();

    // 2.1 Calcular Total Geral
    $totalGeral = 0;
    foreach ($carrinho as $item) {
        $totalGeral += ($item['preco'] * $item['quantidade']);
    }

    // 2.2 Criar o Cabeçalho do Pedido
    $sqlPedido = "INSERT INTO pedidos (id_cliente, total, status) VALUES (:id_cliente, :total, 'aprovado')";
    $stmtP = $db->prepare($sqlPedido);
    $stmtP->bindParam(":id_cliente", $cliente->id);
    $stmtP->bindParam(":total", $totalGeral);
    $stmtP->execute();
    
    $id_pedido = $db->lastInsertId();

    // 3. Processar itens
    foreach ($carrinho as $id_prod => $item) {
        $stmtEstoque = $db->prepare("SELECT quantidade FROM produtos WHERE id = :id FOR UPDATE");
        $stmtEstoque->bindParam(":id", $id_prod);
        $stmtEstoque->execute();
        $produtoBanco = $stmtEstoque->fetch(PDO::FETCH_ASSOC);

        if (!$produtoBanco || $produtoBanco['quantidade'] < $item['quantidade']) {
            $db->rollBack();
            $_SESSION['aviso_estoque'] = "O estoque do item '" . htmlspecialchars($item['nome']) . "' é insuficiente.";
            header("location: carrinho.php?msg=estoque_insuficiente");
            exit();
        }

        $subtotal = $item['preco'] * $item['quantidade'];

        // 4. Inserir na tabela pedido_itens
        $sqlItem = "INSERT INTO pedido_itens (id_pedido, id_produto, quantidade, preco_unitario, total) 
                    VALUES (:id_pedido, :id_prod, :qtd, :preco, :total)";
        $stmtI = $db->prepare($sqlItem);
        $stmtI->bindParam(":id_pedido", $id_pedido);
        $stmtI->bindParam(":id_prod", $id_prod);
        $stmtI->bindParam(":qtd", $item['quantidade']);
        $stmtI->bindParam(":preco", $item['preco']);
        $stmtI->bindParam(":total", $subtotal);
        $stmtI->execute();

        // 5. Atualizar Estoque
        $stmtBaixa = $db->prepare("UPDATE produtos SET quantidade = quantidade - :qtd WHERE id = :id");
        $stmtBaixa->bindParam(":qtd", $item['quantidade']);
        $stmtBaixa->bindParam(":id", $id_prod);
        $stmtBaixa->execute();
    }

    // 6. Commit e Sucesso
    $db->commit();
    unset($_SESSION['carrinho']);
    
    // Guardamos o ID do pedido para o sucesso
    $primeiro_id = $id_pedido;

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    $_SESSION['aviso_estoque'] = "Erro no processamento: " . $e->getMessage();
    header("location: carrinho.php");
    exit();
}

?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Compra Concluída | Loja Farmácia</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #F8FAFC; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .success-card { background: white; padding: 4rem 3rem; border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); text-align: center; max-width: 500px; width: 100%; }
        .icon-circle { width: 80px; height: 80px; background: #D1FAE5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
        .icon-circle i { font-size: 3rem; color: #10B981; }
        h1 { color: #0F172A; margin-bottom: 1rem; }
        p { color: #64748B; margin-bottom: 2rem; line-height: 1.6; }
        .order-number { font-size: 1.25rem; font-weight: 700; color: #1E88E5; padding: 1rem; background: #F1F5F9; border-radius: 0.5rem; margin-bottom: 2rem; }
        .btn { display: inline-block; padding: 0.75rem 2rem; background: #1E88E5; color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600; transition: background 0.2s; }
        .btn:hover { background: #1565C0; }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="icon-circle">
            <i class="ri-check-line"></i>
        </div>
        <h1>Compra Realizada com Sucesso!</h1>
        <p>Obrigado, <?= htmlspecialchars($cliente->nome) ?>! Seu pedido foi confirmado e nosso estoque já foi atualizado.</p>
        
        <div class="order-number">
            Pedido #<?= str_pad($primeiro_id, 6, "0", STR_PAD_LEFT) ?>
        </div>

        <a href="index.php" class="btn"><i class="ri-store-2-line"></i> Voltar para a Loja</a>
    </div>
</body>
</html>
