<?php
session_start();

// Se não estiver logado, força redirecionamento
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/index.html');
    exit();
}

$response = array(
    'logado' => true,
    'message' => 'Utilizador logado'
);

header('Content-Type: application/json');
echo json_encode($response); 