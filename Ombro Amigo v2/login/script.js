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

