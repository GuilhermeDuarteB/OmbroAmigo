<?php
session_start();
include '../../connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['consultaId']) || !isset($data['status'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$consultaId = $data['consultaId'];
$status = $data['status'];
$userId = $_SESSION['user_id'];

try {
    // Verificar se o profissional tem permissão para atualizar esta consulta
    $queryVerifica = "SELECT IdProfissional FROM Consultas WHERE IDConsulta = ? AND IdProfissional = ?";
    $stmtVerifica = $conn->prepare($queryVerifica);
    $stmtVerifica->execute([$consultaId, $userId]);

    if (!$stmtVerifica->fetch()) {
        throw new Exception('Você não tem permissão para atualizar esta consulta');
    }

    // Atualizar o status da consulta
    $query = "UPDATE Consultas SET Status = ?, DataAtualizacao = GETDATE() WHERE IDConsulta = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$status, $consultaId]);

    echo json_encode(['success' => true, 'message' => 'Status da consulta atualizado com sucesso']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
