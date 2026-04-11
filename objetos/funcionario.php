<?php

class funcionario
{

    public $id;
    public $nome;
    public $cargo;
    public $departamento;
    public $pis;
    public $emailCorporativo;
    public $formacao;
    public $imagem;
    public $senha;
    private $bd;

    public function __construct($bd)
    {
        $this->bd = $bd;
    }

    public function lerTodos()
    {
        $sql = "SELECT * FROM funcionarios";
        $resultado = $this->bd->query($sql);
        $resultado->execute();

        return $resultado->fetchAll(PDO::FETCH_OBJ);
    }

    public function pesquisarFuncionario($pesquisa, $tipo)
    {
        if ($tipo == "id") {
            $sql = "SELECT * FROM funcionarios WHERE id = :pesquisa";
        } else {
            $sql = "SELECT * FROM funcionarios WHERE nome like :pesquisa";
            $pesquisa = "%" . $pesquisa . "%";
        }

        $resultado = $this->bd->prepare($sql);
        $resultado->bindParam(":pesquisa", $pesquisa);
        $resultado->execute();

        return $resultado->fetchAll(PDO::FETCH_OBJ);
    }

    public function cadastrar()
    {
        $sql = "INSERT INTO funcionarios(nome, cargo, departamento, pis, emailCorporativo, formacao, imagem, senha) 
                VALUES (:nome, :cargo, :departamento, :pis, :emailCorporativo, :formacao, :imagem, :senha)";

        $senha_hash = password_hash($this->senha, PASSWORD_DEFAULT);
        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(":nome", $this->nome, PDO::PARAM_STR);
        $stmt->bindParam(":cargo", $this->cargo, PDO::PARAM_STR);
        $stmt->bindParam(":departamento", $this->departamento, PDO::PARAM_STR);
        $stmt->bindParam(":pis", $this->pis, PDO::PARAM_STR);
        $stmt->bindParam(":emailCorporativo", $this->emailCorporativo, PDO::PARAM_STR);
        $stmt->bindParam(":formacao", $this->formacao, PDO::PARAM_STR);
        $stmt->bindParam(":imagem", $this->imagem, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha_hash, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function excluir()
    {
        $sql = "DELETE FROM funcionarios WHERE id = :id";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function atualizar()
    {
        $senha_hash = password_hash($this->senha, PASSWORD_DEFAULT);
        $sql = "UPDATE funcionarios SET nome = :nome, cargo = :cargo, departamento = :departamento, 
                pis = :pis, emailCorporativo = :emailCorporativo, formacao = :formacao, 
                imagem = :imagem, senha = :senha WHERE id = :id";

        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(":nome", $this->nome, PDO::PARAM_STR);
        $stmt->bindParam(":cargo", $this->cargo, PDO::PARAM_STR);
        $stmt->bindParam(":departamento", $this->departamento, PDO::PARAM_STR);
        $stmt->bindParam(":pis", $this->pis, PDO::PARAM_STR);
        $stmt->bindParam(":emailCorporativo", $this->emailCorporativo, PDO::PARAM_STR);
        $stmt->bindParam(":formacao", $this->formacao, PDO::PARAM_STR);
        $stmt->bindParam(":imagem", $this->imagem, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha_hash, PDO::PARAM_STR);
        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function buscarFuncionarios($id)
    {
        $sql = "SELECT * FROM funcionarios WHERE id = :id";
        $resultado = $this->bd->prepare($sql);
        $resultado->bindParam(":id", $id);
        $resultado->execute();

        return $resultado->fetch(PDO::FETCH_OBJ);
    }

    public function login()
    {
        $sql = "SELECT * FROM funcionarios WHERE emailCorporativo = :email";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(":email", $this->emailCorporativo, PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_OBJ);

        if ($resultado) {
            if (password_verify($this->senha, $resultado->senha)) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION["funcionario"] = $resultado;
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
