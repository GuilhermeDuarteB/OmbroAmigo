<?php
session_start();
include '../../connection.php';

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? "";

    $updateQuery = "UPDATE UtilizadoresProfissionais SET Email = :email WHERE Id = :Id";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(':Id', $user_id);
    $stmt->execute();
    header('Location: index.php');
    exit();
}