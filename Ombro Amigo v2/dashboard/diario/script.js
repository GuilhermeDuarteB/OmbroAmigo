// Funções de utilidade
const fadeIn = (element, duration = 300) => {
    element.style.display = 'block';
    element.style.opacity = 0;
    let start = null;
    
    const animate = (timestamp) => {
        if (!start) start = timestamp;
        const progress = timestamp - start;
        element.style.opacity = Math.min(progress / duration, 1);
        
        if (progress < duration) {
            requestAnimationFrame(animate);
        }
    };
    
    requestAnimationFrame(animate);
};

const fadeOut = (element, duration = 300) => {
    return new Promise(resolve => {
        let start = null;
        
        const animate = (timestamp) => {
            if (!start) start = timestamp;
            const progress = timestamp - start;
            element.style.opacity = Math.max(1 - (progress / duration), 0);
            
            if (progress < duration) {
                requestAnimationFrame(animate);
            } else {
                element.style.display = 'none';
                resolve();
            }
        };
        
        requestAnimationFrame(animate);
    });
};

// Gerenciamento de caracteres
class CharacterCounter {
    constructor(input, currentElement, maxElement, maxChars) {
        this.input = input;
        this.currentElement = currentElement;
        this.maxElement = maxElement;
        this.maxChars = maxChars;
        
        this.init();
    }
    
    init() {
        this.maxElement.textContent = this.maxChars;
        this.update();
        this.input.addEventListener('input', () => this.update());
    }
    
    update() {
        const length = this.input.value.length;
        this.currentElement.textContent = length;
        
        if (length >= this.maxChars * 0.9) {
            this.currentElement.style.color = '#ff6b6b';
        } else if (length >= this.maxChars * 0.7) {
            this.currentElement.style.color = '#ffd43b';
        } else {
            this.currentElement.style.color = '#666';
        }
    }
}

// Inicialização dos contadores
new CharacterCounter(
    document.getElementById('texto'),
    document.getElementById('caractAtual'),
    document.getElementById('caractFinal'),
    500
);

new CharacterCounter(
    document.getElementById('titulo'),
    document.getElementById('caractAtualTitulo'),
    document.getElementById('caractFinalTitulo'),
    25
);

// Gerenciamento de modais
class Modal {
    constructor(element) {
        this.element = element;
        this.isOpen = false;
    }
    
    async open() {
        this.element.style.display = 'block';
        await fadeIn(this.element);
        this.isOpen = true;
    }
    
    async close() {
        await fadeOut(this.element);
        this.isOpen = false;
    }
}

// Eventos
document.getElementById('addText').addEventListener('click', () => {
    const modal = new Modal(document.getElementById('info'));
    modal.open();
});

// Confirmação para apagar todas as entradas
document.getElementById('ApagarTodasEntradas').addEventListener('click', (e) => {
    e.preventDefault();
    if (confirm('Tem certeza que deseja apagar todas as entradas do diário?')) {
        window.location.href = 'entradasDeleteAll.php';
    }
});

// Preview de imagem
document.getElementById('uploadfiles').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.createElement('img');
            preview.src = e.target.result;
            preview.style.maxWidth = '200px';
            preview.style.marginTop = '10px';
            
            const container = document.querySelector('.boxInfo');
            const existingPreview = container.querySelector('img');
            if (existingPreview) {
                container.removeChild(existingPreview);
            }
            container.appendChild(preview);
        };
        reader.readAsDataURL(file);
    }
});

// Funções para os modais de visualização e edição
function showViewModal(titulo, mensagem, imagemSrc) {
    const modal = document.getElementById('diarioContent');
    if (!modal) return;

    const tituloElement = modal.querySelector('#tituloEntradaView');
    const textoElement = modal.querySelector('#textoEntradaView');
    const imgElement = modal.querySelector('#imagemEntradaView');

    if (tituloElement) tituloElement.textContent = titulo;
    if (textoElement) textoElement.value = mensagem;
    
    if (imgElement) {
        if (imagemSrc) {
            imgElement.src = imagemSrc;
            imgElement.style.display = 'block';
        } else {
            imgElement.style.display = 'none';
        }
    }
    
    fadeIn(modal);
}

