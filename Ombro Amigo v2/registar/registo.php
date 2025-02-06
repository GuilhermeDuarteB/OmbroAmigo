<?php 
session_start();
// Incluir o arquivo de conexão com o banco de dados
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = strtolower(trim($_POST['email'])); 
    $PalavraPasse = $_POST['PalavraPasse'];
    $nomeUtilizador = $_POST['nomeUtilizador'];
    $dataNascimento = $_POST['dataNascimento'];
    $nTelefone = $_POST['nTelefone'];
    $userType = "user";

    // Verifica se o e-mail já está registrado na tabela Utilizadores
    $query = "SELECT Email FROM Utilizadores WHERE Email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $resultUtilizadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Verifica se o e-mail já está registrado na tabela UtilizadoresProfissional
    $query = "SELECT Email FROM UtilizadoresProfissionais WHERE Email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $resultProfissional = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificação de duplicidade
    if (count($resultUtilizadores) > 0 || count($resultProfissional) > 0) {
        // Se o email existir em qualquer das duas tabelas, exibe a mensagem
        echo "E-mail já está registrado em outra conta!";
    } else {
        // Insere novo usuário se o email não estiver em nenhuma das tabelas
        $query = "INSERT INTO Utilizadores (Nome, NomeUtilizador, DataNascimento, Email, Telefone, PalavraPasse, Tipo) 
                  VALUES (:nome, :nomeUtilizador, :dataNascimento, :email, :nTelefone, :PalavraPasse, :userType)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":PalavraPasse", $PalavraPasse);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":nomeUtilizador", $nomeUtilizador);
        $stmt->bindParam(":dataNascimento", $dataNascimento);
        $stmt->bindParam(":nTelefone", $nTelefone);
        $stmt->bindParam(":userType", $userType);

        if ($stmt->execute()) {
            $user_id = $conn->lastInsertId();

            // Armazenar o ID e nome do utilizador na sessão
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $nomeUtilizador;
            $_SESSION['user_type'] = 'user'; // Adicionar tipo de usuário
            $_SESSION['user_photo'] = null; // Inicializar foto como null

            // Redirecionar para a Página de Conta
            header("Location: ../dashboard/dashinicial/index.php");
            exit();
        } else {
            echo "Erro ao registar!";
        }
    }
}
