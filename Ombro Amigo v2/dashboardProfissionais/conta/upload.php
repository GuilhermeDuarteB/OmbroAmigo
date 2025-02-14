<?php
session_start();
include '../../connection.php';

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

// Acessa o ID do utilizador
$user_id = $_SESSION['user_id'];

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se uma nova foto de perfil foi enviada
    if (isset($_FILES['pfp']) && $_FILES['pfp']['error'] === UPLOAD_ERR_OK) {
        // Lê o conteúdo do arquivo da imagem como binário
        $fileTmpPath = $_FILES['pfp']['tmp_name'];

        // Lê o conteúdo do arquivo
        $foto = file_get_contents($fileTmpPath);

        // Atualiza a foto no banco de dados
        $updateQuery = "UPDATE UtilizadoresProfissionais SET Foto = CONVERT(varbinary(max), :foto) WHERE Id = :Id";
        $updateStmt = $conn->prepare($updateQuery);

        // Define o parâmetro como um string binário
        $updateStmt->bindParam(':foto', $foto, PDO::PARAM_LOB);  // LOB para dados binários
        $updateStmt->bindParam(':Id', $user_id);

        // Tenta executar a consulta
        try {
            if ($updateStmt->execute()) {
                // Redireciona para a página de conta após o upload
                header("Location: conta.php");
                exit();
            }
        } catch (PDOException $e) {
            // Trata o erro
            echo "Erro ao atualizar a foto: " . $e->getMessage();
        }
    } else {
        echo "Erro no upload da imagem.";
    }
} else {
    echo "Método não suportado.";
}
?>