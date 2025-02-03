<?php
session_start();
include "../../connection.php"; 

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

// Verificar se o formulário foi enviado
if (isset($_POST['create_user'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $username = $_POST['username']; 
    $telefone = $_POST['telefone'];
    $palavrapasse = $_POST['palavrapasse'];
    $tipo = $_POST['tipo'];
    $DataNascimento = $_POST["DataNascimento"];
    $morada = $_POST["morada"];
    $descricao = $_POST["descricao"];
    
    // Processar foto
    $foto = null;
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
    }

    $query = "INSERT INTO Utilizadores (Nome, Email, NomeUtilizador, Telefone, PalavraPasse, 
              Tipo, DataNascimento, Morada, Descricao, Foto) 
              VALUES (:Nome, :Email, :NomeUtilizador, :Telefone, :PalavraPasse, 
              :Tipo, :DataNascimento, :Morada, :Descricao, :Foto)";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":Nome", $nome);
    $stmt->bindParam(":Email", $email);
    $stmt->bindParam(":NomeUtilizador", $username);
    $stmt->bindParam(":Telefone", $telefone);
    $stmt->bindParam(":PalavraPasse", $palavrapasse);
    $stmt->bindParam(":DataNascimento", $DataNascimento);
    $stmt->bindParam(":Tipo", $tipo);
    $stmt->bindParam(":Morada", $morada);
    $stmt->bindParam(":Descricao", $descricao);
    $stmt->bindParam(":Foto", $foto, PDO::PARAM_LOB);

    if ($stmt->execute()) {
        echo "Utilizador criado com sucesso!";
        header("Location: UserPanel.php");
        exit();
    } else {
        echo "Erro ao criar o utilizador!";
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
    <title>Criar User | Ombro Amigo</title>
</head>
<body>
    <!-- Botão Voltar -->
    <a href="UserPanel.php" class="back-link-fixed">
        <i class="fas fa-arrow-left"></i>
        <span>Voltar ao Painel</span>
    </a>

    <div class="form-container">
        <h2><i class="fas fa-user-plus"></i> Criar Utilizador</h2>
        <form method="POST" action="UserCreate.php" enctype="multipart/form-data">
            <div class="photo-upload">
                <img id="preview" src="../defaultPhoto.png" alt="Preview">
                <label for="foto" class="upload-btn">
                    <i class="fas fa-camera"></i>
                    <input type="file" id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                </label>
            </div>

            <div class="form-grid">
                <!-- Primeira coluna -->
                <div class="form-group">
                    <label for="nome"><i class="fas fa-user"></i> Nome</label>
                    <input type="text" name="nome" required>
                </div>

                <div class="form-group">
                    <label for="username"><i class="fas fa-at"></i> Username</label>
                    <input type="text" name="username" required>
                </div>

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" required>
                </div>

                <!-- Segunda coluna -->
                <div class="form-group">
                    <label for="telefone"><i class="fas fa-phone"></i> Telefone</label>
                    <input type="text" name="telefone" required>
                </div>

                <div class="form-group">
                    <label for="DataNascimento"><i class="fas fa-calendar"></i> Nascimento</label>
                    <input type="date" name="DataNascimento" required>
                </div>

                <div class="form-group">
                    <label for="morada"><i class="fas fa-home"></i> Morada</label>
                    <input type="text" name="morada">
                </div>

                <!-- Terceira coluna -->
                <div class="form-group">
                    <label for="palavrapasse"><i class="fas fa-lock"></i> Password</label>
                    <div class="password-input">
                        <input type="password" name="palavrapasse" id="palavrapasse" required>
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tipo"><i class="fas fa-user-tag"></i> Tipo</label>
                    <select name="tipo" required>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                        <option value="profissional">Profissional</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="descricao"><i class="fas fa-comment"></i> Descrição</label>
                    <textarea name="descricao" rows="2"></textarea>
                </div>
            </div>

            <button type="submit" name="create_user" class="btn-criarUser">
                <i class="fas fa-save"></i> Criar Utilizador
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
            const input = document.getElementById('palavrapasse');
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
