<?php
session_start();
include '../../connection.php';
include_once '../verificar_subscricao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Verificar subscrição antes de permitir acesso
if (!verificarSubscricaoAtiva($conn, $user_id)) {
    header("Location: ../dashinicial/index.php?error=subscription_required");
    exit();
}

// Buscar informações do usuário
$query = "SELECT Nome, Foto FROM Utilizadores WHERE Id = :Id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":Id", $user_id);
$stmt->execute();
$utilizador = $stmt->fetch(PDO::FETCH_ASSOC);

// Processar foto
if (!empty($utilizador['Foto'])) {
    $fotoBase64 = base64_encode($utilizador['Foto']);
    $fotoSrc = "data:image/jpeg;base64,{$fotoBase64}";
} else {
    $fotoSrc = "../conta/uploads/defaultPhoto.png";
}

// Buscar todas as consultas do usuário
$queryConsultas = "
    SELECT 
        c.IDConsulta as ConsultaId,
        c.DataConsulta, 
        c.HoraConsulta, 
        p.Nome as NomeProfissional,
        p.AreaEspecializada as Especialidade,
        c.Status,
        CONVERT(datetime, CONVERT(varchar, c.DataConsulta, 23) + ' ' + CONVERT(varchar, c.HoraConsulta, 8)) as DataHoraCompleta
    FROM Consultas c
    JOIN UtilizadoresProfissionais p ON c.IdProfissional = p.Id
    WHERE c.IdUtilizador = :IdUtilizador 
    ORDER BY c.DataConsulta DESC, c.HoraConsulta DESC";
$stmtConsultas = $conn->prepare($queryConsultas);
$stmtConsultas->bindParam(':IdUtilizador', $user_id);
$stmtConsultas->execute();
$consultas = $stmtConsultas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../../logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../layout.css">
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
    <title>Consultas Marcadas | Ombro Amigo</title>
</head>
<body>
    <!-- Navegação existente -->
    <nav>
        <ul>
            <li><a href="../../initial page/index.php" class="logo">
                    <img src="../../../logo-site-sem-texto.png" alt="">
                    <span class="nav-item">Ombro Amigo</span>
                </a></li>

            <li><a href="../dashinicial/index.php">
                    <i class="fas fa-solid fa-house"></i>
                    <span class="nav-item">Início</span>
                </a></li>

            <li><a href="../diario/index.php">
                    <i class="fas fa-solid fa-book"></i>
                    <span class="nav-item">O Meu Diário</span>
                </a></li>

            <li><a href="../calendario/index.php">
                    <i class="fas fa-solid fa-calendar-days"></i>
                    <span class="nav-item">Calendário</span>
                </a></li>

            <li><a href="../marcarConsultas/index.php">
                    <i class="fas fa-solid fa-clipboard"></i>
                    <span class="nav-item">Marcar Consultas</span>
                </a></li>

            <li>
                <a href="../consultasMarcadas/index.php">
                    <i class="fas fa-solid fa-clipboard-check"></i>
                    <span class="nav-item">Consultas Marcadas</span>
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

            <li class="noti"><a href="../notificacoes/index.php">
                    <i class="fas fa-solid fa-bell"></i>
                    <span class="nav-item">Notificações</span>
                </a></li>

            <li class="logout"><a href="../logout.php">
                    <i class="fas fa-solid fa-right-from-bracket"></i>
                    <span class="nav-item">Sair do Painel</span>
                </a></li>

        </ul>
    </nav>

    <div class="main-container">
        <div class="header-section">
            <h1>Consultas Marcadas</h1>
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="todas">Todas</button>
                <button class="filter-btn" data-filter="proximas">Próximas Consultas</button>
                <button class="filter-btn" data-filter="pendentes">Por Confirmar</button>
                <button class="filter-btn" data-filter="passadas">Histórico</button>
            </div>
        </div>

        <div class="consultas-container">
            <?php foreach ($consultas as $consulta): 
                $dataHora = strtotime($consulta['DataConsulta'] . ' ' . $consulta['HoraConsulta']);
                $hoje = strtotime('now');
                
                $statusClass = strtolower($consulta['Status']);
                $tipoConsulta = $dataHora > $hoje ? 'proximas' : 'passadas';
                
                // Ajustar status para Pendente
                if ($consulta['Status'] == 'Em Confirmacao') {
                    $tipoConsulta = 'pendentes';
                    $consulta['Status'] = 'Pendente';
                    $statusClass = 'pendente';
                }
                
                // Colocar consultas recusadas, canceladas e acabadas no histórico
                if ($consulta['Status'] == 'Recusada' || 
                    $consulta['Status'] == 'Cancelada' || 
                    $consulta['Status'] == 'Acabada') {
                    $tipoConsulta = 'passadas';
                    $statusClass = strtolower($consulta['Status']);
                }

                // Filtrar consultas recusadas, canceladas e acabadas das próximas consultas
                if ($tipoConsulta == 'proximas' && 
                    ($consulta['Status'] == 'Recusada' || 
                     $consulta['Status'] == 'Cancelada' || 
                     $consulta['Status'] == 'Acabada')) {
                    continue;
                }
            ?>
                <div class="consulta-card <?= $statusClass ?>" data-tipo="<?= $tipoConsulta ?>">
                    <div class="consulta-header">
                        <span class="status-badge <?= $statusClass ?>"><?= $consulta['Status'] ?></span>
                        <span class="data">
                            <?= date('d/m/Y', strtotime($consulta['DataConsulta'])) ?>
                            às <?= date('H:i', strtotime($consulta['HoraConsulta'])) ?>
                        </span>
                    </div>
                    <div class="consulta-body">
                        <h3>Dr(a). <?= htmlspecialchars($consulta['NomeProfissional']) ?></h3>
                        <p class="especialidade"><?= htmlspecialchars($consulta['Especialidade']) ?></p>
                        
                        <?php if ($consulta['Status'] == 'Aceite'): ?>
                            <div class="consulta-actions">
                                <button class="action-btn details" data-consulta-id="<?= $consulta['ConsultaId'] ?>">
                                    <i class="fas fa-info-circle"></i> Detalhes da Consulta
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const consultaCards = document.querySelectorAll('.consulta-card');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                button.classList.add('active');

                const filterValue = button.getAttribute('data-filter');

                consultaCards.forEach(card => {
                    if (filterValue === 'todas') {
                        card.style.display = 'block';
                    } else {
                        if (card.getAttribute('data-tipo') === filterValue) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    }
                });
            });
        });
    });
    </script>
</body>
<script src="script.js"></script>
</html>