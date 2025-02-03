<?php
session_start();
include '../../connection.php';

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

// Acessa o ID do utilizador
$user_id = $_SESSION['user_id'];

try {
    // Prepara a consulta para deletar todas as entradas do diário do utilizador
    $stmt = $conn->prepare("DELETE FROM Diarios WHERE IDUtilizador = :idUtilizador");
    $stmt->bindParam(":idUtilizador", $user_id);

    // Executa a consulta
    if ($stmt->execute()) {
        // Redireciona para a página do diário após a exclusão
        header("Location: index.php?message=Entradas apagadas com sucesso!");
        exit();
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
