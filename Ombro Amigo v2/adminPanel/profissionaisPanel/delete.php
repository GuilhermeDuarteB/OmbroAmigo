<?php
session_start();
include "../../connection.php";

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

// Função para deletar um profissional específico
if (isset($_POST['delete_user']) && isset($_POST['id'])) {
    try {
        $conn->beginTransaction();
        $id = $_POST['id'];

        // Deleta registros relacionados em Horarios
        $stmt = $conn->prepare("DELETE FROM Horarios WHERE IdProfissional = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        // Deleta o profissional
        $stmt = $conn->prepare("DELETE FROM UtilizadoresProfissionais WHERE Id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $conn->commit();
        $_SESSION['success_message'] = "Profissional excluído com sucesso!";
        header("Location: ProfissionaisPanel.php");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = "Erro ao excluir o profissional: " . $e->getMessage();
        header("Location: ProfissionaisPanel.php");
        exit();
    }
}

header("Location: ProfissionaisPanel.php");
exit();
?>
