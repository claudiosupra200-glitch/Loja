<?php
session_start();

// Verifica se o funcionário está logado
if (!isset($_SESSION["funcionario"])) {
    header("location: login.php");
    exit();
}

include_once("objetos/produtosController.php");

$controller = new ProdutosController();
$a = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['alterar'])) {
    $a = $controller->procurarProdutos($_GET['alterar']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produtos'])) {
    $controller->atualizarProdutos($_POST['produtos'], $_FILES);
} else {
    header("location: index.php");
    exit();
}

if (!$a) {
    header("location: index.php");
    exit();
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Produto | Loja Farmácia</title>
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
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { font-family: 'Outfit', sans-serif; background-color: var(--background); color: var(--text); padding: 2.5rem; display: flex; justify-content: center; min-height: 100vh; align-items: flex-start; }

        .card { background: var(--white); width: 100%; max-width: 500px; padding: 2.5rem; border-radius: 0.75rem; box-shadow: var(--shadow); border: 1px solid var(--border); }

        .header { margin-bottom: 2rem; border-bottom: 2px solid var(--background); padding-bottom: 1rem; text-align: center; }
        .header h1 { font-size: 1.5rem; color: var(--header); font-weight: 600; }

        .preview-box { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #F8FAFC; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid var(--border); }
        .preview-img { width: 64px; height: 64px; border-radius: 0.375rem; object-fit: cover; border: 1px solid var(--border); }
        .preview-info h3 { font-size: 1rem; color: var(--header); }
        .preview-info p { font-size: 0.8rem; color: #64748B; }

        .form-group { margin-bottom: 1.25rem; }
        label { display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem; }
        input { width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: 0.375rem; outline: none; transition: border-color 0.2s; font-family: inherit; font-size: 0.95rem; }
        input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(30,136,229,0.1); }

        .actions { margin-top: 2rem; display: flex; gap: 1rem; }
        .btn { flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem; border-radius: 0.375rem; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; font-size: 0.95rem; }
        .btn-primary { background-color: var(--primary); color: var(--white); }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .btn-secondary { background-color: var(--secondary); color: var(--text); }
        .btn-secondary:hover { background-color: var(--secondary-hover); }
    </style>
</head>
<body>

<div class="card">
    <div class="header">
        <h1>Editar Produto</h1>
    </div>

    <div class="preview-box">
        <?php if($a->imagem == "") : ?>
            <img src="imagens/OIP.webp" class="preview-img">
        <?php else : ?>
            <img src="uploads/<?= $a->imagem; ?>" class="preview-img">
        <?php endif; ?>
        <div class="preview-info">
            <h3><?= $a->nome; ?></h3>
            <p>ID: #<?= $a->id; ?></p>
        </div>
    </div>

    <form action="atualizar.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="produtos[id]" value="<?= $a->id ?>">

        <div class="form-group">
            <label>Nome Comercial</label>
            <input type="text" name="produtos[nome]" value="<?= $a->nome ?>" required>
        </div>

        <div class="form-group">
            <label>Descrição</label>
            <input type="text" name="produtos[descricao]" value="<?= $a->descricao ?>" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label>Quantidade estoque</label>
                <input type="number" name="produtos[quantidade]" value="<?= $a->quantidade ?>" required>
            </div>
            <div class="form-group">
                <label>Preço unitário</label>
                <input type="text" name="produtos[preco]" value="<?= $a->preco ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Trocar imagem (opcional)</label>
            <input type="file" name="fileToUpload">
        </div>

        <div class="actions">
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">
                <i class="ri-refresh-line"></i> Salvar Alterações
            </button>
        </div>
    </form>
</div>

</body>
</html>