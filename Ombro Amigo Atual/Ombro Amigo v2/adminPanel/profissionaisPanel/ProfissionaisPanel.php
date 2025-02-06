<?php
session_start();
include "../../connection.php"; 


// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

// Excluir um user específico
if (isset($_POST['delete_user'])) {
    $id = $_POST['id']; 
    $query = "DELETE FROM UtilizadoresProfissionais WHERE Id = :Id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":Id", $id);
    
    if ($stmt->execute()) {
        echo "Utilizador com ID $id apagado com sucesso!";
    } else {
        echo "Erro ao excluir o usuário.";
    }
}

// Excluir todos os users e resetar o ID
if (isset($_POST['delete_all'])) {
    $query = "DELETE FROM UtilizadoresProfissionais";
    $stmt = $conn->prepare($query);
    
    if ($stmt->execute()) {
        echo "Todos os Profissionais foram apagados com sucesso.";
        
        // Reseta o valor da identidade (IdUtilizador)
        $query_reset_id = "DBCC CHECKIDENT ('UtilizadoresProfissionais', RESEED, 0)";
        $stmt_reset = $conn->prepare($query_reset_id);
        $stmt_reset->execute();
        echo "ID resetado com sucesso.";
    } else {
        echo "Erro ao excluir os usuários.";
    }
}

// Exibir os dados da tabela
$query = "SELECT * FROM UtilizadoresProfissionais";
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

    <title>Profissionais Admin Panel | Ombro Amigo</title>
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
        <h1>Gestão de Profissionais</h1>
        <div class="header-actions">
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Pesquisar por nome, email ou username">
            </div>
            <button class="btn-create" onclick="location.href='ProfissionaisCreate.php'">
                <i class="fas fa-plus btn-icon"></i> Novo Profissional
            </button>
            <form method="POST" action="" class="delete-all-form">
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
                            <img src="data:image/png;base64,<?= base64_encode($utilizador['Foto']); ?>" alt="Foto de Perfil">
                        <?php else: ?>
                            <img src="../defaultPhoto.png" alt="Foto Padrão">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($utilizador['Nome']); ?></td>
                    <td><?= htmlspecialchars($utilizador['NomeUtilizador']); ?></td>
                    <td><?= htmlspecialchars($utilizador['Email']); ?></td>
                    <td><?= htmlspecialchars($utilizador['Telefone']); ?></td>
                    <td class="actions">
                        <div class="action-buttons">
                            <form method="POST" action="ProfissionaisEdit.php">
                                <input type="hidden" name="id" value="<?= $utilizador['Id']; ?>">
                                <button type="submit" class="btn-edit">
                                    <i class="fas fa-edit btn-icon"></i>
                                </button>
                            </form>
                            <form method="POST" action="delete.php">
                                <input type="hidden" name="id" value="<?= $utilizador['Id']; ?>">
                                <button type="submit" name="delete_user" class="btn-delete">
                                    <i class="fas fa-trash btn-icon"></i>
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