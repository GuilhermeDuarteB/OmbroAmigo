<?php
session_start();
include '../connection.php';
 
if (!isset($_SESSION['user_id']) || !isset($_GET['consulta'])) {
    header("Location: ../login/index.html");
    exit();
}
 
$consulta_id = $_GET['consulta'];
$user_id = $_SESSION['user_id'];
if (!isset($_GET['tipo']) || $_GET['tipo'] === 'null') {
    // Determinar tipo baseado no ID do usuário logado
    $tipoUsuario = ($user_id == $consulta['IdUtilizador']) ? 'user' : 'profissional';
} else {
    $tipoUsuario = $_GET['tipo'];
}
 
// Armazenar o userType na sessão, se necessário
$_SESSION['user_type'] = $tipoUsuario;
 
// Verificar se o usuário é paciente ou profissional
$query = "SELECT
    c.IDConsulta,
    c.IdUtilizador,
    c.IdProfissional,
    u.Nome as NomeUsuario,
    u.Foto as FotoUsuario,
    p.Nome as NomeProfissional,
    p.Foto as FotoProfissional,
    CASE
        WHEN c.IdUtilizador = ? THEN 'user'
        WHEN c.IdProfissional = ? THEN 'profissional'
        ELSE NULL
    END as TipoUsuario
    FROM Consultas c
    INNER JOIN Utilizadores u ON c.IdUtilizador = u.Id
    INNER JOIN UtilizadoresProfissionais p ON c.IdProfissional = p.Id
    WHERE c.IDConsulta = ?";
 
$stmt = $conn->prepare($query);
$stmt->execute([$user_id, $user_id, $consulta_id]);
$consulta = $stmt->fetch(PDO::FETCH_ASSOC);
 
if (!$consulta || !$consulta['TipoUsuario']) {
    header("Location: ../login/index.html");
    exit();
}
 
$tipoUsuario = $consulta['TipoUsuario'];
 
// Processar fotos
function processarFoto($fotoBlob) {
    if (!empty($fotoBlob)) {
        return 'data:image/jpeg;base64,' . base64_encode($fotoBlob);
    }
    return 'defaultPhoto.png';
}
 
$fotoUsuario = processarFoto($consulta['FotoUsuario']);
$fotoProfissional = processarFoto($consulta['FotoProfissional']);
 
?>
 
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Online</title>
    <link rel="stylesheet" href="videocall.css">
    <link rel="shortcut icon" type="x-icon" href="../../logo-icon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="video-container">
        <div class="video-grid">
            <?php if ($tipoUsuario === 'user'): ?>
                <!-- Vista do Paciente -->
                <div class="video-box main">
                    <video id="remoteVideo" autoplay playsinline></video>
                    <img src="<?= $fotoProfissional ?>" class="profile-photo" alt="Foto do profissional">
                    <span class="video-label"><?= htmlspecialchars($consulta['NomeProfissional']) ?></span>
                </div>
                <div class="video-box secondary">
                    <video id="localVideo" autoplay muted playsinline></video>
                    <img src="<?= $fotoUsuario ?>" class="profile-photo" alt="Sua foto">
                    <span class="video-label">Você</span>
                </div>
            <?php else: ?>
                <div class="video-box main">
                    <video id="remoteVideo" autoplay playsinline></video>
                    <img src="<?= $fotoUsuario ?>" class="profile-photo" alt="Foto do paciente">
                    <span class="video-label"><?= htmlspecialchars($consulta['NomeUsuario']) ?></span>
                </div>
                <div class="video-box secondary">
                    <video id="localVideo" autoplay muted playsinline></video>
                    <img src="<?= $fotoProfissional ?>" class="profile-photo" alt="Sua foto">
                    <span class="video-label">Você</span>
                </div>
            <?php endif; ?>
        </div>
 
        <div class="chat-container">
            <div class="chat-messages" id="chatMessages">
                <!-- As mensagens serão inseridas aqui via JavaScript -->
            </div>
            <div class="chat-input">
                <input type="text" id="messageInput" placeholder="Digite sua mensagem...">
                <button onclick="enviarMensagem()">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
 
        <div class="controls">
            <button id="toggleVideo" class="control-btn">
                <i class="fas fa-video"></i>
            </button>
            <button id="toggleAudio" class="control-btn">
                <i class="fas fa-microphone"></i>
            </button>
            <button id="shareScreen" class="control-btn">
                <i class="fas fa-desktop"></i>
            </button>
            <button id="toggleChat" class="control-btn">
                <i class="fas fa-chevron-right"></i>
            </button>
            <button id="endCall" class="control-btn end-call">
                <i class="fas fa-phone-slash"></i>
            </button>
        </div>
    </div>
 
    <script>
        const consultaId = <?php echo json_encode($consulta_id); ?>;
        const tipoUsuario = <?php echo json_encode($tipoUsuario); ?>;
        const userId = <?php echo json_encode($user_id); ?>;
        const consulta = <?php echo json_encode([
            'IdUtilizador' => $consulta['IdUtilizador'],
            'IdProfissional' => $consulta['IdProfissional']
        ]); ?>;
    </script>
    <script src="videocall.js"></script>
</body>
</html>
 