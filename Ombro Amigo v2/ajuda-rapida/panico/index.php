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
    <title>Pânico Ajuda Rápida | Ombro Amigo</title>
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
            Ataque de Pânico
        </h1>
        <p>
            Identificar e lidar com um ataque de pânico pode ser uma
            experiência horrível e desafiadora, <br> aqui irás encontrar algumas
            recomendações de como identificar e lidar com o pânico!
        </p>
    </div>

    <div class="content-section">
        <div class="img">
            <img src="img1.png" alt="">
        </div>
        <div class="texto">
            <div class="backtxt">
                <h2>Sintomas Comuns</h2>
                <li>Tremores</li>
                <li>Suor excessivo</li>
                <li>Náuseas ou vômitos</li>
                <li>Tontura ou vertigens</li>
                <li>Dificuldades para respirar</li>
                <li>Medo de morrer ou perder o controlo</li>
                <li>Sensação de desrealização ou despersonalização</li>
                <li>Aumento repentina da frequência cardíaca</li>
            </div>
        </div>
    </div>

    <div class="content-section">
        <div class="img2">
            <img src="img2.jpg" alt="Imagem da Técnica 424">
        </div>
        <div class="texto">
            <div class="background2">
                <h2>Respiração Diafragmática</h2>
                <p id="frase">Esta técnica tem de ser repetida cerca de 10 vezes para
                    se tornar eficaz.</p>
                <h2>Passo-a-Passo</h2>
                <p>Coloque-se de forma confortável com a postura ereta (sentado ou deitado).</p>
                <p>Coloque a mão na barriga próxima ao umbigo.</p>
                <p>Fecha os olhos e concentra-te na respiração.</p>
                <p>Repara que quando inspira a barriga desce e quando expiras a barriga sobe</p>
                <p>Tenta realizar o movimento contrário: a barriga subir ao inspirar e descer ao expirar</p>
                <p>Agora, inspira lentamente pelo nariz, a contar até <b>quatro</b>. Sente o ar encher os pulmões e a barriga subir</p>
                <p>Segura a respiração durante <b>dois segundos</b></p>
                <p>Expira lentamente pelo boca, a contar até <b>seis</b>. Enquanto esvazias o pulmão percebe a barriga a baixar.</p>
                <p>Repete a técnica <b>10 vezes</b> e lembra-te que terá maior efeito a <b>praticar diariamente</b></p>
                <h2>Vídeo de Ajuda</h2>
                <iframe width="560" height="315" src="https://www.youtube.com/embed/2cufeBFlkq8?si=gfLsSNkrPAjd_d_7" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
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