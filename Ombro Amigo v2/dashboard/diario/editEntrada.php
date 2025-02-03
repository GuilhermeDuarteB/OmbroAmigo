<?php
session_start();
include '../../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $idDiario = $_POST["idDiario"] ?? null;
        $texto = trim($_POST["textoEntradaEdit"] ?? "");
        $titulo = trim($_POST["tituloEdit"] ?? "");

        if (!$idDiario) {
            throw new Exception("ID da entrada não fornecido.");
        }

        if (empty($texto) || empty($titulo)) {
            throw new Exception("Por favor, preencha todos os campos.");
        }

        // Verificar se a entrada pertence ao usuário
        $checkQuery = "
            SELECT COUNT(*) 
            FROM Diarios 
            WHERE IDDiario = :IdDiario 
            AND IDUtilizador = :IdUtilizador
        ";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(":IdDiario", $idDiario);
        $checkStmt->bindParam(":IdUtilizador", $_SESSION['user_id']);
        $checkStmt->execute();

        if ($checkStmt->fetchColumn() == 0) {
            throw new Exception("Entrada não encontrada ou sem permissão.");
        }

        // Atualizar a entrada
        $updateQuery = "
            UPDATE Diarios 
            SET Mensagem = :mensagem,
                Titulo = :titulo
            WHERE IDDiario = :IdDiario
        ";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bindParam(":IdDiario", $idDiario);
        $stmt->bindParam(":mensagem", $texto);
        $stmt->bindParam(":titulo", $titulo);

        if ($stmt->execute()) {
            header("Location: index.php?success=2");
        } else {
            throw new Exception("Erro ao atualizar a entrada.");
        }
    } catch (PDOException $e) {
        // Log do erro específico do PDO
        error_log("Erro PDO: " . $e->getMessage());
        header("Location: index.php?error=" . urlencode("Erro ao atualizar a entrada. Por favor, tente novamente."));
    } catch (Exception $e) {
        header("Location: index.php?error=" . urlencode($e->getMessage()));
    }
    exit();
}
?>