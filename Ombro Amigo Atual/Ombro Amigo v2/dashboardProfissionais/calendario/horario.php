<?php
session_start();
include '../../connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Usuário não autenticado"]);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Query para horários
        $queryHorarios = "SELECT dataInicio, dataFim FROM Horarios WHERE IdProfissional = :IdProfissional";
        $stmtHorarios = $conn->prepare($queryHorarios);
        $stmtHorarios->bindParam(":IdProfissional", $user_id);
        $stmtHorarios->execute();
        $horarios = $stmtHorarios->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "horarios" => $horarios,
            "success" => true
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            "error" => $e->getMessage(),
            "horarios" => []
        ]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dataInicio = $_POST['dataInicio'];
    $dataFim = $_POST['dataFim'];
    $horaInicio = $_POST['horaInicio'];
    $horaFim = $_POST['horaFim'];
    $IdProfissional = $user_id;

    if (empty($dataInicio) || empty($dataFim) || empty($horaInicio) || empty($horaFim)) {
        echo "Todos os campos são obrigatórios!";
        exit();
    }

    try {
        $query = 'INSERT INTO Horarios (dataInicio, horaInicio, horaFim, dataFim, IdProfissional) 
                  VALUES (:dataInicio, :horaInicio, :horaFim, :dataFim, :IdProfissional)';
        $insertStmt = $conn->prepare($query);

        $insertStmt->bindParam(":dataInicio", $dataInicio);
        $insertStmt->bindParam(":horaInicio", $horaInicio);
        $insertStmt->bindParam(":horaFim", $horaFim);
        $insertStmt->bindParam(":dataFim", $dataFim);
        $insertStmt->bindParam(":IdProfissional", $IdProfissional);

        if ($insertStmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            echo "Erro ao registrar o horário!";
        }
    } catch (PDOException $e) {
        echo "Erro ao executar a consulta: " . $e->getMessage();
    }
}
?>
