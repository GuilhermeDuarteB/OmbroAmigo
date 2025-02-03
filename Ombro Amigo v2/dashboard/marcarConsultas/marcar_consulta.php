<?php
include '../../connection.php';
header('Content-Type: application/json');

try {
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['profissionalId']) && isset($_GET['data'])) {
            $profissionalId = $_GET['profissionalId'];
            $data = $_GET['data'];

            // Buscar horários ocupados - sintaxe SQL Server
            $queryHorariosOcupados = "
                SELECT CONVERT(varchar(5), HoraConsulta, 108) as HoraConsulta
                FROM Consultas 
                WHERE IdProfissional = :profissionalId 
                AND DataConsulta = :data
                AND Status IN ('Aceite', 'Em Confirmacao')
            ";
            $stmt = $conn->prepare($queryHorariosOcupados);
            $stmt->bindParam(':profissionalId', $profissionalId);
            $stmt->bindParam(':data', $data);
            $stmt->execute();
            $horariosOcupados = $stmt->fetchAll(PDO::FETCH_COLUMN);

            echo json_encode(["success" => true, "horariosOcupados" => $horariosOcupados]);
            exit();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['profissionalId'], $_POST['data'], $_POST['horario'])) {
            $profissionalId = $_POST['profissionalId'];
            $data = $_POST['data'];
            $horario = $_POST['horario'];
            $userId = $_SESSION['user_id'];

            // Verificar novamente se o horário está ocupado - sintaxe SQL Server
            $queryVerificar = "
                SELECT COUNT(*) AS total 
                FROM Consultas 
                WHERE IdProfissional = :profissionalId 
                AND DataConsulta = :data 
                AND CONVERT(varchar(5), HoraConsulta, 108) = :horario
                AND Status IN ('Aceite', 'Em Confirmacao')
            ";
            $stmt = $conn->prepare($queryVerificar);
            $stmt->bindParam(':profissionalId', $profissionalId);
            $stmt->bindParam(':data', $data);
            $stmt->bindParam(':horario', $horario);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['total'] > 0) {
                echo json_encode(["success" => false, "message" => "Horário indisponível."]);
                exit();
            }

            // Inserir a nova consulta
            $queryInserir = "
                INSERT INTO Consultas (IdUtilizador, IdProfissional, DataConsulta, HoraConsulta, Status) 
                VALUES (:userId, :profissionalId, :data, CAST(:horario AS TIME), 'Em Confirmacao')
            ";
            $stmt = $conn->prepare($queryInserir);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':profissionalId', $profissionalId);
            $stmt->bindParam(':data', $data);
            $stmt->bindParam(':horario', $horario);
            $stmt->execute();

            echo json_encode([
                "success" => true, 
                "message" => "Consulta marcada com sucesso!",
                "redirect" => "../consultasMarcadas/index.php"
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Dados incompletos."]);
        }
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Erro: " . $e->getMessage()]);
}
?>
