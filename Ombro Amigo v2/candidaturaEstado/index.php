<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Verifica se o user_id está correto
$query = "SELECT Nome FROM UtilizadoresProfissionais WHERE Id = :Id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":Id", $user_id);
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($userData) {
    $nome = $userData["Nome"];
} else {
    $nome = "Nome não encontrado";
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
    <title>Estado da Candidatura</title>
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
        <p id="X"><i class="fa-solid fa-check"></i>
        </p>
        <p id="nome"><?=$nome;?></p>
        <p>Parece que a tua candidatura ainda se encontra em análise!</p>
        <a href="../initial page/index.php">Voltar para a Página Inicial</a>
    </div>
</body>
</html>
