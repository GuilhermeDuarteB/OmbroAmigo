<?php
session_start();
include '../../connection.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../../login/index.html");
  exit();
}

$user_id = $_SESSION['user_id'];

// Busca a foto do profissional
$query = "SELECT Foto FROM UtilizadoresProfissionais WHERE Id = :Id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":Id", $user_id);
$stmt->execute();
$profissional = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se há uma imagem e converte para base64
if (!empty($profissional['Foto'])) {
  $fotoBase64 = base64_encode($profissional['Foto']);
  $fotoSrc = "data:image/jpeg;base64,{$fotoBase64}";
} else {
  $fotoSrc = "../conta/uploads/defaultPhoto.png";
}

// Busca as próximas consultas
$queryConsultas = "
    SELECT TOP 5
        c.DataConsulta,
        c.HoraConsulta,
        u.Nome as NomeUtilizador,
        u.Foto as FotoUtilizador
    FROM Consultas c
    JOIN Utilizadores u ON c.IdUtilizador = u.Id
    WHERE c.IdProfissional = :IdProfissional 
    AND c.Status = 'Aceite'
    AND c.DataConsulta >= CAST(GETDATE() AS DATE)
    ORDER BY c.DataConsulta ASC, c.HoraConsulta ASC";

$stmtConsultas = $conn->prepare($queryConsultas);
$stmtConsultas->bindParam(':IdProfissional', $user_id);
$stmtConsultas->execute();
$proximasConsultas = $stmtConsultas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <link rel="shortcut icon" type="x-icon" href="../../../logo-icon.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="../../dashboard/layout.css">
  <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
  <title>Calendário | Ombro Amigo</title>
</head>

<body>

  <nav>
    <ul>
      <li><a href="../../initial page/index.php" class="logo">
          <img src="../../../logo-site-sem-texto.png" alt="">
          <span class="nav-item">Ombro Amigo</span>
        </a></li>
      <li><a href="../dashboardInicial/index.php">
          <i class="fas fa-solid fa-house"></i>
          <span class="nav-item">Início</span>
        </a>
      </li>
      <li>
        <a href="../consultasMarcadas/index.php">
          <i class="fas fa-solid fa-clipboard-check"></i>
          <span class="nav-item">Consultas Marcadas</span>
        </a>
      </li>
      <li><a href="index.php">
          <i class="fas fa-solid fa-calendar-days"></i>
          <span class="nav-item">Calendário</span>
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
      <li><a href="../notificacoes/index.php" class="noti">
          <i class="fas fa-solid fa-bell"></i>
          <span class="nav-item">Notificações</span>
        </a>
      </li>
      <li class="logout"><a href="../logout.php">
          <i class="fas fa-solid fa-right-from-bracket"></i>
          <span class="nav-item">Sair do Painel</span>
        </a></li>
    </ul>
  </nav>
  <div class="contianer2">
    <div class="calendar">
      <div class="calendar-header">
        <span class="month-picker" id="month-picker"> May </span>
        <div class="year-picker" id="year-picker">
          <span class="year-change" id="pre-year">
            <pre><</pre>
          </span>
          <span id="year">2020 </span>
          <span class="year-change" id="next-year">
            <pre>></pre>
          </span>
        </div>
      </div>
      <div class="calendar-body">
        <div class="calendar-week-days">
          <div>Dom</div>
          <div>Seg</div>
          <div>Ter</div>
          <div>Qua</div>
          <div>Qui</div>
          <div>Sex</div>
          <div>Sab</div>
        </div>
        <div class="calendar-days">
        </div>
      </div>
      <div class="date-time-formate">
        <div class="day-text-formate">HOJE</div>
        <div class="date-time-value">
          <div class="time-formate"></div>
          <div class="date-formate"></div>
        </div>
      </div>
      <div class="month-list"></div>

    </div>
    <div class="prox-evento">
      <h3>Próximas Consultas</h3>
      <div class="consultas-container">
        <?php if (isset($proximasConsultas) && count($proximasConsultas) > 0): ?>
            <?php foreach ($proximasConsultas as $consulta): ?>
                <div class="evento-item">
                    <img src="<?= !empty($consulta['FotoUtilizador']) ? 'data:image/jpeg;base64,' . base64_encode($consulta['FotoUtilizador']) : '../conta/uploads/defaultPhoto.png'; ?>" 
                         alt="Foto do Utilizador">
                    <div class="evento-detalhes">
                        <span class="nome-utilizador"><?= htmlspecialchars($consulta['NomeUtilizador']); ?></span>
                        <div class="evento-info">
                            <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($consulta['DataConsulta'])); ?></span>
                            <span><i class="fas fa-clock"></i> <?= date('H:i', strtotime($consulta['HoraConsulta'])); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="sem-eventos">Não há consultas agendadas.</p>
        <?php endif; ?>
      </div>
      <button id="definirHSemanal">Definir Horário Semanal</button>
    </div>
  </div>
  </div>
  <div class="definirHorarioContainer" id="definirHorarioContainer">
    <button id="closeMenu"><i class="fa-solid fa-xmark"></i></button>
    <h4>Definir Agenda Semanal</h4>
    <form id="formHorario" action="horario.php" method="POST">
  <div class="inputContainer">
    <div class="dataInput">
      <label for="dataInicio">Seleciona a data de início.</label><br>
      <input type="date" id="dataInicio" name="dataInicio" /><br>
      <label for="dataFim">Seleciona a data de fim</label><br>
      <input type="date" name="dataFim" id="dataFim">
    </div>
    <div class="horarioInput">
      <label for="horaInicio">Seleciona uma hora de início.</label><br>
      <input type="time" name="horaInicio" id="horaInicio"><br>
      <label for="horaFim">Seleciona hora de fim.</label><br>
      <input type="time" id="horaFim" name="horaFim">
    </div>
  </div>
  <button id="btnHorario" type="submit">Definir Agenda</button>
</form>


  </div>
</body>
<script src="script.js"></script>

</html>