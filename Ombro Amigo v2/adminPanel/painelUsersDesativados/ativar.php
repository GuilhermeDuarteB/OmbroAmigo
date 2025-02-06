<?php
session_start();
include "../../connection.php";

if (!isset($_SESSION['user_id']) || !isset($_POST['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

$userId = $_POST['user_id'];

try {
    $query = "UPDATE Utilizadores SET Tipo = 'user' WHERE Id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $userId);
    
    if ($stmt->execute()) {
        header("Location: index.php");
    } else {
        echo "Erro ao reativar o utilizador.";
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
