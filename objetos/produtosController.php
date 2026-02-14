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
}
