let mudarEmailClass = document.getElementById("mudarEmail")
let btnEditEmail = document.getElementById("btnEditEmail")
let closeMenu = document.getElementById("closeMenu")
let closeMenuDelete = document.getElementById("closeMenuDelete")

btnEditEmail.addEventListener("click", mudarEmailFunc)
closeMenu.addEventListener("click", closeMenuFunc)

closeMenuDelete.addEventListener("click", closeDelete)

mudarEmailClass.classList.remove("visible")

function mudarEmailFunc() {
    if (mudarEmailClass.classList.contains("visible")) {
        mudarEmailClass.classList.remove("visible");
    } else {
        mudarEmailClass.classList.add("visible");
    }

    if(motivoApagarAcc.classList.contains("visible")){
        motivoApagarAcc.classList.remove("visible");
    }
}

function closeMenuFunc(){
    if (mudarEmailClass.classList.contains("visible")) {
        mudarEmailClass.classList.remove("visible");
    }
}


let apagarAccBtnFunc = document.getElementById("apagarAccBtnFunc")
let motivoApagarAcc = document.getElementById("motivoApagarAcc")

apagarAccBtnFunc.addEventListener("click", apagarAccFunc)

motivoApagarAcc.classList.remove("visible")

function  apagarAccFunc() {
    if(motivoApagarAcc.classList.contains("visible")){
        motivoApagarAcc.classList.remove("visible")
    }else{
        motivoApagarAcc.classList.add("visible")
    }

    if(mudarEmailClass.classList.contains("visible")){
        mudarEmailClass.classList.remove("visible")
    }
}

function closeDelete(){
    if(motivoApagarAcc.classList.contains("visible")){
        motivoApagarAcc.classList.remove("visible")}
}

// Controle de caracteres do textArea
const textArea = document.getElementById("outroMotivoSaida");
const totalCharact = document.getElementById("caractAtual");
const finalCharact = document.getElementById("caractFinal");

finalCharact.innerText = 250; // Define o limite de caracteres

function updateCharac() {
    // Atualiza o número de caracteres digitados
    totalCharact.innerText = textArea.value.length;

    // Muda a cor do contador baseado na contagem de caracteres
    if (textArea.value.length >= 225) {
        totalCharact.style.color = "red"; // Alerta para limite próximo
    } else if (textArea.value.length >= 200) {
        totalCharact.style.color = "orange"; // Atenção intermediária
    } else {
        totalCharact.style.color = "black"; // Cor padrão para contagens menores
    }

    if (textArea.value.length == 250) {
        finalCharact.style.color = "red";
    } else {
        finalCharact.style.color = "black";
    }
}

// Chama a função ao carregar a página e ao digitar no textarea
updateCharac();
textArea.addEventListener("keyup", updateCharac);

// Navegação entre seções (simplificada sem subscrições)
document.querySelectorAll('.settings-menu li').forEach(item => {
    item.addEventListener('click', function() {
        if (this.classList.contains('apagar-conta')) {
            showDeleteModal();
            return;
        }
        
        document.querySelectorAll('.settings-menu li').forEach(i => i.classList.remove('active'));
        document.querySelectorAll('.settings-section').forEach(s => s.classList.remove('active'));
        
        this.classList.add('active');
        
        const sectionId = this.className.split(' ')[0] + 'Section';
        const section = document.getElementById(sectionId);
        if (section) section.classList.add('active');
    });
});