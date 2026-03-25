<?php

include_once("objetos/produtosController.php");

$controller = new ProdutosController();

if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['alterar'])){
    $a = $controller->procurarProdutos($_GET['alterar']);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produtos'])){
    $a = $controller->atualizarProdutos($_POST['produtos']);
} else {
    header("location: index.php");
}

?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atualização de Produto</title>0
</head>
<body>
<h1>Atualização de Produto</h1>
<a href="index.php">Voltar</a>

<form action="atualizar.php" method="post" enctype="multipart/form-data">

    <input type="hidden" name="produtos[id]" value="<?=$a->id?>">

    <label>Nome</label>
    <input type="text" name="produtos[nome]" value="<?=$a->nome?>"><br><br>

    <label>Descrição</label>
    <input type="text" name="produtos[descricao]" value="<?=$a->descricao?>"><br><br>

    <label>Quantidade</label>
    <input type="text" name="produtos[quantidade]" value="<?=$a->quantidade?>"><br><br>

    <label>Preço</label>
    <input type="text" name="produtos[preco]" value="<?=$a->preco?>"><br><br>

    <label>Selecionar Foto</label>
    <input type="file" name="fileToUpload"><br><br>

    <button type="submit">Atualizar</button>
</form>

</body>
</html>