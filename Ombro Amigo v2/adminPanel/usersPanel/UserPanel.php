<?php
session_start();
include "../../connection.php";

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

// Apenas buscar os dados da tabela
$query = "SELECT * FROM Utilizadores WHERE Tipo = 'user'";
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
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
    <title>User Admin Panel | Ombro Amigo</title>
</head>
<body>
<nav>
        <ul>
            <li><a href="../../initial page/index.php" class="logo">
            <img src="../../../logo-site-sem-texto.png" >
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
            <h1>Gestão de Utilizadores</h1>
            <div class="header-actions">
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Pesquisar por nome ou email...">
                </div>
                <button class="btn-create" onclick="location.href='UserCreate.php'">
                    <i class="fas fa-plus btn-icon"></i> Novo Utilizador
                </button>
                <form method="POST" action="deleteAll.php" class="delete-all-form" onsubmit="return confirm('Tem certeza que deseja excluir todos os utilizadores?');">
                    <button type="submit" name="delete_all" class="btn-delete-all">
                        <i class="fas fa-trash-alt btn-icon"></i> Apagar Todos
                    </button>
                </form>
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
                            <button onclick="location.href='UserEdit.php?id=<?= $utilizador['Id']; ?>'" class="btn-edit">
                                <i class="fas fa-edit btn-icon"></i>
                            </button>
                            <form method="POST" action="delete.php" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este utilizador?');">
                                <input type="hidden" name="id" value="<?= $utilizador['Id']; ?>">
                                <button type="submit" name="delete_user" class="btn-delete">
                                    <i class="fas fa-trash btn-icon"></i>
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
    // Verifica se há mensagens de sucesso ou erro na sessão
    <?php if (isset($_SESSION['success_message'])): ?>
        alert('<?php echo addslashes($_SESSION['success_message']); ?>');
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        alert('<?php echo addslashes($_SESSION['error_message']); ?>');
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    // Função de pesquisa
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('.user-table tbody tr');

        tableRows.forEach(row => {
            const name = row.children[1].textContent.toLowerCase();
            const email = row.children[3].textContent.toLowerCase();
            
            if (name.includes(searchValue) || email.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    </script>

</body>
</html>