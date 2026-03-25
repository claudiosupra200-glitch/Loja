<?php

include_once "configs/database.php";
include_once "produtos.php";

class produtosController {
    private $bd;
    private $produtos;
    private $img_name;

    public function __construct() {
        $banco = new database();
        $this->bd = $banco->conectar();
        $this->produtos = new produtos($this->bd);
    }

    public function index(){
        return $this->produtos->lerTodos();
    }

    public function pesquisarProduto($id){
        return $this->produtos->pesquisarProduto($id);
    }

    public function cadastrarProduto($dados, $arquivo){

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
        if(empty($dados['nome']) || empty($dados['descricao']) || empty($dados['preco']) || empty($dados['quantidade'])){
            echo "Todos os campos devem ser preenchidos!";
            return;
        }

        // Atribuição
        $this->produtos->nome = $dados['nome'];
        $this->produtos->descricao = $dados['descricao'];
        $this->produtos->preco = str_replace(",", ".", $dados['preco']);
        $this->produtos->quantidade = $dados['quantidade'];
        $this->produtos->imagem = $this->img_name;

        if($this->produtos->cadastrar()){
            header("location: index.php");
            exit();
        }
    }

    public function excluirProdutos($id){
        $this->produtos->id = $id;

        if($this->produtos->excluir()){
            header("location: index.php");
            exit();
        }
    }

    public function atualizarProdutos($dados){

        $this->produtos->id = $dados['id'];
        $this->produtos->nome = $dados['nome'];
        $this->produtos->descricao = $dados['descricao'];
        $this->produtos->preco = str_replace(",", ".", $dados['preco']);
        $this->produtos->quantidade = $dados['quantidade'];

        // ✅ VERIFICA SE FOI ENVIADA NOVA IMAGEM
        if(isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] === UPLOAD_ERR_OK){

            if($this->upload($_FILES)){
                $this->produtos->imagem = $this->img_name;
            }

        } else {
            // ✅ MANTÉM A IMAGEM ANTIGA
            $produtoAtual = $this->produtos->pesquisarProduto($dados['id']);
            $this->produtos->imagem = $produtoAtual->imagem;
        }

        if($this->produtos->atualizar()){
            header("location: index.php");
            exit();
        }
    }

    public function procurarProdutos($id){
        return $this->produtos->buscarProdutos($id);
    }

    public function upload($arquivo){

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