<?php
include_once "configs/banco.php";
$banco = new Banco();
$db = $banco->getConexao();

echo "DEBUG BANCO:\n";
try {
    $res = $db->query("SELECT DATABASE()");
    echo "Database atual: " . $res->fetchColumn() . "\n";
    
    echo "Tabelas encontradas:\n";
    $tabs = $db->query("SHOW TABLES");
    while($t = $tabs->fetch(PDO::FETCH_NUM)) {
        echo "- " . $t[0] . "\n";
    }
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage();
}
?>
