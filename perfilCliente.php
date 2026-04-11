<?php
session_status() === PHP_SESSION_NONE ? session_start() : null;

if (!isset($_SESSION["cliente"])) {
    header("location: login.php");
    exit();
}

include_once "objetos/clienteController.php";
$controller = new clienteController();
$cliente = $_SESSION["cliente"];

$msg_sucesso = null;
$msg_erro = null;

// Lógica de Atualização
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_atualizar'])) {
    $resultado = $controller->atualizarPerfil($_POST, $_FILES);
    if ($resultado === true) {
        $msg_sucesso = "Perfil atualizado com sucesso!";
        $cliente = $_SESSION["cliente"]; // Refresh local object
    } else {
        $msg_erro = $resultado;
    }
}

// Lógica de Histórico
$historico = $controller->pegarHistorico($cliente->id);

// Determina qual aba exibir
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'perfil';
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Minha Área | Loja Farmácia</title>
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
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { font-family: 'Outfit', sans-serif; background-color: var(--background); color: var(--text-main); line-height: 1.5; min-height: 100vh; }

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
        .logo { display:flex; align-items:center; gap:0.75rem; text-decoration:none; color:var(--primary); font-weight:700; font-size:1.5rem; }

        /* Main Layout */
        .container { max-width: 1200px; margin: 3rem auto; padding: 0 1.5rem; display: grid; grid-template-columns: 280px 1fr; gap: 2.5rem; }

        @media (max-width: 992px) {
            .container { grid-template-columns: 1fr; }
        }

        /* Sidebar Menu */
        .sidebar { background: var(--white); border-radius: 1rem; border: 1px solid var(--border); padding: 1.5rem; box-shadow: var(--shadow-sm); height: fit-content; }
        .profile-summary { text-align: center; margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border); }
        .profile-img-sidebar { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary); padding: 3px; margin-bottom: 1rem; }
        .profile-name-sidebar { font-weight: 700; color: var(--header); font-size: 1.1rem; }
        
        .nav-menu { list-style: none; }
        .nav-link { 
            display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1rem; border-radius: 0.5rem; 
            text-decoration: none; color: var(--text-main); font-weight: 500; transition: all 0.2s; margin-bottom: 0.5rem;
        }
        .nav-link:hover { background-color: #F1F5F9; color: var(--primary); }
        .nav-link.active { background-color: var(--primary); color: var(--white); }

        /* Content Area */
        .content-card { background: var(--white); border-radius: 1rem; border: 1px solid var(--border); padding: 2.5rem; box-shadow: var(--shadow-sm); }
        .section-title { font-size: 1.5rem; font-weight: 700; color: var(--header); margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem; }

        /* Alerts */
        .alert { padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.75rem; }
        .alert-success { background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; }
        .alert-danger { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }

        /* View Info Mode */
        .info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; }
        .info-item label { display: block; font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-bottom: 0.25rem; }
        .info-item p { font-size: 1.1rem; color: var(--header); font-weight: 500; }

        /* Form Styles */
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group.full { grid-column: 1 / -1; }
        label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-main); font-size: 0.95rem; }
        input[type="text"], input[type="email"], input[type="password"], input[type="tel"] {
            width: 100%; padding: 0.85rem 1rem; border-radius: 0.5rem; border: 1px solid var(--border); outline: none; transition: border-color 0.2s; font-family: inherit; font-size: 1rem;
        }
        input:focus { border-color: var(--primary); }
        
        .btn-submit { background: var(--primary); color: white; border: none; padding: 1rem 2rem; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; gap: 0.5rem; }
        .btn-submit:hover { background: var(--primary-hover); }

        /* Table History */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 1rem; border-bottom: 2px solid var(--border); color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; }
        td { padding: 1rem; border-bottom: 1px solid var(--border); font-size: 0.95rem; }
        .status-badge { padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .status-pendente { background: #FEF3C7; color: #92400E; }
        .btn-details { color: var(--primary); text-decoration: none; font-weight: 600; }
        .btn-details:hover { text-decoration: underline; }

        .empty-history { text-align: center; padding: 3rem 0; color: var(--text-muted); }
    </style>
</head>

<body>

    <header class="navbar">
        <a href="index.php" class="logo">
            <i class="ri-heart-pulse-fill"></i>
            Loja Farmácia
        </a>
        <a href="index.php" class="btn btn-outline"><i class="ri-arrow-left-line"></i> Voltar à Loja</a>
    </header>

    <main class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="profile-summary">
                <?php if ($cliente->imagem): ?>
                    <img src="uploads/<?= htmlspecialchars($cliente->imagem) ?>" class="profile-img-sidebar" alt="Perfil">
                <?php else: ?>
                    <img src="imagens/OIP.webp" class="profile-img-sidebar" alt="Perfil">
                <?php endif; ?>
                <div class="profile-name-sidebar"><?= htmlspecialchars($cliente->nome) ?></div>
                <div style="font-size: 0.85rem; color: var(--text-muted);"><?= htmlspecialchars($cliente->email) ?></div>
            </div>

            <nav class="nav-menu">
                <a href="?tab=perfil" class="nav-link <?= $tab == 'perfil' ? 'active' : '' ?>">
                    <i class="ri-user-line"></i> Dados do Cadastro
                </a>
                <a href="?tab=editar" class="nav-link <?= $tab == 'editar' ? 'active' : '' ?>">
                    <i class="ri-edit-box-line"></i> Editar Informações
                </a>
                <a href="?tab=historico" class="nav-link <?= $tab == 'historico' ? 'active' : '' ?>">
                    <i class="ri-history-line"></i> Minhas Compras
                </a>
                <hr style="margin: 1rem 0; border: none; border-top: 1px solid var(--border);">
                <a href="logout.php" class="nav-link" style="color: var(--danger);">
                    <i class="ri-logout-box-line"></i> Sair
                </a>
            </nav>
        </aside>

        <!-- Content Area -->
        <section class="content-card">
            <?php if ($msg_sucesso): ?>
                <div class="alert alert-success"><i class="ri-checkbox-circle-line"></i> <?= $msg_sucesso ?></div>
            <?php endif; ?>
            <?php if ($msg_erro): ?>
                <div class="alert alert-danger"><i class="ri-error-warning-line"></i> <?= $msg_erro ?></div>
            <?php endif; ?>

            <?php if ($tab == 'perfil'): ?>
                <h2 class="section-title"><i class="ri-user-3-fill"></i> Dados do Cadastro</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Nome Completo</label>
                        <p><?= htmlspecialchars($cliente->nome) ?></p>
                    </div>
                    <div class="info-item">
                        <label>E-mail</label>
                        <p><?= htmlspecialchars($cliente->email) ?></p>
                    </div>
                    <div class="info-item">
                        <label>Telefone</label>
                        <p><?= htmlspecialchars($cliente->telefone) ?: 'Não informado' ?></p>
                    </div>
                    <div class="info-item">
                        <label>Login de Acesso</label>
                        <p><?= htmlspecialchars($cliente->login) ?></p>
                    </div>
                </div>
                <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                    <a href="?tab=editar" class="btn-submit" style="display: inline-flex; width: auto;">
                        <i class="ri-edit-2-line"></i> Editar Dados
                    </a>
                </div>

            <?php elseif ($tab == 'editar'): ?>
                <h2 class="section-title"><i class="ri-settings-3-fill"></i> Editar Informações</h2>
                <form action="?tab=editar" method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nome Completo</label>
                            <input type="text" name="nome" value="<?= htmlspecialchars($cliente->nome) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>E-mail</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($cliente->email) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Telefone</label>
                            <input type="tel" name="telefone" value="<?= htmlspecialchars($cliente->telefone) ?>">
                        </div>
                        <div class="form-group">
                            <label>Login de Usuário</label>
                            <input type="text" name="login" value="<?= htmlspecialchars($cliente->login) ?>" required>
                        </div>
                        <div class="form-group full">
                            <label>Imagem de Perfil</label>
                            <input type="file" name="fileToUpload" accept="image/*" style="padding-top: 5px;">
                            <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 5px;">Formatos: JPG, PNG, GIF. Tamanho máx: 5MB.</p>
                        </div>

                        <div class="form-group full" style="margin-top: 1rem; padding: 1.5rem; background: #F8FAFC; border-radius: 0.5rem; border: 1px solid var(--border);">
                            <h3 style="font-size: 1rem; margin-bottom: 1.5rem; color: var(--header);">Alterar Senha <small style="font-weight: 400; color: var(--text-muted);">(Opcional)</small></h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Senha Atual</label>
                                    <input type="password" name="senha_atual" placeholder="Necessária para trocar de senha">
                                </div>
                                <div class="form-group">
                                    &nbsp;
                                </div>
                                <div class="form-group">
                                    <label>Nova Senha</label>
                                    <input type="password" name="nova_senha" placeholder="Mínimo 6 caracteres">
                                </div>
                                <div class="form-group">
                                    <label>Confirmar Nova Senha</label>
                                    <input type="password" name="confirma_senha">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 2rem;">
                        <button type="submit" name="btn_atualizar" class="btn-submit">
                            <i class="ri-save-line"></i> Salvar Alterações
                        </button>
                    </div>
                </form>

            <?php elseif ($tab == 'historico'): ?>
                <h2 class="section-title"><i class="ri-shopping-cart-line"></i> Meu Histórico de Compras</h2>
                <div class="table-container">
                    <?php if ($historico): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Pedido #</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historico as $compra): ?>
                                    <tr>
                                        <td><?= date("d/m/Y H:i", strtotime($compra->data)) ?></td>
                                        <td><strong><?= str_pad($compra->id, 5, "0", STR_PAD_LEFT) ?></strong></td>
                                        <td style="font-weight: 600; color: var(--primary);">R$ <?= number_format($compra->total, 2, ',', '.') ?></td>
                                        <td><span class="status-badge status-<?= $compra->status ?>"><?= $compra->status ?></span></td>
                                        <td><a href="detalheCompra.php?id=<?= $compra->id ?>" class="btn-details">Ver Detalhes</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-history">
                            <i class="ri-shopping-bag-line" style="font-size: 3rem; opacity: 0.5;"></i>
                            <p>Você ainda não realizou nenhuma compra.</p>
                            <a href="index.php" class="btn btn-primary" style="margin-top: 1rem;">Começar a comprar</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

</body>

</html>
