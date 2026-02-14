<?php
include_once "objetos/produtosController.php";

$controller = new ProdutosController();
$produtos = $controller->index();
global $produtos;

?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Produtos Farmacia</title>
</head>
<body>

<h1>Produtos Farmacia</h1>
<h2>Produtos Cadastrados</h2>

<table>

    <tr>
        <td><strong><h3>Nome</h3></strong></td>
        <td><strong><h3>Descrição</h3></strong></td>
        <td><strong><h3>Quantidade</h3></strong></td>
        <td><strong><h3>Preço</h3></strong></td>
    </tr>

    <?php if ($produtos) : ?>
    <?php foreach ($produtos as $produto) : ?>

    <tr>
        <td><?php echo $produto->nome; ?></td>
        <td><?php echo $produto->descricao; ?></td>
        <td><?php echo $produto->quantidade; ?></td>
        <td><?php echo $produto->preco; ?></td>
    </tr>

    <?php endforeach; ?>
    <?php endif; ?>

</table>

</body>
</html>
