<?php
session_start();
include '../connection.php';

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.html");
    exit();
}

// Acessa o ID do utilizador
$user_id = $_SESSION['user_id'];

$query = "UPDATE UtilizadoresProfissionais SET Tipo = 'profissional' WHERE  Id = :Id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":Id", $user_id);
$stmt->execute();


// leva para conta
 header("Location: ../dashboardProfissionais/conta/conta.php");
 exit();