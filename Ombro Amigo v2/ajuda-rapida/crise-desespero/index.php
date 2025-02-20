<?php
session_start();
include("../../connection.php");
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../../logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crise de Desespero Ajuda Rápida | Ombro Amigo</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../../layout.css">
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
</head>

<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="checkbtn">
            <i class="fa-solid fa-bars"></i>
        </label>
        <label class="logo">
            <a href="../../initial page/index.php" class="linklogo"><img src="../../../logo-site-novo.png"></a>
        </label>

        <ul>
            <li><a href="../../initial page/index.php">Início</a></li>
            <li class="dropdown">
                <a href="#">Ajuda Rápida <i class="fa-solid fa-angle-down"></i></a>
                <div class="dropdown-menu">
                    <ul>
                        <li><a href="../panico/index.php">Pânico</a></li>
                        <li><a href="../ansiedade/index.php">Ansiedade</a></li>
                        <li><a href="index.php">Crise de Desespero</a></li>
                    </ul>
                </div>
            </li>
            <li><a href="../../quem somos/index.php"> O Projeto</a></li>
            <li><a href="../../candidaturas/candidaturas.php">Candidatar-me</a></li>
            <li><a href="../../planos/index.php">Planos</a></li>
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
                                    <img src="../../dashboard/conta/uploads/defaultPhoto.png" class="user-photo" alt="Foto de perfil padrão">
                                <?php endif; ?>
                            </a>
                            <div class="user-dropdown-menu">
                                <ul>
                                    <li><a href="../../dashboardProfissionais/dashboardInicial/index.php">Dashboard</a></li>
                                    <li><a href="../../dashboard/logout.php">Sair</a></li>
                                </ul>
                            </div>
                        </li>
                    <?php else: ?>
                        <li><a href="../../registar/index.html">Registar-me</a></li>
                        <li><a href="../../login/index.html">Entrar</a></li>
                    <?php endif; ?>
                <?php } else { ?>
                    <li class="user-dropdown">
                        <a href="#">
                            <?php if (isset($_SESSION['user_photo']) && $_SESSION['user_photo']): ?>
                                <img src="data:image/jpeg;base64,<?= $_SESSION['user_photo'] ?>" class="user-photo">
                            <?php else: ?>
                                <img src="../../dashboard/conta/uploads/defaultPhoto.png" class="user-photo" alt="Foto de perfil padrão">
                            <?php endif; ?>
                        </a>
                        <div class="user-dropdown-menu">
                            <ul>
                                <li><a href="../../dashboard/dashinicial/index.php">Dashboard</a></li>
                                <li><a href="../../dashboard/logout.php">Sair</a></li>
                            </ul>
                        </div>
                    </li>
                <?php } ?>
            <?php else: ?>
                <li><a href="../../registar/index.html">Registar-me</a></li>
                <li><a href="../../login/index.html">Entrar</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="head">
        <h1>Crise de Desespero</h1>
        <p>
            Identificar e lidar com uma crise de desespero pode ser uma experiência avassaladora, mas é possível
            aprender formas de reconhecer e controlar esses momentos. Aqui estão algumas orientações úteis para
            enfrentar uma crise de desespero!
        </p>
    </div>

    <div class="content-section">
        <div class="img">
            <img src="img1.png" alt="imagem 1">
        </div>
        <div class="texto">
            <div class="backtxt">
                <h2>Sintomas Comuns</h2>
                <ul>
                    <li>Medo intenso ou pavor.</li>
                    <li>Tontura ou sensação de desmaio.</li>
                    <li>Sensação de aperto ou dor no peito.</li>
                    <li>Sensação de formigueiros nas mãos ou nos pés.</li>
                    <li>Tremores ou sensação de frio ou calor extremo.</li>
                    <li>Respiração rápida ou dificuldade em respirar (hiperventilação).</li>
                    <li>Palpitações cardíacas aceleradas ou batimentos cardíacos irregulares.</li>
                    <li>Sentimento de irrealidade ou desapego da realidade (despersonalização).</li>
                </ul>
            </div>
        </div>
    </div>


    <div class="content-section">
        <div class="img2">
            <img src="img2.png" alt="Imagem 2">
        </div>
        <div class="texto">
            <div class="background2">
                <h2>Técnica de Aterramento</h2>
                <p id="frase">Esta técnica ajuda a redirecionar a atenção para o presente, assim ajuda a aliviar o stress.</p>
                <h2>Passo-a-Passo</h2>
                <p>Identifique <b>cinco coisas</b> que possas ver ao seu redor.</p>
                <p>Reconheça <b>quatro coisas</b> que possas tocar.</p>
                <p>Identifique <b>três coisas</b> que possas sentir.</p>
                <p>Nomeie <b>duas coisas</b> que possas sentir.</p>
                <p>Concentre-se na respiração.</p>
                <h2>Vídeo de Ajuda</h2>
                <iframe 
                    width="560" 
                    height="315" 
                    src="https://www.youtube.com/embed/NAEWCjzULwI" 
                    title="YouTube video player" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    allowfullscreen>
                </iframe>   

            </div>
        </div>
    </div>
    <footer>
        <div class="row">
            <div class="col">
                <img src="../../../logo-site.png" class="logofooter">
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
                        <li><a href="../../initial page/index.html">Início</a></li>
                        <li><a href="../../quem somos/index.html">Projeto Ombro Amigo</a></li>
                        <li><a href="../../planos/index.html">Planos</a></li>
                        <li><a href="../../loginReg/index.html">Entra e Regista-te</a></li>
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
                <h3>Newsletter!</h3>
                <form>
                    <input type="email" placeholder="Insere o teu e-mail aqui!" required>
                    <button type="submit"><i class="fa-solid fa-arrow-right"></i></button>
                </form>
            </div>
        </div>
    </footer>
</body>

</html>
<script src="script.js"></script>