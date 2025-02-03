function loadProfissionais() {
    const area = document.getElementById("selectconsulta").value;
    const data = document.getElementById("data").value;
    const profissionaisList = document.getElementById("profissionaisList");

    // Verificar se área e data foram selecionadas
    if (!area || !data) {
        profissionaisList.innerHTML = "<p>Por favor, selecione uma categoria e uma data.</p>";
        return;
    }

    profissionaisList.style.display = 'grid';

    // Enviar dados para o servidor
    fetch('fetch_profissionais.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `area=${encodeURIComponent(area)}&data=${encodeURIComponent(data)}`
    })
        .then(response => response.json())
        .then(data => {
            if (!Array.isArray(data)) {
                throw new Error("Resposta inválida do servidor. Esperava um array.");
            }

            // Limpar lista de profissionais
            profissionaisList.innerHTML = '';

            // Exibir profissionais disponíveis
            data.forEach(profissional => {
                const profView = document.createElement("div");
                profView.classList.add("profView");

                const fotoSrc = profissional.Foto
                    ? `data:image/jpeg;base64,${profissional.Foto}`
                    : "../conta/uploads/defaultPhoto.png";

                profView.innerHTML = `
                    <img src="${fotoSrc}" alt="Foto do Profissional" class="fas">
                    <p>${profissional.Nome || 'Nome não encontrado'}</p>
                    <i class="fa-solid fa-circle-info" data-profissional='${JSON.stringify(profissional)}'></i>
                `;

                profissionaisList.appendChild(profView);
            });

            // Após renderizar, anexar os eventos aos ícones
            attachInfoIconClickEvents();
        })
        .catch(error => {
            console.error('Erro ao carregar profissionais:', error);
            profissionaisList.innerHTML = "<p>Erro ao carregar profissionais. Tente novamente.</p>";
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
    const profissionalSelecionado = document.querySelector('.fa-circle-info[data-selecionado="true"]');
    const horarioSelecionado = document.querySelector('.horario.horario-selecionado');
    const marcarConsultaBtn = document.getElementById('marcarConsultaBtn');

    if (!profissionalSelecionado || !horarioSelecionado) {
        alert("Por favor, selecione um profissional e um horário.");
        return;
    }

    const profissional = JSON.parse(profissionalSelecionado.dataset.profissional);
    const dataConsulta = document.getElementById('data').value;
    const horario = horarioSelecionado.textContent;

    // Verificar disponibilidade antes de enviar (usando query params para GET)
    fetch(`marcar_consulta.php?profissionalId=${encodeURIComponent(profissional.Id)}&data=${encodeURIComponent(dataConsulta)}&horario=${encodeURIComponent(horario)}`)
    .then(response => response.json())
    .then(data => {
        if (data.horariosOcupados && data.horariosOcupados.includes(horario)) {
            alert("Este horário já está ocupado.");
            horarioSelecionado.classList.add('horario-ocupado');
            horarioSelecionado.classList.remove('horario-selecionado');
            marcarConsultaBtn.classList.remove('btn-ativado');
            marcarConsultaBtn.classList.add('btn-desativado');
            marcarConsultaBtn.disabled = true;
            return;
        }

        // Se o horário estiver disponível, prosseguir com a marcação
        fetch('marcar_consulta.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `profissionalId=${encodeURIComponent(profissional.Id)}&data=${encodeURIComponent(dataConsulta)}&horario=${encodeURIComponent(horario)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            } else {
                alert(`Erro ao marcar consulta: ${data.message}`);
                if (data.message === "Horário indisponível.") {
                    horarioSelecionado.classList.add('horario-ocupado');
                    horarioSelecionado.classList.remove('horario-selecionado');
                    marcarConsultaBtn.classList.remove('btn-ativado');
                    marcarConsultaBtn.classList.add('btn-desativado');
                    marcarConsultaBtn.disabled = true;
                }
            }
        })
        .catch(error => {
            console.error("Erro ao marcar consulta:", error);
            alert("Ocorreu um erro. Tente novamente.");
        });
    });
}

document.getElementById('marcarConsultaBtn').addEventListener('click', marcarConsulta);
