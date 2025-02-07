document.addEventListener("DOMContentLoaded", function() {
    let pfpMenu = document.getElementById("pfpMenu");
    let pfPhoto = document.getElementById("pfp");
    let sairMenu = document.getElementById("sair");
    let filebox = document.getElementById("filebox");
    let fileInput = document.getElementById("fileInput");
    let btnEdit = document.getElementById("editarPerfil");
    let fotoUser = document.getElementById("fotoUser");

    // Garante que o menu começa oculto
    pfpMenu.classList.add('hidden');
    pfpMenu.style.display = 'none';

    function toggleMenu() {
        if (pfpMenu.classList.contains('hidden')) {
            pfpMenu.style.display = 'block';
            setTimeout(() => {
                pfpMenu.classList.remove('hidden');
                pfpMenu.classList.add('visible');
            }, 10);
        } else {
            closeMenu();
        }
    }   

    function closeMenu() {
        pfpMenu.classList.remove('visible');
        pfpMenu.classList.add('hidden');
        setTimeout(() => {
            pfpMenu.style.display = 'none';
        }, 300);
    }

    // Event Listeners
    if (sairMenu) {
        sairMenu.addEventListener("click", closeMenu);
    }
    
    if (fotoUser) {
        fotoUser.addEventListener("click", function(e) {
            // Só permite abrir o menu se estiver em modo de edição
            if (document.getElementById('salvarPerfil').style.display === 'block') {
                toggleMenu();
            }
        });
    }

    // Eventos de drag and drop
    if (filebox) {
        filebox.addEventListener('dragover', (e) => {
            e.preventDefault();
            filebox.classList.add('dragover');
        });

        filebox.addEventListener('dragleave', () => {
            filebox.classList.remove('dragover');
        });

        filebox.addEventListener('drop', (e) => {
            e.preventDefault();
            filebox.classList.remove('dragover');
            
            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                const form = e.target.closest('form');
                if (form) form.submit();
            }
        });

        filebox.addEventListener('click', () => {
            fileInput.click();
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                // Verificar tamanho (máximo 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('A imagem deve ter no máximo 5MB');
                    e.target.value = '';
                    return;
                }

                // Verificar tipo
                if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
                    alert('Apenas imagens JPEG, PNG e GIF são permitidas');
                    e.target.value = '';
                    return;
                }

                const form = e.target.closest('form');
                if (form) {
                    form.submit();
                }
            }
        });
    }
});

function EditarPerfil() {
    const infoElements = document.querySelectorAll('.menuInfo p');
    const inputElements = document.querySelectorAll('.menuInfo input');
    const editarBtn = document.getElementById('editarPerfil');
    const salvarBtn = document.getElementById('salvarPerfil');
    const fotoUser = document.getElementById("fotoUser");
    const editFotoP = document.getElementById("editFotoP");

    infoElements.forEach((p, index) => {
        p.style.display = 'none';
        inputElements[index].style.display = 'block';
    });

    if (editarBtn && salvarBtn) {
        editarBtn.style.display = 'none';
        salvarBtn.style.display = 'block';
    }

    if (fotoUser) {
        fotoUser.style.cursor = 'pointer';
        fotoUser.title = 'Clique para editar a foto';
    }
    if (editFotoP) editFotoP.style.display = "block";
}

// Ocultar descrição se necessário
let descricao = document.getElementById("descricao");
if (descricao && (descricao.textContent.trim() === "Hoje eu estou" || 
                  descricao.textContent.trim() === "Hoje eu estou nResponder")) {
    descricao.style.display = "none";
}