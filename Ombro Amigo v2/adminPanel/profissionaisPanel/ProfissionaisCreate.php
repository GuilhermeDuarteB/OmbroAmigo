<?php
session_start();
include "../../connection.php"; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

if (isset($_POST['create_prof'])) {
    try {
        $conn->beginTransaction();

        // Dados básicos
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

        // Processamento dos arquivos
        $foto = null;
        if(isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
            $foto = file_get_contents($_FILES['foto']['tmp_name']);
        }

        $diploma = null;
        if(isset($_FILES['diploma']) && $_FILES['diploma']['error'] === 0) {
            $diploma = file_get_contents($_FILES['diploma']['tmp_name']);
        }

        $cv = null;
        if(isset($_FILES['cv']) && $_FILES['cv']['error'] === 0) {
            $cv = file_get_contents($_FILES['cv']['tmp_name']);
        }

        $query = "INSERT INTO UtilizadoresProfissionais (Nome, NomeUtilizador, Tipo, DataNascimento, 
                  Email, Telefone, Morada, PalavraPasse, Genero, EstabelecimentoEnsino, 
                  AreaEspecializada, SituacaoAtual, MotivoCandidatura, Diploma, CV, Foto) 
                  VALUES (:nome, :nomeUtilizador, :tipo, :dataNascimento, :email, :telefone, 
                  :morada, :palavraPasse, :genero, :estabelecimentoEnsino, :areaEspecializada, 
                  :situacaoAtual, :motivoCandidatura, :diploma, :cv, :foto)";

        $stmt = $conn->prepare($query);
        
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':nomeUtilizador', $nomeUtilizador);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':dataNascimento', $dataNascimento);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':morada', $morada);
        $stmt->bindParam(':palavraPasse', $palavraPasse);
        $stmt->bindParam(':genero', $genero);
        $stmt->bindParam(':estabelecimentoEnsino', $estabelecimentoEnsino);
        $stmt->bindParam(':areaEspecializada', $areaEspecializada);
        $stmt->bindParam(':situacaoAtual', $situacaoAtual);
        $stmt->bindParam(':motivoCandidatura', $motivoCandidatura);
        $stmt->bindParam(':diploma', $diploma, PDO::PARAM_LOB);
        $stmt->bindParam(':cv', $cv, PDO::PARAM_LOB);
        $stmt->bindParam(':foto', $foto, PDO::PARAM_LOB);

        if ($stmt->execute()) {
            $conn->commit();
            $_SESSION['success_message'] = "Profissional criado com sucesso!";
            header("Location: ProfissionaisPanel.php");
            exit();
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = "Erro ao criar profissional: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../../logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleCreate.css">
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
    <title>Criar Profissional | Ombro Amigo</title>
</head>
<body>
    <!-- Botão Voltar -->
    <a href="ProfissionaisPanel.php" class="back-link-fixed">
        <i class="fas fa-arrow-left"></i>
        <span>Voltar ao Painel</span>
    </a>

    <div class="form-container">
        <h2><i class="fas fa-user-md"></i> Criar Profissional</h2>
        
        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="photo-upload">
                <img id="preview" src="../defaultPhoto.png" alt="Preview">
                <label for="foto" class="upload-btn">
                    <i class="fas fa-camera"></i>
                    <input type="file" id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                </label>
            </div>

            <div class="form-grid">
                <!-- Dados Pessoais -->
                <div class="form-group">
                    <label for="nome"><i class="fas fa-user"></i> Nome</label>
                    <input type="text" name="nome" required>
                </div>

                <div class="form-group">
                    <label for="nomeUtilizador"><i class="fas fa-at"></i> Nome de Utilizador</label>
                    <input type="text" name="nomeUtilizador" required>
                </div>

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="telefone"><i class="fas fa-phone"></i> Telefone</label>
                    <input type="tel" name="telefone" required>
                </div>

                <div class="form-group">
                    <label for="dataNascimento"><i class="fas fa-calendar"></i> Data de Nascimento</label>
                    <input type="date" name="dataNascimento" required>
                </div>

                <div class="form-group">
                    <label for="genero"><i class="fas fa-venus-mars"></i> Gênero</label>
                    <select name="genero" required>
                        <option value="Masculino">Masculino</option>
                        <option value="Feminino">Feminino</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="morada"><i class="fas fa-home"></i> Morada</label>
                    <input type="text" name="morada" required>
                </div>

                <div class="form-group">
                    <label for="palavraPasse"><i class="fas fa-lock"></i> Palavra-passe</label>
                    <div class="password-input">
                        <input type="password" name="palavraPasse" id="palavraPasse" required>
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                </div>

                <!-- Dados Profissionais -->
                <div class="form-group">
                    <label for="estabelecimentoEnsino"><i class="fas fa-university"></i> Estabelecimento de Ensino</label>
                    <input type="text" name="estabelecimentoEnsino" required>
                </div>

                <div class="form-group">
                    <label for="areaEspecializada"><i class="fas fa-briefcase-medical"></i> Área Especializada</label>
                    <input type="text" name="areaEspecializada" required>
                </div>

                <div class="form-group">
                    <label for="situacaoAtual"><i class="fas fa-info-circle"></i> Situação Atual</label>
                    <input type="text" name="situacaoAtual" required>
                </div>

                <div class="form-group full-width">
                    <label for="motivoCandidatura"><i class="fas fa-clipboard-list"></i> Motivo da Candidatura</label>
                    <textarea name="motivoCandidatura" rows="3" required></textarea>
                </div>

                <!-- Documentos -->
                <div class="form-group document-upload">
                    <label for="cv"><i class="fas fa-file-alt"></i> Currículo (CV)</label>
                    <input type="file" name="cv" accept=".pdf,.doc,.docx" required>
                </div>

                <div class="form-group document-upload">
                    <label for="diploma"><i class="fas fa-graduation-cap"></i> Diploma</label>
                    <input type="file" name="diploma" accept=".pdf,.doc,.docx" required>
                </div>
            </div>

            <button type="submit" name="create_prof" class="btn-criarUser">
                <i class="fas fa-save"></i> Criar Profissional
            </button>
        </form>
    </div>

    <script>
        // Preview da imagem
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Toggle da senha
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
