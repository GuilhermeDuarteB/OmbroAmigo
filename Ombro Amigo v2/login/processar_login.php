<?php
session_start();
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $PalavraPasse = $_POST['PalavraPasse'];

    // Verificação especial para "admin@admin" e "admin123"
    if ($email === "admin@admin" && $PalavraPasse === "admin123") {
        $_SESSION['user_id'] = 'admin';
        $_SESSION['user_name'] = 'Administrador';
        header("Location: ../adminPanel/usersPanel/UserPanel.php");
        exit();
    }

    // Verifica se o email e senha estão corretos
    $query = "SELECT * FROM Utilizadores WHERE Email = :email AND PalavraPasse = :PalavraPasse";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":PalavraPasse", $PalavraPasse);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['Id'];
        $_SESSION['user_name'] = $user['Nome'];

        if ($user['Tipo'] == 'admin') {
            header("Location: ../adminPanel/index.php");
        } elseif ($user["Tipo"] == "user") {
            header("Location: ../dashboard/dashinicial/index.php");
        } elseif ($user["Tipo"] == "desativada") {
            header("Location: ../reativarAcc/index.php");
        }

        // Verifica se existe um redirect
        if (isset($_POST['redirect'])) {
            header('Location: ' . $_POST['redirect']);
        } else {
            header('Location: ../initial page/index.html');
        }
        exit();
    } else {
        header("Location: index.html?error=1");
    }
} else {
    header("Location: index.html?error=2");
}
?> 