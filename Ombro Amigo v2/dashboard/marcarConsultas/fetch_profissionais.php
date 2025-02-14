<?php
include '../../connection.php';

header('Content-Type: application/json');

try {
    if (isset($_POST['area']) && isset($_POST['data'])) {
        $area = $_POST['area'];
        $data = $_POST['data'];
        
        // Log para debug
        error_log("Buscando profissionais para área: $area e data: $data");
        
        // Converter a data para objeto DateTime para obter o dia da semana
        $dataObj = new DateTime($data);
        $diaSemana = $dataObj->format('N'); // 1 (Segunda) até 7 (Domingo)
        
        error_log("Dia da semana: $diaSemana");

        // Buscar os profissionais disponíveis na data selecionada
        $query = "
            SELECT DISTINCT p.Id, p.Foto, p.Nome 
            FROM UtilizadoresProfissionais AS p
            INNER JOIN HorariosSemanais AS hs ON p.Id = hs.IdProfissional
            INNER JOIN PeriodosAtendimento AS pa ON hs.IdHorarioSemanal = pa.IdHorarioSemanal
            WHERE p.AreaEspecializada = :area
            AND pa.DiaSemana = :diaSemana
            AND :data BETWEEN hs.DataInicio AND hs.DataFim
            AND hs.Ativo = 1
        ";
        
        error_log("Query: $query");
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":area", $area);
        $stmt->bindParam(":diaSemana", $diaSemana);
        $stmt->bindParam(":data", $data);
        $stmt->execute();
        $profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);

        error_log("Profissionais encontrados: " . print_r($profissionais, true));

        // Processar fotos e adicionar horários
        foreach ($profissionais as &$profissional) {
            // Processar a foto em base64
            if (!empty($profissional['Foto'])) {
                $profissional['Foto'] = base64_encode($profissional['Foto']);
            } else {
                $profissional['Foto'] = "";
            }

            // Buscar os horários disponíveis para o dia da semana
            $queryHorarios = "
                SELECT pa.HoraInicio, pa.HoraFim
                FROM HorariosSemanais hs
                INNER JOIN PeriodosAtendimento pa ON hs.IdHorarioSemanal = pa.IdHorarioSemanal
                WHERE hs.IdProfissional = :idProfissional
                AND pa.DiaSemana = :diaSemana
                AND :data BETWEEN hs.DataInicio AND hs.DataFim
                AND hs.Ativo = 1
            ";
            
            $stmtHorarios = $conn->prepare($queryHorarios);
            $stmtHorarios->bindParam(":idProfissional", $profissional['Id']);
            $stmtHorarios->bindParam(":diaSemana", $diaSemana);
            $stmtHorarios->bindParam(":data", $data);
            $stmtHorarios->execute();
            $horarios = $stmtHorarios->fetchAll(PDO::FETCH_ASSOC);

            error_log("Horários encontrados para profissional {$profissional['Id']}: " . print_r($horarios, true));

            // Gerar os intervalos de horários (1h entre cada horário)
            $intervalos = [];
            foreach ($horarios as $horario) {
                $horaInicio = new DateTime($horario['HoraInicio']);
                $horaFim = new DateTime($horario['HoraFim']);
                
                while ($horaInicio < $horaFim) {
                    $intervalos[] = $horaInicio->format('H:i');
                    $horaInicio->modify('+1 hour');
                }
            }

            // Buscar horários ocupados
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

        // Garantir que sempre retorne um array
        echo json_encode([
            'success' => true,
            'profissionais' => $profissionais,
            'debug' => [
                'area' => $area,
                'data' => $data,
                'diaSemana' => $diaSemana
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => "Área ou data não especificada.",
            'profissionais' => []
        ]);
    }
} catch (Exception $e) {
    error_log("Erro na busca de profissionais: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => "Erro ao buscar profissionais: " . $e->getMessage(),
        'profissionais' => []
    ]);
}
?>