<?php
include_once "configs/banco.php";
$banco = new Banco();
$db = $banco->getConexao();

foreach(['compras', 'itens_compra'] as $t) {
    echo "\nTable: $t\n";
    try {
        $s = $db->query("DESCRIBE $t");
        while($r = $s->fetch(PDO::FETCH_ASSOC)) {
            echo "Column: {$r['Field']} | Type: {$r['Type']} | Null: {$r['Null']} | Key: {$r['Key']}\n";
        }
    } catch (Exception $e) {
        echo "Error describing $t: " . $e->getMessage() . "\n";
    }
}
?>
