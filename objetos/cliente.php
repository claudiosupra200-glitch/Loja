<?php

class cliente
{

    public $id;
    public $nome;
    public $email;
    public $telefone;
    public $login;
    public $senha;
    public $imagem;
    private $bd;

    public function __construct($bd)
    {
        $this->bd = $bd;
    }

    public function cadastrar()
    {
        $sql = "INSERT INTO clientes(nome, email, telefone, login, senha, imagem) 
                VALUES (:nome, :email, :telefone, :login, :senha, :imagem)";

        $senha_hash = password_hash($this->senha, PASSWORD_DEFAULT);
        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(":nome", $this->nome, PDO::PARAM_STR);
        $stmt->bindParam(":email", $this->email, PDO::PARAM_STR);
        $stmt->bindParam(":telefone", $this->telefone, PDO::PARAM_STR);
        $stmt->bindParam(":login", $this->login, PDO::PARAM_STR);
        $stmt->bindParam(":senha", $senha_hash, PDO::PARAM_STR);
        $stmt->bindParam(":imagem", $this->imagem, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function atualizar()
    {
        $sql = "UPDATE clientes SET nome = :nome, email = :email, telefone = :telefone, 
                login = :login, imagem = :imagem";
        
        $parametrosAtualizacao = [
            ":nome" => $this->nome,
            ":email" => $this->email,
            ":telefone" => $this->telefone,
            ":login" => $this->login,
            ":imagem" => $this->imagem,
            ":id" => $this->id
        ];

        // Se a senha foi informada, atualiza também a senha
        if (!empty($this->senha)) {
            $sql .= ", senha = :senha";
            $parametrosAtualizacao[":senha"] = password_hash($this->senha, PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";
        
        $stmt = $this->bd->prepare($sql);
        
        if ($stmt->execute($parametrosAtualizacao)) {
            return true;
        } else {
            return false;
        }
    }

    public function buscarCliente($id)
    {
        $sql = "SELECT * FROM clientes WHERE id = :id";
        $resultado = $this->bd->prepare($sql);
        $resultado->bindParam(":id", $id);
        $resultado->execute();

        return $resultado->fetch(PDO::FETCH_OBJ);
    }

    public function login()
    {
        // Usa o campo 'login' para authenticação (conforme especificado na tabela)
        $sql = "SELECT * FROM clientes WHERE login = :login LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(":login", $this->login, PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_OBJ);

        if ($resultado) {
            if (password_verify($this->senha, $resultado->senha)) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION["cliente"] = $resultado;
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function listarCompras($id_cliente)
    {
        // Agora usamos a tabela pedidos (cabeçalho)
        $sql = "SELECT * FROM pedidos WHERE id_cliente = :id ORDER BY data DESC";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(":id", $id_cliente);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function buscarDetalhesCompra($id_pedido, $id_cliente)
    {
        // Buscamos itens na tabela pedido_itens vinculados ao id_pedido
        // Validamos também o id_cliente através do JOIN com pedidos para segurança
        $sql = "SELECT pi.*, p.nome as produto_nome, p.imagem as produto_imagem 
                FROM pedido_itens pi
                JOIN pedidos ped ON pi.id_pedido = ped.id
                JOIN produtos p ON pi.id_produto = p.id
                WHERE ped.id_cliente = :id_cliente AND ped.id = :id_pedido";
        
        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(":id_cliente", $id_cliente);
        $stmt->bindParam(":id_pedido", $id_pedido);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function verificarLoginUnico($login, $id_atual)
    {
        $sql = "SELECT id FROM clientes WHERE login = :login AND id != :id LIMIT 1";
        $stmt = $this->bd->prepare($sql);
        $stmt->bindParam(":login", $login);
        $stmt->bindParam(":id", $id_atual);
        $stmt->execute();
        return $stmt->rowCount() == 0;
    }

}
