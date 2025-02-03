<?php
session_start();
include "../../connection.php"; 

// Verifica se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

// Verificar se o ID do usuário foi passado
if (isset($_POST['id']) || isset($_GET['id'])) {
    $id = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

    // Buscar os dados do usuário com base no ID
    $query = "SELECT * FROM Utilizadores WHERE Id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $utilizador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utilizador) {
        echo "Utilizador não encontrado!";
        exit();
    }
} else {
    echo "ID do utilizador não fornecido!";
    exit();
}

// Atualizar os dados do usuário
if (isset($_POST['update_user'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $telefone = $_POST['telefone'];
    $palavrapasse = $_POST['palavrapasse'];
    $tipo = $_POST['tipo'];
    $dataNascimento = $_POST['DataNascimento'];
    $morada = $_POST['morada'];
    $descricao = $_POST['descricao'];

    // Processar foto se foi enviada
    $fotoQuery = "";
    $params = [
        ":Nome" => $nome,
        ":Email" => $email,
        ":NomeUtilizador" => $username,
        ":Telefone" => $telefone,
        ":PalavraPasse" => $palavrapasse,
        ":Tipo" => $tipo,
        ":DataNascimento" => $dataNascimento,
        ":Morada" => $morada,
        ":Descricao" => $descricao,
        ":Id" => $id
    ];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
        $fotoQuery = ", Foto = :Foto";
        $params[":Foto"] = $foto;
    }

    $query = "UPDATE Utilizadores SET 
              Nome = :Nome, 
              Email = :Email, 
              NomeUtilizador = :NomeUtilizador, 
              Telefone = :Telefone, 
              PalavraPasse = :PalavraPasse, 
              Tipo = :Tipo,
              DataNascimento = :DataNascimento,
              Morada = :Morada,
              Descricao = :Descricao" . 
              $fotoQuery . 
              " WHERE Id = :Id";

    $stmt = $conn->prepare($query);

    if ($stmt->execute($params)) {
        header("Location: UserPanel.php");
        exit();
    } else {
        echo "Erro ao atualizar os dados do utilizador!";
    }
}

?>


<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar User | Ombro Amigo</title>
    <link rel="stylesheet" href="styleEdit.css">
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- Botão Voltar -->
    <a href="UserPanel.php" class="back-link-fixed">
        <i class="fas fa-arrow-left"></i>
        <span>Voltar ao Painel</span>
    </a>

    <div class="form-container">
        <h2><i class="fas fa-user-edit"></i> Editar Utilizador</h2>
        <form method="POST" action="UserEdit.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($utilizador['Id']); ?>">
            
            <div class="photo-upload">
                <?php if ($utilizador['Foto']): ?>
                    <img src="data:image/png;base64,<?= base64_encode($utilizador['Foto']); ?>" alt="Imagem de Perfil" id="preview">
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
                    <input type="text" name="nome" value="<?= htmlspecialchars($utilizador['Nome']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="username"><i class="fas fa-at"></i> Username</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($utilizador['NomeUtilizador']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($utilizador['Email']); ?>" required>
                </div>

                <!-- Segunda coluna -->
                <div class="form-group">
                    <label for="telefone"><i class="fas fa-phone"></i> Telefone</label>
                    <input type="text" name="telefone" value="<?= htmlspecialchars($utilizador['Telefone']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="DataNascimento"><i class="fas fa-calendar"></i> Nascimento</label>
                    <input type="date" name="DataNascimento" value="<?= htmlspecialchars($utilizador['DataNascimento']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="morada"><i class="fas fa-home"></i> Morada</label>
                    <input type="text" name="morada" value="<?= htmlspecialchars($utilizador['Morada']); ?>">
                </div>

                <!-- Terceira coluna -->
                <div class="form-group">
                    <label for="palavrapasse"><i class="fas fa-lock"></i> Password</label>
                    <div class="password-input">
                        <input type="password" name="palavrapasse" id="palavrapasse" value="<?= htmlspecialchars($utilizador['PalavraPasse']); ?>" required>
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tipo"><i class="fas fa-user-tag"></i> Tipo</label>
                    <select name="tipo" required>
                        <option value="admin" <?= $utilizador['Tipo'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="user" <?= $utilizador['Tipo'] == 'user' ? 'selected' : ''; ?>>User</option>
                        <option value="profissional" <?= $utilizador['Tipo'] == 'profissional' ? 'selected' : ''; ?>>Profissional</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="descricao"><i class="fas fa-comment"></i> Descrição</label>
                    <textarea name="descricao" rows="2"><?= htmlspecialchars($utilizador['Descricao']); ?></textarea>
                </div>
            </div>

            <button type="submit" name="update_user" class="btn-update">
                <i class="fas fa-save"></i> Atualizar Utilizador
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