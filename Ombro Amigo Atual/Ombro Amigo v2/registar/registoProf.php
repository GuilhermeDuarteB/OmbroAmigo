<?php
session_start();
include '../connection.php';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta os dados do formulário
    $nome = $_POST['nome'] ?? '';
    $email = strtolower(trim($_POST['email'])) ?? '';
    $palavraPasse = $_POST['PalavraPasse'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $dataNascimento = $_POST['dataNascimento'] ?? '';
    $nTelefone = $_POST['nTelefone'] ?? '';
    $morada = $_POST['morada'] ?? '';
    $situacao = $_POST['situacao'] ?? '';
    $estabelecimentoEnsino = $_POST['estabelecimentoEnsino'] ?? '';
    $areaEspecializada = $_POST['areaEspecializada'] ?? '';
    $motivo = $_POST['motivo'] ?? '';
    $nomeUtilizador = $_POST['nomeUtilizador'] ?? '';
    $userType = "candidato";

    // Inicializa variáveis para os dados dos arquivos
    $cv = null;
    $diploma = null;

    // Verifica se o CV foi enviado e se é um arquivo PDF válido
    if (isset($_FILES['CV']) && $_FILES['CV']['error'] === UPLOAD_ERR_OK) {
        $cvTmpPath = $_FILES['CV']['tmp_name'];
        $cvFileType = mime_content_type($cvTmpPath); // Verifica o tipo de arquivo

        if ($cvFileType === 'application/pdf') { // Verifica se é PDF
            $cv = file_get_contents($cvTmpPath); // Lê o conteúdo do arquivo
        } else {
            echo "O CV deve ser um arquivo PDF.";
            exit();
        }
    } else {
        echo "Erro ao enviar o CV.";
        exit();
    }

    // Verifica se o Diploma foi enviado e se é um arquivo PDF válido
    if (isset($_FILES['Diploma']) && $_FILES['Diploma']['error'] === UPLOAD_ERR_OK) {
        $diplomaTmpPath = $_FILES['Diploma']['tmp_name'];
        $diplomaFileType = mime_content_type($diplomaTmpPath); // Verifica o tipo de arquivo

        if ($diplomaFileType === 'application/pdf') { // Verifica se é PDF
            $diploma = file_get_contents($diplomaTmpPath); // Lê o conteúdo do arquivo
        } else {
            echo "O Diploma deve ser um arquivo PDF.";
            exit();
        }
    } else {
        echo "Erro ao enviar o Diploma.";
        exit();
    }

    // Insere os dados no banco de dados
    $insertQuery = "INSERT INTO UtilizadoresProfissionais (
        Nome, Email, PalavraPasse, Genero, DataNascimento, Telefone, Morada, 
        SituacaoAtual, EstabelecimentoEnsino, AreaEspecializada, MotivoCandidatura, CV, Diploma, NomeUtilizador, Tipo
    ) 
    VALUES (
        :nome, :email, :palavraPasse, :genero, :dataNascimento, :nTelefone, :morada, 
        :situacao, :estabelecimentoEnsino, :areaEspecializada, :motivo, 
        CONVERT(VARBINARY(MAX), :cv), CONVERT(VARBINARY(MAX), :diploma), :nomeUtilizador, :userType
    )";

    $insertStmt = $conn->prepare($insertQuery);
    
    // Bind de todos os parâmetros
    $insertStmt->bindParam(':nome', $nome);
    $insertStmt->bindParam(':email', $email);
    $insertStmt->bindParam(':palavraPasse', $palavraPasse);
    $insertStmt->bindParam(':genero', $genero);
    $insertStmt->bindParam(':dataNascimento', $dataNascimento);
    $insertStmt->bindParam(':nTelefone', $nTelefone);
    $insertStmt->bindParam(':morada', $morada);
    $insertStmt->bindParam(':situacao', $situacao);
    $insertStmt->bindParam(':estabelecimentoEnsino', $estabelecimentoEnsino);
    $insertStmt->bindParam(':areaEspecializada', $areaEspecializada);
    $insertStmt->bindParam(':motivo', $motivo);
    $insertStmt->bindParam(':nomeUtilizador', $nomeUtilizador);
    $insertStmt->bindParam(':userType', $userType);
    
    // Bind para dados binários
    $insertStmt->bindParam(':cv', $cv, PDO::PARAM_LOB);
    $insertStmt->bindParam(':diploma', $diploma, PDO::PARAM_LOB);

    // Tenta executar a inserção
    try {
        if ($insertStmt->execute()) {
            $user_id = $conn->lastInsertId();

            // Armazenar o ID e nome do utilizador na sessão
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $nomeUtilizador;
            $_SESSION['user_type'] = 'UtilizadorProfissional'; // Adicionar tipo de usuário
            $_SESSION['user_photo'] = null; // Inicializar foto como null

            // Redirecionar para a página de candidaturaEstado
            header("Location: ../candidaturaEstado/index.php");
            exit();
        }
    } catch (PDOException $e) {
        echo "Erro ao registrar o profissional: " . $e->getMessage();
        exit();
    }
}
?>
