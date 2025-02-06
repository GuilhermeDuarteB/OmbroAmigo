document.addEventListener("DOMContentLoaded", function() {
    const pfpMenu = document.getElementById("pfpMenu");
    const fotoUser = document.getElementById("fotoUser");
    const sairMenu = document.getElementById("sair");
    const filebox = document.getElementById("filebox");
    const fileInput = document.getElementById("fileInput");

    // Garante que o menu começa oculto
    if (pfpMenu) {
        pfpMenu.style.display = 'none';
    }

    function toggleMenu() {
        if (pfpMenu.style.display === 'none') {
            pfpMenu.style.display = 'block';
        } else {
            closeMenu();
        }
    }

    function closeMenu() {
        if (pfpMenu) {
            pfpMenu.style.display = 'none';
        }
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
            const form = e.target.closest('form');
            if (form && fileInput.files.length > 0) {
                form.submit();
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
        if (p.id !== 'editFotoP') {  // Não oculta o texto de editar foto
            p.style.display = 'none';
            if (inputElements[index]) {
                inputElements[index].style.display = 'block';
            }
        }
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
