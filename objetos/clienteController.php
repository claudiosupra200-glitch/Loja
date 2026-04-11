<?php

include_once "configs/database.php";
include_once "cliente.php";

class clienteController
{
    private $bd;
    private $cliente;
    private $img_name;

    public function __construct()
    {
        $banco = new database();
        $this->bd = $banco->conectar();
        $this->cliente = new cliente($this->bd);
    }

    public function index()
    {
        // For potential future admin list or something similar
    }

    public function cadastrarCliente($dados, $arquivo)
    {
        $temArquivo = isset($arquivo['fileToUpload']['name']) &&
            $arquivo['fileToUpload']['name'] !== "" &&
            $arquivo['fileToUpload']['error'] === UPLOAD_ERR_OK;

        // Upload da imagem
        if ($temArquivo) {
            if (!$this->upload($arquivo)) {
                return false;
            }
        } else {
            $this->img_name = null;
        }

        // Validação simples
        if (empty($dados['nome']) || empty($dados['email']) || empty($dados['login']) || empty($dados['senha'])) {
            echo "Campos obrigatórios devem ser preenchidos!";
            return;
        }

        // Atribuição
        $this->cliente->nome = $dados['nome'];
        $this->cliente->email = $dados['email'];
        $this->cliente->telefone = $dados['telefone'];
        $this->cliente->login = $dados['login'];
        $this->cliente->senha = $dados['senha'];
        $this->cliente->imagem = $this->img_name;

        if ($this->cliente->cadastrar()) {
            header("location: login.php?msg=success_cadastro");
            exit();
        }
    }

    public function upload($arquivo)
    {
        if (!isset($arquivo['fileToUpload']) || $arquivo['fileToUpload']['error'] !== UPLOAD_ERR_OK) {
            echo "Erro no upload da imagem.";
            return false;
        }

        $target_dir = "uploads/";
        $nome = $arquivo['fileToUpload']['name'];
        $tmp = $arquivo['fileToUpload']['tmp_name'];
        $tamanho = $arquivo['fileToUpload']['size'];

        $imageFileType = strtolower(pathinfo($nome, PATHINFO_EXTENSION));
        $random_name = uniqid('client_', true) . '.' . $imageFileType;
        $this->img_name = $random_name;
        $upload_file = $target_dir . $random_name;

        if (empty($tmp) || !file_exists($tmp)) {
            echo "Arquivo inválido.";
            return false;
        }

        $check = getimagesize($tmp);
        if ($check === false) {
            echo "O arquivo não é uma imagem.";
            return false;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Formato não suportado.";
            return false;
        }

        if (move_uploaded_file($tmp, $upload_file)) {
            return true;
        } else {
            echo "Erro ao salvar a imagem.";
            return false;
        }
    }

    public function atualizarPerfil($dados, $arquivo)
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $clienteSessao = $_SESSION['cliente'];

        // 1. Validar campos obrigatórios
        if (empty($dados['nome']) || empty($dados['email']) || empty($dados['login'])) {
            return "Nome, Email e Login são obrigatórios.";
        }

        // 2. Validar Login Único
        if (!$this->cliente->verificarLoginUnico($dados['login'], $clienteSessao->id)) {
            return "Este Login já está sendo utilizado por outro usuário.";
        }

        // 3. Tratar Upload de Imagem
        $temArquivo = isset($arquivo['fileToUpload']['name']) && $arquivo['fileToUpload']['name'] !== "";
        if ($temArquivo) {
            if ($this->upload($arquivo)) {
                $this->cliente->imagem = $this->img_name;
            } else {
                return "Erro no upload da imagem.";
            }
        } else {
            $this->cliente->imagem = $clienteSessao->imagem;
        }

        // 4. Tratar Senha
        if (!empty($dados['nova_senha'])) {
            // Validar senha atual
            $stmt = $this->bd->prepare("SELECT senha FROM clientes WHERE id = :id");
            $stmt->bindParam(":id", $clienteSessao->id);
            $stmt->execute();
            $cliLog = $stmt->fetch(PDO::FETCH_OBJ);

            if (!password_verify($dados['senha_atual'], $cliLog->senha)) {
                return "A senha atual está incorreta.";
            }

            if ($dados['nova_senha'] !== $dados['confirma_senha']) {
                return "A nova senha e a confirmação não conferem.";
            }
            $this->cliente->senha = $dados['nova_senha'];
        }

        // 5. Configurar objeto e atualizar
        $this->cliente->id = $clienteSessao->id;
        $this->cliente->nome = $dados['nome'];
        $this->cliente->email = $dados['email'];
        $this->cliente->telefone = $dados['telefone'];
        $this->cliente->login = $dados['login'];

        if ($this->cliente->atualizar()) {
            // Atualizar Sessão
            $_SESSION['cliente'] = $this->cliente->buscarCliente($clienteSessao->id);
            return true;
        } else {
            return "Erro ao atualizar dados no banco.";
        }
    }

    public function pegarHistorico($id_cliente)
    {
        return $this->cliente->listarCompras($id_cliente);
    }

    public function pegarDetalhes($id_compra, $id_cliente)
    {
        return $this->cliente->buscarDetalhesCompra($id_compra, $id_cliente);
    }
}
