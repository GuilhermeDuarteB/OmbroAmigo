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

// Buscar a foto do perfil do utilizador logado
$query = "SELECT Foto FROM Utilizadores WHERE Id = :Id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":Id", $user_id);
$stmt->execute();
$utilizador = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se há uma imagem e converte para base64
if (!empty($utilizador['Foto'])) {
    $fotoBase64 = base64_encode($utilizador['Foto']);
    $fotoSrc = "data:image/jpeg;base64,{$fotoBase64}";
} else {
    $fotoSrc = "../conta/uploads/defaultPhoto.png";
}

// Buscar todos os profissionais
$query = "SELECT * FROM UtilizadoresProfissionais";
$stmt = $conn->prepare($query);
$stmt->execute();
$profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Adicionar lógica de paginação
$notificacoesPorPagina = 6;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaAtual - 1) * $notificacoesPorPagina;

// Adicionar lógica de filtro
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'todas';
$whereStatus = "AND c.Status IN ('Aceite', 'Recusada')";
if ($filtro === 'aceites') {
    $whereStatus = "AND c.Status = 'Aceite'";
} elseif ($filtro === 'recusadas') {
    $whereStatus = "AND c.Status = 'Recusada'";
}

// Modificar a query para ordenar por DataAtualizacao
$queryNotificacoes = "
    SELECT 
        c.Status,
        c.DataConsulta,
        c.HoraConsulta,
        c.DataAtualizacao,
        p.Nome as NomeProfissional
    FROM Consultas c
    JOIN UtilizadoresProfissionais p ON c.IdProfissional = p.Id
    WHERE c.IdUtilizador = :IdUtilizador 
    $whereStatus
    ORDER BY c.DataAtualizacao DESC
    OFFSET :offset ROWS
    FETCH NEXT :limit ROWS ONLY";

$stmtNotificacoes = $conn->prepare($queryNotificacoes);
$stmtNotificacoes->bindParam(':IdUtilizador', $user_id);
$stmtNotificacoes->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmtNotificacoes->bindParam(':limit', $notificacoesPorPagina, PDO::PARAM_INT);
$stmtNotificacoes->execute();
$notificacoes = $stmtNotificacoes->fetchAll(PDO::FETCH_ASSOC);

// Buscar total de notificações para paginação
$queryTotal = "SELECT COUNT(*) as total FROM Consultas c
               WHERE c.IdUtilizador = :IdUtilizador 
               AND c.Status IN ('Aceite', 'Recusada')";
$stmtTotal = $conn->prepare($queryTotal);
$stmtTotal->bindParam(':IdUtilizador', $user_id);
$stmtTotal->execute();
$totalNotificacoes = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
$totalPaginas = ceil($totalNotificacoes / $notificacoesPorPagina);

// Carregar todas as notificações sem filtro
$queryNotificacoes = "
    SELECT 
        c.Status,
        c.DataConsulta,
        c.HoraConsulta,
        c.DataAtualizacao,
        p.Nome as NomeProfissional
    FROM Consultas c
    JOIN UtilizadoresProfissionais p ON c.IdProfissional = p.Id
    WHERE c.IdUtilizador = :IdUtilizador 
    ORDER BY c.DataAtualizacao DESC";

$stmtNotificacoes = $conn->prepare($queryNotificacoes);
$stmtNotificacoes->bindParam(':IdUtilizador', $user_id);
$stmtNotificacoes->execute();
$notificacoes = $stmtNotificacoes->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Notificações | Ombro Amigo</title>
</head>

<body>
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

    <div class="back">
    <h1>Notificações</h1>
    <div class="filter-buttons">
        <button onclick="filterNotifications('todas')" class="filter-btn active">Todas</button>
        <button onclick="filterNotifications('aceite')" class="filter-btn">Aceites</button>
        <button onclick="filterNotifications('recusada')" class="filter-btn">Recusadas</button>
    </div>
    <div class="notifications-list">
        <?php if (count($notificacoes) > 0): ?>
            <?php foreach ($notificacoes as $notificacao): 
                $mensagem = $notificacao['NomeProfissional'] . " " . 
                           ($notificacao['Status'] === 'Aceite' ? "aceitou" : "recusou") . 
                           " a tua consulta marcada para " . 
                           date('d-m-Y', strtotime($notificacao['DataConsulta'])) . 
                           " às " . date('H:i', strtotime($notificacao['HoraConsulta']));
            ?>
                <div class="notification-item <?= strtolower($notificacao['Status']) ?>">
                    <p><?= htmlspecialchars($mensagem); ?></p>
                    <small>Data da consulta: <?= date('d-m-Y H:i', strtotime($notificacao['DataConsulta'] . ' ' . $notificacao['HoraConsulta'])); ?></small>
                </div>
            <?php endforeach;
        else: ?>
            <p>Não há notificações no momento.</p>
        <?php endif; ?>
    </div>
</div>

<?php if ($totalPaginas > 1): ?>
    <div class="pagination">
        <?php if ($paginaAtual > 1): ?>
            <a href="?pagina=<?= $paginaAtual - 1 ?>" class="page-btn">Anterior</a>
        <?php endif; ?>
        
        <?php if ($paginaAtual < $totalPaginas): ?>
            <a href="?pagina=<?= $paginaAtual + 1 ?>" class="page-btn">Próxima</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
</body>
<script src="script.js"></script>
</html>