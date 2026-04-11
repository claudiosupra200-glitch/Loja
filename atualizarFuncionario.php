<?php
session_start();

// Verifica se o funcionário está logado
if (!isset($_SESSION["funcionario"])) {
    header("location: login.php");
    exit();
}

// Verifica se o funcionário é Gerente (Controle de Acesso)
if ($_SESSION['funcionario']->cargo !== 'Gerente') {
    header("location: indexFuncionario.php?denied=true");
    exit();
}

include_once("objetos/funcionarioController.php");

$controller = new funcionarioController();
$a = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['alterar'])) {
    $a = $controller->buscarFuncionario($_GET['alterar']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['funcionario'])) {
    $controller->atualizarFuncionario($_POST['funcionario'], $_FILES);
} else {
    header("location: funcionarios.php");
    exit();
}

if (!$a) {
    header("location: funcionarios.php");
    exit();
}
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Funcionário | Loja Farmácia</title>
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
            --shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { font-family: 'Outfit', sans-serif; background-color: var(--background); color: var(--text); padding: 2.5rem; display: flex; justify-content: center; min-height: 100vh; align-items: flex-start; }

        .card { background: var(--white); width: 100%; max-width: 600px; padding: 2.5rem; border-radius: 0.75rem; box-shadow: var(--shadow); border: 1px solid var(--border); }

        .header { margin-bottom: 2rem; border-bottom: 2px solid var(--background); padding-bottom: 1rem; text-align: center; }
        .header h1 { font-size: 1.5rem; color: var(--header); font-weight: 600; }

        .profile-hero { display: flex; align-items: center; gap: 1.5rem; padding: 1.25rem; background: #F8FAFC; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid var(--border); }
        .hero-img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .hero-info h3 { font-size: 1.1rem; color: var(--header); }
        .hero-info p { font-size: 0.85rem; color: #64748B; font-weight: 500; }

        .grid-form { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .form-group.full-width { grid-column: span 2; }
        
        label { display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem; }
        input, select { width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: 0.375rem; outline: none; transition: border-color 0.2s; font-family: inherit; font-size: 0.95rem; }
        input:focus, select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(30,136,229,0.1); }

        .helper-text { font-size: 0.8rem; color: #94A3B8; margin-top: 0.25rem; }

        .actions { margin-top: 2.5rem; display: flex; gap: 1rem; }
        .btn { flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.85rem; border-radius: 0.375rem; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; font-size: 0.95rem; }
        .btn-primary { background-color: var(--primary); color: var(--white); }
        .btn-primary:hover { background-color: var(--primary-hover); }
        .btn-secondary { background-color: var(--secondary); color: var(--text); }
        .btn-secondary:hover { background-color: var(--secondary-hover); }

        @media (max-width: 480px) { .grid-form { grid-template-columns: 1fr; } .form-group.full-width { grid-column: span 1; } }
    </style>
</head>

<body>

    <div class="card">
        <div class="header">
            <h1>Editar Colaborador</h1>
        </div>

        <div class="profile-hero">
            <?php if ($a->imagem == ""): ?>
                <img src="https://via.placeholder.com/150" class="hero-img">
            <?php else: ?>
                <img src="uploads/<?= $a->imagem; ?>" class="hero-img">
            <?php endif; ?>
            <div class="hero-info">
                <h3><?= $a->nome; ?></h3>
                <p><i class="ri-briefcase-line"></i> <?= $a->cargo; ?> &bull; <?= $a->departamento; ?></p>
            </div>
        </div>

        <form action="atualizarFuncionario.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="funcionario[id]" value="<?= $a->id ?>">

            <div class="grid-form">
                <div class="form-group full-width">
                    <label>Nome Completo</label>
                    <input type="text" name="funcionario[nome]" value="<?= $a->nome ?>" required>
                </div>

                <div class="form-group">
                    <label>Cargo</label>
                    <input type="text" name="funcionario[cargo]" value="<?= $a->cargo ?>" required>
                </div>

                <div class="form-group">
                    <label>Departamento</label>
                    <input type="text" name="funcionario[departamento]" value="<?= $a->departamento ?>" required>
                </div>

                <div class="form-group">
                    <label>PIS</label>
                    <input type="text" name="funcionario[pis]" value="<?= $a->pis ?>" required>
                </div>

                <div class="form-group">
                    <label>Formação</label>
                    <input type="text" name="funcionario[formacao]" value="<?= $a->formacao ?>" required>
                </div>

                <div class="form-group full-width">
                    <label>E-mail Corporativo</label>
                    <input type="email" name="funcionario[emailCorporativo]" value="<?= $a->emailCorporativo ?>" required>
                </div>

                <div class="form-group full-width">
                    <label>Senha de Acesso</label>
                    <input type="password" name="funcionario[senha]" placeholder="••••••••">
                    <p class="helper-text">* Deixe em branco para manter a senha atual.</p>
                </div>

                <div class="form-group full-width">
                    <label>Trocar Foto do Perfil</label>
                    <input type="file" name="fileToUpload">
                </div>
            </div>

            <div class="actions">
                <a href="funcionarios.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-3-line"></i> Salvar Alterações
                </button>
            </div>
        </form>
    </div>

</body>

</html>