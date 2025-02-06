const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');
const registerInfo2 = document.getElementById("registerInfo2");
const regInfo1 = document.getElementById("reginfo1");
const btnseguinte = document.getElementById("btnseguinte");
const passwordInput = document.getElementById("Pass");
const confPasswordInput = document.getElementById("confPass");
const confMsg = document.getElementById("confMsg");

// Eventos de clique para alternar entre a tela de login e registro
registerBtn.addEventListener('click', () => {
  container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
  container.classList.remove("active");
});

function showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    if (!errorDiv) {
        console.error('Elemento de erro não encontrado');
        return;
    }
    
    // Limpa o conteúdo anterior
    errorDiv.innerHTML = '';
    
    // Cria a barra de progresso
    const progressBar = document.createElement('div');
    progressBar.className = 'error-progress';
    
    // Cria o elemento para o texto
    const messageText = document.createElement('span');
    messageText.textContent = message;
    
    // Adiciona os elementos na ordem correta
    errorDiv.appendChild(progressBar);
    errorDiv.appendChild(messageText);
    
    // Reseta o display e animações
    errorDiv.style.display = 'block';
    void errorDiv.offsetWidth; // Força um reflow
    
    // Aplica as animações
    errorDiv.style.animation = 'slideIn 0.5s ease forwards';
    
    setTimeout(() => {
        errorDiv.classList.add('error-hiding');
        setTimeout(() => {
            errorDiv.style.display = 'none';
            errorDiv.classList.remove('error-hiding');
            errorDiv.innerHTML = '';
        }, 500);
    }, 10000);
}

// Manipular formulário de paciente
document.querySelector('.sign-in form').addEventListener('submit', function(e) {
    e.preventDefault(); // Impede o envio normal do formulário
    
    const formData = new FormData(this);
    
    fetch('login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        showError('Erro ao processar a solicitação');
    });
});

// Manipular formulário de profissional
document.querySelector('.sign-up form').addEventListener('submit', function(e) {
    e.preventDefault(); // Impede o envio normal do formulário
    
    const formData = new FormData(this);
    
    fetch('loginProf.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        showError('Erro ao processar a solicitação');
    });
});

