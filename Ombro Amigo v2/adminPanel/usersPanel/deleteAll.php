<?php
session_start();
include "../../connection.php";

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

// Função para deletar todos os utilizadores
if (isset($_POST['delete_all'])) {
    try {
        $conn->beginTransaction();

        // Array com as queries na ordem correta
        $queries = [
            "DELETE FROM Sinalizacao",
            "DELETE FROM Diarios",
            "DELETE FROM MotivoSaida",
            "DELETE FROM Sentimentos",
            "DELETE FROM ChatConsultas",
            "DELETE FROM Consultas",
            "DELETE FROM Pagamentos",
            "DELETE FROM SubscricaoPorUtilizador",
            "DELETE FROM Utilizadores WHERE Tipo = 'user'",
            "DBCC CHECKIDENT ('Utilizadores', RESEED, 0)"
        ];

        foreach ($queries as $query) {
            $stmt = $conn->prepare($query);
            $stmt->execute();
        }

        $conn->commit();
        $_SESSION['success_message'] = "Todos os utilizadores foram excluídos com sucesso!";
        header("Location: UserPanel.php");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = "Erro ao excluir todos os utilizadores: " . $e->getMessage();
        header("Location: UserPanel.php");
        exit();
    }
}

header("Location: UserPanel.php");
exit();
?>
