<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['acao'])) {
    $id = intval($_POST['id']);
    $acao = $_POST['acao'];

    if (isset($_SESSION['carrinho'][$id])) {
        $item = &$_SESSION['carrinho'][$id]; // Usando referência para modificar diretamente

        if ($acao == 'aumentar') {
            if ($item['quantidade'] + 1 <= $item['estoque']) {
                $item['quantidade'] += 1;
            } else {
                $_SESSION['aviso_estoque'] = "O estoque máximo para " . htmlspecialchars($item['nome']) . " é " . $item['estoque'] . " unidades.";
            }
        } elseif ($acao == 'diminuir') {
            if ($item['quantidade'] > 1) {
                $item['quantidade'] -= 1;
            } else {
                // Se a quantidade for 1 e diminuir, remove o item
                unset($_SESSION['carrinho'][$id]);
            }
        }
    }
}

// Redireciona de volta ao carrinho
header("location: carrinho.php");
exit();
?>
