<?php
include_once "objetos/funcionario.php";
include_once "configs/banco.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $database = new Banco();
    $db = $database->getConexao();

    $f = new funcionario($db);
    $f->emailCorporativo = $_POST["login"];
    $f->senha = $_POST["senha"];
    $f->login();
}
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Corporativo | Loja Farmácia</title>
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
            --text: #1F2937;
            --border: #E2E8F0;
            --error: #E53935;
            --shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--background);
            color: var(--text);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .login-card {
            background-color: var(--white);
            width: 100%;
            max-width: 400px;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: var(--shadow);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo-icon {
            background: var(--header);
            width: 56px;
            height: 56px;
            border-radius: 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: white;
            font-size: 1.75rem;
        }

        .title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--header);
            margin-bottom: 0.5rem;
        }

        .subtitle {
            font-size: 0.9rem;
            color: #64748B;
        }

        .form-group {
            margin-bottom: 1.25rem;
            position: relative;
        }

        .input-field {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            background: #F8FAFC;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            font-family: inherit;
            outline: none;
            transition: all 0.2s;
            font-size: 0.95rem;
        }

        .input-field:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
        }

        .form-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 1.1rem;
        }

        .error-message {
            background-color: #FEF2F2;
            color: var(--error);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            border: 1px solid #FEE2E2;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-login {
            width: 100%;
            padding: 0.9rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-login:hover {
            background-color: var(--primary-hover);
        }

        .footer-text {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.8rem;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="logo-container">
            <div class="logo-icon">
                <i class="ri-medicine-bottle-fill"></i>
            </div>
            <h2 class="title">Login Corporativo</h2>
            <p class="subtitle">Acesse o sistema de gestão Loja Farmácia</p>
        </div>

        <?php if (isset($_GET["error"])): ?>
            <div class="error-message">
                <i class="ri-error-warning-line"></i>
                Credenciais inválidas. Verifique seu login.
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <input type="email" name="login" class="input-field" placeholder="E-mail Corporativo" required>
                <i class="ri-mail-line"></i>
            </div>

            <div class="form-group">
                <input type="password" name="senha" class="input-field" placeholder="Senha" required>
                <i class="ri-lock-password-line"></i>
            </div>

            <button type="submit" class="btn-login">
                <span>Entrar</span>
                <i class="ri-arrow-right-line"></i>
            </button>
        </form>

        <p class="footer-text">
            Acesso Seguro &bull; Portal do Funcionário
        </p>
    </div>

</body>

</html>