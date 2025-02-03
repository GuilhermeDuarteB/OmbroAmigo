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
        $_SESSION['user_type'] = 'admin'; // Armazenar tipo de usuário
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
        $_SESSION['user_type'] = 'user'; // Armazenar tipo de usuário

        // Buscar a foto do usuário
        $_SESSION['user_photo'] = $user['Foto'] ? base64_encode($user['Foto']) : null; // Armazena a foto em base64

        // Verifica o tipo de usuário e redireciona para a página apropriada
        if ($user['Tipo'] == 'admin') {
            header("Location: ../adminPanel/usersPanel/UserPanel.php");
        } elseif ($user["Tipo"] == "user") {
            header("Location: ../dashboard/dashinicial/index.php");
        } elseif ($user["Tipo"] == "desativada") {
            header("Location: ../reativarAcc/index.php "); 
        }
        exit();
    } else {
        echo "Senha inválida!";
    }
} else {
    echo "E-mail não encontrado!";
}
?>