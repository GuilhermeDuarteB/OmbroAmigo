<?php
session_start();
include '../connection.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $PalavraPasse = $_POST['PalavraPasse'];

    // Verificação especial para "admin@admin" e "admin123"
    if ($email === "admin@admin" && $PalavraPasse === "admin123") {
        $_SESSION['user_id'] = 'admin';
        $_SESSION['user_name'] = 'Administrador';
        $_SESSION['user_type'] = 'admin';
        echo json_encode(['success' => true, 'redirect' => '../adminPanel/index.php']);
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
        $_SESSION['user_type'] = 'user';
        $_SESSION['user_photo'] = $user['Foto'] ? base64_encode($user['Foto']) : null;

        $redirect = '';
        if ($user['Tipo'] == 'admin') {
            $redirect = '../adminPanel/usersPanel/UserPanel.php';
        } elseif ($user["Tipo"] == "user") {
            $redirect = '../dashboard/dashinicial/index.php';
        } elseif ($user["Tipo"] == "desativada") {
            $redirect = '../reativarAcc/index.php';
        }

        echo json_encode(['success' => true, 'redirect' => $redirect]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Email ou senha incorretos']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
}
?>