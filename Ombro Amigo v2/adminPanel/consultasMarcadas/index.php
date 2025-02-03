<?php
session_start();
include "../../connection.php"; 

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

// Buscar todas as consultas com informações dos usuários e profissionais
$query = "SELECT 
            c.IDConsulta,
            c.DataConsulta,
            c.HoraConsulta,
            c.Status,
            u.Nome as NomeUtilizador,
            u.Email as EmailUtilizador,
            p.Nome as NomeProfissional,
            p.Email as EmailProfissional
          FROM Consultas c
          JOIN Utilizadores u ON c.IdUtilizador = u.Id
          JOIN UtilizadoresProfissionais p ON c.IdProfissional = p.Id
          ORDER BY c.DataConsulta DESC, c.HoraConsulta DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../../logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
    <title>Gestão de Consultas | Ombro Amigo</title>
</head>
<body>
<nav>
        <ul>
            <li><a href="../../initial page/index.php" class="logo">
                <img src="../../../logo-site-sem-texto.png" alt="">
                <span class="nav-item">Ombro Amigo</span>
            </a></li>

            <li><a href="../index.php">
                <i class="fas fa-solid fa-house"></i>
                <span class="nav-item">Painel Principal</span>
            </a></li>

            <li><a href="../usersPanel/UserPanel.php">
                <i class="fas fa-solid fa-user"></i>
                <span class="nav-item">Painel Users</span>
            </a></li>

            <li><a href="../profissionaisPanel/profissionaisPanel.php">
                <i class="fas fa-solid fa-user-doctor"></i>
                <span class="nav-item">Painel Profissionais</span>
            </a></li>
            
            <li><a href="../painelUsersDesativados/index.php">
                <i class="fas fa-solid fa-user-minus"></i>
                <span class="nav-item">Painel Users Desativados</span>
            </a></li>

            <li><a href="../candidaturas/candidaturas.php">
                <i class="fas fa-solid fa-clipboard"></i>
                <span class="nav-item">Candidaturas Profissionais</span>
            </a></li>

            <li><a href="../consultasMarcadas/index.php">
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
        <div class="header">
            <h1>Gestão de Consultas</h1>
        </div>

        <div class="table-container">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Utilizador</th>
                        <th>Profissional</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consultas as $consulta): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($consulta['DataConsulta'])); ?></td>
                        <td><?= date('H:i', strtotime($consulta['HoraConsulta'])); ?></td>
                        <td>
                            <div class="user-info">
                                <span class="user-name"><?= htmlspecialchars($consulta['NomeUtilizador']); ?></span>
                                <span class="user-email"><?= htmlspecialchars($consulta['EmailUtilizador']); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="user-info">
                                <span class="user-name"><?= htmlspecialchars($consulta['NomeProfissional']); ?></span>
                                <span class="user-email"><?= htmlspecialchars($consulta['EmailProfissional']); ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-<?= strtolower($consulta['Status']); ?>">
                                <?= htmlspecialchars($consulta['Status']); ?>
                            </span>
                        </td>
                        <td class="actions">
                            <div class="action-buttons">
                                <form method="POST" action="deleteConsulta.php" onsubmit="return confirm('Tem certeza que deseja cancelar esta consulta?');">
                                    <input type="hidden" name="id" value="<?= $consulta['IDConsulta']; ?>">
                                    <button type="submit" class="btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
