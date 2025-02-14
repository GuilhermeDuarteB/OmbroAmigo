<?php
session_start();
include '../../connection.php';
include_once '../verificar_subscricao.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Verificar subscrição antes de permitir acesso
if (!verificarSubscricaoAtiva($conn, $user_id)) {
    header("Location: ../dashinicial/index.php?error=subscription_required");
    exit();
}

// Buscar a foto do perfil do utilizador logado
$query = "SELECT Foto FROM Utilizadores WHERE Id = :Id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":Id", $user_id);
$stmt->execute();
$utilizador = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se há uma imagem e converte para base64
if (!empty($utilizador['Foto'])) {
    $fotoBase64 = base64_encode($utilizador['Foto']);
    $fotoSrc = "data:image/jpeg;base64,{$fotoBase64}";
} else {
    $fotoSrc = "../conta/uploads/defaultPhoto.png";
}

// Buscar todos os profissionais
$query = "SELECT * FROM UtilizadoresProfissionais";
$stmt = $conn->prepare($query);
$stmt->execute();
$profissionais = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../../logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../layout.css">
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
    <title>Marcar Consultas | Ombro Amigo</title>
</head>

<body>
<nav>
        <ul>
            <li><a href="../../initial page/index.php" class="logo">
                    <img src="../../../logo-site-sem-texto.png" alt="">
                    <span class="nav-item">Ombro Amigo</span>
                </a></li>

            <li><a href="../dashinicial/index.php">
                    <i class="fas fa-solid fa-house"></i>
                    <span class="nav-item">Início</span>
                </a></li>

            <li><a href="../diario/index.php">
                    <i class="fas fa-solid fa-book"></i>
                    <span class="nav-item">O Meu Diário</span>
                </a></li>

            <li><a href="../calendario/index.php">
                    <i class="fas fa-solid fa-calendar-days"></i>
                    <span class="nav-item">Calendário</span>
                </a></li>

            <li><a href="../marcarConsultas/index.php">
                    <i class="fas fa-solid fa-clipboard"></i>
                    <span class="nav-item">Marcar Consultas</span>
                </a></li>

            <li>
                <a href="../consultasMarcadas/index.php">
                    <i class="fas fa-solid fa-clipboard-check"></i>
                    <span class="nav-item">Consultas Marcadas</span>
                </a>
            </li>

            <div class="fotoAcc">
                <li><a href="../conta/conta.php">
                        <img class="fas" src="<?= $fotoSrc; ?>" id="fotoAcc">
                        <span class="nav-item2">A Minha Conta</span>
                    </a></li>
            </div>

            <li><a href="../settings/index.php">
                    <i class="fas fa-cog"></i>
                    <span class="nav-item">Definições</span>
                </a>
            </li>

            <li class="noti"><a href="../notificacoes/index.php">
                    <i class="fas fa-solid fa-bell"></i>
                    <span class="nav-item">Notificações</span>
                </a></li>

            <li class="logout"><a href="../logout.php">
                    <i class="fas fa-solid fa-right-from-bracket"></i>
                    <span class="nav-item">Sair do Painel</span>
                </a></li>

        </ul>
    </nav>
    <div class="back">
        <div class="head">
            <h1>Marcar Consultas</h1>
            <h3>Aqui poderás marcar as tuas futuras consultas de maneira rápida!</h3>
        </div>

        <div class="consultasSelect">
            <div class="categoria-data">
                <div class="categoria">
                    <label for="selectconsulta">Seleciona a categoria de consulta.</label><br>
                    <select id="selectconsulta" onchange="loadProfissionais()">
                        <option value="" data-default disabled selected></option>
                        <option value="Nutricao">Nutrição</option>
                        <option value="Psiquiatria">Psiquiatria</option>
                        <option value="Psicologia">Psicologia</option>
                        <option value="Medicina Geral">Medicina Geral</option>
                    </select>
                </div>

                <div class="dataInput">
                    <label for="data">Seleciona a data da consulta.</label><br>
                    <input type="date" id="data" name="data" onchange="loadProfissionais()" />
                </div>
            </div>
        </div>

        <div id="profissionaisList" class="profissionais-list"></div>

        <div id="detalhesProfissional" class="detalhes-profissional">
            <div class="detalhes-content">
                <span id="fecharDetalhes" class="fechar-detalhes">&times;</span>
                <img id="fotoDetalhes" src="" alt="Foto do Profissional">
                <p id="nomeDetalhes"></p>
                <div id="horariosDetalhes" class="horarios-list"></div>
                <button id="marcarConsultaBtn" class="btn-marcar" disabled>Marcar Consulta</button>
            </div>
        </div>
    </div>
</body>

</html>
<script src="script.js"></script>
<script>
    function loadProfissionais() {
        const area = document.getElementById("selectconsulta").value;
        const data = document.getElementById("data").value;
        const profissionaisList = document.getElementById("profissionaisList");

        // Verificar se área e data foram selecionadas
        if (!area || !data) {
            profissionaisList.innerHTML = "<p class='mensagem-erro'>Por favor, selecione uma categoria e uma data.</p>";
            return;
        }

        profissionaisList.style.display = 'grid';

        // Enviar dados para o servidor
        const formData = new FormData();
        formData.append('area', area);
        formData.append('data', data);

        fetch('fetch_profissionais.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProfissionais(data.profissionais);
            } else {
                throw new Error(data.error || 'Erro desconhecido');
            }
        })
        .catch(error => {
            console.error('Erro ao carregar profissionais:', error);
            displayError('Erro ao carregar profissionais: ' + error.message);
        });
    }
</script>