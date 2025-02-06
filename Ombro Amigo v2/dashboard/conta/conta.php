<?php
session_start();
include '../../connection.php';

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

// Acessa o ID do utilizador
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $dataNascimento = $_POST['dataNascimento'] ?? '';
    $telefone = $_POST['telefone'] ?? '';

    // Verifica se uma nova foto de perfil foi enviada
    if (isset($_FILES['pfp']) && $_FILES['pfp']['error'] === UPLOAD_ERR_OK) {
        // Verifica o tipo de arquivo
        $fileTmpPath = $_FILES['pfp']['tmp_name'];
        $fileType = mime_content_type($fileTmpPath);

        // Permitir apenas imagens
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($fileType, $allowedMimeTypes)) {
            // Lê o conteúdo do arquivo da imagem como binário
            $foto = file_get_contents($fileTmpPath);

            // Atualiza a foto no banco de dados
            $updateFotoQuery = "UPDATE Utilizadores SET Foto = :foto WHERE Id = :Id";
            $updateFotoStmt = $conn->prepare($updateFotoQuery);
            $updateFotoStmt->bindParam(':foto', $foto, PDO::PARAM_LOB);  // LOB para dados binários
            $updateFotoStmt->bindParam(':Id', $user_id);
            $updateFotoStmt->execute();
        } else {
            echo "Formato de arquivo não suportado. Apenas JPEG, PNG e GIF são permitidos.";
        }
    }

    // Atualiza os outros dados
    $updateQuery = "UPDATE Utilizadores SET Nome = :nome, DataNascimento = :dataNascimento, Telefone = :telefone WHERE Id = :Id";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bindParam(':nome', $nome);
    $updateStmt->bindParam(':dataNascimento', $dataNascimento);
    $updateStmt->bindParam(':telefone', $telefone);
    $updateStmt->bindParam(':Id', $user_id);

    // Executa a consulta
    if ($updateStmt->execute()) {
        // Atualiza os dados na sessão se necessário
        $_SESSION['user_name'] = $nome;
        header("Location: conta.php"); // Redireciona para a mesma página
        exit();
    }
}

// Buscar username e foto da db
$query = "SELECT NomeUtilizador, Nome, DataNascimento, Telefone, Foto FROM Utilizadores WHERE Id = :Id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":Id", $user_id);
$stmt->execute();
$utilizador = $stmt->fetch(PDO::FETCH_ASSOC);

$descricao = ''; 
$sentimentoQuery = "SELECT TOP 1 descricao FROM Sentimentos WHERE IDUtilizador = :user_id ORDER BY data DESC";
$sentimentoStmt = $conn->prepare($sentimentoQuery);
$sentimentoStmt->bindParam(":user_id", $user_id);
$sentimentoStmt->execute();
$sentimento = $sentimentoStmt->fetch(PDO::FETCH_ASSOC);

if ($sentimento) {
    $descricao = htmlspecialchars($sentimento['descricao']);
}

if ($utilizador) {
    $username = htmlspecialchars($utilizador['NomeUtilizador']);
    $nome = htmlspecialchars($utilizador['Nome']);
    $dataNascimento = htmlspecialchars($utilizador["DataNascimento"]);
    $telefone = htmlspecialchars($utilizador["Telefone"]);

    // Converte a data de yyyy-mm-dd para dd-mm-yyyy
    $dateTime = DateTime::createFromFormat('Y-m-d', $dataNascimento);
    $dataNascimento = $dateTime ? $dateTime->format('d-m-Y') : 'Data inválida';

    // Verifica se há uma imagem e converte para base64
    if (!empty($utilizador['Foto'])) {
        $fotoBase64 = base64_encode($utilizador['Foto']);
        $fotoSrc = "data:image/jpeg;base64,{$fotoBase64}"; // Exibir a imagem em base64
    } else {
        $fotoSrc = "uploads/defaultPhoto.png";  // Caminho para a imagem padrão
    }
} else {
    $username = "Utilizador não encontrado";
    $nome = "Nome não encontrado";
    $dataNascimento = "Data não encontrada";
    $fotoSrc = "uploads/defaultPhoto.png"; 
}

include_once '../verificar_subscricao.php';
$temSubscricaoAtiva = verificarSubscricaoAtiva($conn, $user_id);
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
    <title>A Minha Conta | Ombro Amigo</title>
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

        <?php if ($temSubscricaoAtiva): ?>
            <li><a href="../calendario/index.php">
                    <i class="fas fa-solid fa-calendar-days"></i>
                    <span class="nav-item">Calendário</span>
                </a></li>

            <li><a href="../marcarConsultas/index.php">
                    <i class="fas fa-solid fa-clipboard"></i>
                    <span class="nav-item">Marcar Consultas</span>
                </a></li>

            <li><a href="../consultasMarcadas/index.php">
                    <i class="fas fa-solid fa-clipboard-check"></i>
                    <span class="nav-item">Consultas Marcadas</span>
                </a></li>

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
            </a></li>

        <li class="logout"><a href="../logout.php">
                <i class="fas fa-solid fa-right-from-bracket"></i>
                <span class="nav-item">Sair do Painel</span>
            </a></li>
    </ul>
</nav>
    <div class="accMenu">
        <div class="info">
            <div class="profilePhoto">
                <div class="profile-container">
                    <img src="<?= $fotoSrc; ?>" id="fotoUser" alt="Foto de perfil">
                    <button id="pfp" style="display: none"><img src="<?= $fotoSrc; ?>" id="fotoUser"
                            alt="Foto de perfil">
                    </button>
                    <div class="username">
                        <p id="editFotoP" style="display:none">Editar Foto</p>
                        <p> @<?= $username; ?></p>
                        <p id="descricao">Hoje eu estou <?= $descricao; ?></p>
                    </div>
                </div>
            </div>

            <form id="perfilForm" method="POST" action="conta.php">
                <div class="menuInfo">
                    <div class="nome">
                        <h2>Nome</h2>
                        <p><?= $nome; ?></p>
                        <input type="text" name="nome" value="<?= $nome; ?>" style="display:none;">
                    </div>
                    <div class="dataNascimento">
                        <h2>Data de Nascimento</h2>
                        <p><?= $dataNascimento; ?></p>
                        <input type="date" name="dataNascimento" value="<?= $dataNascimento; ?>" style="display:none;">
                    </div>
                    <div class="telefone">
                        <h2>Telefone</h2>
                        <p><?= $telefone; ?></p>
                        <input type="tel" name="telefone" value="<?= $telefone; ?>" style="display:none;">
                    </div>
                </div>

                <div class="editBtn">
                    <button type="button" id="editarPerfil" onclick="EditarPerfil()">Editar Perfil</button>
                    <button type="submit" id="salvarPerfil" style="display:none;">Salvar</button>
                </div>
            </form>

            <!-- Menu Inserir Foto -->
            <div class="pfpMenu hidden" id="pfpMenu">
                <i class="fa-solid fa-x" id="sair"></i>
                <p id="t1">Editar Foto de Perfil</p>
                <form action="upload.php" method="POST" enctype="multipart/form-data">
                    <div class="filebox" id="filebox">
                        <i class="fa-solid fa-images" id="iconimg"></i>
                        <p>Seleciona ou arrasta a foto de perfil que pretendes utilizar.</p>
                        <input style="display: none" type="file" id="fileInput" name="pfp" accept="image/*" required>
                    </div>
                    <button type="submit" id="salvarFotoBtn">Salvar Foto</button>
                </form>
            </div>
        </div>
    </div>
    </div>
    <script src="script.js"></script>

</body>

</html>