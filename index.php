<?php
include_once "configs\database.php";

$banco = new Database();
$bd = $banco->conectar();

if($bd){
    $sql = "SELECT * FROM produtos";
    $resultado = $bd->query($sql);
    $resultado->execute();
    $resultado = $resultado->fetchAll(PDO::FETCH_ASSOC);

    foreach ($resultado as $produtos) {
        echo "Nome: " . $produtos['nome'] . "<br>";
        echo "Descricao: " . $produtos['descricao'] . "<br>";
        echo "Quantidade: " . $produtos['quantidade'] . "<br>";
        echo "Preço: " . $produtos['preco'] . "<br>" . "<br>";
    }
} else {
    echo "Falha ao conectar banco de dados.";
}
