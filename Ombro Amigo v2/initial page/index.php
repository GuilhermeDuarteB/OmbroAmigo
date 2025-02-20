<?php
session_start();
include '../connection.php';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="x-icon" href="../../logo-icon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial | Ombro Amigo</title>
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="../layout.css">
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
</head>
<body>
    
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="checkbtn">
            <i class="fa-solid fa-bars"></i>
        </label>
        <label class="logo">
            <a href="../initial page/index.php" class="linklogo"><img src="../../logo-site-novo.png"></a>
        </label>
     
        <ul>
            <li><a class="active" href="../initial page/index.php">Início</a></li>
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
              <li><a href="../quem somos/index.php"> O Projeto</a></li>   
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

<div class="container"> 
<section class="infoIn">
    <div class="sobre-texto">
        <h2>
            Precisas de um Ombro Amigo?
        </h2>
        <div class="t1">
            <p>
                Aqui no Ombro Amigo tem toda a ajuda necessária para cuidar <br>
                da sua saúde física e <b>principalmente mental.</b>
            </p>
        </div>
    </div>
    <div class="imagem">
        <img src="imgs/imagem.png">
    </div>
</section>
</div>


<div class="container2">
    <section class="infoMeio">
    
        <div class="imagem2">
            <img src="imgs/imagem2.png">
            </div>
    
            <div class="sobre-texto2">
                <h2>
                    Como funciona?
                </h2>
                <div class="t2">
                   <p>Através de uma subscrição de um dos <a href="../planos/index.php">nossos planos</a>
                    terá acesso imediato a todos os nossos <b>  serviços de Psicologia, Nutricionismo,
                    Psiquiatria e Medicina Geral completamente online! </b> <br>
                    Conheça mais sobre o <a href="../quem somos/index.php">Projeto Ombro Amigo</a>
                   </p>
                </div>
            </div>
    </section>
    </div>
 
    <div class="container3"> 
        <section class="infoFim">
            <div class="sobre-texto3">
                <h2>
                    Quais os objetivos do projeto?
                </h2>
                <div class="t3">
                        <p>
                            Auxiliar jovens com patologias mentais. <br>
                            Conscientalizar a população sobre a saúde mental.<br>
                            Aumentar a visibilidade de profissionais da área da saúde mental.
                        </p>
                </div>
            </div>
            <div class="imagem3">
                <img src="imgs/imagem3.png">
            </div>
        </section>
        </div>

        <div class="container4">
            <section class="infoFim2">
                <div class="imagem4">
                    <img src="imgs/imagem4.png" alt="">
                </div>
                <div class="sobre-texto4">
                <h2>
                    Plano "Heróis Sem Capa"
                </h2>
                <div class="t4">
                    <p>
                        O Ombro Amigo decidiu incluir no projeto <b> um plano específico para profissionais com empregos de risco, que podem 
                        desenvolver traumas nas suas profissões </b> e aqui poderão ser ajudados, decidimos então criar o <a href="../planos/index.php#HSC"> Plano dos Heróis Sem Capa</a>.
                    </p>
                </div>
            </div>

            </section>
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
                        <li><a href="../initial page/index.php">Início</a></li>
                        <li><a href="../quem somos/index.php">Projeto Ombro Amigo</a></li>
                        <li><a href="../planos/index.php">Planos</a></li>
                        <li><a href="../registar/index.php">Registar-me</a></li>
                        <li><a href="../login/index.php">Entrar</a></li>
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
</body>
</html>
<script src="script.js"></script>
<script src="../layout.js"></script>