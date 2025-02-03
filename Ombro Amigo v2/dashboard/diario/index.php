<?php
session_start();
include '../../connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Paginação
$entriesPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $entriesPerPage;

// Buscar total de entradas para paginação
$totalQuery = $conn->prepare("SELECT COUNT(*) FROM Diarios WHERE IDUtilizador = :idUtilizador");
$totalQuery->bindParam(":idUtilizador", $user_id);
$totalQuery->execute();
$totalEntries = $totalQuery->fetchColumn();
$totalPages = ceil($totalEntries / $entriesPerPage);

// Buscar entradas com paginação
try {
    $stmt = $conn->prepare("
        SELECT IDDiario, Mensagem, DataEntrada, Titulo, Imagens,
               DATEDIFF(day, DataEntrada, GETDATE()) as DiasAtras
        FROM Diarios 
        WHERE IDUtilizador = :idUtilizador 
        ORDER BY DataEntrada DESC
        OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY
    ");
    $stmt->bindParam(":idUtilizador", $user_id);
    $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
    $stmt->bindParam(":limit", $entriesPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $entradas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erro ao carregar entradas: " . $e->getMessage();
}

// Processar nova entrada
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $mensagem = trim($_POST["mensagem"] ?? "");
        $titulo = trim($_POST["Titulo"] ?? "");
        $image = null;

        if (empty($titulo) || empty($mensagem)) {
            throw new Exception("Por favor, preencha todos os campos obrigatórios.");
        }

        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['file']['tmp_name'];
            $imageFileType = mime_content_type($imageTmpPath);
            $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp'];

            if (!in_array($imageFileType, $allowedImageTypes)) {
                throw new Exception("Tipo de arquivo não permitido. Use apenas imagens JPEG, PNG, GIF, BMP ou WEBP.");
            }

            $image = file_get_contents($imageTmpPath);
        }

        $Query = "
            INSERT INTO Diarios (IDUtilizador, Mensagem, Imagens, DataEntrada, Titulo) 
            VALUES (:idUtilizador, :mensagem, CONVERT(VARBINARY(MAX), :imagens), GETDATE(), :titulo)
        ";

        $Stmt = $conn->prepare($Query);
        $Stmt->bindParam(":idUtilizador", $user_id);
        $Stmt->bindParam(":mensagem", $mensagem);
        $Stmt->bindParam(":imagens", $image, PDO::PARAM_LOB);
        $Stmt->bindParam(":titulo", $titulo);

        if ($Stmt->execute()) {
            header("Location: index.php?success=1");
            exit();
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Buscar informações do usuário
$query = "SELECT Foto, Nome FROM Utilizadores WHERE Id = :Id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":Id", $user_id);
$stmt->execute();
$utilizador = $stmt->fetch(PDO::FETCH_ASSOC);

$fotoSrc = !empty($utilizador['Foto']) 
    ? "data:image/jpeg;base64," . base64_encode($utilizador['Foto'])
    : "../conta/uploads/defaultPhoto.png";

include_once '../verificar_subscricao.php';
$temSubscricaoAtiva = verificarSubscricaoAtiva($conn, $user_id);
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
    <title>Diário | Ombro Amigo</title>
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

        <?php if ($temSubscricaoAtiva): ?>
            <li><a href="../calendario/index.php">
                    <i class="fas fa-solid fa-calendar-days"></i>
                    <span class="nav-item">Calendário</span>
                </a></li>

            <li><a href="../marcarConsultas/index.php">
                    <i class="fas fa-solid fa-clipboard"></i>
                    <span class="nav-item">Marcar Consultas</span>
                </a></li>

            <li><a href="../consultasMarcadas/index.php">
                    <i class="fas fa-solid fa-clipboard-check"></i>
                    <span class="nav-item">Consultas Marcadas</span>
                </a></li>

            <li class="noti"><a href="../notificacoes/index.php">
                    <i class="fas fa-solid fa-bell"></i>
                    <span class="nav-item">Notificações</span>
                </a></li>
        <?php endif; ?>

        <div class="fotoAcc">
            <li><a href="../conta/conta.php">
                    <img class="fas" src="<?= $fotoSrc; ?>" id="fotoAcc">
                    <span class="nav-item2">A Minha Conta</span>
                </a></li>
        </div>

        <li><a href="../settings/index.php">
                <i class="fas fa-cog"></i>
                <span class="nav-item">Definições</span>
            </a></li>

        <li class="logout"><a href="../logout.php">
                <i class="fas fa-solid fa-right-from-bracket"></i>
                <span class="nav-item">Sair do Painel</span>
            </a></li>
    </ul>
</nav>
<div class="diario">
    <h1>O Meu Diário</h1>

    <button id="addText"><i class="fa-solid fa-plus" style="color: #3398b6; font-size: 20px;"></i></button>
    <form action="entradasDeleteAll.php" method="POST">
        <button id="ApagarTodasEntradas">Apagar Todas as Entradas</button>
    </form>
    <div class="info" id="info" style="display:none">
        <div class="boxInfo">
            <i class="fa-solid fa-xmark"></i>
            <h1>Nova Entrada</h1>
            <form action="index.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="Titulo" id="titulo" placeholder="Título" maxlength="25">
                <div class="contadorCaracteres">
                    <span id="caractAtualTitulo">0</span>/<span id="caractFinalTitulo">25</span>
                </div>
                
                <textarea name="mensagem" id="texto" placeholder="Escreva aqui..." maxlength="500"></textarea>
                <div class="contadorCaracteres">
                    <span id="caractAtual">0</span>/<span id="caractFinal">500</span>
                </div>

                <input type="file" name="file" id="uploadfiles" accept="image/*">
                
                <div class="edit-actions">
                    <button type="button" class="btn-cancelar">Cancelar</button>
                    <button type="submit" class="btn-salvar">Adicionar Entrada</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Aqui começa a exibição das entradas -->
    <div class="entradas">
        <ul>
            <?php foreach ($entradas as $entrada): ?>
                <li class="diario-entradas">
                    <?php echo date('d/m/Y', strtotime($entrada['DataEntrada'])); ?>
                    <strong id="tituloEntrada"><?php echo htmlspecialchars($entrada['Titulo']); ?></strong>
                    <div class="buttons">
                        <button type="button" class="view-button" id="view-button"
                            data-titulo="<?php echo htmlspecialchars($entrada['Titulo']); ?>"
                            data-mensagem="<?php echo htmlspecialchars($entrada['Mensagem']); ?>"
                            data-imagem="<?php echo !empty($entrada['Imagens']) ? 'data:image/jpeg;base64,' . base64_encode($entrada['Imagens']) : ''; ?>">
                            <i class="fa-solid fa-eye"></i>
                        </button>

                        <button type="button" class="edit-button" id="edit-button"
                            data-id="<?php echo htmlspecialchars($entrada['IDDiario']); ?>"
                            data-titulo="<?php echo htmlspecialchars($entrada['Titulo']); ?>"
                            data-mensagem="<?php echo htmlspecialchars($entrada['Mensagem']); ?>"
                            data-imagem="<?php echo !empty($entrada['Imagens']) ? 'data:image/jpeg;base64,' . base64_encode($entrada['Imagens']) : ''; ?>">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>

                        <form action="entradasDelete.php" method="POST">
                            <input type="hidden" name="idDiario" value="<?php echo $entrada['IDDiario']; ?>">
                            <button type="submit"><i id="delete" class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="diarioContent" id="diarioContent" style="display:none">
        <div class="diariocontentfunc">
            <i class="fa-solid fa-xmark"></i>
            <h1 id="tituloEntradaView"></h1>
            <textarea id="textoEntradaView" readonly></textarea>
            <img id="imagemEntradaView" src="" alt="Imagem do Diário" style="display: none;">
        </div>
    </div>

    <form action="editEntrada.php" method="POST">
        <div class="editContent" id="editContent" style="display:none">
            <div class="editcontentfunc">
                <i class="fa-solid fa-xmark"></i>
                <h1>Editar Entrada</h1>
                <input type="text" name="tituloEdit" id="tituloEntradaEdit" class="edit-titulo" maxlength="25">
                <div class="contadorCaracteres">
                    <span id="caractAtualTituloEdit">0</span>/<span id="caractFinalTituloEdit">25</span>
                </div>
                <textarea id="textoEntradaEdit" name="textoEntradaEdit" maxlength="500"></textarea>
                <div class="contadorCaracteres">
                    <span id="caractAtualEdit">0</span>/<span id="caractFinalEdit">500</span>
                </div>
                <img id="imagemEntrada" src="" alt="Imagem do Diário" style="display: none;">
                <div class="edit-actions">
                    <button type="button" class="btn-cancelar">Cancelar</button>
                    <button type="submit" class="btn-salvar">Salvar Alterações</button>
                </div>
            </div>
            <input type="hidden" id="idDiario" name="idDiario" value="">
        </div>
    </form>
</div>
</div>
</div>

<script src="script.js"></script>
</body>

</html>