<?php
session_start();

// Redireciona funcionários para a área administrativa
if (isset($_SESSION["funcionario"])) {
    header("location: indexFuncionario.php");
    exit();
}

$carrinho = isset($_SESSION['carrinho']) ? $_SESSION['carrinho'] : [];
$clienteLogado = isset($_SESSION["cliente"]) ? $_SESSION["cliente"] : null;
$totalCompra = 0;

// Captura mensagens de erro/estoque
$avisoEstoque = null;
if (isset($_SESSION['aviso_estoque'])) {
    $avisoEstoque = $_SESSION['aviso_estoque'];
    unset($_SESSION['aviso_estoque']);
}

$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Meu Carrinho | Loja Farmácia</title>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --background: #F8FAFC;
            --white: #FFFFFF;
            --header: #0F172A;
            --primary: #1E88E5;
            --primary-hover: #1565C0;
            --text-main: #334155;
            --text-muted: #64748B;
            --border: #E2E8F0;
            --success: #10B981;
            --danger: #EF4444;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { font-family: 'Outfit', sans-serif; background-color: var(--background); color: var(--text-main); line-height: 1.5; }

        /* Navbar */
        .navbar {
            background-color: var(--white);
            padding: 1rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
            border-bottom: 1px solid var(--border);
        }

        .logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: var(--primary); font-weight: 700; font-size: 1.5rem; }
        .logo i { font-size: 1.8rem; }
        .nav-actions { display: flex; align-items: center; gap: 1rem; }

        .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; border-radius: 0.5rem; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; font-size: 0.95rem; }
        .btn-outline { background-color: transparent; color: var(--text-main); border: 2px solid var(--border); }
        .btn-outline:hover { background-color: #F8FAFC; border-color: var(--text-muted); }
        .btn-primary { background-color: var(--primary); color: var(--white); }
        .btn-primary:hover { background-color: var(--primary-hover); transform: translateY(-1px); box-shadow: var(--shadow-md); }
        .btn-success { background-color: var(--success); color: var(--white); width: 100%; justify-content: center; padding: 1rem; font-size: 1.1rem; }
        .btn-success:hover { background-color: #059669; }

        .user-greeting { font-weight: 500; font-size: 0.9rem; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem; }

        /* Alerts */
        .alert { padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.75rem; }
        .alert-warning { background-color: #FFFBEB; color: #B45309; border: 1px solid #FEF3C7; }
        .alert-danger { background-color: #FEF2F2; color: var(--danger); border: 1px solid #FEE2E2; }
        .alert-success { background-color: #F0FDF4; color: var(--success); border: 1px solid #DCFCE7; }

        /* Main Container */
        main { max-width: 1000px; margin: 3rem auto; padding: 0 1.5rem; }
        .page-title { font-size: 2rem; font-weight: 700; color: var(--header); margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem; }

        .cart-container { display: flex; flex-direction: column; gap: 2rem; }
        
        .cart-items { background: var(--white); border-radius: 1rem; border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; }
        
        .cart-item { display: flex; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border); gap: 1.5rem; }
        .cart-item:last-child { border-bottom: none; }
        
        .item-img { width: 80px; height: 80px; object-fit: contain; background: #F1F5F9; border-radius: 0.5rem; padding: 0.5rem; border: 1px solid var(--border); }
        .item-details { flex: 1; }
        .item-title { font-size: 1.1rem; font-weight: 600; color: var(--header); margin-bottom: 0.25rem; }
        .item-price { font-size: 0.9rem; color: var(--text-muted); }
        
        .item-actions { display: flex; align-items: center; gap: 1rem; }
        .qty-control { display: flex; align-items: center; border: 1px solid var(--border); border-radius: 0.5rem; overflow: hidden; }
        .qty-btn { background: #F8FAFC; border: none; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s; color: var(--text-main); }
        .qty-btn:hover { background: var(--border); }
        .qty-value { width: 40px; text-align: center; font-weight: 600; font-size: 0.95rem; border-left: 1px solid var(--border); border-right: 1px solid var(--border); }
        
        .item-subtotal { font-weight: 700; color: var(--primary); font-size: 1.1rem; min-width: 100px; text-align: right; }
        .btn-remove { background: transparent; color: var(--danger); border: none; font-size: 1.5rem; cursor: pointer; transition: opacity 0.2s; padding: 0.5rem; }
        .btn-remove:hover { opacity: 0.7; }

        .cart-summary { background: var(--white); border-radius: 1rem; border: 1px solid var(--border); padding: 2rem; box-shadow: var(--shadow-sm); }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 1rem; font-size: 1.1rem; color: var(--text-muted); }
        .summary-total { display: flex; justify-content: space-between; font-size: 1.5rem; font-weight: 800; color: var(--header); padding-top: 1rem; border-top: 2px dashed var(--border); margin-bottom: 1.5rem; }

        .empty-cart { text-align: center; padding: 4rem 2rem; }
        .empty-cart i { font-size: 4rem; color: var(--border); margin-bottom: 1rem; }
        .empty-cart h3 { font-size: 1.5rem; color: var(--text-main); margin-bottom: 1rem; }

        /* Responsive */
        @media (max-width: 768px) {
            .cart-item { flex-direction: column; align-items: flex-start; }
            .item-actions { width: 100%; justify-content: space-between; margin-top: 1rem; }
            .item-subtotal { text-align: left; }
        }
    </style>
</head>

<body>

    <header class="navbar">
        <a href="index.php" class="logo">
            <i class="ri-heart-pulse-fill"></i>
            Loja Farmácia
        </a>
        <div class="nav-actions">
            <?php if ($clienteLogado): ?>
                <div class="user-greeting">
                    <i class="ri-user-smile-line"></i> <?= htmlspecialchars($clienteLogado->nome); ?>
                </div>
            <?php endif; ?>
            <a href="index.php" class="btn btn-outline"><i class="ri-arrow-left-line"></i> Continuar Comprando</a>
        </div>
    </header>

    <main>
        <h1 class="page-title"><i class="ri-shopping-cart-2-fill"></i> Meu Carrinho</h1>

        <?php if ($avisoEstoque): ?>
            <div class="alert alert-warning">
                <i class="ri-error-warning-fill"></i> <?= $avisoEstoque ?>
            </div>
        <?php endif; ?>
        
        <?php if ($msg == 'login_compra'): ?>
            <div class="alert alert-danger">
                <i class="ri-lock-2-fill"></i> Você precisa estar logado para finalizar a compra.
            </div>
        <?php endif; ?>

        <?php if ($msg == 'estoque_insuficiente'): ?>
            <div class="alert alert-danger">
                <i class="ri-close-circle-fill"></i> Um ou mais itens não possem estoque suficiente. Verifique as quantidades.
            </div>
        <?php endif; ?>

        <?php if (!empty($carrinho)): ?>
            <div class="cart-container">
                <div class="cart-items">
                    <?php foreach ($carrinho as $id => $item): 
                        $subtotal = $item['preco'] * $item['quantidade'];
                        $totalCompra += $subtotal;
                    ?>
                        <div class="cart-item">
                            <?php if (!$item['imagem']): ?>
                                <img src="imagens/OIP.webp" class="item-img" alt="Produto">
                            <?php else: ?>
                                <img src="uploads/<?= htmlspecialchars($item['imagem']) ?>" class="item-img" alt="Produto">
                            <?php endif; ?>
                            
                            <div class="item-details">
                                <h3 class="item-title"><?= htmlspecialchars($item['nome']) ?></h3>
                                <p class="item-price">R$ <?= number_format($item['preco'], 2, ',', '.') ?> / un</p>
                            </div>

                            <div class="item-actions">
                                <div class="qty-control">
                                    <form action="atualizarCarrinho.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <input type="hidden" name="acao" value="diminuir">
                                        <button type="submit" class="qty-btn"><i class="ri-subtract-line"></i></button>
                                    </form>
                                    
                                    <div class="qty-value"><?= $item['quantidade'] ?></div>
                                    
                                    <form action="atualizarCarrinho.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $id ?>">
                                        <input type="hidden" name="acao" value="aumentar">
                                        <button type="submit" class="qty-btn"><i class="ri-add-line"></i></button>
                                    </form>
                                </div>

                                <div class="item-subtotal">
                                    R$ <?= number_format($subtotal, 2, ',', '.') ?>
                                </div>

                                <form action="removerCarrinho.php" method="POST" style="margin: 0;">
                                    <input type="hidden" name="id" value="<?= $id ?>">
                                    <button type="submit" class="btn-remove" title="Remover item"><i class="ri-delete-bin-line"></i></button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <div class="summary-row">
                        <span>Subtotal da Compra</span>
                        <span>R$ <?= number_format($totalCompra, 2, ',', '.') ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Frete</span>
                        <span style="color: var(--success); font-weight: 600;">Grátis</span>
                    </div>
                    <div class="summary-total">
                        <span>Total GERAL</span>
                        <span>R$ <?= number_format($totalCompra, 2, ',', '.') ?></span>
                    </div>

                    <?php if ($clienteLogado): ?>
                        <form action="finalizarCompra.php" method="POST">
                            <button type="submit" class="btn btn-success"><i class="ri-secure-payment-line"></i> Finalizar Compra Segura</button>
                        </form>
                    <?php else: ?>
                        <div style="text-align: center;">
                            <a href="login.php?msg=login_compra" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem; font-size: 1.1rem;">
                                <i class="ri-lock-line"></i> Faça Login para Finalizar
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <i class="ri-shopping-cart-2-line"></i>
                <h3>Seu carrinho está vazio</h3>
                <p style="color: var(--text-muted); margin-bottom: 2rem;">Explore nossa vitrine e adicione os melhores produtos para a sua saúde.</p>
                <a href="index.php" class="btn btn-primary"><i class="ri-store-2-line"></i> Ver Produtos</a>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>
