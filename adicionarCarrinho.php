<?php
session_start();
include_once "objetos/produtosController.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $acao = isset($_POST['acao']) ? $_POST['acao'] : 'adicionar';

    $prodController = new produtosController();
    $produto = $prodController->pesquisarProduto($id);

    if ($produto) {
        // Inicializa o carrinho se não existir
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

        // Se o produto já está no carrinho, pega a quantidade atual
        $qtdAtual = isset($_SESSION['carrinho'][$id]) ? $_SESSION['carrinho'][$id]['quantidade'] : 0;

        // Verifica se há estoque suficiente para adicionar +1
        if ($qtdAtual + 1 <= $produto->quantidade) {
            if (isset($_SESSION['carrinho'][$id])) {
                $_SESSION['carrinho'][$id]['quantidade'] += 1;
            } else {
                $_SESSION['carrinho'][$id] = [
                    'id' => $produto->id,
                    'nome' => $produto->nome,
                    'preco' => $produto->preco,
                    'quantidade' => 1,
                    'imagem' => $produto->imagem,
                    'estoque' => $produto->quantidade
                ];
            }
        } else {
            // Sem estoque suficiente
            $_SESSION['aviso_estoque'] = "O estoque máximo para " . htmlspecialchars($produto->nome) . " é " . $produto->quantidade . " unidades.";
        }

        if ($acao == 'comprar') {
            header("location: carrinho.php");
            exit();
        } else {
            // Se for AJAX, retorna JSON
            if (isset($_POST['ajax']) && $_POST['ajax'] == 'true') {
                $totalQtd = 0;
                foreach ($_SESSION['carrinho'] as $item) {
                    $totalQtd += $item['quantidade'];
                }
                echo json_encode([
                    'success' => true,
                    'total_count' => $totalQtd,
                    'message' => 'Produto adicionado!'
                ]);
                exit();
            }

            // Ação: adicionar (continua navegando - fallback para formulário comum)
            header("location: index.php");
            exit();
        }
    }
}

// Em caso de erro na requisição AJAX
if (isset($_POST['ajax']) && $_POST['ajax'] == 'true') {
    echo json_encode(['success' => false, 'message' => 'Erro ao adicionar produto.']);
    exit();
}

// Em caso de acesso indevido ou erro comum
header("location: index.php");
exit();
?>
