const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');
const registerInfo2 = document.getElementById("registerInfo2");
const regInfo1 = document.getElementById("reginfo1");
const btnseguinte = document.getElementById("btnseguinte");
const passwordInput = document.getElementById("Pass");
const confPasswordInput = document.getElementById("confPass");
const confMsg = document.getElementById("confMsg");
const btnseguinte2 = document.getElementById("btnseguinte2")
const regInfoUser1 = document.getElementById("regInfo1User")
const regInfoUser2 = document.getElementById("regInfo2User")
const confPasswordInputUser = document.getElementById("confPassUser");
const passwordInputUser = document.getElementById("PassUser");
const confMsgUser = document.getElementById("confMsgUser");
const btnseguinteUser = document.getElementById("btnseguinteUser")
const registerInfo3 = document.getElementById("registerInfo3")
const btnseguinte3 = document.getElementById("btnseguinte3")
const regInfo3 = document.getElementById("regInfo3")
const btnseguinte4 = document.getElementById("btnseguinte4")
const registerInfo4 = document.getElementById("registerInfo4")

let h1Cand = document.getElementById("h1Cand")
let spanCand = document.getElementById("spanCand")
let h1Paciente = document.getElementById("h1Pac")
let pPaciente = document.getElementById("pPac")
let h1Profissional = document.getElementById("h1Prof")
let pProfissional = document.getElementById("pProf")

function camposPreenchidos() {
  const inputs = regInfo1.querySelectorAll('input[required]');
  const selectGenero = document.querySelector('select[name="genero"]');

  for (let i = 0; i < inputs.length; i++) {
    if (!inputs[i].value) {
      return false;
    }
  }

  if (!selectGenero.value) {
    return false;
  }

  return true;
}

function camposPreenchidos2(){
  const inputs2 = registerInfo2.querySelectorAll("input[required]");  
  const selectSituacao = document.querySelector('select[name="situacao"]'); 

  for (let i = 0; i < inputs2.length; i++) {
    if (!inputs2[i].value) {
      return false;
    }
  }

  if (!selectSituacao.value) {
    return false;
  }
  return true;
}

function camposPreenchidos3() {
  const inputs3 = registerInfo3.querySelectorAll('input[required], textarea[required]');

  for (let i = 0; i < inputs3.length; i++) {  
    if (!inputs3[i].value) {
      return false;
    }
  }
  return true;
}

// Função para validar o número de telefone
function validarNumeroTelefone(input) {
    if (input.value.length > 9) {
        alert("O número de telefone deve ter exatamente 9 dígitos.");
        input.value = input.value.slice(0, 9); // Limita o valor a 9 dígitos
    }
}

// Adiciona o evento de input para o primeiro telefone
document.getElementById('telemovel1').addEventListener('input', function (e) {
    this.value = this.value.replace(/[^0-9]/g, ''); 
    validarNumeroTelefone(this); // Chama a função de validação
});

// Adiciona o evento de input para o segundo telefone
document.getElementById('telemovel2').addEventListener('input', function (e) {
    this.value = this.value.replace(/[^0-9]/g, ''); 
    validarNumeroTelefone(this); // Chama a função de validação
});

const selectGenero = document.getElementById("genero");
const generoLabel = document.getElementById("generoLabel");

selectGenero.addEventListener("change", function () {
  if (selectGenero.value !== "") {
    generoLabel.style.display = "none";
  } else {
    generoLabel.style.display = "block";
  }
});

const selectSituacao = document.querySelector('select[name="situacao"]'); 
const situacaoLabel = document.getElementById("situacaoLabel")

selectSituacao.addEventListener("change", function () {
  if (selectSituacao.value !== "") {
    situacaoLabel.style.display = "none";
  } else {
    situacaoLabel.style.display = "block";
  }
});

const selectArea = document.querySelector('select[name="areaEspecializada"]'); 
const areaLabel = document.getElementById("labelAreaEspecializada");

selectArea.addEventListener("change", function () {
  if (selectArea.value !== "") {
    areaLabel.style.display = "none";
  } else {
    areaLabel.style.display = "block";
  }
});

function camposPreenchidosUser() {
  const inputsUser = regInfoUser1.querySelectorAll('input[required]');
  for (let i = 0; i < inputsUser.length; i++) {  
    if (!inputsUser[i].value) {
      return false;
    }
  }
  return true;
}

function validarEmail() {
  const emailInput = document.querySelector('input[name="email"]');
  const email = emailInput.value;
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!emailRegex.test(email)) {
    alert("Por favor, insere um e-mail válido.");
    return false;
  }
  return true;
}

function validarEmailUser() {
  const emailInput = regInfoUser1.querySelector('input[name="email"]');
  const email = emailInput.value;
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!emailRegex.test(email)) {
    alert("Por favor, insere um e-mail válido.");
    return false;
  }
  return true;
}

function validarNumero() {
  const numeroInput = document.querySelector('input[name="nTelefone"]');
  const numero = numeroInput.value;
  const numeroRegex = /^\d{9}$/;

  if (!numeroRegex.test(numero)) {
    alert("Por favor, insira um número de telefone válido (apenas dígitos, exatamente 9 caracteres).");
    return false;
  }

  return true;
}

