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
        $fileTmpPath = $_FILES['pfp']['tmp_name'];
        
        // Verificar o tipo de arquivo
        $fileType = mime_content_type($fileTmpPath);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (!in_array($fileType, $allowedTypes)) {
            die("Tipo de arquivo não permitido. Apenas JPEG, PNG e GIF são aceitos.");
        }

        // Processar a imagem
        $imageInfo = getimagesize($fileTmpPath);
        if ($imageInfo === false) {
            die("Arquivo não é uma imagem válida.");
        }

        // Ler o conteúdo do arquivo
        $foto = file_get_contents($fileTmpPath);
        
        // Converter para base64 para debug
        $base64 = base64_encode($foto);
        error_log("Tamanho da imagem em base64: " . strlen($base64));

        try {
            // Primeiro, verificar se já existe uma foto
            $checkQuery = "SELECT Foto FROM Utilizadores WHERE Id = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->execute([$user_id]);
            
            if ($checkStmt->fetch()) {
                // Update existente
                $updateQuery = "UPDATE Utilizadores SET Foto = ? WHERE Id = ?";
            } else {
                // Insert novo
                $updateQuery = "UPDATE Utilizadores SET Foto = ? WHERE Id = ?";
            }

            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bindParam(1, $foto, PDO::PARAM_LOB);
            $updateStmt->bindParam(2, $user_id);

            if ($updateStmt->execute()) {
                header("Location: conta.php");
                exit();
            } else {
                error_log("Erro ao executar query: " . print_r($updateStmt->errorInfo(), true));
                die("Erro ao atualizar foto no banco de dados.");
            }
        } catch (PDOException $e) {
            error_log("Erro PDO: " . $e->getMessage());
            die("Erro ao processar a foto: " . $e->getMessage());
        }
    } else {
        die("Erro no upload da imagem: " . $_FILES['pfp']['error']);
    }
} else {
    echo "Método não suportado.";
}
?>