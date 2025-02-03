<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

try {
    error_log("Iniciando acabar_consulta.php"); // Debug
    
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Usuário não autenticado');
    }

    // Pegar consultaId do POST ou GET
    $consulta_id = isset($_POST['consultaId']) ? $_POST['consultaId'] : 
                  (isset($_GET['consultaId']) ? $_GET['consultaId'] : null);
                  
    if (!$consulta_id) {
        throw new Exception('ID da consulta não fornecido');
    }

    $user_id = $_SESSION['user_id'];
    
    error_log("User ID: $user_id, Consulta ID: $consulta_id"); // Debug

    // Verificar se é um profissional e se pertence a esta consulta
    $queryVerifica = "SELECT IdProfissional 
                     FROM Consultas 
                     WHERE IDConsulta = ? AND IdProfissional = ?";
    
    $stmtVerifica = $conn->prepare($queryVerifica);
    $stmtVerifica->execute([$consulta_id, $user_id]);
    
    if (!$stmtVerifica->fetch()) {
        throw new Exception('Apenas profissionais podem acabar a consulta');
    }

    error_log("Verificação do profissional ok, atualizando status"); // Debug

    // Atualizar status da consulta para "Acabada"
    $queryUpdate = "UPDATE Consultas 
                   SET Status = 'Acabada',
                       DataAtualizacao = GETDATE()
                   WHERE IDConsulta = ?";
    
    $stmtUpdate = $conn->prepare($queryUpdate);
    $result = $stmtUpdate->execute([$consulta_id]);

    if (!$result) {
        $error = $stmtUpdate->errorInfo();
        throw new Exception('Erro ao acabar consulta: ' . implode(', ', $error));
    }

    error_log("Consulta acabada com sucesso"); // Debug

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log("Erro em acabar_consulta.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 