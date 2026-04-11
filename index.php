<?php
session_start();

// Redireciona funcionários para a área administrativa
if (isset($_SESSION["funcionario"])) {
    header("location: indexFuncionario.php");
    exit();
}

include_once "objetos/produtosController.php";

$prodController = new produtosController();
$produtos = $prodController->index();

$clienteLogado = isset($_SESSION["cliente"]) ? $_SESSION["cliente"] : null;

// Cálculo da quantidade de itens no carrinho
$qtdCarrinho = 0;
if (isset($_SESSION['carrinho']) && is_array($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $item) {
        $qtdCarrinho += $item['quantidade'];
    }
}
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Loja Farmácia | Produtos</title>
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
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
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
        .btn-outline { background-color: transparent; color: var(--primary); border: 2px solid var(--primary); }
        .btn-outline:hover { background-color: rgba(30, 136, 229, 0.05); }
        .btn-primary { background-color: var(--primary); color: var(--white); }
        .btn-primary:hover { background-color: var(--primary-hover); transform: translateY(-1px); box-shadow: var(--shadow-md); }

        .user-greeting { font-weight: 500; font-size: 0.95rem; color: var(--text-main); display: flex; align-items: center; gap: 0.5rem; }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #0F172A 0%, #1E88E5 100%);
            color: var(--white);
            padding: 4rem 5%;
            text-align: center;
            margin-bottom: 3rem;
        }

        .hero h1 { font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem; }
        .hero p { font-size: 1.1rem; opacity: 0.9; max-width: 600px; margin: 0 auto; }

        /* Store Section */
        main { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem 4rem; }

        .section-title { font-size: 1.75rem; font-weight: 700; color: var(--header); margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem; }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        /* Product Card */
        .card {
            background: var(--white);
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(30, 136, 229, 0.3);
        }

        .card-img-wrapper {
            width: 100%;
            height: 220px;
            background-color: #F1F5F9;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--border);
        }

        .card-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 1rem;
            transition: transform 0.3s ease;
        }

        .card:hover .card-img {
            transform: scale(1.05);
        }

        .badge-estoque {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            backdrop-filter: blur(4px);
        }

        .card-body {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--header);
            margin-bottom: 0.5rem;
        }

        .card-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed var(--border);
        }

        .price {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
        }

        .currency {
            font-size: 0.9rem;
            position: relative;
            top: -0.25rem;
        }

        .btn-buy {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            padding: 0.85rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-buy:hover {
            background-color: var(--primary-hover);
        }

        /* Footer */
        footer {
            background-color: var(--header);
            color: var(--white);
            padding: 2rem 5%;
            text-align: center;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Toast Message */
        #toast {
            visibility: hidden;
            min-width: 250px;
            background-color: var(--header);
            color: #fff;
            text-align: center;
            border-radius: 0.5rem;
            padding: 1rem;
            position: fixed;
            z-index: 1000;
            left: 50%;
            bottom: 30px;
            transform: translateX(-50%);
            font-weight: 600;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        #toast.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        @keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
        }

        @keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
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
                    <i class="ri-user-smile-line"></i> <span style="font-weight: 400;">Olá,</span> <?= htmlspecialchars($clienteLogado->nome); ?>
                </div>
                <a href="perfilCliente.php" class="btn btn-outline" title="Ver meu Perfil"><i class="ri-user-settings-line"></i> Meu Perfil</a>
                <a href="logout.php" class="btn btn-outline" title="Sair da Conta">Sair</a>
            <?php else: ?>
                <a href="cadastroCliente.php" class="btn btn-outline">Criar Conta</a>
                <a href="login.php" class="btn btn-primary">
                    <i class="ri-login-box-line"></i> Login
                </a>
            <?php endif; ?>

            <a href="carrinho.php" class="btn btn-outline" style="position: relative;">
                <i class="ri-shopping-cart-2-line" style="font-size: 1.25rem;"></i>
                <span id="cart-badge" style="position: absolute; top: -5px; right: -5px; background: var(--success); color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 0.75rem; display: <?= $qtdCarrinho > 0 ? 'flex' : 'none' ?>; align-items: center; justify-content: center; font-weight: bold;">
                    <?= $qtdCarrinho ?>
                </span>
            </a>
        </div>
    </header>

    <div class="hero">
        <h1>Sua Saúde em Primeiro Lugar</h1>
        <p>Encontre os melhores produtos farmacêuticos com garantia de qualidade, preços justos e a confiança que você e sua família merecem.</p>
    </div>

    <main>
        <h2 class="section-title"><i class="ri-shopping-bag-3-line"></i> Últimos Lançamentos</h2>

        <div class="product-grid">
            <?php if ($produtos): ?>
                <?php foreach ($produtos as $produto): ?>
                    <div class="card">
                        <?php if ($produto->quantidade > 0): ?>
                            <div class="badge-estoque">Em Estoque</div>
                        <?php else: ?>
                            <div class="badge-estoque" style="color: #EF4444; background: rgba(239, 68, 68, 0.1);">Esgotado</div>
                        <?php endif; ?>

                        <div class="card-img-wrapper">
                            <?php if (!$produto->imagem): ?>
                                <img src="imagens/OIP.webp" class="card-img" alt="Imagem do Produto">
                            <?php else: ?>
                                <img src="uploads/<?= htmlspecialchars($produto->imagem); ?>" class="card-img" alt="Imagem do Produto">
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <h3 class="card-title"><?= htmlspecialchars($produto->nome); ?></h3>
                            <p class="card-desc"><?= htmlspecialchars($produto->descricao); ?></p>
                            
                            <div class="price-row">
                                <span class="price"><span class="currency">R$</span> <?= number_format($produto->preco, 2, ',', '.'); ?></span>
                            </div>
                            
                            <?php if ($produto->quantidade > 0): ?>
                                <div style="display: flex; gap: 0.5rem; margin-top: auto;">
                                    <form action="adicionarCarrinho.php" method="POST" style="flex: 1;">
                                        <input type="hidden" name="id" value="<?= $produto->id ?>">
                                        <input type="hidden" name="acao" value="comprar">
                                        <button type="submit" class="btn-buy" style="background-color: var(--header); width: 100%;">
                                            <i class="ri-shopping-cart-2-fill"></i> Comprar agora
                                        </button>
                                    </form>
                                    <form class="ajax-cart-form">
                                        <input type="hidden" name="id" value="<?= $produto->id ?>">
                                        <input type="hidden" name="acao" value="adicionar">
                                        <input type="hidden" name="ajax" value="true">
                                        <button type="submit" class="btn-buy" style="width: 100%; border: 1px solid var(--primary); background: transparent; color: var(--primary);">
                                            <i class="ri-add-shopping-cart-line"></i> Adicionar ao Carrinho
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <button class="btn-buy" disabled style="background: #ccc; cursor: not-allowed; margin-top: auto;">
                                    <i class="ri-close-circle-line"></i> Produto Indisponível
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Nenhum produto cadastrado no momento.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date("Y"); ?> Loja Farmácia. Todos os direitos reservados.</p>
        <p style="font-size: 0.8rem; margin-top: 0.5rem; opacity: 0.7;">Desenvolvido para demonstração pública de Vendas.</p>
    </footer>

    <div id="toast"><i class="ri-checkbox-circle-fill" style="color: var(--success); font-size: 1.5rem;"></i> <span id="toast-msg">Produto adicionado!</span></div>

    <script>
        document.querySelectorAll('.ajax-cart-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('adicionarCarrinho.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Atualiza o contador
                        const badge = document.getElementById('cart-badge');
                        badge.textContent = data.total_count;
                        badge.style.display = 'flex';
                        
                        // Mostra o Toast
                        const toast = document.getElementById('toast');
                        toast.className = "show";
                        setTimeout(() => { toast.className = toast.className.replace("show", ""); }, 3000);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
            });
        });
    </script>
</body>

</html>
