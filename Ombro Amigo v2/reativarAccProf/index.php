<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// query username e noeme

$queryUser = "SELECT Nome, NomeUtilizador FROM UtilizadoresProfissionais WHERE Id = :Id";
$stmt = $conn->prepare($queryUser);
$stmt->bindParam(":Id", $user_id);
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

$nome = $userData["Nome"] ?? "Nome não encontrado";
$username = $userData["NomeUtilizador"] ?? "User não encontrado";

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
    $fotoSrc = "../dashboard/conta/uploads/defaultPhoto.png";
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reativar Conta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav>
        <label class="logo">
        <a href="../initial page/index.php" class="linklogo"><img src="../../logo-site.png"></a>
     </label>
    
        <input type="checkbox" id="check">
        <label for="check" class="checkbtn">
            <i class="fa-solid fa-bars"></i>
        </label>
    </nav>

    <div class="container">
        <p>Parece que desativaste a conta <br> <?= $username; ?></p>
        <img src="<?= $fotoSrc; ?>" id="fotoAcc">
        <p id="nome"><?=$nome;?></p>
        <form action="reativar.php">
        <button>Reativar Conta</button></form>
        <form action="cancel.php">
        <button>Cancelar</button></form>
    </div>
</body>
</html>