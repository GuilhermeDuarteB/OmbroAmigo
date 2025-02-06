<?php
session_start();
include '../../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

$user_id = $_SESSION['user_id']; // ID do profissional logado

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['IDConsulta'])) {
        $idConsulta = $_POST['IDConsulta']; // ID da consulta enviada pelo formulário

        // Atualizar o status da consulta para "Aceito"
        $updateQuery = "UPDATE Consultas SET Status = 'Aceite' WHERE IDConsulta = :IdConsulta AND IdProfissional = :IdProfissional";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bindParam(':IdConsulta', $idConsulta, PDO::PARAM_INT);
        $stmt->bindParam(':IdProfissional', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirecionar após a atualização
        header('Location: index.php');
        exit();
    } else {
        echo "Erro: ID da consulta não foi fornecido.";
        exit();
    }
} else {
    echo "Método de requisição inválido.";
    exit();
}
?>
