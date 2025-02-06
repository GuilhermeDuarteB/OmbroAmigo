// Elementos dos modais
const mudarEmailModal = document.getElementById("mudarEmail");
const mudarSenhaModal = document.getElementById("mudarSenha");
const btnEditEmail = document.getElementById("btnEditEmail");
const btnEditSenha = document.getElementById("btnEditSenha");
const closeEmailBtn = document.getElementById("closeMenu");
const closeSenhaBtn = document.getElementById("closeSenhaMenu");
const modalOverlay = document.getElementById("modalOverlay");

// Elementos do modal Apagar Conta
const motivoApagarAcc = document.getElementById("motivoApagarAcc");
const closeMenuDelete = document.getElementById("closeMenuDelete");

// Funções para mostrar/esconder modais
function showEmailModal() {
    mudarEmailModal.style.display = "block";
    modalOverlay.style.display = "block";
}

function hideEmailModal() {
    mudarEmailModal.style.display = "none";
    modalOverlay.style.display = "none";
}

function showSenhaModal() {
    mudarSenhaModal.style.display = "block";
    modalOverlay.style.display = "block";
}

function hideSenhaModal() {
    mudarSenhaModal.style.display = "none";
    modalOverlay.style.display = "none";
}

function showDeleteModal() {
    motivoApagarAcc.style.display = "block";
    modalOverlay.style.display = "block";
}

function hideDeleteModal() {
    motivoApagarAcc.style.display = "none";
    modalOverlay.style.display = "none";
}

// Aguardar o DOM estar completamente carregado
document.addEventListener('DOMContentLoaded', function() {
    // Event Listeners
    if (btnEditEmail) btnEditEmail.addEventListener("click", showEmailModal);
    if (btnEditSenha) btnEditSenha.addEventListener("click", showSenhaModal);
    if (closeEmailBtn) closeEmailBtn.addEventListener("click", hideEmailModal);
    if (closeSenhaBtn) closeSenhaBtn.addEventListener("click", hideSenhaModal);
    if (closeMenuDelete) closeMenuDelete.addEventListener("click", hideDeleteModal);

    // Botão Apagar Conta
    const apagarAccBtn = document.querySelector('.apagar-conta');
    if (apagarAccBtn) {
        apagarAccBtn.addEventListener("click", showDeleteModal);
    }

    // Fechar modais ao clicar fora
    if (modalOverlay) {
        modalOverlay.addEventListener("click", () => {
            hideEmailModal();
            hideSenhaModal();
            hideDeleteModal();
        });
    }
});

// Controle de caracteres do textArea
const textArea = document.getElementById("outroMotivoSaida");
const totalCharact = document.getElementById("caractAtual");
const finalCharact = document.getElementById("caractFinal");

if (finalCharact) finalCharact.innerText = 250;

function updateCharac() {
    if (!textArea || !totalCharact || !finalCharact) return;
    
    totalCharact.innerText = textArea.value.length;

    if (textArea.value.length >= 225) {
        totalCharact.style.color = "red";
    } else if (textArea.value.length >= 200) {
        totalCharact.style.color = "orange";
    } else {
        totalCharact.style.color = "black";
    }

    if (textArea.value.length == 250) {
        finalCharact.style.color = "red";
    } else {
        finalCharact.style.color = "black";
    }
}

if (textArea) {
    updateCharac();
    textArea.addEventListener("keyup", updateCharac);
}

// Navegação entre seções
document.querySelectorAll('.settings-menu li').forEach(item => {
    item.addEventListener('click', function() {
        // Remove active de todos
        document.querySelectorAll('.settings-menu li').forEach(i => i.classList.remove('active'));
        document.querySelectorAll('.settings-section').forEach(s => s.classList.remove('active'));
        
        // Adiciona active ao clicado
        this.classList.add('active');
        
        // Mostra a seção correspondente
        const sectionId = this.className.split(' ')[0] + 'Section';
        const section = document.getElementById(sectionId);
        if (section) section.classList.add('active');
    });
});

// Animação suave para modais
function showModal(modal) {
    if (!modal) return;
    modal.style.display = 'block';
    setTimeout(() => modal.classList.add('visible'), 10);
}

function hideModal(modal) {
    if (!modal) return;
    modal.classList.remove('visible');
    setTimeout(() => modal.style.display = 'none', 300);
}