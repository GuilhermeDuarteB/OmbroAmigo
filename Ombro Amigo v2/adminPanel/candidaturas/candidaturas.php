<?php
session_start();
include "../../connection.php";

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

// Exibir os dados da tabela
$query = "SELECT Id, Nome, Email, NomeUtilizador, AreaEspecializada, Telefone, CV, Diploma 
          FROM UtilizadoresProfissionais 
          WHERE Tipo = 'candidato'";
$stmt = $conn->prepare($query);
$stmt->execute();
$utilizadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="PT-pt">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../../logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../shared/style.css">
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
    <title>Candidaturas | Ombro Amigo</title>
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

    <div class="admin-container">
        <div class="header">
            <h1>Candidaturas de Profissionais</h1>
            <div class="header-actions">
                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="Pesquisar por nome, email ou especialização">
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Especialização</th>
                        <th>Telefone</th>
                        <th>Informações</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilizadores as $utilizador): ?>
                        <tr>
                            <td><?= htmlspecialchars($utilizador['Nome']); ?></td>
                            <td><?= htmlspecialchars($utilizador['Email']); ?></td>
                            <td><?= htmlspecialchars($utilizador['NomeUtilizador']); ?></td>
                            <td><?= htmlspecialchars($utilizador['AreaEspecializada']); ?></td>
                            <td><?= htmlspecialchars($utilizador['Telefone']); ?></td>
                            <td>
                                <div class="button-container">
                                    <button class="cvbtn" data-cv="<?= base64_encode($utilizador['CV']); ?>"
                                        data-prof-id="<?= htmlspecialchars($utilizador['Id']); ?>">
                                        <i class="fas fa-file"></i> CV
                                    </button>
                                    <button class="diplomabtn" data-diploma="<?= base64_encode($utilizador['Diploma']); ?>"
                                        data-prof-id="<?= htmlspecialchars($utilizador['Id']); ?>">
                                        <i class="fas fa-file"></i> Diploma
                                    </button>
                                </div>
                            </td>
                            <td class="actions">
                                <div class="action-buttons">
                                    <form action="acceptProf.php" method="POST">
                                        <input type="hidden" name="prof_id"
                                            value="<?= htmlspecialchars($utilizador['Id']); ?>">
                                        <button id="acceptProf_<?= htmlspecialchars($utilizador['Id']); ?>" class="btn-edit"
                                            disabled>
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="refuseProf.php" method="POST">
                                        <input type="hidden" name="prof_id"
                                            value="<?= htmlspecialchars($utilizador['Id']); ?>">
                                        <button id="refuseProf_<?= htmlspecialchars($utilizador['Id']); ?>"
                                            class="btn-delete" disabled>
                                            <i class="fas fa-xmark"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="infoCandidato" id="showCV">
        <button id="closeCV"><i class="fa-solid fa-x"></i></button>
        <div id="pdfCV" style="width: 100%; max-width: 500px; height: 500px; overflow: auto; border: 1px solid #ddd;">
            <canvas id="pdf-canvas-cv"></canvas>
        </div>
        <div class="acceptBtn">
            <button class="acceptCVBtn" id="acceptCVBtn">
                <i class="fa-solid fa-check"></i>
            </button>
        </div>
    </div>

    <div class="infoCandidato" id="showDiploma">
        <button id="closeDiploma"><i class="fa-solid fa-x"></i></button>
        <div id="pdfDiploma"
            style="width: 100%; max-width: 500px; height: 500px; overflow: auto; border: 1px solid #ddd;"><canvas
                id="pdf-canvas-diploma"></canvas>
        </div>
        <div class="acceptBtn">
            <button class="acceptDiplomaBtn" id="acceptDiplomaBtn">
                <i class="fa-solid fa-check"></i>
            </button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script src="script.js"></script>
    <script>
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('.user-table tbody tr');

        rows.forEach(row => {
            const nome = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const especializacao = row.querySelector('td:nth-child(4)').textContent.toLowerCase();

            if (nome.includes(searchValue) || email.includes(searchValue) || especializacao.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    </script>
</body>

</html>