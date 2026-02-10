<?php

class database
{
    private $host = "localhost:3316";
    private $usuario = "root";
    private $senha = "123456";
    private $banco = "loja";
    private $con;

    public function conectar()
    {
        $this->con = null;

        try {
            $this->con = new PDO("mysql:host=$this->host;dbname=$this->banco", $this->usuario, $this->senha);
        } catch (PDOException $e) {
            echo "Falha ao conectar com o banco de dados.";
        }
        return $this->con;
    }
}