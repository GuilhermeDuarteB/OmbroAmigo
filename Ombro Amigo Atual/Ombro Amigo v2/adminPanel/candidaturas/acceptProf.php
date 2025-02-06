// acceptProf.php
<?php
session_start();
include "../../connection.php";

if (!isset($_SESSION['user_id']) || !isset($_POST['prof_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

try {
    $profId = $_POST['prof_id'];
    $query = "UPDATE UtilizadoresProfissionais SET Tipo = 'profissional' WHERE ID = :prof_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':prof_id', $profId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: candidaturas.php");
        exit();
    }
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}