<?php
session_start();
include("../connection.php");
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidaturas | Ombro Amigo</title>
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../layout.css">
    
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
</head>

<body>
    <!-- Navbar -->
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="checkbtn">
            <i class="fa-solid fa-bars"></i>
        </label>
        <label class="logo">
            <a href="../initial page/index.php" class="linklogo">
                <img src="../../logo-site-novo.png" alt="Logo Ombro Amigo">
            </a>
        </label>
        <ul>
            <li><a href="../initial page/index.php">Início</a></li>
            <li class="dropdown">
                <a href="#">Ajuda Rápida <i class="fa-solid fa-angle-down"></i></a>
                <div class="dropdown-menu">
                    <ul>
                        <li><a href="../ajuda-rapida/panico/index.php">Pânico</a></li>
                        <li><a href="../ajuda-rapida/ansiedade/index.php">Ansiedade</a></li>
                        <li><a href="../ajuda-rapida/crise-desespero/index.php">Crise de Desespero</a></li>
                    </ul>
                </div>
            </li>
            <li><a href="../quem somos/index.php">O Projeto</a></li>
            <li><a class="active" href="candidaturas.php">Candidatar-me</a></li>
            <li><a href="../planos/index.php">Planos</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php 
                // Verifica se é um utilizador profissional
                if ($_SESSION['user_type'] == 'UtilizadorProfissional') {
                    // Busca o tipo do profissional na base de dados
                    $user_id = $_SESSION['user_id'];
                    $query = "SELECT Tipo FROM UtilizadoresProfissionais WHERE ID = :user_id";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($user && $user['Tipo'] == 'profissional'): ?>
                        <li class="user-dropdown">
                            <a href="#">
                                <?php if (isset($_SESSION['user_photo']) && $_SESSION['user_photo']): ?>
                                    <img src="data:image/jpeg;base64,<?= $_SESSION['user_photo'] ?>" class="user-photo">
                                <?php else: ?>
                                    <img src="../dashboard/conta/uploads/defaultPhoto.png" class="user-photo" alt="Foto de perfil padrão">
                                <?php endif; ?>
                            </a>
                            <div class="user-dropdown-menu">
                                <ul>
                                    <li><a href="../dashboardProfissionais/dashboardInicial/index.php">Dashboard</a></li>
                                    <li><a href="../dashboard/logout.php">Sair</a></li>
                                </ul>
                            </div>
                        </li>
                    <?php else: ?>
                        <li><a href="../registar/index.html">Registar-me</a></li>
                        <li><a href="../login/index.html">Entrar</a></li>
                    <?php endif; ?>
                <?php } else { ?>
                    <li class="user-dropdown">
                        <a href="#">
                            <?php if (isset($_SESSION['user_photo']) && $_SESSION['user_photo']): ?>
                                <img src="data:image/jpeg;base64,<?= $_SESSION['user_photo'] ?>" class="user-photo">
                            <?php else: ?>
                                <img src="../dashboard/conta/uploads/defaultPhoto.png" class="user-photo" alt="Foto de perfil padrão">
                            <?php endif; ?>
                        </a>
                        <div class="user-dropdown-menu">
                            <ul>
                                <li><a href="../dashboard/dashinicial/index.php">Dashboard</a></li>
                                <li><a href="../dashboard/logout.php">Sair</a></li>
                            </ul>
                        </div>
                    </li>
                <?php } ?>
            <?php else: ?>
                <li><a href="../registar/index.html">Registar-me</a></li>
                <li><a href="../login/index.html">Entrar</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="head">
        <h1>Participa da Equipa Ombro Amigo!</h1>
        <p>És profissional de saúde? Então candidata-te para ajudar o próximo!</p>
    </div>

    <div class="vantagens">
        <img src="img/img1.png" alt="Imagem Descritiva">
        <div class="vantagens-texto">
            <h2>Porque me Candidatar?</h2>
          <p>Ao te candidatares no Ombro Amigo, recebes inúmeros benefícios, tanto profissionais como pessoais. A nossa plataforma permite-lhe contribuir para a batalha
            para ajudar a saúde mental e bem-estar da comunidade e assim apoiar alguem que precisa, e ainda
            receber algo com isso!
          </p>
        </div>
    </div>

    <div class="vantagens2">
        <img src="img/img3.png" alt="">
        <div class="vantagens2-texto">
            <h2>Vantagens ao me Candidatar</h2>
            <p> Ao te juntares ao Ombro Amigo, terás a oportunidade de expandir 
                a tua rede de contactos profissionais, <br> assim podes trocar experiências com colegas da área,
                 e ainda adquirir novas competências,<br>assim também enriquecendo o teu desenvolvimento pessoal e profissional.</p>
                <p><a href="../registar/index.html#formProfissional">Candidatar-me para ser um Profissional do Ombro Amigo</a> </p>
        </div>
    </div>

    <footer>
        <div class="row">
            <div class="col">
                <img src="../../logo-site.png" class="logofooter">
                <p>
                    O projeto "Ombro Amigo" oferece cuidados de saúde online 
                    para jovens, abrangendo áreas como psicologia, psiquiatria, 
                    nutrição e medicina geral. Criou-se o "Um Ombro Amigo" 
                    para ajudar jovens com problemas graves de saúde 
                    mental, visando prevenir casos extremos.
                </p>
            </div>
            <div class="col">
                <h3>Links</h3>
                <div class="linkfooter">
                    <ul>
                        <li><a href="../initial page/index.html">Início</a></li>
                        <li><a href="../quem somos/index.html">Projeto Ombro Amigo</a></li>
                        <li><a href="../planos/index.html">Planos</a></li>
                        <li><a href="../registar/index.html">Registar-me</a></li>
                        <li><a href="../login/index.html">Entrar</a></li>
                    </ul>
                </div>
            </div>

            <div class="colsocial">
                <h3>Redes Sociais</h3>
                <div class="linkfooter">
                    <ul>
                        <li><i id="insta" class="fa-brands fa-instagram" style="color: #ffffff;"></i></a></li>
                    </ul>
                </div>
            </div>

            <div class="col">
                <h3>Recebe todas as novidades!</h3>
                <form>
                    <input type="email" placeholder="Insere o teu e-mail aqui!" required>
                    <button type="submit"><i class="fa-solid fa-arrow-right"></i></button>
                </form>
            </div>
        </div>
    </footer>
    <script src="script.js"></script>
</body>
</html>
