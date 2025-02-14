function loadProfissionais() {
    const area = document.getElementById("selectconsulta").value;
    const data = document.getElementById("data").value;
    const profissionaisList = document.getElementById("profissionaisList");

    // Verificar se área e data foram selecionadas
    if (!area || !data) {
        profissionaisList.innerHTML = "<p class='mensagem-erro'>Por favor, selecione uma categoria e uma data.</p>";
        return;
    }

    profissionaisList.style.display = 'grid';

    // Enviar dados para o servidor
    const formData = new FormData();
    formData.append('area', area);
    formData.append('data', data);

    fetch('fetch_profissionais.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayProfissionais(data.profissionais);
        } else {
            throw new Error(data.error || 'Erro desconhecido');
        }
    })
    .catch(error => {
        console.error('Erro ao carregar profissionais:', error);
        displayError('Erro ao carregar profissionais: ' + error.message);
    });
}

function showProfissionalDetails(profissional) {
    const detalhesContainer = document.getElementById('detalhesProfissional');
    const fotoDetalhes = document.getElementById('fotoDetalhes');
    const nomeDetalhes = document.getElementById('nomeDetalhes');
    const horariosDetalhes = document.getElementById('horariosDetalhes');
    const marcarConsultaBtn = document.getElementById('marcarConsultaBtn');

    // Reset do botão
    marcarConsultaBtn.classList.remove('btn-ativado');
    marcarConsultaBtn.classList.add('btn-desativado');
    marcarConsultaBtn.disabled = true;

    const fotoSrc = profissional.Foto
        ? `data:image/jpeg;base64,${profissional.Foto}`
        : "../conta/uploads/defaultPhoto.png";

    fotoDetalhes.src = fotoSrc;
    nomeDetalhes.textContent = profissional.Nome || "Nome não encontrado";

    if (profissional.Horarios && profissional.Horarios.length > 0) {
        const horariosHtml = profissional.Horarios.map(horario => {
            const classes = `horario ${horario.ocupado ? 'horario-ocupado' : ''}`;
            const clickHandler = !horario.ocupado ? 'onclick="selectHorario(this)"' : '';
            
            return `<span class="${classes}" 
                         data-disponivel="${!horario.ocupado}" 
                         ${clickHandler}>${horario.horario}</span>`;
        }).join('');

        horariosDetalhes.innerHTML = `
            <p>Horários disponíveis:</p>
            <div class="horarios-list">${horariosHtml}</div>
        `;
    } else {
        horariosDetalhes.innerHTML = `<p>Sem horários disponíveis para a data selecionada.</p>`;
    }

    detalhesContainer.style.display = 'block';
    setTimeout(() => detalhesContainer.classList.add('show'), 10);
}

function selectHorario(elemento) {
    if (elemento.classList.contains('horario-ocupado')) {
        return;
    }

    const horarios = document.querySelectorAll('.horario');
    const marcarConsultaBtn = document.getElementById('marcarConsultaBtn');
    const dataConsulta = document.getElementById('data').value;
    const profissionalSelecionado = document.querySelector('.fa-circle-info[data-selecionado="true"]');
    
    if (!profissionalSelecionado) return;
    
    const profissional = JSON.parse(profissionalSelecionado.dataset.profissional);

    // Verificar disponibilidade do horário
    fetch(`marcar_consulta.php?profissionalId=${profissional.Id}&data=${dataConsulta}`)
    .then(response => response.json())
    .then(data => {
        const horarioSelecionado = elemento.textContent;
        
        if (data.horariosOcupados && data.horariosOcupados.includes(horarioSelecionado)) {
            elemento.classList.add('horario-ocupado');
            marcarConsultaBtn.classList.remove('btn-ativado');
            marcarConsultaBtn.classList.add('btn-desativado');
            marcarConsultaBtn.disabled = true;
            return;
        }

        horarios.forEach(horario => horario.classList.remove('horario-selecionado'));
        elemento.classList.add('horario-selecionado');
        marcarConsultaBtn.classList.remove('btn-desativado');
        marcarConsultaBtn.classList.add('btn-ativado');
        marcarConsultaBtn.disabled = false;
    });
}


// Fechar o contêiner ao clicar no "x"
document.getElementById('fecharDetalhes').addEventListener('click', () => {
    const detalhesContainer = document.getElementById('detalhesProfissional');
    detalhesContainer.classList.remove('show');
    setTimeout(() => {
        detalhesContainer.style.display = 'none';
    }, 300);
});

function attachInfoIconClickEvents() {
    const infoIcons = document.querySelectorAll('.fa-circle-info');

    infoIcons.forEach((icon) => {
        icon.addEventListener('click', () => {
            const profissional = JSON.parse(icon.dataset.profissional);
            showProfissionalDetails(profissional);
        });
    });
}

function attachInfoIconClickEvents() {
    const infoIcons = document.querySelectorAll('.fa-circle-info');

    infoIcons.forEach((icon) => {
        icon.addEventListener('click', () => {
            const profissional = JSON.parse(icon.dataset.profissional);

            // Desmarcar todos os outros profissionais
            document.querySelectorAll('.fa-circle-info').forEach((item) => {
                item.dataset.selecionado = "false";
            });

            // Marcar o profissional atual como selecionado
            icon.dataset.selecionado = "true";

            // Mostrar os detalhes do profissional
            showProfissionalDetails(profissional);
        });
    });
}


