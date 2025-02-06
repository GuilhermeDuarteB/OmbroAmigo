<?php
session_start();
include("../connection.php");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="../layout.css">
    <script src="https://kit.fontawesome.com/430134e04b.js" crossorigin="anonymous"></script>
    <link rel="shortcut icon" type="x-icon" href="../../logo-icon.png">
    <title>Planos | Ombro Amigo</title>
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
              <li><a href="../quem somos/index.php"> O Projeto</a></li>   
            <li><a href="../candidaturas/candidaturas.php">Candidatar-me</a></li>
            <li><a class="active" href="../planos/index.php">Planos</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
            <li class="user-dropdown">
                <a href="<?= ($_SESSION['user_type'] == 'UtilizadorProfissional') ? '../dashboardProfissionais/dashboardInicial/index.php' : '../dashboard/dashinicial/index.php'; ?>">
                    <img src="<?= !empty($_SESSION['user_photo']) ? 'data:image/jpeg;base64,' . $_SESSION['user_photo'] : '../dashboard/conta/uploads/defaultPhoto.png'; ?>" class="user-photo" alt="Foto do usuário">
                </a>
                <div class="user-dropdown-menu">
                    <ul>
                        <li><a href="<?= ($_SESSION['user_type'] == 'UtilizadorProfissional') ? '../dashboardProfissionais/dashboardInicial/index.php' : '../dashboard/dashinicial/index.php'; ?>">Dashboard</a></li>
                        <li><a href="../dashboard/logout.php">Sair</a></li>
                    </ul>
                </div>
            </li>
        <?php else: ?>
            <li><a href="../registar/index.html">Registar-me</a></li>
            <li><a href="../login/index.html">Entrar</a></li>
        <?php endif; ?>
        </ul>
    </nav>
</head>
<body>

<div class="tab-precos">
    <div class="detalhes">
        <h2>Escolhe o teu Plano!</h2>
        <p>Aqui podes ver todos os detalhes e vantagens dos nossos planos!</p>
    </div>
    <div class="tabela">
        <div class="caixa individual">
            <div class="titulo">Plano Individual</div>
            <div class="preco"><b>54,99€</b>
            <span>por Consulta</span>
            </div>
            <div class="detalhes">
                <div>Apenas <b> <br>Uma Consulta</b></div> <!-- colocar todos os detales, cada um em uma <div> diferente -->
                <div>Serviços Como:</div>
                <div>Psicologia</div>
                <div>Psiquiatria</div>
                <div>Nutricionismo</div>
                <div>Medicina Geral</div>
            </div>
            <div class="botao">
            <a href="../subscreverMetodos/index.html?IdSubscricao=1&preco=54.99&nome=Plano%20Individual&ciclo=individual" class="subscribe-btn">
            <button>Subscrever!</button>
                </a>
            </div>
        </div>
        <div class="caixa mensal">
            <div class="titulo">Plano Mensal</div>
            <div class="preco"><b>224,99€</b>
            <span>por Mês</span>
            </div>
            <div class="detalhes">
                <div>Número Ilímitado de Consultas Durante o <b> <br>Mês Ativo!</b></div> <!-- colocar todos os detales, cada um em uma <div> diferente -->
                <div>Serviços Como:</div>
                <div>Psicologia</div>
                <div>1 Serviço à Escolha</div>
            </div>
            <div class="botao">
            <a href="../subscreverMetodos/index.html?IdSubscricao=2&preco=224.99&nome=Plano%20Mensal&ciclo=mensal" class="subscribe-btn">
            <button>Subscrever!</button>
                </a>
            </div>
        </div>
        <div class="caixa anual">
            <div class="titulo">Plano Anual</div>
            <div class="preco"><b>700€</b>
            <span>por Ano</span>
            </div>
            <div class="detalhes">
                <div>Número Ilímitado de Consultas Durante o <b> <br>Ano Ativo!</b></div> <!-- colocar todos os detales, cada um em uma <div> diferente -->
                <div>Serviços Como:</div>
                <div>Psicologia</div>
                <div>3 Serviços à Escolha</div>
            </div>
            <div class="botao">
            <a href="../subscreverMetodos/index.html?IdSubscricao=3&preco=700.00&nome=Plano%20Anual&ciclo=anual" class="subscribe-btn">
            <button>Subscrever!</button>
                </a>
            </div>
        </div>
        
    </div>
</div>


<div class="head" id="HSC">
    <h1>
        O Plano “Heróis Sem Capa”
    </h1>
</div>

<div class="container">
    <div class="flex-container">
        <!-- Primeira Tabela -->
        <div class="tab-container">
            <div class="tab-precos">
                <div class="tabela">
                    <div class="caixa individual">
                        <div class="titulo">Plano Herois Sem Capa</div>
                        <div class="preco"><b>15,00€</b>
                            <span>por Mês</span>
                        </div>
                        <div class="detalhes">
                            <div>Consultas ilímitadas de:</div>
                            <div>Serviços Como:</div>
                            <div>Psicologia</div>
                            <div>Psiquiatria</div>
                        </div>
                        <div class="botao">
                        <a href="../subscreverMetodos/index.html?IdSubscricao=4&preco=15.00&nome=Plano%20Heroico&ciclo=mensal" class="subscribe-btn">
                                <button>Subscrever!</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="sobre-texto">
            <h2>O que é o Plano "Heróis Sem Capa"?</h2>
            <div class="p1">
                <p>
                    O Plano "Heróis Sem Capa" é um plano inovador pensado por nós para promover os nossos serviços oferecios para profissionais que trabalham em áreas de risco e que podem desenvolver algum tipo de trauma, como Polícias, Bombeiros, Médicos e Militares, onde iremos oferecer os nossos serviços de uma forma diferente e mais acessível a essas áreas, fazendo os nossos serviços serem oferecidos a todos sem qualquer excessão!
                </p>
            </div>
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
            <h3>Newsletter!</h3>
            <form>
                <input type="email" placeholder="Insere o teu e-mail aqui!" required>
                <button type="submit"><i class="fa-solid fa-arrow-right"></i></button>
            </form>
        </div>
    </div>
</footer>
<script src="script.js"></script>
</html>