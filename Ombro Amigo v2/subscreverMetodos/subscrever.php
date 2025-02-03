<?php
session_start();
include '../connection.php';

// Debug - Mostrar todos os dados recebidos
error_log("POST data: " . print_r($_POST, true));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Utilizador não está logado']);
        exit();
    }

    // Verificar cada campo individualmente
    $errors = [];
    
    if (!isset($_POST['planoId']) || empty($_POST['planoId'])) {
        $errors[] = 'ID do plano não fornecido';
    }
    
    if (!isset($_POST['valor']) || empty($_POST['valor'])) {
        $errors[] = 'Valor não fornecido';
    }
    
    if (!isset($_POST['metodoPagamento']) || empty($_POST['metodoPagamento'])) {
        $errors[] = 'Método de pagamento não fornecido';
    }

    // Se houver erros, retorna mensagem detalhada
    if (!empty($errors)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Dados incompletos',
            'errors' => $errors,
            'received_data' => $_POST,
            'debug' => [
                'post' => $_POST,
                'raw_input' => file_get_contents('php://input')
            ]
        ]);
        exit();
    }

    $userId = $_SESSION['user_id'];
    $planoId = $_POST['planoId'];
    $metodoPagamento = $_POST['metodoPagamento'];
    $valor = str_replace(',', '.', $_POST['valor']); // Garantir formato decimal correto

    try {
        $conn->beginTransaction();
    
        // 1. Inserir na SubscricaoPorUtilizador com sintaxe SQL Server
        $querySubscricao = "INSERT INTO SubscricaoPorUtilizador 
                           (IdUtilizador, IdSubscricao, Status, DataInicio, DataFim) 
                           OUTPUT INSERTED.IdSubscricaoUsuario
                           VALUES 
                           (?, ?, 'Ativa', GETDATE(), 
                            CASE 
                                WHEN ? = 3 THEN DATEADD(year, 1, GETDATE())
                                ELSE DATEADD(month, 1, GETDATE())
                            END)";
        
        $stmtSubscricao = $conn->prepare($querySubscricao);
        $stmtSubscricao->execute([$userId, $planoId, $planoId]);
        
        // Obter o ID inserido no SQL Server
        $result = $stmtSubscricao->fetch(PDO::FETCH_ASSOC);
        $idSubscricaoUsuario = $result['IdSubscricaoUsuario'];

        // 2. Inserir o pagamento
        $queryPagamento = "INSERT INTO Pagamentos 
                          (IdSubscricaoUsuario, IdUtilizador, Valor, MetodoPagamento, Estado) 
                          VALUES 
                          (?, ?, ?, ?, 'sucesso')";
        
        $stmtPagamento = $conn->prepare($queryPagamento);
        $stmtPagamento->execute([
            $idSubscricaoUsuario,
            $userId,
            $valor,
            $metodoPagamento
        ]);

        // 3. Atualizar o IdSubscricao no Utilizador
        $queryUpdateUser = "UPDATE Utilizadores 
                           SET IdSubscricao = ? 
                           WHERE Id = ?";
        
        $stmtUpdateUser = $conn->prepare($queryUpdateUser);
        $stmtUpdateUser->execute([$planoId, $userId]);

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Subscrição ativada com sucesso']);

    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode([
            'success' => false, 
            'message' => 'Erro ao processar pagamento: ' . $e->getMessage(),
            'debug' => [
                'userId' => $userId,
                'planoId' => $planoId,
                'valor' => $valor,
                'metodoPagamento' => $metodoPagamento
            ]
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
}
?>