function showEditModal(id, titulo, mensagem, imagemSrc) {
    const modal = document.getElementById('editContent');
    if (!modal) return;

    // Encontrar elementos dentro do modal
    const tituloInput = modal.querySelector('#tituloEntradaEdit');
    const textoInput = modal.querySelector('#textoEntradaEdit');
    const idInput = modal.querySelector('#idDiario');
    const imgElement = modal.querySelector('#imagemEntrada');

    // Verificar se os elementos existem antes de definir valores
    if (tituloInput) tituloInput.value = titulo;
    if (textoInput) textoInput.value = mensagem;
    if (idInput) idInput.value = id;
    
    // Atualizar contadores se existirem
    const tituloCounter = document.getElementById('caractAtualTituloEdit');
    const textoCounter = document.getElementById('caractAtualEdit');

    if (tituloCounter && tituloInput) {
        tituloCounter.textContent = tituloInput.value.length;
        updateCharacterCount('tituloEntradaEdit', 'caractAtualTituloEdit', 25);
    }

    if (textoCounter && textoInput) {
        textoCounter.textContent = textoInput.value.length;
        updateCharacterCount('textoEntradaEdit', 'caractAtualEdit', 500);
    }
    
    // Gerenciar imagem
    if (imgElement) {
        if (imagemSrc) {
            imgElement.src = imagemSrc;
            imgElement.style.display = 'block';
        } else {
            imgElement.style.display = 'none';
        }
    }
    
    fadeIn(modal);
}

// Função para atualizar contadores com verificação de existência
function updateCharacterCount(inputId, counterId, maxLength) {
    const input = document.getElementById(inputId);
    const counter = document.getElementById(counterId);
    
    if (!input || !counter) return;
    
    const currentLength = input.value.length;
    counter.textContent = currentLength;
    
    if (currentLength >= maxLength * 0.9) {
        counter.style.color = 'red';
    } else if (currentLength >= maxLength * 0.7) {
        counter.style.color = 'orange';
    } else {
        counter.style.color = '#666';
    }
}

// Eventos com verificação de existência
document.addEventListener('DOMContentLoaded', function() {
    // Eventos para os botões de editar
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const titulo = this.dataset.titulo;
            const mensagem = this.dataset.mensagem;
            const imagemSrc = this.dataset.imagem;
            showEditModal(id, titulo, mensagem, imagemSrc);
        });
    });

    // Eventos para os contadores
    const tituloEdit = document.getElementById('tituloEntradaEdit');
    const textoEdit = document.getElementById('textoEntradaEdit');

    if (tituloEdit) {
        tituloEdit.addEventListener('input', function() {
            updateCharacterCount('tituloEntradaEdit', 'caractAtualTituloEdit', 25);
        });
    }

    if (textoEdit) {
        textoEdit.addEventListener('input', function() {
            updateCharacterCount('textoEntradaEdit', 'caractAtualEdit', 500);
        });
    }

    // Evento para o botão de cancelar
    const btnCancelar = document.querySelector('.btn-cancelar');
    if (btnCancelar) {
        btnCancelar.addEventListener('click', function() {
            const modal = document.getElementById('editContent');
            if (modal) fadeOut(modal);
        });
    }

    // Eventos para os botões de visualizar
    document.querySelectorAll('.view-button').forEach(button => {
        button.addEventListener('click', function() {
            const titulo = this.dataset.titulo;
            const mensagem = this.dataset.mensagem;
            const imagemSrc = this.dataset.imagem;
            showViewModal(titulo, mensagem, imagemSrc);
        });
    });

    // Eventos para fechar modais
    document.querySelectorAll('.fa-xmark').forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            const modal = this.closest('.diarioContent, .editContent, .info');
            if (modal) fadeOut(modal);
        });
    });

    // Eventos para botões cancelar
    document.querySelectorAll('.btn-cancelar').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.diarioContent, .editContent, .info');
            if (modal) fadeOut(modal);
        });
    });
});

// Fechar modais ao clicar fora deles
document.addEventListener('click', function(event) {
    const diarioContent = document.getElementById('diarioContent');
    const editContent = document.getElementById('editContent');
    
    if (event.target === diarioContent) {
        fadeOut(diarioContent);
    } else if (event.target === editContent) {
        fadeOut(editContent);
    }
});

// Prevenir que cliques dentro dos modais os fechem
document.querySelectorAll('.diariocontentfunc, .editcontentfunc').forEach(content => {
    content.addEventListener('click', function(event) {
        event.stopPropagation();
    });
});

