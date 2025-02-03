<?php
session_start();
include '../../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Buscar a foto do perfil do profissional logado
$query = "SELECT Foto FROM UtilizadoresProfissionais WHERE Id = :Id";
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

// Carregar todas as notificações incluindo histórico
$queryNotificacoes = "
    SELECT 
        c.Status,
        c.DataConsulta,
        c.HoraConsulta,
        c.DataAtualizacao,
        u.Nome as NomeUtilizador
    FROM Consultas c
    JOIN Utilizadores u ON c.IdUtilizador = u.Id
    WHERE c.IdProfissional = :IdProfissional 
    ORDER BY c.DataAtualizacao DESC";

$stmtNotificacoes = $conn->prepare($queryNotificacoes);
$stmtNotificacoes->bindParam(':IdProfissional', $user_id);
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
    <link rel="stylesheet" href="../../dashboard/layout.css">
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
            <li><a href="index.php" class="noti">
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
        <div class="notifications-list">
            <?php if (count($notificacoes) > 0): ?>
                <?php foreach ($notificacoes as $notificacao): 
                    switch($notificacao['Status']) {
                        case 'Em Confirmacao':
                            $mensagem = "Nova consulta marcada com " . $notificacao['NomeUtilizador'];
                            $status_class = "pendente";
                            break;
                        case 'Aceite':
                            $mensagem = "Consulta aceita com " . $notificacao['NomeUtilizador'];
                            $status_class = "aceite";
                            break;
                        case 'Recusada':
                            $mensagem = "Consulta recusada com " . $notificacao['NomeUtilizador'];
                            $status_class = "recusada";
                            break;
                        default:
                            continue 2;
                    }
                ?>
                    <div class="notification-item <?= $status_class ?>">
                        <p><?= htmlspecialchars($mensagem); ?></p>
                        <small>Data da consulta: <?= date('d-m-Y H:i', strtotime($notificacao['DataConsulta'] . ' ' . $notificacao['HoraConsulta'])); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Não há notificações no momento.</p>
            <?php endif; ?>
            
            <!-- Botão sempre visível no final do container -->
            <div class="gerenciar-btn-container">
                <a href="../consultasMarcadas/index.php" class="gerenciar-btn">
                    Gerenciar Consultas
                </a>
            </div>
        </div>
    </div>
</body>
<script src="script.js"></script>
</html>