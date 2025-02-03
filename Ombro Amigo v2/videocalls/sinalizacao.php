<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../connection.php';

header('Content-Type: application/json');

try {
    error_log("Iniciando sinalizacao.php");
    
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Usuário não autenticado');
    }

    // Pegar dados do POST
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['consultaId'])) {
        throw new Exception('ID da consulta não fornecido');
    }

    $consulta_id = $data['consultaId'];
    $user_id = $_SESSION['user_id'];

    // Verificar se o IdRemetente existe nas tabelas Utilizadores ou UtilizadoresProfissionais
    $queryVerificacao = "SELECT COUNT(*) FROM Utilizadores WHERE Id = ? 
                         UNION ALL 
                         SELECT COUNT(*) FROM UtilizadoresProfissionais WHERE Id = ?";
    $stmtVerificacao = $conn->prepare($queryVerificacao);
    $stmtVerificacao->execute([$user_id, $user_id]);
    $resultados = $stmtVerificacao->fetchAll(PDO::FETCH_COLUMN);

    if (array_sum($resultados) == 0) {
        throw new Exception('IdRemetente não encontrado em Utilizadores ou UtilizadoresProfissionais');
    }

    // Verificar se as chaves necessárias estão definidas
    if (!isset($data['idDestinatario']) || !isset($data['tipoRemetente']) || !isset($data['tipoDestinatario'])) {
        throw new Exception('Dados de sinalização incompletos');
    }

    // Inserir sinalização
    $query = "INSERT INTO Sinalizacao (
        ConsultaId, 
        IdRemetente,
        IdDestinatario,
        TipoRemetente,
        TipoDestinatario,
        Dados,
        Processado,
        DataCriacao
    ) VALUES (?, ?, ?, ?, ?, ?, 0, GETDATE())";

    $stmt = $conn->prepare($query);
    $params = [
        $consulta_id,
        $user_id,
        $data['idDestinatario'],
        $data['tipoRemetente'],
        $data['tipoDestinatario'],
        json_encode($data)
    ];

    error_log("Parâmetros para inserção: " . print_r($params, true));

    if (!$stmt->execute($params)) {
        $error = $stmt->errorInfo();
        throw new Exception('Erro ao salvar sinalização: ' . implode(', ', $error));
    }

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    error_log("Erro em sinalizacao.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>