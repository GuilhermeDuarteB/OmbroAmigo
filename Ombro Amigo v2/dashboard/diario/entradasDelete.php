<?php
session_start();
include '../../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $idDiario = $_POST['idDiario'] ?? null;

        if (!$idDiario) {
            throw new Exception("ID da entrada não fornecido.");
        }

        // Verificar se a entrada pertence ao usuário
        $checkQuery = "
            SELECT COUNT(*) 
            FROM Diarios 
            WHERE IdDiario = :IdDiario 
            AND IDUtilizador = :IdUtilizador
        ";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(":IdDiario", $idDiario);
        $checkStmt->bindParam(":IdUtilizador", $_SESSION['user_id']);
        $checkStmt->execute();

        if ($checkStmt->fetchColumn() == 0) {
            throw new Exception("Entrada não encontrada ou sem permissão.");
        }

        // Deletar a entrada
        $stmt = $conn->prepare("DELETE FROM Diarios WHERE IDDiario = :idDiario");
        $stmt->bindParam(":idDiario", $idDiario);
        
        if ($stmt->execute()) {
            header("Location: index.php?success=3");
        } else {
            throw new Exception("Erro ao deletar a entrada.");
        }
    } catch (Exception $e) {
        header("Location: index.php?error=" . urlencode($e->getMessage()));
    }
    exit();
}
?>
