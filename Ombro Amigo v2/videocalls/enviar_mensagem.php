<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Utilizador não autenticado');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['consultaId']) || !isset($data['mensagem']) || trim($data['mensagem']) === '') {
        throw new Exception('Dados inválidos');
    }

    $user_id = $_SESSION['user_id'];
    $consulta_id = $data['consultaId'];
    $mensagem = trim($data['mensagem']);

    // Buscar informações da consulta
    $queryConsulta = "SELECT IdUtilizador, IdProfissional FROM Consultas WHERE IDConsulta = ?";
    $stmtConsulta = $conn->prepare($queryConsulta);
    $stmtConsulta->execute([$consulta_id]);
    $consulta = $stmtConsulta->fetch(PDO::FETCH_ASSOC);

    if (!$consulta) {
        throw new Exception('Consulta não encontrada');
    }

    // Determinar se é paciente ou profissional
    if ($user_id == $consulta['IdUtilizador']) {
        $enviadoPor = 'user';
    } elseif ($user_id == $consulta['IdProfissional']) {
        $enviadoPor = 'profissional';
    } else {
        throw new Exception('Utilizador não autorizado para esta consulta');
    }

    // Inserir mensagem
    $query = "INSERT INTO ChatConsultas (
        ConsultaId,
        IdUser,
        IdProfissional,
        Mensagem,
        DataHora,
        EnviadoPor
    ) VALUES (?, ?, ?, ?, GETDATE(), ?)";

    $stmt = $conn->prepare($query);
    $result = $stmt->execute([
        $consulta_id,
        $consulta['IdUtilizador'],
        $consulta['IdProfissional'],
        $mensagem,
        $enviadoPor
    ]);

    if (!$result) {
        throw new Exception('Erro ao inserir mensagem no banco de dados');
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log("Erro em enviar_mensagem.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 