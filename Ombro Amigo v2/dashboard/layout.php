<?php
include_once __DIR__ . '/verificar_subscricao.php';

// Verificar se o usuário tem subscrição ativa
$temSubscricaoAtiva = verificarSubscricaoAtiva($conn, $user_id);

// Função auxiliar para renderizar item do menu
function renderMenuItem($href, $icon, $text) {
    return "<li><a href=\"$href\">
                <i class=\"$icon\"></i>
                <span class=\"nav-item\">$text</span>
            </a></li>";
}
?>

<style>
.disabled-link {
    opacity: 0.6;
    cursor: not-allowed;
    position: relative;
}

.subscription-lock {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.8em;
    color: #ff4444;
}
</style>

<script>
function alertSubscricao() {
    alert("Esta funcionalidade requer uma subscrição ativa. Por favor, ative uma subscrição para continuar.");
}
</script>

<nav>
    <ul>
        <?php
        // Menu items que sempre aparecem
        echo renderMenuItem("../../initial page/index.php", "fas fa-home", "Página Inicial");
        echo renderMenuItem("../dashinicial/index.php", "fas fa-laptop-house", "Dashboard");
        echo renderMenuItem("../diario/index.php", "fas fa-solid fa-book", "O Meu Diário");
        
        // Menu items que só aparecem com subscrição ativa
        if ($temSubscricaoAtiva) {
            echo renderMenuItem("../consultasMarcadas/index.php", "fas fa-solid fa-calendar-check", "Consultas Marcadas");
            echo renderMenuItem("../marcarConsultas/index.php", "fas fa-solid fa-calendar-plus", "Marcar Consultas");
            echo renderMenuItem("../notificacoes/index.php", "fas fa-solid fa-bell", "Notificações");
        }
        
        // Menu items que sempre aparecem
        echo renderMenuItem("../conta/conta.php", "fas fa-solid fa-user", "A Minha Conta");
        echo renderMenuItem("../logout.php", "fas fa-solid fa-sign-out-alt", "Terminar Sessão");
        ?>
    </ul>
</nav> 