document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const consultaCards = document.querySelectorAll('.consulta-card');
 
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            button.classList.add('active');
 
            const filterValue = button.getAttribute('data-filter');
 
            consultaCards.forEach(card => {
                if (filterValue === 'todas') {
                    card.style.display = 'block';
                } else {
                    if (card.getAttribute('data-tipo') === filterValue) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        });
    });
});
 
function toggleDetails(btn) {
    const card = btn.closest('.consulta-card');
    const details = card.querySelector('.consulta-details').innerHTML;
   
    // Criar o modal
    const modalHTML = `
        <div class="modal-overlay" id="detailsModal">
            <div class="modal-container">
                <div class="modal-header">
                    <h2>Detalhes da Consulta</h2>
                    <button class="close-modal" onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-content">
                    ${details}
                </div>
            </div>
        </div>
    `;
 
    // Remover modal existente se houver
    const existingModal = document.getElementById('detailsModal');
    if (existingModal) {
        existingModal.remove();
    }
 
    // Adicionar novo modal
    document.body.insertAdjacentHTML('beforeend', modalHTML);
   
    // Mostrar o modal
    const modal = document.getElementById('detailsModal');
    modal.style.display = 'flex';
 
    // Fechar modal ao clicar fora
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
}
 
function closeModal() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
        modal.remove();
    }
}
 
function salvarLink(btn) {
    const inputGroup = btn.closest('.link-input-group');
    const input = inputGroup.querySelector('.link-input');
    const consultaId = input.dataset.consultaId;
    const link = input.value.trim();
 
    if (!link) {
        alert('Por favor, insira um link válido');
        return;
    }
 
    fetch('salvar_link.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            consultaId: consultaId,
            link: link
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const linkSection = inputGroup.closest('.link-section');
            linkSection.innerHTML = `
                <div class="link-display">
                    <a href="${link}" class="link-consulta" target="_blank" data-consulta-id="${consultaId}">
                        <i class="fas fa-video"></i> Iniciar Consulta
                    </a>
                    <button class="edit-link-btn" onclick="editarLink(this)">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            `;
           
            // Atualizar também o card original
            const cardDetails = document.querySelector(`[data-consulta-id="${consultaId}"]`)
                .closest('.consulta-card')
                .querySelector('.consulta-details');
            cardDetails.innerHTML = linkSection.innerHTML;
        } else {
            alert('Erro ao salvar o link');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao salvar o link');
    });
}
 
function editarLink(btn) {
    const linkSection = btn.closest('.link-section');
    const linkAtual = linkSection.querySelector('.link-consulta').href;
    const consultaId = linkSection.querySelector('.link-consulta').dataset.consultaId;
 
    linkSection.innerHTML = `
        <div class="link-input-group">
            <input type="text"
                   class="link-input"
                   value="${linkAtual}"
                   data-consulta-id="${consultaId}">
            <button class="save-link-btn" onclick="salvarLink(this)">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    `;
}
 
function responderConsulta(consultaId, status) {
    if (!confirm(`Tem certeza que deseja ${status === 'Aceite' ? 'aceitar' : 'recusar'} esta consulta?`)) {
        return;
    }
 
    fetch('responder_consulta.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            consultaId: consultaId,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Erro ao atualizar o status da consulta: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao atualizar o status da consulta');
    });
}
 
 
function mostrarDetalhesConsulta(consultaId) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-container">
            <div class="modal-header">
                <h2>Detalhes da Consulta</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-content">
                <button class="start-call-btn" onclick="iniciarConsulta(${consultaId})">
                    <i class="fas fa-video"></i> Começar Consulta
                </button>
            </div>
        </div>
    `;
   
    // Adicionar evento para fechar ao clicar fora do modal
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
   
    document.body.appendChild(modal);
    modal.style.display = 'flex';
}
 
function iniciarConsulta(consultaId) {
    // Definir tipo explicitamente como 'profissional' para profissionais
    window.open(`../../videocalls/videocall.php?consulta=${consultaId}&tipo=profissional`, '_blank',
        'width=1200,height=800,toolbar=0,menubar=0,location=0');
}