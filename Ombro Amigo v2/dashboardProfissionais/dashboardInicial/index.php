<?php
session_start();
include '../../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Buscar informações do profissional
$query = "SELECT Foto, Nome FROM UtilizadoresProfissionais WHERE Id = :Id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":Id", $user_id);
$stmt->execute();
$profissional = $stmt->fetch(PDO::FETCH_ASSOC);

// Processar foto
if (!empty($profissional['Foto'])) {
    $fotoBase64 = base64_encode($profissional['Foto']);
    $fotoSrc = "data:image/jpeg;base64,{$fotoBase64}";
} else {
    $fotoSrc = "../conta/uploads/defaultPhoto.png";
}

// Ajuste na query para buscar próximas consultas
$queryConsultas = "
    SELECT c.*, u.Nome as NomeUtilizador, u.Foto as FotoUtilizador
    FROM Consultas c
    JOIN Utilizadores u ON c.IdUtilizador = u.Id
    WHERE c.IdProfissional = :IdProfissional 
    AND c.DataConsulta >= CONVERT(date, GETDATE())
    AND c.Status = 'Aceite'
    ORDER BY c.DataConsulta ASC, c.HoraConsulta ASC
    OFFSET 0 ROWS FETCH NEXT 5 ROWS ONLY
";

try {
    $stmtConsultas = $conn->prepare($queryConsultas);
    $stmtConsultas->bindParam(":IdProfissional", $user_id);
    $stmtConsultas->execute();
    $proximasConsultas = $stmtConsultas->fetchAll(PDO::FETCH_ASSOC);

    // Debug para verificar os dados
    // var_dump($proximasConsultas);
} catch (PDOException $e) {
    // Log do erro
    error_log("Erro ao buscar consultas: " . $e->getMessage());
    $proximasConsultas = [];
}

// Buscar estatísticas
$queryStats = "
    SELECT 
        COUNT(CASE WHEN Status = 'Aceite' AND DataConsulta >= GETDATE() THEN 1 END) as ConsultasFuturas,
        COUNT(CASE WHEN Status = 'Em Confirmacao' THEN 1 END) as ConsultasPendentes,
        COUNT(CASE WHEN Status = 'Aceite' AND DataConsulta < GETDATE() THEN 1 END) as ConsultasRealizadas
    FROM Consultas
    WHERE IdProfissional = :IdProfissional
";
$stmtStats = $conn->prepare($queryStats);
$stmtStats->bindParam(":IdProfissional", $user_id);
$stmtStats->execute();
$stats = $stmtStats->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../../logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Profissionais</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../../dashboard/layout.css">
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- Navbar existente -->
    <nav>
        <ul>
          <li><a href="../../initial page/index.php" class="logo">
              <img src="../../../logo-site-sem-texto.png" alt="">
              <span class="nav-item">Ombro Amigo</span>
            </a></li>
          <li><a href="../dashboardInicial/index.php">
              <i class="fas fa-solid fa-house"></i>
              <span class="nav-item">Início</span>
            </a>
          </li>
          <li>
            <a href="../consultasMarcadas/index.php">
              <i class="fas fa-solid fa-clipboard-check"></i>
              <span class="nav-item">Consultas Marcadas</span>
            </a>
          </li>
          <li><a href="../calendario/index.php">
              <i class="fas fa-solid fa-calendar-days"></i>
              <span class="nav-item">Calendário</span>
            </a>
          </li>
          <div class="fotoAcc">
            <li><a href="../conta/conta.php">
                <img class="fas" src="<?= $fotoSrc; ?>" id="fotoAcc">
                <span class="nav-item2">A Minha Conta</span>
              </a></li>
          </div>
          <li><a href="../settings/index.php">
              <i class="fas fa-cog"></i>
              <span class="nav-item">Definições</span>
            </a>
          </li>
          <li><a href="../notificacoes/index.php" class="noti">
              <i class="fas fa-solid fa-bell"></i>
              <span class="nav-item">Notificações</span>
            </a>
          </li>
          <li class="logout"><a href="../logout.php">
              <i class="fas fa-solid fa-right-from-bracket"></i>
              <span class="nav-item">Sair do Painel</span>
            </a></li>
        </ul>
      </nav>

    <div class="dashboard-container">
        <div class="welcome-section">
            <h1>Bem-vindo(a), <?= htmlspecialchars($profissional['Nome']) ?></h1>
            <p class="date"><?= date('d/m/Y') ?></p>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-calendar-check"></i>
                <h3>Próximas Consultas</h3>
                <p><?= $stats['ConsultasFuturas'] ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3>Consultas Pendentes</h3>
                <p><?= $stats['ConsultasPendentes'] ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <h3>Consultas Realizadas</h3>
                <p><?= $stats['ConsultasRealizadas'] ?></p>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Próximas Consultas -->
            <div class="dashboard-card">
                <h2><i id="calendarioIcon" class="fas fa-calendar-check"></i> Próximas Consultas</h2>
                <div class="appointments-list">
                    <?php if (!empty($proximasConsultas)): ?>
                        <?php foreach ($proximasConsultas as $consulta): ?>
                            <?php if (isset($consulta['DataConsulta']) && isset($consulta['HoraConsulta'])): ?>
                                <div class="appointment-card">
                                    <img src="<?= isset($consulta['FotoUtilizador']) && !empty($consulta['FotoUtilizador']) ? 
                                        'data:image/jpeg;base64,' . base64_encode($consulta['FotoUtilizador']) : 
                                        '../conta/uploads/defaultPhoto.png' ?>" 
                                        alt="Foto do Utilizador">
                                    <div class="appointment-info">
                                        <h4><?= isset($consulta['NomeUtilizador']) ? htmlspecialchars($consulta['NomeUtilizador']) : 'Nome não disponível' ?></h4>
                                        <p><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($consulta['DataConsulta'])) ?></p>
                                        <p><i class="fas fa-clock"></i> <?= date('H:i', strtotime($consulta['HoraConsulta'])) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-appointments">Não há consultas agendadas</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Dicas do Dia -->
            <div class="dashboard-card">
                <h2><i id="dicaIcon" class="fas fa-lightbulb"></i> Dica do Dia</h2>
                <div class="dica-content">
                    <?php
                    $dicas = [
                        "Mantenha um ambiente calmo e acolhedor durante as consultas.",
                        "Pratique a escuta ativa com seus pacientes.",
                        "Reserve um tempo para sua própria saúde mental.",
                        "Mantenha registros organizados das consultas.",
                        "Estabeleça limites saudáveis no trabalho.",
                        "Pratique exercícios de respiração entre consultas.",
                        "Mantenha-se atualizado com novas técnicas terapêuticas.",
                        "Cultive empatia e compreensão com cada paciente."
                    ];
                    $dicaDoDia = $dicas[array_rand($dicas)];
                    ?>
                    <p><?=$dicaDoDia;?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>