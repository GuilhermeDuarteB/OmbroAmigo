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
        <div class="month-picker-container">
          <span class="month-change" id="pre-month">
            <i class="fas fa-chevron-left"></i>
          </span>
          <span id="month-picker"><?= $month_names[date('n')-1] ?></span>
          <span class="month-change" id="next-month">
            <i class="fas fa-chevron-right"></i>
          </span>
        </div>
        <div class="year-picker">
          <span class="year-change" id="pre-year">
            <i class="fas fa-chevron-left"></i>
          </span>
          <span id="year"><?= date('Y') ?></span>
          <span class="year-change" id="next-year">
            <i class="fas fa-chevron-right"></i>
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
      <button id="definirHSemanal">Definir Horário</button>
    </div>
  </div>
  </div>
  <div class="overlay" id="overlay"></div>
  <div class="definirHorarioContainer" id="definirHorarioContainer">
    <button id="closeMenu"><i class="fa-solid fa-xmark"></i></button>
    <h4>
        Definir Horário
        <span class="info-icon">
            <i class="fas fa-info-circle"></i>
            <span class="info-tooltip">
                Configure seus horários de trabalho semanais. Os dias selecionados serão seus dias fixos de atendimento durante o período definido.
            </span>
        </span>
    </h4>
    <form id="formHorario">
        <div class="periodo-vigencia">
            <label>
                Período de Vigência
                <span class="info-icon">
                    <i class="fas fa-info-circle"></i>
                    <span class="info-tooltip">
                        Define o período em que este horário estará ativo. Após a data fim, o horário será automaticamente desativado.
                    </span>
                </span>
            </label>
            <div class="datas-container">
                <div>
                    <label for="dataInicio">Data Início</label>
                    <input type="date" id="dataInicio" name="dataInicio" required>
                </div>
                <div>
                    <label for="dataFim">Data Fim</label>
                    <input type="date" id="dataFim" name="dataFim" required>
                </div>
            </div>
        </div>

        <div class="periodos-container">
            <div class="dias-semana">
                <label>
                    Dias de Atendimento
                    <span class="info-icon">
                        <i class="fas fa-info-circle"></i>
                        <span class="info-tooltip">
                            Selecione os dias da semana em que você estará disponível para atendimento. Estes serão seus dias fixos de trabalho.
                        </span>
                    </span>
                </label>
                <div class="dias-checks">
                    <label><input type="checkbox" value="2"> Segunda-feira</label>
                    <label><input type="checkbox" value="3"> Terça-feira</label>
                    <label><input type="checkbox" value="4"> Quarta-feira</label>
                    <label><input type="checkbox" value="5"> Quinta-feira</label>
                    <label><input type="checkbox" value="6"> Sexta-feira</label>
                    <label><input type="checkbox" value="7"> Sábado</label>
                    <label><input type="checkbox" value="1"> Domingo</label>
                </div>
            </div>

            <div id="periodos">
                <div class="periodo">
                    <label>
                        Horário de Atendimento
                        <span class="info-icon">
                            <i class="fas fa-info-circle"></i>
                            <span class="info-tooltip">
                                Defina os horários de início e fim do seu atendimento. Você pode adicionar múltiplos períodos por dia.
                            </span>
                        </label>
                    <div class="horarios">
                        <input type="time" class="hora-inicio" required>
                        <span>até</span>
                        <input type="time" class="hora-fim" required>
                    </div>
                </div>
            </div>

            <button type="button" id="addPeriodo">
                <i class="fas fa-plus"></i> Adicionar Novo Período
            </button>
        </div>

        <button type="submit" id="btnHorario">Salvar Horários</button>
    </form>
</div>
</body>
<script src="script.js"></script>

</html>