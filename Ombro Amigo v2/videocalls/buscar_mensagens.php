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

    // Verificar se o usuário pertence à consulta
    $queryVerifica = "SELECT 
        CASE 
            WHEN IdUtilizador = ? THEN 'paciente'
            WHEN IdProfissional = ? THEN 'profissional'
            ELSE NULL
        END as TipoUsuario
        FROM Consultas 
        WHERE IDConsulta = ?";

    $stmtVerifica = $conn->prepare($queryVerifica);
    $stmtVerifica->execute([$user_id, $user_id, $consulta_id]);
    $tipoUsuario = $stmtVerifica->fetchColumn();

    if (!$tipoUsuario) {
        throw new Exception('Usuário não autorizado');
    }

    // Buscar mensagens com informações adicionais
    $query = "SELECT 
        c.*,
        u.Nome as NomeUser,
        p.Nome as NomeProfissional,
        CONVERT(VARCHAR, c.DataHora, 120) as DataHoraFormatada
        FROM ChatConsultas c
        LEFT JOIN Utilizadores u ON c.IdUser = u.Id
        LEFT JOIN UtilizadoresProfissionais p ON c.IdProfissional = p.Id
        WHERE c.ConsultaId = ?
        ORDER BY c.DataHora ASC";

    $stmt = $conn->prepare($query);
    $stmt->execute([$consulta_id]);
    $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'mensagens' => $mensagens,
        'tipoUsuario' => $tipoUsuario
    ]);

} catch (Exception $e) {
    error_log("Erro em buscar_mensagens.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 