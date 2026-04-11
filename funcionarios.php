<?php
session_start();

// Verifica se o funcionário está logado
if (!isset($_SESSION["funcionario"])) {
    header("location: login.php");
    exit();
}

// Verifica se o funcionário é Gerente (Role Check)
if ($_SESSION['funcionario']->cargo !== 'Gerente') {
    header("location: indexFuncionario.php?denied=true");
    exit();
}

include_once "objetos/funcionarioController.php";

$funcController = new funcionarioController();
$funcionarios = $funcController->index();

// Lógica de exclusão
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["excluirFunc"])) {
    $funcController->excluirFuncionario($_GET["excluirFunc"]);
    header("location: funcionarios.php");
    exit();
}
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestão de Equipe | Loja Farmácia</title>
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
        .nav-links { display: flex; gap: 1rem; align-items: center; }
        .nav-item { color: var(--white); text-decoration: none; font-weight: 500; font-size: 0.9rem; padding: 0.5rem 1rem; border-radius: 0.375rem; transition: background 0.2s; }
        .nav-item:hover { background: rgba(255,255,255,0.1); }
        .btn-back { color: var(--white); display: flex; align-items: center; gap: 0.25rem; font-weight: 600; text-decoration: none; }

        /* Main */
        main { max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem; }

        .section-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid var(--border);
        }
        .section-header h2 { font-size: 1.5rem; color: var(--header); font-weight: 600; display: flex; align-items: center; gap: 0.75rem; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.25rem; border-radius: 0.5rem; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; font-size: 0.9rem; }
        .btn-success { background-color: var(--success); color: var(--white); }
        .btn-success:hover { background-color: #16A34A; }

        /* Table */
        .table-container { background: var(--white); border-radius: 0.5rem; overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow); }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        thead { background-color: #F8FAFC; border-bottom: 2px solid var(--border); }
        th { padding: 1rem 1.25rem; font-weight: 600; color: #475569; font-size: 0.85rem; text-transform: uppercase; }
        td { padding: 1.25rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        
        .profile-img { width: 52px; height: 52px; object-fit: cover; border-radius: 50%; border: 3px solid #E2E8F0; }
        .badge { display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: #EEF2F7; color: var(--header); }

        .btn-action { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border-radius: 0.5rem; transition: background 0.2s; }
        .btn-edit { color: var(--primary); }
        .btn-edit:hover { background: #E3F2FD; }
        .btn-delete { color: var(--danger); }
        .btn-delete:hover { background: #FFEBEE; }
    </style>
</head>

<body>

    <header class="navbar">
        <div class="user-info">
            <i class="ri-team-line"></i>
            <span>Painel de Equipe Loja Farmácia</span>
        </div>

        <nav class="nav-links">
            <a href="indexFuncionario.php" class="btn-back"><i class="ri-arrow-left-s-line"></i> Painel Principal</a>
        </nav>
    </header>

    <main>
        <!-- 🏢 Gestão de Equipe -->
        <div class="section-header">
            <h2><i class="ri-group-line"></i> Colaboradores</h2>
            <div class="header-actions">
                <a href="cadastroFuncionario.php" class="btn btn-success">
                    <i class="ri-user-add-line"></i> Novo Funcionário
                </a>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Cargo</th>
                        <th>Departamento</th>
                        <th>E-mail</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($funcionarios): ?>
                        <?php foreach ($funcionarios as $func): ?>
                            <tr>
                                <td>
                                    <?php if (!$func->imagem): ?>
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($func->nome) ?>&background=random" class="profile-img">
                                    <?php else: ?>
                                        <img src="uploads/<?= $func->imagem; ?>" class="profile-img">
                                    <?php endif; ?>
                                </td>
                                <td style="font-weight: 600; color: var(--header);"><?= $func->nome; ?></td>
                                <td><span class="badge"><?= $func->cargo; ?></span></td>
                                <td style="color: #64748B; font-size: 0.9rem;"><?= $func->departamento; ?></td>
                                <td style="color: #64748B; font-size: 0.9rem;"><?= $func->emailCorporativo; ?></td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <a href="atualizarFuncionario.php?alterar=<?= $func->id ?>" class="btn-action btn-edit" title="Editar"><i class="ri-edit-line"></i></a>
                                        <a href="funcionarios.php?excluirFunc=<?= $func->id ?>" class="btn-action btn-delete" title="Excluir" onclick="return confirm('Deseja excluir este colaborador?')"><i class="ri-delete-bin-line"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>

</html>
