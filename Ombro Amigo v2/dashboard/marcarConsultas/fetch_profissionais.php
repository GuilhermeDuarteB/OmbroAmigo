<?php
include '../../connection.php';

header('Content-Type: application/json');

try {
    if (isset($_POST['area']) && isset($_POST['data'])) {
        $area = $_POST['area'];
        $data = $_POST['data'];

        // Buscar os profissionais disponíveis na data selecionada
        $query = "
            SELECT DISTINCT p.Id, p.Foto, p.Nome 
            FROM UtilizadoresProfissionais AS p
            INNER JOIN Horarios AS h ON p.Id = h.IdProfissional
            WHERE p.AreaEspecializada = :area
              AND :data BETWEEN h.dataInicio AND h.dataFim
        ";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":area", $area);
        $stmt->bindParam(":data", $data);
        $stmt->execute();
        $profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Processar fotos e adicionar horários
        foreach ($profissionais as &$profissional) {
            // Processar a foto em base64
            if (!empty($profissional['Foto'])) {
                $profissional['Foto'] = base64_encode($profissional['Foto']);
            } else {
                $profissional['Foto'] = ""; // Foto padrão será tratada no frontend
            }

            // Buscar os horários disponíveis
            $queryHorarios = "
                SELECT horaInicio, horaFim
                FROM Horarios
                WHERE IdProfissional = :idProfissional
                  AND :data BETWEEN dataInicio AND dataFim
            ";
            $stmtHorarios = $conn->prepare($queryHorarios);
            $stmtHorarios->bindParam(":idProfissional", $profissional['Id']);
            $stmtHorarios->bindParam(":data", $data);
            $stmtHorarios->execute();
            $horarios = $stmtHorarios->fetchAll(PDO::FETCH_ASSOC);

            // Gerar os intervalos de horários (1h30 entre cada horário)
            $intervalos = [];
            foreach ($horarios as $horario) {
                $horaInicio = new DateTime($horario['horaInicio']);
                $horaFim = new DateTime($horario['horaFim']);
                while ($horaInicio < $horaFim) {
                    $intervalos[] = $horaInicio->format('H:i');
                    $horaInicio->modify('+1 hour');
                }
            }

            // Buscar horários ocupados - usando sintaxe SQL Server
            $queryHorariosOcupados = "
                SELECT CONVERT(varchar(5), HoraConsulta, 108) as HoraConsulta
                FROM Consultas 
                WHERE IdProfissional = :idProfissional
                AND DataConsulta = :data
                AND Status IN ('Aceite', 'Em Confirmacao')
            ";
            $stmtHorariosOcupados = $conn->prepare($queryHorariosOcupados);
            $stmtHorariosOcupados->bindParam(":idProfissional", $profissional['Id']);
            $stmtHorariosOcupados->bindParam(":data", $data);
            $stmtHorariosOcupados->execute();
            $horariosOcupados = $stmtHorariosOcupados->fetchAll(PDO::FETCH_COLUMN);

            // Gerar os intervalos de horários com status
            $horariosComStatus = [];
            foreach ($intervalos as $horario) {
                $horariosComStatus[] = [
                    'horario' => $horario,
                    'disponivel' => !in_array($horario, $horariosOcupados),
                    'ocupado' => in_array($horario, $horariosOcupados)
                ];
            }

            $profissional['Horarios'] = $horariosComStatus;
        }

        // Debug para verificar a estrutura final dos dados
        error_log("Dados dos profissionais: " . print_r($profissionais, true));

        echo json_encode($profissionais);
    } else {
        echo json_encode(["error" => "Área ou data não especificada."]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => "Erro ao buscar profissionais: " . $e->getMessage()]);
}
?>