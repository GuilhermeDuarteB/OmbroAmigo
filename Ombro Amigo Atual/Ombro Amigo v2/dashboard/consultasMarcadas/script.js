document.addEventListener('DOMContentLoaded', function() {
    // Seleção de elementos
    const filterButtons = document.querySelectorAll('.filter-btn');
    const consultaCards = document.querySelectorAll('.consulta-card');
 
    // Função para filtrar consultas
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class de todos os botões
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Adiciona active class ao botão clicado
            button.classList.add('active');
 
            const filterValue = button.getAttribute('data-filter');
 
            // Filtra os cards
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
 
    // Adicionar event listeners aos botões de detalhes
    document.querySelectorAll('.action-btn.details').forEach(button => {
        button.addEventListener('click', function() {
            const consultaId = this.dataset.consultaId;
            mostrarDetalhesConsulta(consultaId);
        });
    });
});
 
// Função para mostrar modal de detalhes
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
                    <i class="fas fa-video"></i> Entrar na Consulta
                </button>
            </div>
        </div>
    `;
   
    // Fechar modal ao clicar fora
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
   
    document.body.appendChild(modal);
    modal.style.display = 'flex';
}
 
// Função para fechar o modal
function closeModal() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
        modal.remove();
    }
}
 
// Função para iniciar a consulta
function iniciarConsulta(consultaId) {
    // Definir tipo explicitamente como 'user' para clientes
    window.open(`../../videocalls/videocall.php?consulta=${consultaId}&tipo=user`, '_blank',
        'width=1200,height=800,toolbar=0,menubar=0,location=0');
}
 
// Função para atualizar status da consulta (se necessário)
function atualizarStatusConsulta(consultaId, novoStatus) {
    fetch('atualizar_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            consultaId: consultaId,
            status: novoStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualizar interface se necessário
            location.reload();
        } else {
            alert('Erro ao atualizar status da consulta');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao atualizar status da consulta');
    });
}
 
// Exportar funções para uso global
window.mostrarDetalhesConsulta = mostrarDetalhesConsulta;
window.closeModal = closeModal;
window.iniciarConsulta = iniciarConsulta;
window.atualizarStatusConsulta = atualizarStatusConsulta;