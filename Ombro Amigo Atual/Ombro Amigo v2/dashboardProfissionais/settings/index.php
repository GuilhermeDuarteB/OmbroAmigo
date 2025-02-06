<?php
session_start();
include '../../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Consulta para obter a foto do usuário
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

// Consulta para obter o email do usuário
$queryUser = "SELECT Email FROM UtilizadoresProfissionais WHERE Id = :Id";
$stmtUser = $conn->prepare($queryUser);
$stmtUser->bindParam(":Id", $user_id);
$stmtUser->execute();
$utilizadorEmail = $stmtUser->fetch(PDO::FETCH_ASSOC);

// Define a variável $email com o valor obtido da consulta
$email = $utilizadorEmail['Email'] ?? 'Email não encontrado';
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
    <title>Definições | Ombro Amigo</title>
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
                </a></li>

            <li><a href="../consultasMarcadas/index.php">
                    <i class="fas fa-solid fa-calendar-check"></i>
                    <span class="nav-item">Consultas Marcadas</span>
                </a></li>

            <li><a href="../calendario/index.php">
                    <i class="fas fa-solid fa-calendar-days"></i>
                    <span class="nav-item">Calendário</span>
                </a></li>

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

    <div class="container">
        <div class="menuLateral">
            <img src="<?= $fotoSrc; ?>" id="fotoAcc">
            <ul class="settings-menu">
                <li class="conta active">
                    <i class="fas fa-user"></i>
                    <span class="nav-item">Conta</span>
                </li>
                <li class="apagar-conta" id="apagarAccBtnFunc">
                    <i class="fas fa-trash"></i>
                    <span class="nav-item">Apagar Conta</span>
                </li>
            </ul>
        </div>

        <!-- Seção Conta -->
        <div class="settings-section active" id="contaSection">
            <h1>Conta</h1>
            
            <div class="settings-card">
                <h4>
                    <div class="icon-circle">
                        <button id="btnEditEmail"><i class="fa fa-pencil"></i></button>
                    </div> 
                    Endereço de E-Mail
                </h4>
                <p name="email"><?= $email; ?></p>
            </div>

            <div class="settings-card">
                <h4>
                    <div class="icon-circle">
                        <button id="btnEditSenha"><i class="fa fa-key"></i></button>
                    </div> 
                    Alterar Palavra-Passe
                </h4>
                <p>********</p>
            </div>
        </div>

        <!-- Modal Editar Email -->
        <div class="mudarEmail" id="mudarEmail">
            <button class="closeBtn" id="closeMenu"><i class="fa-solid fa-x"></i></button>
            <form action="editEmail.php" method="post">
                <h4 class="modal-title">Editar E-Mail</h4>
                <p class="modal-subtitle">Por favor insere o e-mail para o que desejas mudar</p>
                <input type="email" name="email" placeholder="Insere o novo e-mail" required>
                <button type="submit" class="editBtn" id="editMailBtn">Editar E-Mail</button>
            </form>
        </div>

        <!-- Modal Editar Senha -->
        <div class="mudarSenha" id="mudarSenha">
            <button class="closeBtn" id="closeSenhaMenu"><i class="fa-solid fa-x"></i></button>
            <form action="editSenha.php" method="post">
                <h4 class="modal-title">Alterar Senha</h4>
                <p class="modal-subtitle">Digite sua senha atual e a nova senha</p>
                <input type="password" name="senhaAtual" placeholder="Senha atual" required>
                <input type="password" name="novaSenha" placeholder="Nova senha" required>
                <input type="password" name="confirmarSenha" placeholder="Confirmar nova senha" required>
                <button type="submit" class="editBtn" id="editSenhaBtn">Alterar Senha</button>
            </form>
        </div>

        <div class="modal-overlay" id="modalOverlay"></div>
        
        <!-- Modal Apagar Conta -->
        <div class="motivoApagar" id="motivoApagarAcc">
            <button id="closeMenuDelete"><i class="fa-solid fa-x"></i></button>
            <form action="accOff.php" method="post">
                <h4>Porque razão queres apagar a tua conta? (opcional)</h4>

                <li><input type="checkbox" name="motivos[]" value="Preco Alto">Preços Altos</li>
                <li><input type="checkbox" name="motivos[]" value="Ma Experiencia">Má Experiência</li>
                <li><input type="checkbox" name="motivos[]" value="Falta Funcoes">Falta de Funções</li>
                <li><input type="checkbox" name="motivos[]" value="Falta Suporte">Falta de Suporte</li>
                <li><input type="checkbox" name="motivos[]" value="Relevancia Limitada">Relevância Limitada</li>

                <div>
                    <label>Outro:</label>
                    <textarea name="motivoOutro" id="outroMotivoSaida" maxlength="250"></textarea>
                    <div class="contadorCaracteres">
                        <span id="caractAtual">0</span>/<span id="caractFinal">250</span>
                    </div>
                </div>

                <button type="submit" name="desativarConta" id="apagarAccBtn">Apagar Conta</button>
            </form>
        </div>
    </div>
</body>
<script src="script.js"></script>
</html>