<?php
session_start();

include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['emailProf'];
    $PalavraPasse = $_POST['PalavraPasse'];


// Verificação especial para "admin@admin" e "admin123"
if ($email === "admin@admin" && $PalavraPasse === "admin123") {
    // Armazena dados administrativos na sessão
    $_SESSION['user_id'] = 'admin';
    $_SESSION['user_name'] = 'Administrador';
    $_SESSION['user_type'] = 'admin'; // Armazenar tipo de usuário

    // Redireciona imediatamente para o painel de admin
    header("Location: ../adminPanel/usersPanel/UserPanel.php");
    exit();
}

    // Verifica se o email e senha estão corretos
    $query = "SELECT * FROM UtilizadoresProfissionais WHERE Email = :email AND PalavraPasse = :PalavraPasse";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":PalavraPasse", $PalavraPasse);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Armazenar os dados do usuário na sessão
        $_SESSION['user_id'] = $user['Id'];
        $_SESSION['user_name'] = $user['Nome'];
        $_SESSION['user_type'] = 'UtilizadorProfissional'; // Armazenar tipo de usuário

        // Buscar a foto do profissional
        $_SESSION['user_photo'] = $user['Foto'] ? base64_encode($user['Foto']) : null; // Armazena a foto em base64

        if ($user['Tipo'] == 'profissional') {
            header("Location: ../dashboardProfissionais/dashboardInicial/index.php"); 
        } elseif ($user["Tipo"] == "desativadaProf") {
            header("Location: ../reativarAccProf/index.php");
        } elseif ($user["Tipo"] == "candidato") {
            header("Location: ../candidaturaEstado/index.php");
        }
        exit();
    } else {
        echo "Senha inválida!";
    }
} else {
    echo "E-mail não encontrado!";
}
?>
