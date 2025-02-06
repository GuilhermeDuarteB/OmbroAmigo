// Configuração do PDF.js
const pdfjsLib = window['pdfjsLib'];
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

let currentProfId = null;
const profissionaisStatus = {};

// Função para renderizar o PDF
function renderPDF(base64Data, canvas) {
    const pdfData = atob(base64Data);
    const loadingTask = pdfjsLib.getDocument({ data: pdfData });

    loadingTask.promise.then(pdfDoc => {
        pdfDoc.getPage(1).then(page => {
            const containerWidth = canvas.parentElement.clientWidth;
            const viewport = page.getViewport({ scale: 1.0 });
            
            // Calcular a escala para ajustar ao container
            const scale = (containerWidth - 40) / viewport.width;
            const scaledViewport = page.getViewport({ scale });

            canvas.height = scaledViewport.height;
            canvas.width = scaledViewport.width;

            page.render({
                canvasContext: canvas.getContext('2d'),
                viewport: scaledViewport
            });
        });
    }).catch(error => {
        console.error("Erro ao carregar o PDF:", error);
    });
}

function initializeProfissionalStatus(profId) {
    if (!profissionaisStatus[profId]) {
        profissionaisStatus[profId] = {
            cvChecked: false,
            diplomaChecked: false
        };
    }
}

function checkScrollNeeded(pdfDiv, acceptBtn) {
    // Verifica se existe necessidade de scroll
    const needsScroll = pdfDiv.scrollHeight > pdfDiv.clientHeight;
    
    if (!needsScroll) {
        // Se não precisar de scroll, habilita o botão imediatamente
        acceptBtn.disabled = false;
        acceptBtn.classList.add('enabled');
        return true;
    }
    return false;
}

function checkScrollEnd(pdfDiv, acceptBtn) {
    if (!pdfDiv || !acceptBtn) return;
    
    // Primeiro verifica se precisa de scroll
    if (checkScrollNeeded(pdfDiv, acceptBtn)) return;
    
    const scrollTolerance = 5;
    const isAtBottom = (pdfDiv.scrollHeight - pdfDiv.scrollTop - pdfDiv.clientHeight) <= scrollTolerance;
    
    if (isAtBottom) {
        acceptBtn.disabled = false;
        acceptBtn.classList.add('enabled');
    }
}

// Eventos para os botões de CV
document.querySelectorAll('.cvbtn').forEach(button => {
    button.addEventListener("click", () => {
        const cvData = button.getAttribute("data-cv");
        currentProfId = button.dataset.profId;
        
        if (cvData) {
            const canvas = document.getElementById('pdf-canvas-cv');
            const modal = document.getElementById('showCV');
            const acceptBtn = document.getElementById('acceptCVBtn');
            const pdfDiv = document.getElementById('pdfCV');
            
            if (canvas && modal && acceptBtn && pdfDiv) {
                renderPDF(cvData, canvas);
                modal.classList.add('visible');
                acceptBtn.disabled = true;
                acceptBtn.classList.remove('enabled');
                
                // Verifica após o PDF ser renderizado
                setTimeout(() => {
                    checkScrollNeeded(pdfDiv, acceptBtn);
                }, 500);
                
                pdfDiv.addEventListener('scroll', () => checkScrollEnd(pdfDiv, acceptBtn));
            }
        }
    });
});

// Eventos para os botões de Diploma
document.querySelectorAll('.diplomabtn').forEach(button => {
    button.addEventListener("click", () => {
        const diplomaData = button.getAttribute("data-diploma");
        currentProfId = button.dataset.profId;
        
        if (diplomaData) {
            const canvas = document.getElementById('pdf-canvas-diploma');
            const modal = document.getElementById('showDiploma');
            const acceptBtn = document.getElementById('acceptDiplomaBtn');
            const pdfDiv = document.getElementById('pdfDiploma');
            
            if (canvas && modal && acceptBtn && pdfDiv) {
                renderPDF(diplomaData, canvas);
                modal.classList.add('visible');
                acceptBtn.disabled = true;
                acceptBtn.classList.remove('enabled');
                
                // Verifica após o PDF ser renderizado
                setTimeout(() => {
                    checkScrollNeeded(pdfDiv, acceptBtn);
                }, 500);
                
                pdfDiv.addEventListener('scroll', () => checkScrollEnd(pdfDiv, acceptBtn));
            }
        }
    });
});

// Eventos para fechar os modais
const closeCV = document.getElementById('closeCV');
const closeDiploma = document.getElementById('closeDiploma');

if (closeCV) {
    closeCV.addEventListener('click', () => {
        document.getElementById('showCV')?.classList.remove('visible');
    });
}

if (closeDiploma) {
    closeDiploma.addEventListener('click', () => {
        document.getElementById('showDiploma')?.classList.remove('visible');
    });
}

// Eventos para aceitar documentos
const acceptCVBtn = document.getElementById('acceptCVBtn');
const acceptDiplomaBtn = document.getElementById('acceptDiplomaBtn');

if (acceptCVBtn) {
    acceptCVBtn.addEventListener("click", () => {
        if (!currentProfId) return;
        
        initializeProfissionalStatus(currentProfId);
        profissionaisStatus[currentProfId].cvChecked = true;
        
        document.getElementById('showCV')?.classList.remove('visible');
        document.querySelector(`.cvbtn[data-prof-id="${currentProfId}"]`)?.classList.add('verified');
        
        updateButtons(currentProfId);
    });
}

if (acceptDiplomaBtn) {
    acceptDiplomaBtn.addEventListener("click", () => {
        if (!currentProfId) return;
        
        initializeProfissionalStatus(currentProfId);
        profissionaisStatus[currentProfId].diplomaChecked = true;
        
        document.getElementById('showDiploma')?.classList.remove('visible');
        document.querySelector(`.diplomabtn[data-prof-id="${currentProfId}"]`)?.classList.add('verified');
        
        updateButtons(currentProfId);
    });
}

function updateButtons(profId) {
    const status = profissionaisStatus[profId];
    const acceptProf = document.getElementById(`acceptProf_${profId}`);
    const refuseProf = document.getElementById(`refuseProf_${profId}`);
    
    if (acceptProf && refuseProf && status.cvChecked && status.diplomaChecked) {
        acceptProf.disabled = false;
        refuseProf.disabled = false;
    }
}
