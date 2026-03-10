<?php
include_once "objetos/produtosController.php";

$controller = new ProdutosController();
$produtos = $controller->index();
global $produtos;
$a = null;
if($_SERVER["REQUEST_METHOD"] === "POST"){
    if(isset($_POST["pesquisar"])){
        $a = $controller->pesquisarProduto($_POST["pesquisar"]);
    }
}

if($_SERVER["REQUEST_METHOD"] === "GET"){
    if(isset($_GET["excluir"])){
        $a = $controller->excluirProdutos($_GET["excluir"]);
        exit();
    }
}

?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Produtos Farmacia</title>
    <style>
        table, tr, td{
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
</head>
<body>

<h1>Produtos Farmacia</h1>
<a href="cadastro.php">Cadastrar Produto</a>
<h2>Produtos Cadastrados</h2>

<form method="POST" action="index.php">
    <label>ID</label>
    <input type="number" name="pesquisar">
    <button>Pesquisar</button>

</form>
<table>
    <tr>
        <th>ID</th>
        <th>Nome</th>
    </tr>
    <?php if($a) : ?>
    <?php foreach($a as $produto) : ?>
    <tr>
        <td><?= $produto->id; ?></td>
        <td><?= $produto->nome; ?></td>
    </tr>
    <?php endforeach; ?>
    <?php endif;?>

</table>
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
        <td><?php echo $produto->nome;?></td>
        <td><?php echo $produto->descricao;?></td>
        <td><?php echo $produto->quantidade;?></td>
        <td>R$ <?php echo $produto->preco;?></td>
        <td><a href="atualizar.php?alterar=<?= $produto->id ?>">Alterar</a> </td>
        <td><a href="index.php?excluir=<?= $produto->id ?>">Excluir</a> </td>
    </tr>

    <?php endforeach; ?>
    <?php endif; ?>
</table>


</body>
</html>
