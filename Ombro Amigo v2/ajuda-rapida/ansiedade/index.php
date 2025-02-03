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
    <title>Ansiedade Ajuda Rápida | Ombro Amigo</title>
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
                        <li><a class="active" href="../panico/index.php">Pânico</a></li>
                        <li><a href="../ansiedade/index.php">Ansiedade</a></li>
                        <li><a href="../crise-desespero/index.php">Crise de Desespero</a></li>
                    </ul>
                  </div>            
            </li>
            <li><a href="../../quem somos/index.php"> O Projeto</a></li>   
            <li><a href="../../candidaturas/candidaturas.php">Candidatar-me</a></li>
            <li><a href="../../planos/index.php">Planos</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="user-dropdown">
                    <a href="<?= ($_SESSION['user_type'] == 'UtilizadorProfissional') ? '../../dashboardProfissionais/dashboardInicial/index.php' : '../../dashboard/dashinicial/index.php'; ?>">
                        <img src="<?= !empty($_SESSION['user_photo']) ? 'data:image/jpeg;base64,' . $_SESSION['user_photo'] : '../../dashboard/conta/uploads/defaultPhoto.png'; ?>" class="user-photo" alt="Foto do usuário">
                    </a>
                    <div class="user-dropdown-menu">
                        <ul>
                            <li><a href="<?= ($_SESSION['user_type'] == 'UtilizadorProfissional') ? '../../dashboardProfissionais/dashboardInicial/index.php' : '../../dashboard/dashinicial/index.php'; ?>">Dashboard</a></li>
                            <li><a href="../../dashboard/logout.php">Sair</a></li>
                        </ul>
                    </div>
                </li>
            <?php else: ?>
                <li><a href="../../registar/index.html">Registar-me</a></li>
                <li><a href="../../login/index.html">Entrar</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="head">
        <h1>
        Ataque de Ansiedade
        </h1>
        <p>
        Identificar e lidar com um ataque de ansiedade pode ser uma
            experiência horrível e desafiadora, aqui irás encontrar algumas
            recomendações de como identificar e lidar com a ansiedade!

        </p>
    </div>

    <div class="content-section">
        <div class="img">
            <img src="img.png" alt="">
        </div>
        <div class="texto">
            <div class="backtxt">
                <h2>Sintomas Comuns</h2>
                <li>Sensação de pânico ou perda de controle.</li>
                <li>Choro intenso ou incontrolável. </li>
                <li>Dificuldade em organizar pensamentos.</li>
                <li>Desejo urgente de fugir da realidade ou enconder-se.</li>
                <li>Aperto ou dor no peito, acompanhado de dificuldade para respirar. </li>
                <li>Mãos trêmulas, suadas, sensação de fraqueza e vista turva. </li>
                <li> Tontura ou sensação de desorientação.</li> 
            </div>
        </div>
    </div>

    <div class="content-section">
        <div class="img2">
            <img src="img2.jpg" alt="Imagem da Técnica 424">
        </div>
        <div class="texto">
            <div class="background2">
                <h2>Técnica 478</h2>
                <p id="frase">Esta técnica pode demorar algum tempo a tornar-se eficaz.</p>
                <h2>Passo-a-Passo</h2>
                <p>Inspira profundamente enquanto contas até <b>quatro.</b></p>
                <p>Segura a respiração por <b>sete segundos.</b></p>
                <p>Solta enquanto contas até <b>oito.</b></p>
                <h2>Vídeo de Ajuda</h2>
                <iframe width="560" height="315" src="https://www.youtube.com/embed/85pQAwyNgYI?si=8ueL09lMy_-wgLgP" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
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