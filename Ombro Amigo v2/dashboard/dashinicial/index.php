<?php
session_start();
include '../../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Adicione isso após a conexão com o banco de dados
if (isset($_GET['error']) && $_GET['error'] === 'subscription_required') {
    echo '<script>
        alert("Esta funcionalidade requer uma subscrição ativa. Por favor, ative uma subscrição para continuar.");
        window.location.href = window.location.pathname;
    </script>';
}

// Processar o formulário de sentimentos
if (isset($_POST['submit_sentimentos']) && isset($_POST['sentimentos'])) {
    $sentimento = $_POST['sentimentos'][0]; // Pega o primeiro valor do array
    $data = date('Y-m-d H:i:s');

    $querySalvarSentimento = "
        INSERT INTO Sentimentos (idutilizador, descricao, data) 
        VALUES (:idutilizador, :descricao, :data)";

    try {
        $stmtSalvar = $conn->prepare($querySalvarSentimento);
        $stmtSalvar->bindParam(':idutilizador', $user_id);
        $stmtSalvar->bindParam(':descricao', $sentimento);
        $stmtSalvar->bindParam(':data', $data);
        $stmtSalvar->execute();

        // Recarrega a página para atualizar o sentimento exibido
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        // Opcional: adicionar tratamento de erro
        error_log("Erro ao salvar sentimento: " . $e->getMessage());
    }
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

// Buscar próximas consultas
$queryConsultas = "
    SELECT TOP 3 c.DataConsulta, c.HoraConsulta, p.Nome as NomeProfissional, c.Status
    FROM Consultas c
    JOIN UtilizadoresProfissionais p ON c.IdProfissional = p.Id
    WHERE c.IdUtilizador = :IdUtilizador 
    AND c.DataConsulta >= CAST(GETDATE() AS DATE)
    ORDER BY c.DataConsulta ASC, c.HoraConsulta ASC";
$stmtConsultas = $conn->prepare($queryConsultas);
$stmtConsultas->bindParam(':IdUtilizador', $user_id);
$stmtConsultas->execute();
$proximasConsultas = $stmtConsultas->fetchAll(PDO::FETCH_ASSOC);

// Buscar últimas entradas do diário
$queryDiario = "
    SELECT TOP 3 Titulo, DataEntrada 
    FROM Diarios 
    WHERE IDUtilizador = :IdUtilizador 
    ORDER BY DataEntrada DESC";
$stmtDiario = $conn->prepare($queryDiario);
$stmtDiario->bindParam(':IdUtilizador', $user_id);
$stmtDiario->execute();
$entradasDiario = $stmtDiario->fetchAll(PDO::FETCH_ASSOC);

// Buscar último sentimento registrado
$querySentimento = "
    SELECT TOP 1 descricao, data 
    FROM Sentimentos 
    WHERE idutilizador = :IdUtilizador 
    ORDER BY data DESC";
$stmtSentimento = $conn->prepare($querySentimento);
$stmtSentimento->bindParam(':IdUtilizador', $user_id);
$stmtSentimento->execute();
$ultimoSentimento = $stmtSentimento->fetch(PDO::FETCH_ASSOC);

// Adicione após a conexão com o banco de dados
include_once '../verificar_subscricao.php';
$temSubscricaoAtiva = verificarSubscricaoAtiva($conn, $user_id);

// Consulta para obter o total de consultas realizadas
if ($temSubscricaoAtiva) {
    $queryConsultas = "SELECT COUNT(*) as total 
                      FROM Consultas 
                      WHERE IdUtilizador = :user_id 
                      AND Status = 'Realizada'";
    $stmtConsultas = $conn->prepare($queryConsultas);
    $stmtConsultas->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmtConsultas->execute();
    $resultConsultas = $stmtConsultas->fetch(PDO::FETCH_ASSOC);
    $consultasRealizadas = $resultConsultas['total'];
}

// Consulta para obter o total de entradas no diário
$queryDiario = "SELECT COUNT(*) as total 
                FROM Diarios 
                WHERE IDUtilizador = :user_id";
$stmtDiario = $conn->prepare($queryDiario);
$stmtDiario->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmtDiario->execute();
$resultDiario = $stmtDiario->fetch(PDO::FETCH_ASSOC);
$totalDiario = $resultDiario['total'];

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
    <title>Painel de Controlo | Ombro Amigo</title>
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

            <?php if ($temSubscricaoAtiva): ?>
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

                <li class="noti"><a href="../notificacoes/index.php">
                        <i class="fas fa-solid fa-bell"></i>
                        <span class="nav-item">Notificações</span>
                    </a></li>
            <?php endif; ?>

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

            <li class="logout"><a href="../logout.php">
                    <i class="fas fa-solid fa-right-from-bracket"></i>
                    <span class="nav-item">Sair do Painel</span>
                </a></li>

        </ul>
    </nav>

    <div class="dashboard-container">
        <div class="welcome-section">
            <div class="user-welcome">
                <img src="<?= $fotoSrc; ?>" alt="Foto de Perfil" class="profile-pic">
                <div class="welcome-text">
                    <h1>Bem-vindo(a), <?= htmlspecialchars($utilizador['Nome']); ?></h1>
                    <?php if ($ultimoSentimento): ?>
                        <p>Hoje sinto-me <span
                                class="sentiment"><?= htmlspecialchars($ultimoSentimento['descricao']); ?></span></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <?php if ($temSubscricaoAtiva): ?>
                <!-- Próximas Consultas -->
                <div class="dashboard-card consultas">
                    <h2><i class="fas fa-calendar-check"></i> Próximas Consultas</h2>
                    <?php if ($proximasConsultas): ?>
                        <ul class="consultas-list">
                            <?php foreach ($proximasConsultas as $consulta): ?>
                                <li>
                                    <div class="consulta-info">
                                        <span class="consulta-data">
                                            <?= date('d/m/Y', strtotime($consulta['DataConsulta'])); ?>
                                            às <?= date('H:i', strtotime($consulta['HoraConsulta'])); ?>
                                        </span>
                                        <span class="consulta-prof">
                                            Dr(a). <?= htmlspecialchars($consulta['NomeProfissional']); ?>
                                        </span>
                                        <span class="consulta-status <?= strtolower($consulta['Status']); ?>">
                                            <?= htmlspecialchars($consulta['Status']); ?>
                                        </span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="../marcarConsultas/index.php" class="card-link">Marcar Nova Consulta</a>
                    <?php else: ?>
                        <p class="no-data">Nenhuma consulta agendada</p>
                        <a href="../marcarConsultas/index.php" class="card-link">Marcar Consulta</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Card de incentivo à subscrição -->
                <div class="dashboard-card subscription-card">
                    <h2><i class="fas fa-star"></i> Benefícios Premium</h2>
                    <div class="subscription-content">
                        <div class="benefits-list">
                            <div class="benefit-item">
                                <i class="fas fa-video"></i>
                                <p>Consultas Online com Profissionais</p>
                            </div>
                            <div class="benefit-item">
                                <i class="fas fa-calendar-check"></i>
                                <p>Agendamento Flexível</p>
                            </div>
                            <div class="benefit-item">
                                <i class="fas fa-bell"></i>
                                <p>Notificações e Lembretes</p>
                            </div>
                        </div>
                        <div class="subscription-cta">
                            <p class="subscription-message">Desbloqueie todos os recursos premium e comece sua jornada de bem-estar hoje!</p>
                            <a href="../../planos/index.php" class="subscribe-button">Ativar Subscrição</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="dashboard-card diario">
                <h2><i class="fas fa-book"></i>O Meu Diário</h2>
                <?php if ($entradasDiario): ?>
                    <ul class="diario-list">
                        <?php foreach ($entradasDiario as $entrada): ?>
                            <li>
                                <span class="diario-data">
                                    <?= date('d/m/Y', strtotime($entrada['DataEntrada'])); ?>
                                </span>
                                <span class="diario-titulo">
                                    <?= htmlspecialchars($entrada['Titulo']); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="../diario/index.php" class="card-link">Ver Diário Completo</a>
                <?php else: ?>
                    <p class="no-data">Nenhuma entrada no diário</p>
                    <a href="../diario/index.php" class="card-link">Criar Primeira Entrada</a>
                <?php endif; ?>
            </div>

            <div class="dashboard-card dicas">
                <h2><i class="fas fa-lightbulb"></i> Dica do Dia</h2>
                <div class="dica-content">
                    <?php
                    $dicas = [
                        "Respira fundo 3 vezes quando te sentires ansioso(a).",
                        "Bebe água regularmente durante o dia.",
                        "Faz uma pequena caminhada ao ar livre.",
                        "Escreve <a href='../diario/index.php'>3 coisas</a> pelas quais tu és grato(a).",
                        "Tira um momento para meditar hoje.",
                        "Escreve <a href='../diario/index.php'>no diário algo</a> que gostas de fazer.",
                        "Faz exercícios físicos leves hoje.",
                        "Conecta-te com um amigo ou familiar."
                    ];
                    $dicaDoDia = $dicas[array_rand($dicas)];
                    ?>
                    <p><?= $dicaDoDia; ?></p>
                </div>
            </div>

            <div class="dashboard-card progresso">
                <h2><i class="fas fa-chart-line"></i> O Teu Progresso</h2>
                <div class="progress-stats">
                    <?php if ($temSubscricaoAtiva): ?>
                        <div class="stat-item">
                            <span class="stat-label">Consultas Realizadas</span>
                            <span class="stat-value"><?= $consultasRealizadas ?? 0; ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="stat-item">
                        <span class="stat-label">Entradas no Diário</span>
                        <span class="stat-value"><?= $totalDiario ?? 0; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dmsg" id="dmsg" style="display: none;">
        <h2>Como te sentes hoje?</h2>
        <form action="index.php" method="post">
            <ul>
                <li><input type="radio" name="sentimentos[]" value="feliz"> Feliz <i class="fa-solid fa-face-smile"></i>
                </li>
                <li><input type="radio" name="sentimentos[]" value="triste"> Triste <i
                        class="fa-solid fa-face-frown"></i></li>
                <li><input type="radio" name="sentimentos[]" value="ansioso"> Ansioso(a) <i
                        class="fa-solid fa-face-sad-cry"></i></li>
                <li><input type="radio" name="sentimentos[]" value="zangado"> Zangado(a) <i
                        class="fa-solid fa-face-angry"></i></li>
                <li><input type="radio" name="sentimentos[]" value="cansado"> Cansado(a) <i
                        class="fa-solid fa-face-tired"></i></li>
                <li><input type="radio" name="sentimentos[]" value="nResponder"> Prefiro Não Responder <i
                        class="fa-solid fa-circle-xmark"></i></li>
            </ul>
            <button type="submit" name="submit_sentimentos">Confirmar</button>
        </form>
    </div>

    <script src="script.js"></script>
    <script>
    function openSubscriptionSection() {
        // Quando chegar na página de configurações, ativa automaticamente a seção de subscrição
        localStorage.setItem('openSection', 'subscricao');
    }
    </script>
</body>

</html>