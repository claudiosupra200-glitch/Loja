<?php
session_start();

include_once "objetos/clienteController.php";

$controller = new clienteController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $controller->cadastrarCliente($_POST["cliente"], $_FILES);
}
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro de Cliente | Loja Farmácia</title>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        .card { background: var(--white); width: 100%; max-width: 600px; padding: 2.5rem; border-radius: 0.75rem; box-shadow: var(--shadow); border: 1px solid var(--border); }

        .header { margin-bottom: 2rem; border-bottom: 2px solid var(--background); padding-bottom: 1rem; }
        .header h1 { font-size: 1.5rem; color: var(--header); font-weight: 600; display: flex; align-items: center; gap: 0.6rem; }

        .grid-form { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .form-group { margin-bottom: 0.25rem; }
        .form-group.full-width { grid-column: span 2; }
        
        label { display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem; }
        input { width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: 0.375rem; outline: none; transition: border-color 0.2s; font-family: inherit; font-size: 0.95rem; }
        input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(30,136,229,0.1); }

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
            <h1><i class="ri-user-add-line"></i> Cadastro de Cliente</h1>
        </div>

        <form action="cadastroCliente.php" method="post" enctype="multipart/form-data">
            <div class="grid-form">
                <div class="form-group full-width">
                    <label>Nome Completo</label>
                    <input type="text" name="cliente[nome]" placeholder="Ex: Maria das Graças" required>
                </div>

                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="cliente[email]" placeholder="maria@email.com" required>
                </div>

                <div class="form-group">
                    <label>Telefone</label>
                    <input type="text" name="cliente[telefone]" placeholder="(00) 00000-0000">
                </div>

                <div class="form-group">
                    <label>Nome de Usuário (Login)</label>
                    <input type="text" name="cliente[login]" placeholder="maria123" required>
                </div>

                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" name="cliente[senha]" placeholder="Senha de acesso" required>
                </div>

                <div class="form-group full-width">
                    <label>Foto de Perfil (Opcional)</label>
                    <input type="file" name="fileToUpload">
                </div>
            </div>

            <div class="actions">
                <a href="login.php" class="btn btn-secondary">Já tenho conta</a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Criar minha Conta
                </button>
            </div>
        </form>
    </div>
</body>

</html>
