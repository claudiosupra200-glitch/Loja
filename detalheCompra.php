<?php
session_status() === PHP_SESSION_NONE ? session_start() : null;

if (!isset($_SESSION["cliente"])) {
    header("location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("location: perfilCliente.php?tab=historico");
    exit();
}

include_once "objetos/clienteController.php";
$controller = new clienteController();
$cliente = $_SESSION["cliente"];
$id_pedido = intval($_GET['id']);

$itens = $controller->pegarDetalhes($id_pedido, $cliente->id);

if (!$itens || count($itens) == 0) {
    echo "Compra não encontrada ou acesso negado.";
    exit();
}

$valorTotalGeral = 0;
foreach($itens as $it) {
    $valorTotalGeral += $it->total;
}
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalhes da Compra | Loja Farmácia</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --background: #F8FAFC;
            --white: #FFFFFF;
            --header: #0F172A;
            --primary: #1E88E5;
            --text-main: #334155;
            --text-muted: #64748B;
            --border: #E2E8F0;
            --success: #10B981;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        body { font-family: 'Outfit', sans-serif; background-color: var(--background); color: var(--text-main); line-height: 1.5; padding: 2rem; }
        .card { background: var(--white); border-radius: 1rem; border: 1px solid var(--border); padding: 2.5rem; max-width: 900px; margin: 0 auto; box-shadow: var(--shadow-sm); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 2px solid var(--border); }
        .title { font-size: 1.5rem; font-weight: 700; color: var(--header); }
        .btn-back { display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; color: var(--text-muted); font-weight: 600; font-size: 0.9rem; margin-bottom: 1rem; }
        
        .items-list { width: 100%; border-collapse: collapse; }
        .item-row { border-bottom: 1px solid var(--border); }
        .item-cell { padding: 1.5rem 0; }
        .item-img { width: 80px; height: 80px; object-fit: contain; background: #F1F5F9; border-radius: 0.5rem; padding: 0.5rem; margin-right: 1.5rem; }
        .item-name { font-size: 1.1rem; font-weight: 700; color: var(--header); }
        .item-meta { font-size: 0.85rem; color: var(--text-muted); }
        
        .summary { margin-top: 2rem; padding-top: 2rem; border-top: 2px solid var(--header); max-width: 300px; margin-left: auto; }
        .summary-row { display: flex; justify-content: space-between; font-size: 1rem; margin-bottom: 0.5rem; }
        .total-row { display: flex; justify-content: space-between; font-size: 1.4rem; font-weight: 800; color: var(--primary); margin-top: 0.5rem; border-top: 1px solid var(--border); padding-top: 0.5rem; }
    </style>
</head>

<body>

    <div style="max-width: 900px; margin: 0 auto;">
        <a href="perfilCliente.php?tab=historico" class="btn-back"><i class="ri-arrow-left-line"></i> Voltar ao Histórico</a>
        
        <div class="card">
            <div class="header">
                <div>
                    <div class="title">Pedido #<?= str_pad($id_pedido, 5, "0", STR_PAD_LEFT) ?></div>
                </div>
                <div style="color: var(--text-muted); font-weight: 500;"><i class="ri-calendar-line"></i> <?= date("d/m/Y H:i", strtotime($itens[0]->data)) ?></div>
            </div>

            <table class="items-list">
                <thead>
                    <tr style="text-align: left; color: var(--text-muted); border-bottom: 1px solid var(--border);">
                        <th style="padding-bottom: 1rem;">Produto</th>
                        <th style="padding-bottom: 1rem; text-align: center;">Qtd</th>
                        <th style="padding-bottom: 1rem; text-align: center;">Preço Unit.</th>
                        <th style="padding-bottom: 1rem; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($itens as $item): ?>
                    <tr class="item-row">
                        <td class="item-cell" style="display: flex; align-items: center;">
                            <img src="<?= $item->produto_imagem ? 'uploads/'.htmlspecialchars($item->produto_imagem) : 'imagens/OIP.webp' ?>" class="item-img" alt="Produto">
                            <span class="item-name"><?= htmlspecialchars($item->produto_nome) ?></span>
                        </td>
                        <td class="item-cell" style="text-align: center; font-weight: 600;">
                            <?= $item->quantidade ?>
                        </td>
                        <td class="item-cell" style="text-align: center; color: var(--text-muted);">
                            R$ <?= number_format($item->preco_unitario, 2, ',', '.') ?>
                        </td>
                        <td class="item-cell" style="text-align: right; font-weight: 700; color: var(--header);">
                            R$ <?= number_format($item->total, 2, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>R$ <?= number_format($valorTotalGeral, 2, ',', '.') ?></span>
                </div>
                <div class="summary-row">
                    <span>Entrega</span>
                    <span style="color: var(--success);">Grátis</span>
                </div>
                <div class="total-row">
                    <span>TOTAL</span>
                    <span>R$ <?= number_format($valorTotalGeral, 2, ',', '.') ?></span>
                </div>
            </div>

            <div style="margin-top: 3rem; text-align: center; font-size: 0.85rem; color: var(--text-muted);">
                Equipe Loja Farmácia
            </div>
        </div>
    </div>

</body>

</html>
