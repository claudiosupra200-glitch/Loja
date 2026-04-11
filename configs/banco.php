<?php

class Banco
{
    private $host = "localhost:3316"; // Ajuste a porta se necessário (ex: 3306 ou 3316)
    private $usuario = "root";
    private $senha = "123456"; // Insira sua senha do banco aqui se houver
    private $banco = "loja";
    private $con;

    public function getConexao()
    {
        $this->con = null;

        try {
            $this->con = new PDO("mysql:host=$this->host;dbname=$this->banco", $this->usuario, $this->senha);
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Falha ao conectar: " . $e->getMessage();
        }
        return $this->con;
    }
}