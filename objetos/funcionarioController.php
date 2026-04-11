<?php

include_once "configs/database.php";
include_once "funcionario.php";

class funcionarioController
{
    private $bd;
    private $funcionario;
    private $img_name;

    public function __construct()
    {
        $banco = new database();
        $this->bd = $banco->conectar();
        $this->funcionario = new funcionario($this->bd);
    }

    public function index()
    {
        return $this->funcionario->lerTodos();
    }

    public function pesquisarFuncionario($pesquisa, $tipo)
    {
        return $this->funcionario->pesquisarFuncionario($pesquisa, $tipo);
    }

    public function cadastrarFuncionario($dados, $arquivo)
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
        if (empty($dados['nome']) || empty($dados['cargo']) || empty($dados['departamento']) || empty($dados['pis']) || empty($dados['emailCorporativo']) || empty($dados['formacao']) || empty($dados['senha'])) {
            echo "Todos os campos devem ser preenchidos!";
            return;
        }

        // Atribuição
        $this->funcionario->nome = $dados['nome'];
        $this->funcionario->cargo = $dados['cargo'];
        $this->funcionario->departamento = $dados['departamento'];
        $this->funcionario->pis = $dados['pis'];
        $this->funcionario->emailCorporativo = $dados['emailCorporativo'];
        $this->funcionario->formacao = $dados['formacao'];
        $this->funcionario->imagem = $this->img_name;
        $this->funcionario->senha = $dados['senha'];

        if ($this->funcionario->cadastrar()) {
            header("location: indexFuncionario.php");
            exit();
        }
    }

    public function excluirFuncionario($id)
    {
        $this->funcionario->id = $id;

        if ($this->funcionario->excluir()) {
            header("location: indexFuncionario.php");
            exit();
        }
    }

    public function atualizarFuncionario($dados)
    {

        $this->funcionario->id = $dados['id'];
        $this->funcionario->nome = $dados['nome'];
        $this->funcionario->cargo = $dados['cargo'];
        $this->funcionario->departamento = $dados['departamento'];
        $this->funcionario->pis = $dados['pis'];
        $this->funcionario->emailCorporativo = $dados['emailCorporativo'];
        $this->funcionario->formacao = $dados['formacao'];
        $this->funcionario->senha = $dados['senha'];

        // ✅ VERIFICA SE FOI ENVIADA NOVA IMAGEM
        if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] === UPLOAD_ERR_OK) {

            if ($this->upload($_FILES)) {
                $this->funcionario->imagem = $this->img_name;
            }

        } else {
            // ✅ MANTÉM A IMAGEM ANTIGA
            $funcionarioAtual = $this->funcionario->buscarFuncionarios($dados['id']);
            $this->funcionario->imagem = $funcionarioAtual->imagem;
        }

        if ($this->funcionario->atualizar()) {
            header("location: indexFuncionario.php");
            exit();
        }
    }

    public function procurarFuncionarios($id)
    {
        return $this->funcionario->buscarFuncionarios($id);
    }

    public function login($email, $senha)
    {
        $this->funcionario->emailCorporativo = $email;
        $this->funcionario->senha = $senha;
        $this->funcionario->login();
    }

    public function upload($arquivo)
    {

        if (!isset($arquivo['fileToUpload']) || $arquivo['fileToUpload']['error'] !== UPLOAD_ERR_OK) {
            echo "Nenhum arquivo enviado ou erro no upload.";
            return false;
        }

        $target_dir = "uploads/";

        // Dados do arquivo
        $nome = $arquivo['fileToUpload']['name'];
        $tmp = $arquivo['fileToUpload']['tmp_name'];
        $tamanho = $arquivo['fileToUpload']['size'];

        $imageFileType = strtolower(pathinfo($nome, PATHINFO_EXTENSION));

        // Nome único
        $random_name = uniqid('img_', true) . '.' . $imageFileType;
        $this->img_name = $random_name;

        $upload_file = $target_dir . $random_name;

        // Verifica se é imagem
        if (empty($tmp) || !file_exists($tmp)) {
            echo "Arquivo inválido.";
            return false;
        }

        $check = getimagesize($tmp);
        if ($check === false) {
            echo "O arquivo não é uma imagem.";
            return false;
        }

        // Tamanho máximo
        if ($tamanho > 50000000) {
            echo "Arquivo muito grande.";
            return false;
        }

        // Tipos permitidos
        if (
            $imageFileType != "jpg" &&
            $imageFileType != "png" &&
            $imageFileType != "jpeg" &&
            $imageFileType != "gif"
        ) {
            echo "Tipo de arquivo não suportado.";
            return false;
        }

        // Upload
        if (move_uploaded_file($tmp, $upload_file)) {
            return true;
        } else {
            echo "Erro ao enviar a imagem.";
            return false;
        }
    }
}
