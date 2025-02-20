<?php
session_start();
include("../connection.php");
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projeto "Ombro Amigo" | Ombro Amigo</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../layout.css">
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>

    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="checkbtn">
            <i class="fa-solid fa-bars"></i>
        </label>
        <label class="logo">
            <a href="../initial page/index.php" class="linklogo"><img src="../../logo-site-novo.png"></a>
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
              <li><a class="active" href="../quem somos/index.php"> O Projeto</a></li>   
            <li><a href="../candidaturas/candidaturas.php">Candidatar-me</a></li>
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
</head>
<body>
    <div class="head">
        <h1>
            O Que é o "Ombro Amigo"?
        </h1>
    </div>
    <div class="container">
        <section class="sobre">
        <div class="imagem">
            <img src="imgs/imagem.png">
        </div>
        <div class="sobre-texto">
           
            <div class="p1">
                <p>
                    O projeto "Ombro Amigo" é um projeto criado a partir de uma Prova de Aptidão Profissional (PAP), 
                    realizado na Escola Profissional de Salvaterra de Magos (EPSM), o realizado no triênio de 2022/2025. O projeto de PAP consiste na criação de um WebSite 
                   que disponibliza vários cuidados de saúde online para os jovens.
                </p>
            </div>
        
            <p>
                <div class="p2">
                O "Ombro Amigo" disponibliza cuidados nas vastas áreas da saúde como 
                a Psicologia, Psquiatria, Nutricionismo e Medicina geral, com o aumento 
                de casos de depressão,ansiedade entre outros maior parte deste casos são de jovens. Com estes problemas
                 cada vez mais graves levando a morte dos jovens, com isto criamos o <br> "Ombro Amigo" que tem por objetivo ajudar o próximo!
                </div>
            </p>
        </div>
        </section>
    </div>
    <div class="membros">
        <h1>Os Membros do "Ombro Amigo"</h1>
        <div class="pcima">
            <div class="membro">
                <img src="imgs/gui.jpg">
                <h2>Guilherme Branco</h2>
                <p>Programador</p>
                <i id="git1" class="fa-brands fa-github" style="color: #0082e6; font-size: 45px; cursor: pointer;"></i></a>
            
            </div>
            <div class="membro">
             <img id="gab" src="imgs/gabriel.jpg">
                <h2>Gabriel Lopes</h2>
                <p>Gestor</p>
            </div>
            <div class="membro">
                <img src="imgs/madalena.jpg">
                <h2>Madalena Pereira</h2>
                <p>Gestora</p>
            </div>
            <div class="membro">
                <img src="imgs/moller.jpg">
                <h2>Tomás Lobito</h2>
                <p>Gestor</p>
            </div>
        </div>
    </div>
    
</body>
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
                    <li><a href="../initial page/index.php">Início</a></li>
                    <li><a href="../quem somos/index.php">Projeto Ombro Amigo</a></li>
                    <li><a href="../planos/index.php">Planos</a></li>
                    <li><a href="../registar/index.php">Registar-me</a></li>
                    <li><a href="../login/index.php">Entrar</a></li>                </ul>
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
                <i class="fa-solid fa-envelope"></i>
                <input type="email" placeholder="Insere o teu e-mail aqui!" required>
                <button type="submit"><i class="fa-solid fa-arrow-right"></i></button>
            </form>
        </div>
    </div>
</footer>
<script src="script.js"></script>
</html>