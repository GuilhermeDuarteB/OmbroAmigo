<?php
session_start();
include '../../connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "error" => "Usuário não autenticado",
        "horarios" => []
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Log para debug
        error_log("Buscando horários para o usuário: " . $user_id);
        
        $query = "SELECT hs.DataInicio, hs.DataFim, pa.DiaSemana 
                 FROM HorariosSemanais hs
                 LEFT JOIN PeriodosAtendimento pa ON hs.IdHorarioSemanal = pa.IdHorarioSemanal
                 WHERE hs.IdProfissional = :IdProfissional AND hs.Ativo = 1";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":IdProfissional", $user_id);
        $stmt->execute();
        
        $horarios = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Log para debug
            error_log("Dados encontrados: " . print_r($row, true));
            
            $dataInicio = $row['DataInicio'];
            $dataFim = $row['DataFim'];
            
            $found = false;
            foreach ($horarios as &$horario) {
                if ($horario['dataInicio'] === $dataInicio && $horario['dataFim'] === $dataFim) {
                    if (!in_array($row['DiaSemana'], $horario['diasSemana'])) {
                        $horario['diasSemana'][] = (int)$row['DiaSemana'];
                    }
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $horarios[] = [
                    'dataInicio' => $dataInicio,
                    'dataFim' => $dataFim,
                    'diasSemana' => [(int)$row['DiaSemana']]
                ];
            }
        }

        // Log do resultado final
        error_log("Horários processados: " . print_r($horarios, true));

        echo json_encode([
            "horarios" => $horarios,
            "success" => true
        ]);
    } catch (PDOException $e) {
        error_log("Erro na consulta: " . $e->getMessage());
        echo json_encode([
            "error" => $e->getMessage(),
            "horarios" => []
        ]);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dataInicio = $_POST['dataInicio'];
        $dataFim = $_POST['dataFim'];
        $periodos = json_decode($_POST['periodos'], true);

        // Inicia a transação
        $conn->beginTransaction();

        // Insere o horário semanal
        $queryHorario = "INSERT INTO HorariosSemanais (IdProfissional, DataInicio, DataFim) 
                        VALUES (:IdProfissional, :DataInicio, :DataFim)";
        $stmtHorario = $conn->prepare($queryHorario);
        $stmtHorario->execute([
            ':IdProfissional' => $user_id,
            ':DataInicio' => $dataInicio,
            ':DataFim' => $dataFim
        ]);

        $idHorarioSemanal = $conn->lastInsertId();

        // Insere os períodos
        $queryPeriodo = "INSERT INTO PeriodosAtendimento 
                        (IdHorarioSemanal, DiaSemana, HoraInicio, HoraFim)
                        VALUES (:IdHorarioSemanal, :DiaSemana, :HoraInicio, :HoraFim)";
        $stmtPeriodo = $conn->prepare($queryPeriodo);

        foreach ($periodos as $periodo) {
            $stmtPeriodo->execute([
                ':IdHorarioSemanal' => $idHorarioSemanal,
                ':DiaSemana' => $periodo['diaSemana'],
                ':HoraInicio' => $periodo['horaInicio'],
                ':HoraFim' => $periodo['horaFim']
            ]);
        }

        $conn->commit();
        echo json_encode(["success" => true]);

    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(["error" => $e->getMessage()]);
    }
}
?>
