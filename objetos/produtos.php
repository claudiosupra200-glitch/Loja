<?php

Class produtos{

    public $nome;
    public $descricao;
    public $quantidade;
    public $preco;
    public function __construct($bd){
        $this->bd = $bd;
    }

    public function lerTodos (){
        $sql = "SELECT * FROM produtos";
        $resultado = $this->bd->query($sql);
        $resultado->execute();

        return $resultado->fetchAll(pdo::FETCH_OBJ);
    }
}
