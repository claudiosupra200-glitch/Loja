<?php
session_start();

// Verifica se o funcionário está logado
if (!isset($_SESSION["funcionario"])) {
    header("location: login.php");
    exit();
}

include_once "objetos/produtosController.php";

// Controllers
$prodController = new ProdutosController();

// Dados
$produtos = $prodController->index();

$a = null; // Resultados de pesquisa (produtos)

// Lógica de pesquisa de produtos
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["pesquisar"])) {
        $resultado = $prodController->pesquisarProduto($_POST["pesquisar"]);
        $a = $resultado ? [$resultado] : [];
    }
}

// Lógica de exclusão (apenas produtos)
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET["excluir"])) {
        $prodController->excluirProdutos($_GET["excluir"]);
        header("location: indexFuncionario.php");
        exit();
    }
}

$isGerente = ($_SESSION['funcionario']->cargo === 'Gerente');
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel Administrativo | Loja Farmácia</title>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Remix Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --background: #F5F7FA;
            --white: #FFFFFF;
            --header: #1565C0;
            --primary: #1E88E5;
            --primary-hover: #1565C0;
            --secondary: #E3EAF2;
            --secondary-hover: #D0D7E2;
            --success: #22C55E;
            --danger: #E53935;
            --text: #1F2937;
            --border: #E2E8F0;
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { font-family: 'Outfit', sans-serif; background-color: var(--background); color: var(--text); line-height: 1.6; }

        /* Navbar */
        .navbar {
            background-color: var(--header);
            padding: 0.75rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--white);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .user-info { display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem; }
        .user-info i { font-size: 1.25rem; }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-item { color: var(--white); text-decoration: none; font-weight: 500; font-size: 0.9rem; padding: 0.5rem 1rem; border-radius: 0.375rem; transition: background 0.2s; }
        .nav-item:hover { background: rgba(255,255,255,0.1); }
        .nav-item.active { background: rgba(255,255,255,0.2); font-weight: 600; }
        .logout-btn { color: #FFCDD2; text-decoration: none; font-weight: 600; font-size: 0.9rem; display: flex; align-items: center; gap: 0.4rem; }

        /* Main */
        main { max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem; }

        .alert-denied {
            background-color: #FFF9C4; border: 1px solid #FBC02D; color: #827717;
            padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;
            display: flex; align-items: center; gap: 0.75rem;
        }

        .section-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid var(--border);
        }
        .section-header h2 { font-size: 1.5rem; color: var(--header); font-weight: 600; display: flex; align-items: center; gap: 0.75rem; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; border-radius: 0.5rem; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; font-size: 0.9rem; }
        .btn-primary { background-color: var(--primary); color: var(--white); }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .btn-secondary { background-color: var(--secondary); color: var(--text); }
        .btn-secondary:hover { background-color: var(--secondary-hover); }

        /* Search */
        .search-card {
            background: var(--white); padding: 1.25rem; border-radius: 0.5rem; border: 1px solid var(--border);
            margin-bottom: 2rem; display: flex; gap: 1rem;
        }
        .search-card input { flex: 1; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: 0.375rem; outline: none; transition: border-color 0.2s; }
        .search-card input:focus { border-color: var(--primary); }

        /* Table */
        .table-container { background: var(--white); border-radius: 0.5rem; overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow); }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead { background-color: #F8FAFC; border-bottom: 2px solid var(--border); }
        th { padding: 1rem; font-weight: 600; color: #475569; font-size: 0.85rem; text-transform: uppercase; }
        td { padding: 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        .product-img { width: 48px; height: 48px; object-fit: cover; border-radius: 0.375rem; border: 1px solid var(--border); }

        .btn-action { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 0.375rem; transition: background 0.2s; }
        .btn-edit { color: var(--primary); }
        .btn-edit:hover { background: #E3F2FD; }
        .btn-delete { color: var(--danger); }
        .btn-delete:hover { background: #FFEBEE; }
    </style>
</head>

<body>

    <header class="navbar">
        <div class="user-info">
            <i class="ri-user-settings-line"></i>
            <span>Olá, <strong><?= $_SESSION['funcionario']->nome ?></strong> (<?= $_SESSION['funcionario']->cargo ?>)</span>
        </div>

        <nav class="nav-links">
            <a href="indexFuncionario.php" class="nav-item active"><i class="ri-medicine-bottle-line"></i> Produtos</a>

            <?php if ($isGerente): ?>
                <a href="funcionarios.php" class="nav-item"><i class="ri-team-line"></i> Equipe</a>
            <?php endif; ?>

            <a href="logout.php" class="logout-btn">
                <i class="ri-logout-box-r-line"></i> SAIR
            </a>
        </nav>
    </header>

    <main>

        <?php if (isset($_GET['denied'])): ?>
            <div class="alert-denied">
                <i class="ri-alert-line"></i>
                <span><strong>Acesso negado:</strong> Somente usuários com cargo de Gerente podem gerenciar funcionários.</span>
            </div>
        <?php endif; ?>

        <!-- 📦 Seção de Produtos -->
        <div class="section-header">
            <h2><i class="ri-stack-line"></i> Produtos em Estoque</h2>
            <div class="header-actions">
                <a href="cadastro.php" class="btn btn-primary">
                    <i class="ri-add-line"></i> Novo Produto
                </a>
            </div>
        </div>

        <!-- Barra de pesquisa -->
        <form method="POST" action="indexFuncionario.php" class="search-card">
            <input type="number" name="pesquisar" placeholder="Pesquisar por ID do produto..." required>
            <button type="submit" class="btn btn-secondary">
                <i class="ri-search-line"></i> Pesquisar
            </button>
        </form>

        <?php if ($a): ?>
            <p style="margin-bottom: 1rem; font-size: 0.9rem; color: #64748B;">
                Mostrando resultados para o ID #<?= $_POST['pesquisar'] ?>:
            </p>
        <?php endif; ?>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Preço</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $listaProdutos = $a ? $a : $produtos;
                    if ($listaProdutos): 
                        foreach ($listaProdutos as $produto): 
                    ?>
                        <tr>
                            <td>
                                <?php if (!$produto->imagem): ?>
                                    <img src="imagens/OIP.webp" class="product-img">
                                <?php else: ?>
                                    <img src="uploads/<?= $produto->imagem; ?>" class="product-img">
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: 600; color: var(--header);"><?= $produto->nome; ?></td>
                            <td style="color: #64748B; font-size: 0.875rem;"><?= $produto->descricao; ?></td>
                            <td style="font-weight: 600; color: var(--success);">
                                R$ <?= number_format($produto->preco, 2, ',', '.'); ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="atualizar.php?alterar=<?= $produto->id ?>" class="btn-action btn-edit" title="Editar"><i class="ri-edit-line"></i></a>
                                    <a href="indexFuncionario.php?excluir=<?= $produto->id ?>" class="btn-action btn-delete" title="Excluir" onclick="return confirm('Deseja excluir este produto?')"><i class="ri-delete-bin-line"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        endforeach; 
                    endif; 
                    ?>
                </tbody>
            </table>
        </div>

    </main>

</body>

</html>