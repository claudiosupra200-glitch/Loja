<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    if (isset($_SESSION['carrinho'][$id])) {
        unset($_SESSION['carrinho'][$id]);
    }
}

// Redireciona sempre de volta ao carrinho
header("location: carrinho.php");
exit();
?>
