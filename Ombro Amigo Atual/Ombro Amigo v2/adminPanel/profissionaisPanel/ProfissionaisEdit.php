<?php
session_start();
include "../../connection.php"; 

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

// Verificar se o ID do profissional foi passado
if (isset($_POST['id']) || isset($_GET['id'])) {
    $id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

    // Buscar os dados do profissional
    $query = "SELECT * FROM UtilizadoresProfissionais WHERE Id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $profissional = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profissional) {
        echo "Profissional não encontrado!";
        exit();
    }
} else {
    echo "ID do profissional não fornecido!";
    exit();
}

// Atualizar os dados do profissional
if (isset($_POST['update_prof'])) {
    try {
        $conn->beginTransaction();

        $nome = $_POST['nome'];
        $nomeUtilizador = $_POST['nomeUtilizador'];
        $tipo = "profissional"; // Fixo para profissionais
        $dataNascimento = $_POST['dataNascimento'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $morada = $_POST['morada'];
        $palavraPasse = $_POST['palavraPasse'];
        $genero = $_POST['genero'];
        $estabelecimentoEnsino = $_POST['estabelecimentoEnsino'];
        $areaEspecializada = $_POST['areaEspecializada'];
        $situacaoAtual = $_POST['situacaoAtual'];
        $motivoCandidatura = $_POST['motivoCandidatura'];

        // Arrays para armazenar campos e parâmetros da query
        $updateFields = [];
        $params = [];

        // Campos básicos
        $basicFields = [
            'Nome' => $nome,
            'NomeUtilizador' => $nomeUtilizador,
            'Tipo' => $tipo,
            'DataNascimento' => $dataNascimento,
            'Email' => $email,
            'Telefone' => $telefone,
            'Morada' => $morada,
            'PalavraPasse' => $palavraPasse,
            'Genero' => $genero,
            'EstabelecimentoEnsino' => $estabelecimentoEnsino,
            'AreaEspecializada' => $areaEspecializada,
            'SituacaoAtual' => $situacaoAtual,
            'MotivoCandidatura' => $motivoCandidatura
        ];

        foreach ($basicFields as $field => $value) {
            $updateFields[] = "$field = :$field";
            $params[":$field"] = $value;
        }

        // Processar arquivos se foram enviados
        $fileFields = ['Foto', 'Diploma', 'CV'];
        foreach ($fileFields as $field) {
            $fieldLower = strtolower($field);
            if (isset($_FILES[$fieldLower]) && $_FILES[$fieldLower]['error'] === 0) {
                $content = file_get_contents($_FILES[$fieldLower]['tmp_name']);
                $updateFields[] = "$field = :$field";
                $params[":$field"] = $content;
            }
        }

        $params[':Id'] = $id;

        $query = "UPDATE UtilizadoresProfissionais SET " . 
                 implode(', ', $updateFields) . 
                 " WHERE Id = :Id";

        $stmt = $conn->prepare($query);
        
        if ($stmt->execute($params)) {
            $conn->commit();
            $_SESSION['success_message'] = "Profissional atualizado com sucesso!";
            header("Location: ProfissionaisPanel.php");
            exit();
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = "Erro ao atualizar o profissional: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../../logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleEdit.css">
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
    <title>Editar Profissional | Ombro Amigo</title>
</head>
<body>
    <!-- Botão Voltar -->
    <a href="ProfissionaisPanel.php" class="back-link-fixed">
        <i class="fas fa-arrow-left"></i>
        <span>Voltar ao Painel</span>
    </a>

    <div class="form-container">
        <h2><i class="fas fa-user-edit"></i> Editar Profissional</h2>
        <form method="POST" action="ProfissionaisEdit.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($profissional['Id']); ?>">
            
            <div class="photo-upload">
                <?php if ($profissional['Foto']): ?>
                    <img src="data:image/png;base64,<?= base64_encode($profissional['Foto']); ?>" alt="Foto do Profissional" id="preview">
                <?php else: ?>
                    <img src="../defaultPhoto.png" alt="Foto Padrão" id="preview">
                <?php endif; ?>
                <label for="foto" class="upload-btn">
                    <i class="fas fa-camera"></i>
                    <input type="file" id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                </label>
            </div>

            <div class="form-grid">
                <!-- Primeira coluna -->
                <div class="form-group">
                    <label for="nome"><i class="fas fa-user"></i> Nome</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($profissional['Nome']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="nomeUtilizador"><i class="fas fa-at"></i> Nome de Utilizador</label>
                    <input type="text" name="nomeUtilizador" value="<?= htmlspecialchars($profissional['NomeUtilizador']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($profissional['Email']); ?>" required>
                </div>

                <!-- Segunda coluna -->
                <div class="form-group">
                    <label for="telefone"><i class="fas fa-phone"></i> Telefone</label>
                    <input type="text" name="telefone" value="<?= htmlspecialchars($profissional['Telefone']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="dataNascimento"><i class="fas fa-calendar"></i> Data de Nascimento</label>
                    <input type="date" name="dataNascimento" value="<?= htmlspecialchars($profissional['DataNascimento']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="morada"><i class="fas fa-home"></i> Morada</label>
                    <input type="text" name="morada" value="<?= htmlspecialchars($profissional['Morada']); ?>" required>
                </div>

                <!-- Terceira coluna -->
                <div class="form-group">
                    <label for="palavraPasse"><i class="fas fa-lock"></i> Password</label>
                    <div class="password-input">
                        <input type="password" name="palavraPasse" id="palavraPasse" value="<?= htmlspecialchars($profissional['PalavraPasse']); ?>" required>
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="genero"><i class="fas fa-venus-mars"></i> Gênero</label>
                    <select name="genero" required>
                        <option value="Masculino" <?= $profissional['Genero'] == 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                        <option value="Feminino" <?= $profissional['Genero'] == 'Feminino' ? 'selected' : ''; ?>>Feminino</option>
                        <option value="Outro" <?= $profissional['Genero'] == 'Outro' ? 'selected' : ''; ?>>Outro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="estabelecimentoEnsino"><i class="fas fa-university"></i> Estabelecimento</label>
                    <input type="text" name="estabelecimentoEnsino" value="<?= htmlspecialchars($profissional['EstabelecimentoEnsino']); ?>" required>
                </div>

                <!-- Quarta linha -->
                <div class="form-group">
                    <label for="areaEspecializada"><i class="fas fa-briefcase-medical"></i> Área Especializada</label>
                    <input type="text" name="areaEspecializada" value="<?= htmlspecialchars($profissional['AreaEspecializada']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="situacaoAtual"><i class="fas fa-info-circle"></i> Situação Atual</label>
                    <input type="text" name="situacaoAtual" value="<?= htmlspecialchars($profissional['SituacaoAtual']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="motivoCandidatura"><i class="fas fa-clipboard-list"></i> Motivo</label>
                    <textarea name="motivoCandidatura" rows="2" required><?= htmlspecialchars($profissional['MotivoCandidatura']); ?></textarea>
                </div>

                <!-- Documentos -->
                <div class="form-group document-upload">
                    <label for="cv"><i class="fas fa-file-alt"></i> Currículo (CV)</label>
                    <input type="file" name="cv" accept=".pdf,.doc,.docx">
                    <?php if ($profissional['CV']): ?>
                        <small class="file-info">CV atual já carregado</small>
                    <?php endif; ?>
                </div>

                <div class="form-group document-upload">
                    <label for="diploma"><i class="fas fa-graduation-cap"></i> Diploma</label>
                    <input type="file" name="diploma" accept=".pdf,.doc,.docx">
                    <?php if ($profissional['Diploma']): ?>
                        <small class="file-info">Diploma atual já carregado</small>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" name="update_prof" class="btn-update">
                <i class="fas fa-save"></i> Atualizar Profissional
            </button>
        </form>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.querySelector('.toggle-password').addEventListener('click', function() {
            const input = document.getElementById('palavraPasse');
            if (input.type === 'password') {
                input.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>
