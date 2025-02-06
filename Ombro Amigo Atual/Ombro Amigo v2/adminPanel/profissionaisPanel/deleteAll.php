<?php
session_start();
include "../../connection.php";

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

// Função para deletar todos os profissionais
if (isset($_POST['delete_all'])) {
    try {
        $conn->beginTransaction();

        // Deleta todos os registros de Horarios
        $stmt = $conn->prepare("DELETE FROM Horarios");
        $stmt->execute();

        // Deleta todos os profissionais
        $stmt = $conn->prepare("DELETE FROM UtilizadoresProfissionais");
        $stmt->execute();

        // Reseta o contador de ID
        $stmt = $conn->prepare("DBCC CHECKIDENT ('UtilizadoresProfissionais', RESEED, 0)");
        $stmt->execute();

        $conn->commit();
        $_SESSION['success_message'] = "Todos os profissionais foram excluídos com sucesso!";
        header("Location: ProfissionaisPanel.php");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = "Erro ao excluir todos os profissionais: " . $e->getMessage();
        header("Location: ProfissionaisPanel.php");
        exit();
    }
}

header("Location: ProfissionaisPanel.php");
exit();
?>