function checkPassword() {
  const password = passwordInput.value;
  const confPass = confPasswordInput.value;
  const errorMessage = document.createElement('div');
  
  // Remove mensagem de erro anterior se existir
  const oldError = confPasswordInput.parentNode.querySelector('.error-message');
  if (oldError) {
    oldError.remove();
  }
  
  // Remove classe de erro do input
  confPasswordInput.classList.remove('error-input');

  if (password !== confPass) {
    // Adiciona classe de erro ao input
    confPasswordInput.classList.add('error-input');
    
    // Cria e adiciona mensagem de erro
    errorMessage.className = 'error-message';
    errorMessage.textContent = 'As palavras-passe não coincidem';
    confPasswordInput.parentNode.appendChild(errorMessage);
    return false;
  }

  return true;
}

function checkPasswordUser() {
  const password = passwordInputUser.value;
  const confPass = confPasswordInputUser.value;
  const errorMessage = document.createElement('div');
  
  // Remove mensagem de erro anterior se existir
  const oldError = confPasswordInputUser.parentNode.querySelector('.error-message');
  if (oldError) {
    oldError.remove();
  }
  
  // Remove classe de erro do input
  confPasswordInputUser.classList.remove('error-input');

  if (password !== confPass) {
    // Adiciona classe de erro ao input
    confPasswordInputUser.classList.add('error-input');
    
    // Cria e adiciona mensagem de erro
    errorMessage.className = 'error-message';
    errorMessage.textContent = 'As palavras-passe não coincidem';
    confPasswordInputUser.parentNode.appendChild(errorMessage);
    return false;
  }

  return true;
}

btnseguinte.addEventListener("click", (event) => {
  event.preventDefault();

  if (!camposPreenchidos()) {
    alert("Por favor, preenche todos os campos obrigatórios.");
    return;
  }

  if (!validarEmail()) {
    return;
  }

  if (!checkPassword()) {
    return;
  }

  regInfo1.style.display = "none";
  btnseguinte.style.display = "none";
  registerInfo2.style.display = "block";
  h1Paciente.innerHTML = "Parabéns!"
  pPaciente.innerHTML = "Estás cada vez mais próximo de te candidatares a Profissional!"
  pPaciente.style.fontSize = "15px"
  loginBtn.style.display = "none"
  spanCand.style.display = "none"
  h1Cand.style.display = "none"
});

btnseguinte2.addEventListener("click", (event) => {
  event.preventDefault();

  if (!camposPreenchidos2()) {
    alert("Por favor, preenche todos os campos obrigatórios.");
    return;
  }

  registerInfo2.style.display = "none";
  btnseguinte2.style.display = "none";
  registerInfo3.style.display = "block";
  btnseguinte3.style.display = "block"
  h1Paciente.innerHTML = "Parabéns!"
  pPaciente.innerHTML = "Estás cada vez mais próximo de te candidatares a Profissional! <br> Faltam Apenas Duas Etapas!"
  pPaciente.style.fontSize = "15px"
  loginBtn.style.display = "none"
});

btnseguinte3.addEventListener("click", (event) =>{
  event.preventDefault()

  if (!camposPreenchidos3()){
    alert ("Por favor, preenche todos os campos obrigatórios.")
    return
  }

  registerInfo3.style.display = "none"
  btnseguinte3.style.display = "none"
  registerInfo4.style.display = "block"
  btnseguinte4.style.display = "block"
  h1Paciente.innerHTML ="Parabéns!"
  pPaciente.innerHTML = "Estás mesmo quase a acabar a candidatura! <br> Já estás na Última etapa!"
})

btnseguinteUser.addEventListener("click", (event) => {
  event.preventDefault();

  if (!camposPreenchidosUser()) {
    alert("Por favor, preenche todos os campos obrigatórios.");
    return;
  }

  if (!validarEmailUser()) {
    return;
  }

  if (!checkPasswordUser()) {
    return;
  }

  regInfoUser1.style.display = "none";
  btnseguinteUser.style.display = "none";
  regInfoUser2.style.display = "block";
  h1Profissional.innerHTML = "Parabéns!"
  pProfissional.innerHTML = "Estás cada vez mais próximo de criares a tua conta!"
  pProfissional.style.fontSize = "15px"
  registerBtn.style.display = "none"
});

registerBtn.addEventListener('click', () => {
  container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
  container.classList.remove("active");
});

confPasswordInput.addEventListener('input', checkPassword);
confPasswordInputUser.addEventListener('input', checkPasswordUser);

document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0]; // Obtém a data atual no formato YYYY-MM-DD
    
    // Define a data máxima para o campo de data de nascimento do usuário
    const dataNascimentoUserInput = document.getElementById('dataNascimentoUser');
    if (dataNascimentoUserInput) {
        dataNascimentoUserInput.setAttribute('max', today);
    }

    // Define a data máxima para o campo de data de nascimento do profissional
    const dataNascimentoProfInput = document.getElementById('dataNascimentoProf');
    if (dataNascimentoProfInput) {
        dataNascimentoProfInput.setAttribute('max', today);
    }
});