<?php
session_start();

// Verifica se o funcionário está logado
if (!isset($_SESSION["funcionario"])) {
    header("location: login.php");
    exit();
}

// Verifica se o funcionário é Gerente (Controle de Acesso)
if ($_SESSION['funcionario']->cargo !== 'Gerente') {
    header("location: index.php?denied=true");
    exit();
}

include_once "objetos/funcionarioController.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $controller = new funcionarioController();

    if (isset($_POST["cadastrar"])) {
        $controller->cadastrarFuncionario($_POST["funcionario"], $_FILES);
    }
}
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Novo Funcionário | Loja Farmácia</title>
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

        .card { background: var(--white); width: 100%; max-width: 600px; padding: 2.5rem; border-radius: 0.75rem; box-shadow: var(--shadow); border: 1px solid var(--border); }

        .header { margin-bottom: 2rem; border-bottom: 2px solid var(--background); padding-bottom: 1rem; }
        .header h1 { font-size: 1.5rem; color: var(--header); font-weight: 600; display: flex; align-items: center; gap: 0.6rem; }

        .grid-form { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .form-group { margin-bottom: 0.25rem; }
        .form-group.full-width { grid-column: span 2; }
        
        label { display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem; }
        input, select { width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: 0.375rem; outline: none; transition: border-color 0.2s; font-family: inherit; font-size: 0.95rem; }
        input:focus, select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(30,136,229,0.1); }

        .actions { margin-top: 2.5rem; display: flex; gap: 1rem; }
        .btn { flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.85rem; border-radius: 0.375rem; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; font-size: 0.95rem; }
        .btn-success { background-color: var(--success); color: var(--white); }
        .btn-success:hover { background-color: #16A34A; }
        .btn-secondary { background-color: var(--secondary); color: var(--text); }
        .btn-secondary:hover { background-color: var(--secondary-hover); }

        @media (max-width: 480px) { .grid-form { grid-template-columns: 1fr; } .form-group.full-width { grid-column: span 1; } }
    </style>
</head>

<body>

    <div class="card">
        <div class="header">
            <h1><i class="ri-user-add-line"></i> Cadastro de Funcionário</h1>
        </div>

        <form action="cadastroFuncionario.php" method="post" enctype="multipart/form-data">
            <div class="grid-form">
                <div class="form-group full-width">
                    <label>Nome Completo</label>
                    <input type="text" name="funcionario[nome]" placeholder="Ex: João da Silva" required>
                </div>

                <div class="form-group">
                    <label>Cargo</label>
                    <select name="funcionario[cargo]" required>
                        <option value="" disabled selected>Selecione...</option>
                        <option value="Gerente">Gerente</option>
                        <option value="Vendedor">Vendedor</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Departamento</label>
                    <input type="text" name="funcionario[departamento]" placeholder="Ex: Farmácia" required>
                </div>

                <div class="form-group">
                    <label>PIS</label>
                    <input type="text" name="funcionario[pis]" placeholder="000.00000.00-0" required>
                </div>

                <div class="form-group">
                    <label>Formação</label>
                    <input type="text" name="funcionario[formacao]" placeholder="Ex: Superior Completo" required>
                </div>

                <div class="form-group full-width">
                    <label>E-mail Corporativo</label>
                    <input type="email" name="funcionario[emailCorporativo]" placeholder="email@loja.com" required>
                </div>

                <div class="form-group full-width">
                    <label>Senha de Acesso</label>
                    <input type="password" name="funcionario[senha]" placeholder="Mínimo 6 caracteres" required>
                </div>

                <div class="form-group full-width">
                    <label>Foto do Perfil</label>
                    <input type="file" name="fileToUpload">
                </div>
            </div>

            <div class="actions">
                <a href="funcionarios.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" name="cadastrar" class="btn btn-success">
                    <i class="ri-save-line"></i> Cadastrar Funcionário
                </button>
            </div>
        </form>
    </div>

</body>

</html>