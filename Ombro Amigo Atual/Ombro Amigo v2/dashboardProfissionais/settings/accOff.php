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

// Verifica se o método de requisição é POST para desativar a conta e registrar o motivo
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['desativarConta'])) {
    // Obtenha os motivos do formulário
    $motivos = $_POST['motivos'] ?? []; // Array de motivos (se forem múltiplos checkboxes)
    $motivoOutro = trim($_POST['motivoOutro']) ?? ""; // Motivo adicional

    try {
        // Iniciar uma transação
        $conn->beginTransaction();

        $queryUpdate = "UPDATE UtilizadoresProfissionais SET Tipo = 'desativadaProf' WHERE Id = :Id";
        $stmtUpdate = $conn->prepare($queryUpdate);
        $stmtUpdate->bindParam(':Id', $user_id);
        if (!$stmtUpdate->execute()) {
            throw new Exception("Falha ao atualizar o tipo do usuário.");
        }

        // 2. Registrar motivos de exclusão na tabela `MotivoSaida`
        $queryMotivo = "INSERT INTO MotivoSaida (IdUtilizador, Motivo) VALUES (:IdUtilizador, :Motivo)";
        $stmtMotivo = $conn->prepare($queryMotivo);
        $stmtMotivo->bindParam(':IdUtilizador', $user_id);

        // Inserir cada motivo selecionado
        foreach ($motivos as $motivo) {
            $stmtMotivo->bindParam(':Motivo', $motivo);
            if (!$stmtMotivo->execute()) {
                throw new Exception("Falha ao inserir o motivo: " . $motivo);
            }
        }

        // Inserir o motivo adicional, se fornecido
        if (!empty($motivoOutro)) {
            $stmtMotivo->bindParam(':Motivo', $motivoOutro);
            if (!$stmtMotivo->execute()) {
                throw new Exception("Falha ao inserir o motivo adicional.");
            }
        }

        // Confirma a transação
        $conn->commit();

        // Redireciona o usuário para a página de logout após a exclusão
        header("Location: ../logout.php");
        exit();

    } catch (Exception $e) {
        // Desfazer a transação em caso de erro
        $conn->rollBack();
        echo "Erro ao desativar a conta: " . $e->getMessage();
        exit();
    }
}