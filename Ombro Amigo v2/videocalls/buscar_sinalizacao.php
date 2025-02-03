<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id']) || !isset($_GET['consultaId'])) {
        throw new Exception('Dados inválidos');
    }

    $user_id = $_SESSION['user_id'];
    $consulta_id = $_GET['consultaId'];

    // Primeiro, verificar se o usuário está na consulta
    $queryConsulta = "SELECT 
        CASE 
            WHEN IdUtilizador = ? THEN 'user'
            WHEN IdProfissional = ? THEN 'profissional'
            ELSE NULL
        END as TipoUsuario
        FROM Consultas 
        WHERE IDConsulta = ?";

    $stmtConsulta = $conn->prepare($queryConsulta);
    $stmtConsulta->execute([$user_id, $user_id, $consulta_id]);
    
    $tipoUsuario = $stmtConsulta->fetchColumn();

    if (!$tipoUsuario) {
        throw new Exception('Usuário não autorizado para esta consulta');
    }

    // Buscar sinalizações não processadas
    $query = "SELECT TOP 1 * 
              FROM Sinalizacao 
              WHERE ConsultaId = ? 
              AND IdDestinatario = ? 
              AND TipoDestinatario = ? 
              AND Processado = 0 
              ORDER BY Id DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute([$consulta_id, $user_id, $tipoUsuario]);
    
    $sinalizacao = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($sinalizacao) {
        // Marcar como processado
        $updateQuery = "UPDATE Sinalizacao SET Processado = 1 WHERE Id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute([$sinalizacao['Id']]);
        
        echo json_encode([
            'success' => true,
            'sinalizacao' => json_decode($sinalizacao['Dados'], true)
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'sinalizacao' => null
        ]);
    }

} catch (Exception $e) {
    error_log("Erro em buscar_sinalizacao.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 