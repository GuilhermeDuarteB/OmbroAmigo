<?php
session_start();
include "../../connection.php";

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

// Exibir apenas usuários desativados
$query = "SELECT * FROM Utilizadores WHERE Tipo = 'desativada'";
$stmt = $conn->prepare($query);
$stmt->execute();
$utilizadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../../logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../shared/style.css">
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
    <title>Users Desativados | Ombro Amigo</title>
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
            <h1>Gestão de Utilizadores Desativados</h1>
            <div class="header-actions">
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Pesquisar por nome, email ou username">
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilizadores as $utilizador): ?>
                    <tr>
                        <td class="user-photo">
                            <?php if ($utilizador['Foto']): ?>
                                <img src="data:image/png;base64,<?= base64_encode($utilizador['Foto']); ?>" alt="Imagem de Perfil">
                            <?php else: ?>
                                <img src="../defaultPhoto.png" alt="Foto Padrão">
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($utilizador['Nome']); ?></td>
                        <td><?= htmlspecialchars($utilizador['NomeUtilizador']); ?></td>
                        <td><?= htmlspecialchars($utilizador['Email']); ?></td>
                        <td><?= htmlspecialchars($utilizador['Telefone']); ?></td>
                        <td class="actions">
                            <form action="ativar.php" method="POST">
                                <input type="hidden" name="user_id" value="<?= $utilizador['Id']; ?>">
                                <button type="submit" class="btn-edit">
                                    <i class="fas fa-user-check"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('.user-table tbody tr');

        rows.forEach(row => {
            const nome = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const username = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

            if (nome.includes(searchValue) || username.includes(searchValue) || email.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    </script>
</body>
</html>
