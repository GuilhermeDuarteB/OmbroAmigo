<?php
session_start();
include "../connection.php";

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.html");
    exit();
}

// Buscar estatísticas de subscrições
$querySubscricoes = "SELECT s.NomeSubscricao, COUNT(su.IdSubscricaoUsuario) as Total
                    FROM Subscricoes s
                    LEFT JOIN SubscricaoPorUtilizador su ON s.IdSubscricao = su.IdSubscricao
                    WHERE su.Status = 'Ativa'
                    GROUP BY s.NomeSubscricao";
$stmtSubscricoes = $conn->prepare($querySubscricoes);
$stmtSubscricoes->execute();
$subscricoes = $stmtSubscricoes->fetchAll(PDO::FETCH_ASSOC);

// Buscar últimas candidaturas
$queryCandidaturas = "SELECT TOP 5 Id, Nome, Email, AreaEspecializada 
                      FROM UtilizadoresProfissionais 
                      WHERE Tipo = 'candidato' 
                      ORDER BY Id DESC";
$stmtCandidaturas = $conn->prepare($queryCandidaturas);
$stmtCandidaturas->execute();
$candidaturas = $stmtCandidaturas->fetchAll(PDO::FETCH_ASSOC);

// Buscar próximas consultas
$queryConsultas = "SELECT TOP 5 c.DataConsulta, c.HoraConsulta, 
                   u.Nome as NomeUtilizador, p.Nome as NomeProfissional
                   FROM Consultas c
                   JOIN Utilizadores u ON c.IdUtilizador = u.Id
                   JOIN UtilizadoresProfissionais p ON c.IdProfissional = p.Id
                   WHERE c.DataConsulta >= GETDATE()
                   ORDER BY c.DataConsulta, c.HoraConsulta";
$stmtConsultas = $conn->prepare($queryConsultas);
$stmtConsultas->execute();
$consultas = $stmtConsultas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="x-icon" href="../../logo-icon.png">
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
    <title>Painel Administrativo | Ombro Amigo</title>
</head>
<body>
<nav>
        <ul>
            <li><a href="../initial page/index.php" class="logo">
            <img src="../../logo-site-sem-texto.png" >
            <span class="nav-item">Ombro Amigo</span>
            </a></li>

            <li><a href="index.php">
            <i class="fas fa-solid fa-house"></i>
            <span class="nav-item">Painel Principal</span>
            </a></li>

            <li><a href="usersPanel/UserPanel.php">
                <i class="fas fa-solid fa-user"></i>
                <span class="nav-item">Painel Users</span>
            </a></li>

            <li><a href="profissionaisPanel/profissionaisPanel.php">
                <i class="fas fa-solid fa-user-doctor"></i>
                <span class="nav-item">Painel Profissionais</span>
            </a></li>
            
            <li><a href="painelUsersDesativados/index.php">
                <i class="fas fa-solid fa-user-minus"></i>
                <span class="nav-item">Painel Users Desativados</span>
            </a></li>

            <li><a href="candidaturas/candidaturas.php">
                <i class="fas fa-solid fa-clipboard"></i>
                <span class="nav-item">Candidaturas Profissionais</span>
            </a></li>

            <li><a href="consultasMarcadas/index.php">
            <i class="fas fa-calendar-check"></i>
                <span class="nav-item">Gestão de Consultas</span>
            </a></li>

            <li class="logout"><a href="../logout.php">
                <i class="fas fa-solid fa-right-from-bracket"></i>
                <span class="nav-item">Sair do Painel</span>
            </a></li>
        </ul>
    </nav>

    <main class="main-content">
        <h1 style="color: #333;">Painel Administrativo</h1>
        
        <div class="dashboard-grid">
            <!-- Estatísticas de Subscrições -->
            <div class="dashboard-card subscriptions">
                <h2><i class="fas fa-chart-pie"></i> Subscrições Ativas</h2>
                <div class="subscription-stats">
                    <?php foreach ($subscricoes as $subscricao): ?>
                        <div class="stat-item">
                            <span class="stat-label"><?= htmlspecialchars($subscricao['NomeSubscricao']) ?></span>
                            <span class="stat-value"><?= $subscricao['Total'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Últimas Candidaturas -->
            <div class="dashboard-card applications">
                <h2><i class="fas fa-user-plus"></i> Últimas Candidaturas</h2>
                <div class="applications-list">
                    <?php foreach ($candidaturas as $candidatura): ?>
                        <div class="application-item">
                            <div class="application-info">
                                <strong><?= htmlspecialchars($candidatura['Nome']) ?></strong>
                                <span><?= htmlspecialchars($candidatura['AreaEspecializada']) ?></span>
                            </div>
                            <a href="candidaturas/candidaturas.php" class="btn-view">Ver</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Próximas Consultas -->
            <div class="dashboard-card appointments">
                <h2><i class="fas fa-calendar-alt"></i> Próximas Consultas</h2>
                <div class="appointments-list">
                    <?php foreach ($consultas as $consulta): ?>
                        <div class="appointment-item">
                            <div class="appointment-info">
                                <div class="appointment-date">
                                    <?= date('d/m/Y', strtotime($consulta['DataConsulta'])) ?>
                                    <span class="appointment-time">
                                        <?= date('H:i', strtotime($consulta['HoraConsulta'])) ?>
                                    </span>
                                </div>
                                <div class="appointment-users">
                                    <span>Utilizador: <?= htmlspecialchars($consulta['NomeUtilizador']) ?></span>
                                    <span>Profissional: <?= htmlspecialchars($consulta['NomeProfissional']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 