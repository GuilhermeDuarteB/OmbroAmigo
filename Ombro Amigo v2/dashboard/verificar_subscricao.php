<?php
function verificarSubscricaoAtiva($conn, $user_id) {
    try {
        $query = "SELECT TOP 1 spu.Status
                  FROM SubscricaoPorUtilizador spu
                  WHERE spu.IdUtilizador = :user_id
                  ORDER BY spu.DataInicio DESC";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return ($resultado && $resultado['Status'] === 'Ativa');
    } catch (PDOException $e) {
        error_log("Erro ao verificar subscrição: " . $e->getMessage());
        return false;
    }
}
?> 