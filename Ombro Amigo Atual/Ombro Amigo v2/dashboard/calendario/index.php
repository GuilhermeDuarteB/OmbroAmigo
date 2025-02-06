<?php
session_start();
include '../../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT Foto FROM Utilizadores WHERE Id = :Id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":Id", $user_id);
$stmt->execute();
$utilizador = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se há uma imagem e converte para base64
    if (!empty($utilizador['Foto'])) {
        $fotoBase64 = base64_encode($utilizador['Foto']);
        $fotoSrc = "data:image/jpeg;base64,{$fotoBase64}"; // Exibir a imagem em base64
    } else {
        $fotoSrc = "../conta/uploads/defaultPhoto.png";  
    }

    $queryConsultas = "
    SELECT TOP 5
        c.DataConsulta,
        c.HoraConsulta,
        p.Nome as NomeProfissional,
        p.Foto as FotoProfissional
    FROM Consultas c
    JOIN UtilizadoresProfissionais p ON c.IdProfissional = p.Id
    WHERE c.IdUtilizador = :IdUtilizador 
    AND c.Status = 'Aceite'
    AND c.DataConsulta >= CAST(GETDATE() AS DATE)
    ORDER BY c.DataConsulta ASC, c.HoraConsulta ASC";

$stmtConsultas = $conn->prepare($queryConsultas);
$stmtConsultas->bindParam(':IdUtilizador', $user_id);
$stmtConsultas->execute();
$proximasConsultas = $stmtConsultas->fetchAll(PDO::FETCH_ASSOC);

// Buscar todas as datas de consultas do usuário
$queryDatasConsultas = "
    SELECT DISTINCT 
        DataConsulta
    FROM Consultas 
    WHERE IdUtilizador = :IdUtilizador 
    AND Status = 'Aceite'
    AND DataConsulta >= CAST(GETDATE() AS DATE)";

$stmtDatasConsultas = $conn->prepare($queryDatasConsultas);
$stmtDatasConsultas->bindParam(':IdUtilizador', $user_id);
$stmtDatasConsultas->execute();
$datasConsultas = $stmtDatasConsultas->fetchAll(PDO::FETCH_COLUMN);

// Converter as datas para o formato necessário para o JavaScript
$datasConsultasJS = array_map(function($data) {
    return date('Y-m-d', strtotime($data));
}, $datasConsultas);
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
    <title>Calendário | Ombro Amigo</title>
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
            <div class="calendar-footer">
            </div>
            <div class="date-time-formate">
              <div class="day-text-formate">HOJE</div>
              <div class="date-time-value">
                <div class="time-formate">02:51:20</div>
                <div class="date-formate">23 - Julho - 2022</div>
              </div>
            </div>
            <div class="month-list"></div>

          </div>
          <div class="prox-evento">
            <h3>Próximas Consultas</h3>
            <?php if (count($proximasConsultas) > 0): ?>
              <?php foreach ($proximasConsultas as $consulta): ?>
                <div class="evento-item">
                    <img src="<?= !empty($consulta['FotoProfissional']) ? 'data:image/jpeg;base64,' . base64_encode($consulta['FotoProfissional']) : '../conta/uploads/defaultPhoto.png'; ?>" 
                         alt="Foto do Profissional">
                    <div class="evento-detalhes">
                        <span class="nome-profissional"><?= htmlspecialchars($consulta['NomeProfissional']); ?></span>
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
      </div>
    </div>
    </div>
</body>
<script>
    // Passar as datas das consultas para o JavaScript
    const datasConsultas = <?php echo json_encode($datasConsultasJS); ?>;
</script>
<script src="script.js"></script>
</html>
