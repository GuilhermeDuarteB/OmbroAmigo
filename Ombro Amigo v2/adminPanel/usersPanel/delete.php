<?php
session_start();
include "../../connection.php";

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

// Função para deletar um utilizador específico
if (isset($_POST['delete_user']) && isset($_POST['id'])) {
    try {
        $conn->beginTransaction();
        $id = $_POST['id'];

        // Deletar todas as referências primeiro
        $queries = [
            "DELETE FROM Sinalizacao WHERE IdRemetente = ? OR IdDestinatario = ?",
            "DELETE FROM Diarios WHERE IDUtilizador = ?",
            "DELETE FROM MotivoSaida WHERE IdUtilizador = ?",
            "DELETE FROM Sentimentos WHERE IDUtilizador = ?",
            "DELETE FROM ChatConsultas WHERE IdUser = ?",
            "DELETE FROM Consultas WHERE IdUtilizador = ?",
            "DELETE FROM Pagamentos WHERE IdUtilizador = ?",
            "DELETE FROM SubscricaoPorUtilizador WHERE IdUtilizador = ?",
            "DELETE FROM Utilizadores WHERE Id = ?"
        ];

        foreach ($queries as $query) {
            $stmt = $conn->prepare($query);
            if (strpos($query, 'OR') !== false) {
                $stmt->execute([$id, $id]); // Para a query da Sinalizacao que tem dois parâmetros
            } else {
                $stmt->execute([$id]); // Para as outras queries que têm apenas um parâmetro
            }
        }

        $conn->commit();
        $_SESSION['success_message'] = "Utilizador excluído com sucesso!";
        header("Location: UserPanel.php");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = "Erro ao excluir o utilizador: " . $e->getMessage();
        header("Location: UserPanel.php");
        exit();
    }
}

header("Location: UserPanel.php");
exit();
?>
