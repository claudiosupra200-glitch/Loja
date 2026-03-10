<?php

include_once "configs/database.php";
include_once "produtos.php";

Class produtosController{
    private $bd;

    private $produtos;

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

    public function cadastrarProduto($dados){

        if(empty($dados['nome'])){
            echo "Todos os campos devem ser preenchidos!";
            return;
        }

        if(empty($dados['descricao'])){
            echo "Todos os campos devem ser preenchidos!";
            return;
        }

        if(empty($dados['preco'])){
            echo "Todos os campos devem ser preenchidos!";
            return;
        }

        if(empty($dados['quantidade'])){
            echo "Todos os campos devem ser preenchidos!";
            return;
        }

    $this->produtos->nome = $dados['nome'];
    $this->produtos->descricao = $dados['descricao'];
    $this->produtos->preco = str_replace(",", ".", $dados['preco']);
    $this->produtos->quantidade = $dados['quantidade'];

    if($this->produtos->cadastrar()){
        header("location: index.php?");
        exit();
    }
    }

    public function excluirProdutos($id){
        $this->produtos->id = $id;

        if($this->produtos->excluir()){
            header("location: index.php?");
            exit();
        }
    }

    public function atualizarProdutos($dados){
        $this->produtos->id = $dados['id'];
        $this->produtos->nome = $dados['nome'];
        $this->produtos->descricao = $dados['descricao'];
        $this->produtos->preco = str_replace(",", ".", $dados['preco']);
        $this->produtos->quantidade = $dados['quantidade'];

        if($this->produtos->atualizar()){
            header("location: index.php?");
            exit();
        }

    }

    public function procurarProdutos($id){
        return $this->produtos->buscarProdutos($id);
    }


}