function marcarConsulta() {
    const marcarConsultaBtn = document.getElementById('marcarConsultaBtn');
    const horario = marcarConsultaBtn.dataset.horario;
    const profissionalId = marcarConsultaBtn.dataset.profissionalId;
    const data = document.getElementById('data').value;

    if (!profissionalId || !horario) {
        alert("Por favor, selecione um profissional e um horário.");
        return;
    }

    // Criar FormData para envio
    const formData = new FormData();
    formData.append('profissionalId', profissionalId);
    formData.append('horario', horario);
    formData.append('data', data);

    fetch('marcar_consulta.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            if (data.redirect) {
                window.location.href = data.redirect;
            }
        } else {
            alert(data.message || 'Erro ao marcar consulta');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao processar a requisição');
    });
}

document.getElementById('marcarConsultaBtn').addEventListener('click', marcarConsulta);

// Adicionar listener para redimensionamento da janela
window.addEventListener('resize', () => {
    const profissionaisList = document.getElementById("profissionaisList");
    const body = document.body;
    
    if (profissionaisList && profissionaisList.children.length > 0) {
        const contentHeight = profissionaisList.scrollHeight;
        const viewportHeight = window.innerHeight;
        
        if (contentHeight > viewportHeight) {
            body.classList.remove('no-scroll');
        } else {
            body.classList.add('no-scroll');
        }
    }
});

// Função para exibir os profissionais
function displayProfissionais(profissionais) {
    const profissionaisList = document.getElementById('profissionaisList');
    profissionaisList.innerHTML = '';

    if (profissionais.length === 0) {
        profissionaisList.innerHTML = `
            <div class="no-results">
                <p>Nenhum profissional disponível para esta data e especialidade.</p>
            </div>`;
        return;
    }

    profissionais.forEach(profissional => {
        const profView = document.createElement('div');
        profView.className = 'profView';
        
        const fotoSrc = profissional.Foto 
            ? `data:image/jpeg;base64,${profissional.Foto}`
            : "../conta/uploads/defaultPhoto.png";

        profView.innerHTML = `
            <img src="${fotoSrc}" alt="Foto de ${profissional.Nome}">
            <p>${profissional.Nome}</p>
            <i class="fas fa-calendar-alt" onclick="mostrarDetalhes(${JSON.stringify(profissional).replace(/"/g, '&quot;')})"></i>
        `;

        profissionaisList.appendChild(profView);
    });
}

// Função para exibir erros
function displayError(message) {
    const profissionaisList = document.getElementById('profissionaisList');
    profissionaisList.innerHTML = `
        <div class="error-message">
            <p>${message}</p>
        </div>`;
}

// Função para mostrar detalhes do profissional e seus horários
function mostrarDetalhes(profissional) {
    const detalhesProfissional = document.getElementById('detalhesProfissional');
    const fotoDetalhes = document.getElementById('fotoDetalhes');
    const nomeDetalhes = document.getElementById('nomeDetalhes');
    const horariosDetalhes = document.getElementById('horariosDetalhes');
    const marcarConsultaBtn = document.getElementById('marcarConsultaBtn');

    // Configurar foto
    fotoDetalhes.src = profissional.Foto 
        ? `data:image/jpeg;base64,${profissional.Foto}`
        : "../conta/uploads/defaultPhoto.png";

    nomeDetalhes.textContent = profissional.Nome;
    horariosDetalhes.innerHTML = '';

    if (profissional.Horarios && profissional.Horarios.length > 0) {
        const horariosHtml = profissional.Horarios
            .map(horario => `
                <button class="horario-btn ${horario.disponivel ? 'disponivel' : 'ocupado'}"
                        ${horario.disponivel ? '' : 'disabled'}
                        onclick="selecionarHorario(this, '${horario.horario}', ${profissional.Id})">
                    ${horario.horario}
                </button>
            `)
            .join('');

        horariosDetalhes.innerHTML = horariosHtml;
    } else {
        horariosDetalhes.innerHTML = '<p>Não há horários disponíveis para este profissional.</p>';
    }

    detalhesProfissional.style.display = 'block';
}

// Função para selecionar um horário
function selecionarHorario(button, horario, profissionalId) {
    // Remover seleção anterior
    document.querySelectorAll('.horario-btn.selecionado').forEach(btn => {
        btn.classList.remove('selecionado');
    });

    // Adicionar seleção ao horário clicado
    button.classList.add('selecionado');

    // Ativar botão de marcar consulta
    const marcarConsultaBtn = document.getElementById('marcarConsultaBtn');
    marcarConsultaBtn.removeAttribute('disabled');
    marcarConsultaBtn.classList.remove('btn-desativado');
    marcarConsultaBtn.classList.add('btn-ativado');
    
    // Armazenar dados da consulta
    marcarConsultaBtn.dataset.horario = horario;
    marcarConsultaBtn.dataset.profissionalId = profissionalId;
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    const fecharDetalhes = document.getElementById('fecharDetalhes');
    if (fecharDetalhes) {
        fecharDetalhes.addEventListener('click', () => {
            document.getElementById('detalhesProfissional').style.display = 'none';
        });
    }

    const marcarConsultaBtn = document.getElementById('marcarConsultaBtn');
    if (marcarConsultaBtn) {
        marcarConsultaBtn.addEventListener('click', marcarConsulta);
    }
});
