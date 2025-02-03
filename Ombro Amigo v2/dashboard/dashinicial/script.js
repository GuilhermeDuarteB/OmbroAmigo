document.addEventListener("DOMContentLoaded", function() {
    const dmsg = document.getElementById("dmsg");
    const submitButton = document.querySelector("button[name='submit_sentimentos']");

    // Função para exibir a div
    function showDiv() {
        dmsg.style.display = "block";
    }

    // Função para ocultar a div e registrar o horário no localStorage
    function hideDiv() {
        dmsg.style.display = "none";
        const now = new Date().getTime();
        localStorage.setItem("lastHiddenTime", now);  // Armazena o horário atual
    }

    // Verificação de 24 horas usando o valor no `localStorage`
    function checkVisibility() {
        const lastHiddenTime = localStorage.getItem("lastHiddenTime");
        const now = new Date().getTime();
        const oneDay = 24 * 60 * 60 * 1000; // 24 horas em milissegundos

        // Se não houver `lastHiddenTime` (primeira visita) ou se já passaram 24 horas
        if (!lastHiddenTime || (now - lastHiddenTime) > oneDay) {
            showDiv();
        } else {
            dmsg.style.display = "none";  // Garante que a div permaneça oculta ao atualizar
        }
    }

    // Adiciona o evento ao botão para esconder a div e registrar o horário
    if (submitButton) {
        submitButton.addEventListener("click", function() {
            hideDiv();
        });
    }

    // Chama a função de verificação ao carregar a págaa
    checkVisibility();
});
