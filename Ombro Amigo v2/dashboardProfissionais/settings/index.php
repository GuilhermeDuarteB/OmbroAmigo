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
    <link rel="stylesheet" href="../../dashboard/layout.css">
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
        </a>
      </li>
      <li>
        <a href="../consultasMarcadas/index.php">
          <i class="fas fa-solid fa-clipboard-check"></i>
          <span class="nav-item">Consultas Marcadas</span>
        </a>
      </li>
      <li><a href="calendario/index.php">
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
      <li><a href="#" class="noti">
          <i class="fas fa-solid fa-bell"></i>
          <span class="nav-item">Notificações</span>
        </a>
      </li>
      <li class="logout"><a href="../logout.php">
          <i class="fas fa-solid fa-right-from-bracket"></i>
          <span class="nav-item">Sair do Painel</span>
        </a></li>
    </ul>
  </nav>

    <div class="container">
        <div class="menuLateral">
            <img src="<?= $fotoSrc; ?>" id="fotoAcc">

            <li class="conta">
                <span class="nav-item">Conta</span>
            </li>

            <li class="notificacoes">
                <span class="nav-item">Notificações</span>
            </li>


            <li class="subscricao">
                <span class="nav-item">Subscrição</span>
            </li>
        </div>

        <div class="accSettings">
            <h1>Conta</h1>
            <h4>
                <div class="icon-circle">
                    <button id="btnEditEmail"> <i class="fa fa-pencil"></i></button>
                </div> Endereço de E-Mail:
            </h4>
            <p name="email"> <?= $email; ?></p>
        </div>
        <div class="mudarEmail" id="mudarEmail">
            <button id="closeMenu"><i class="fa-solid fa-x"></i></button>
            <form action="editEmail.php" method="post">
                <h4>Editar E-Mail</h4>
                <p>Por favor insere o e-mail para o que desejas mudar</p>
                <input type="email" name="email" placeholder="Insere o novo e-mail"><br>
                <button type="submit" id="editMailBtn">Editar E-Mail</button>
            </form>
        </div>
        <div class="motivoApagar" id="motivoApagarAcc">
            <button id="closeMenuDelete"><i class="fa-solid fa-x"></i></button>
            <form action="accOff.php" method="post">
                <h4>Porque razão queres apagar a tua conta? (opcional)</h4>

                <li><input type="checkbox" name="motivos[]" value="Preco Alto">Preços Altos</li>
                <li><input type="checkbox" name="motivos[]" value="Ma Experiencia">Má Experiência</li>
                <li><input type="checkbox" name="motivos[]" value="Falta Funcoes">Falta de Funções</li>
                <li><input type="checkbox" name="motivos[]" value="Falta Suporte">Falta de Suporte</li>
                <li><input type="checkbox" name="motivos[]" value="Relevancia Limitada">Relevância Limitada</li>

                <li>Outro:<br>
                <textarea name="motivoOutro" id="outroMotivoSaida" maxlength="250"></textarea>
                </li>
                <div class="contadorCaracteres" style="float:right; margin-right:5%;">
                    <span id="caractAtual">0</span>/<span id="caractFinal">250</span>
                </div>

                <button type="submit" name="desativarConta" id="apagarAccBtn">Apagar Conta</button>
            </form>
        </div>
        <div class="apagarAcc">
            <button id="apagarAccBtnFunc">Apagar Conta</button>
        </div>
    </div>

</body>
<script src="script.js"></script>

</html>