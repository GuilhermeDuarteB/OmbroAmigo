let localStream;
let peerConnection;
const ACS_ENDPOINT = 'ombroamigo-calls.unitedstates.communication.azure.com';
const ACS_CREDENTIAL = 'YC4NNvgEJwKhADhuCW6vuA4wtuu7hOTS9CLUDnt6QbiHDSV4IEMgJQQJ99BBACULyCpIwRSxAAAAAZCS7CBO';

const configuration = {
    iceServers: [
        // Servidores STUN básicos como fallback
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' },
        
        // TURN via UDP
        {
            urls: [`turn:${ACS_ENDPOINT}:3478?transport=udp`],
            username: ACS_CREDENTIAL,
            credential: ACS_CREDENTIAL
        },
        // TURN via TCP
        {
            urls: [`turn:${ACS_ENDPOINT}:3478?transport=tcp`],
            username: ACS_CREDENTIAL,
            credential: ACS_CREDENTIAL
        }
    ]
};

let screenStream = null;
let isScreenSharing = false;
let iceCandidatesBuffer = [];

// Variáveis para controle de reconexão
let isReconnecting = false;
let reconnectionAttempts = 0;
const MAX_RECONNECTION_ATTEMPTS = 5;
const RECONNECTION_DELAY = 2000; // 2 segundos

//webrtc API
//Entrar sempre no site com https senao nao funciona e com o firewall desligado



// iniciar vídeo local
async function iniciarVideo() {
    try {
        localStream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: true
        });
        
        // Mostrar vídeo local
        const localVideo = document.getElementById('localVideo');
        localVideo.srcObject = localStream;
        
        console.log('Vídeo local iniciado');
        
        // Iniciar conexão peer apenas após ter o vídeo local
        await iniciarConexaoPeer();
        
    } catch (error) {
        console.error('Erro ao iniciar vídeo:', error);
        alert('Erro ao acessar câmera: ' + error.message);
    }
}

async function iniciarConexaoPeer() {
    try {
        peerConnection = new RTCPeerConnection(configuration);
        
        // Adicionar tracks locais
        localStream.getTracks().forEach(track => {
            peerConnection.addTrack(track, localStream);
        });
        
        // Configurar handlers de eventos
        configurarEventHandlers();
        
        // Iniciar oferta se for o profissional
        if (tipoUsuario === 'profissional') {
            console.log('Criando oferta como profissional');
            await criarOfertar();
        }
        
    } catch (error) {
        console.error('Erro ao iniciar peer connection:', error);
    }
}

async function criarOfertar() {
    try {
        const oferta = await peerConnection.createOffer();
        await peerConnection.setLocalDescription(oferta);
        
        console.log('Oferta criada e enviando');
        enviarSinalização({
            type: 'offer',
            offer: oferta,
            consultaId: consultaId
        });
    } catch (error) {
        console.error('Erro ao criar oferta:', error);
    }
}

async function receberOferta(oferta) {
    try {
        console.log('Recebendo oferta');
        await peerConnection.setRemoteDescription(new RTCSessionDescription(oferta));
        
        const resposta = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(resposta);
        
        console.log('Enviando resposta');
        enviarSinalização({
            type: 'answer',
            answer: resposta,
            consultaId: consultaId
        });
    } catch (error) {
        console.error('Erro ao receber oferta:', error);
    }
}

function enviarSinalização(data) {
    // Adicionar idDestinatario e tipos com base no tipoUsuario atual
    const dadosCompletos = {
        ...data,
        idDestinatario: tipoUsuario === 'user' ? 
            data.idDestinatario || consulta.IdProfissional : 
            data.idDestinatario || consulta.IdUtilizador,
        tipoRemetente: tipoUsuario,
        tipoDestinatario: tipoUsuario === 'user' ? 'profissional' : 'user'
    };

    console.log('Enviando sinalização completa:', dadosCompletos);

    fetch('sinalizacao.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(dadosCompletos)
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error('Erro na sinalização: ' + text);
            });
        }
        return response.json();
    })
    .then(data => {
        if (!data.success) {
            throw new Error(data.message);
        }
        console.log('Sinalização enviada:', data);
    })
    .catch(error => console.error('Erro ao enviar sinalização:', error));
}

// Verificar sinalizações a cada 1 segundo
function verificarSinalizacoes() {
    fetch(`buscar_sinalizacao.php?consultaId=${consultaId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.sinalizacao) {
                console.log('Sinalização recebida:', data.sinalizacao);
                const sinal = data.sinalizacao;
                
                processarSinalizacao(sinal);
            }
        })
        .catch(error => console.error('Erro ao verificar sinalizações:', error));
}

// Atualizar a função de processamento de sinalizações
function processarSinalizacao(sinal) {
    try {
        switch (sinal.type) {
            case 'offer':
                console.log('Processando oferta');
                receberOferta(sinal.offer);
                break;
            case 'answer':
                console.log('Processando resposta');
                if (peerConnection.signalingState !== 'stable') {
                    peerConnection.setRemoteDescription(new RTCSessionDescription(sinal.answer))
                        .then(() => {
                            // Processar ICE candidates em buffer
                            while (iceCandidatesBuffer.length > 0) {
                                const candidate = iceCandidatesBuffer.shift();
                                peerConnection.addIceCandidate(new RTCIceCandidate(candidate))
                                    .catch(e => console.error('Erro ao adicionar ICE candidate do buffer:', e));
                            }
                        })
                        .catch(e => console.error('Erro ao definir descrição remota:', e));
                }
                break;
            case 'ice-candidate':
                console.log('Processando ICE candidate');
                if (sinal.candidate) {
                    if (peerConnection.remoteDescription) {
                        peerConnection.addIceCandidate(new RTCIceCandidate(sinal.candidate))
                            .catch(e => console.error('Erro ao adicionar ICE candidate:', e));
                    } else {
                        console.log('Armazenando ICE candidate no buffer');
                        iceCandidatesBuffer.push(sinal.candidate);
                    }
                }
                break;
            case 'chat':
                receberMensagem(sinal.message);
                break;
            case 'video-state':
                const remoteVideoWrapper = document.getElementById('remoteVideo').parentElement;
                remoteVideoWrapper.classList.toggle('video-off', !sinal.enabled);
                break;
        }
    } catch (error) {
        console.error('Erro ao processar sinalização:', error);
    }
}

// iniciar
document.addEventListener('DOMContentLoaded', () => {
    console.log('Iniciando aplicação');
    iniciarVideo();
    setInterval(verificarSinalizacoes, 1000);

    // Chat toggle com ajuste do container de vídeo
    const toggleChat = document.getElementById('toggleChat');
    const chatContainer = document.querySelector('.chat-container');
    const videoContainer = document.querySelector('.video-container');
    
    toggleChat.addEventListener('click', () => {
        chatContainer.classList.toggle('hidden');
        videoContainer.classList.toggle('chat-hidden');
        toggleChat.querySelector('i').classList.toggle('fa-chevron-right');
        toggleChat.querySelector('i').classList.toggle('fa-chevron-left');
    });

    // Controles de vídeo e áudio
    const toggleVideo = document.getElementById('toggleVideo');
    const toggleAudio = document.getElementById('toggleAudio');
    const endCall = document.getElementById('endCall');

    toggleVideo.addEventListener('click', () => {
        const videoTrack = localStream.getVideoTracks()[0];
        if (videoTrack) {
            videoTrack.enabled = !videoTrack.enabled;
            toggleVideo.querySelector('i').classList.toggle('fa-video');
            toggleVideo.querySelector('i').classList.toggle('fa-video-slash');
            
            // Atualizar visibilidade do vídeo local e mostrar/esconder foto
            const localVideoWrapper = document.getElementById('localVideo').parentElement;
            localVideoWrapper.classList.toggle('video-off', !videoTrack.enabled);
            
            // Enviar estado do vídeo para o outro participante
            enviarSinalização({
                type: 'video-state',
                enabled: videoTrack.enabled,
                consultaId: consultaId
            });
        }
    });

    toggleAudio.addEventListener('click', () => {
        const audioTrack = localStream.getAudioTracks()[0];
        if (audioTrack) {
            audioTrack.enabled = !audioTrack.enabled;
            toggleAudio.querySelector('i').classList.toggle('fa-microphone');
            toggleAudio.querySelector('i').classList.toggle('fa-microphone-slash');
        }
    });

    endCall.addEventListener('click', async () => {
        console.log('Botão de encerrar clicado'); // Debug
        
        if (confirm('Deseja realmente acabar a consulta?')) {
            console.log('Usuário confirmou'); // Debug
            
            try {
                if (tipoUsuario === 'profissional') {
                    console.log('Usuário é profissional, acabando consulta...'); // Debug
                    
                    // Aguarda a resposta da finalização da consulta
                    const response = await fetch('acabar_consulta.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `consultaId=${consultaId}`
                    });

                    const data = await response.json();
                    console.log('Resposta do servidor:', data); // Debug
                    
                    if (!data.success) {
                        throw new Error(data.message || 'Erro ao acabar consulta');
                    }
                    
                    console.log('Consulta acabada com sucesso');
                    alert('Consulta acabada com sucesso!');
                }
                
                // Encerra a chamada
                encerrarChamada();
                
                // Delay maior para garantir que tudo foi processado
                setTimeout(() => {
                    console.log('Fechando janela...'); // Debug
                    window.close();
                }, 1000);
                
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao acabar consulta: ' + error.message);
                encerrarChamada();
                window.close();
            }
        } else {
            console.log('Usuário cancelou'); // Debug
        }
    });

    // Chat input
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.querySelector('.chat-input button');
    
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            enviarMensagem();
        }
    });

    sendButton.addEventListener('click', () => {
        enviarMensagem();
    });

    // iniciar chat e atualização periódica
    atualizarChat();
    setInterval(atualizarChat, 3000);

    // Inicialização do botão de compartilhamento de tela
    const shareScreenBtn = document.getElementById('shareScreen');
    if (shareScreenBtn) {
        shareScreenBtn.addEventListener('click', async () => {
            console.log('Botão de compartilhamento clicado'); // Debug
            
            try {
                if (!isScreenSharing) {
                    console.log('Iniciando compartilhamento de tela'); // Debug
                    
                    // Verificar suporte ao compartilhamento de tela
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getDisplayMedia) {
                        throw new Error('Compartilhamento de tela não é suportado neste navegador');
                    }

                    // Iniciar compartilhamento
                    screenStream = await navigator.mediaDevices.getDisplayMedia({
                        video: {
                            cursor: "always"
                        },
                        audio: false
                    });

                    console.log('Tela capturada com sucesso'); // Debug

                    // Substituir o video track atual
                    const videoSender = peerConnection
                        .getSenders()
                        .find(sender => sender.track && sender.track.kind === 'video');

                    if (videoSender) {
                        const screenTrack = screenStream.getVideoTracks()[0];
                        await videoSender.replaceTrack(screenTrack);

                        // Atualizar vídeo local
                        const localVideo = document.getElementById('localVideo');
                        localVideo.srcObject = screenStream;

                        // Eventos para quando o usuário parar o compartilhamento
                        screenTrack.onended = async () => {
                            console.log('Compartilhamento de tela encerrado pelo usuário'); // Debug
                            await stopScreenSharing();
                        };

                        isScreenSharing = true;
                        shareScreenBtn.querySelector('i').classList.replace('fa-desktop', 'fa-stop-circle');
                        shareScreenBtn.classList.add('sharing');
                    }
                } else {
                    console.log('Parando compartilhamento de tela'); // Debug
                    await stopScreenSharing();
                }
            } catch (error) {
                console.error('Erro ao compartilhar tela:', error);
                alert('Erro ao compartilhar tela: ' + error.message);
            }
        });
    }
});

// Função para enviar mensagem
function enviarMensagem(mensagemInput = null) {
    const messageInput = document.getElementById('messageInput');
    const mensagem = mensagemInput || messageInput.value;
    
    if (!mensagem || !mensagem.trim()) return;

    fetch('enviar_mensagem.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            consultaId: consultaId,
            mensagem: mensagem.trim()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            atualizarChat();
        } else {
            console.error('Erro ao enviar mensagem:', data.message);
        }
    })
    .catch(error => console.error('Erro ao enviar mensagem:', error));
}

let lastMessageTimestamp = '';

function atualizarChat() {
    fetch(`buscar_mensagens.php?consultaId=${consultaId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const chatMessages = document.getElementById('chatMessages');
                
                // Verifica se há novas mensagens comparando a última mensagem
                const lastMessage = data.mensagens[data.mensagens.length - 1];
                if (!lastMessage || lastMessage.DataHoraFormatada === lastMessageTimestamp) {
                    return; // Não atualiza se não houver novas mensagens
                }
                
                lastMessageTimestamp = lastMessage.DataHoraFormatada;
                
                // Salva a posição atual do scroll
                const wasScrolledToBottom = chatMessages.scrollHeight - chatMessages.clientHeight <= chatMessages.scrollTop + 1;
                
                chatMessages.innerHTML = '';
                
                data.mensagens.forEach(msg => {
                    const messageDiv = document.createElement('div');
                    
                    const isSent = (tipoUsuario === 'user' && msg.EnviadoPor === 'user') || 
                                 (tipoUsuario === 'profissional' && msg.EnviadoPor === 'profissional');
                    
                    messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
                    
                    const time = new Date(msg.DataHoraFormatada).toLocaleTimeString('pt-BR', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    let senderName;
                    if (isSent) {
                        senderName = 'Você';
                    } else {
                        senderName = msg.EnviadoPor === 'user' ? msg.NomeUser : msg.NomeProfissional;
                    }
                    
                    messageDiv.innerHTML = `
                        <div class="message-sender">${escapeHtml(senderName)}</div>
                        <div class="message-content">${escapeHtml(msg.Mensagem)}</div>
                        <div class="message-time">${time}</div>
                    `;
                    
                    chatMessages.appendChild(messageDiv);
                });
                
                // Só rola para baixo se já estava no final
                if (wasScrolledToBottom) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }
        })
        .catch(error => console.error('Erro ao buscar mensagens:', error));
}

// Função auxiliar para escapar HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Função para parar o compartilhamento de tela
async function stopScreenSharing() {
    console.log('Função stopScreenSharing chamada'); // Debug
    
    try {
        if (screenStream) {
            screenStream.getTracks().forEach(track => track.stop());
            screenStream = null;

            // Restaurar câmera
            const videoSender = peerConnection
                .getSenders()
                .find(sender => sender.track && sender.track.kind === 'video');

            if (videoSender && localStream) {
                const videoTrack = localStream.getVideoTracks()[0];
                if (videoTrack) {
                    await videoSender.replaceTrack(videoTrack);

                    // Restaurar vídeo local
                    const localVideo = document.getElementById('localVideo');
                    localVideo.srcObject = localStream;
                }
            }
        }

        isScreenSharing = false;
        const shareScreenBtn = document.getElementById('shareScreen');
        if (shareScreenBtn) {
            shareScreenBtn.querySelector('i').classList.replace('fa-stop-circle', 'fa-desktop');
            shareScreenBtn.classList.remove('sharing');
        }
        
        console.log('Compartilhamento de tela parado com sucesso'); // Debug
    } catch (error) {
        console.error('Erro ao parar compartilhamento:', error);
    }
}

// Função para encerrar a chamada
function encerrarChamada() {
    console.log('Encerrando streams...'); // Debug
    if (screenStream) {
        screenStream.getTracks().forEach(track => track.stop());
    }
    if (peerConnection) {
        peerConnection.close();
    }
    if (localStream) {
        localStream.getTracks().forEach(track => track.stop());
    }
}

// Função para monitorar estado da conexão
peerConnection.oniceconnectionstatechange = () => {
    console.log('ICE Connection State:', peerConnection.iceConnectionState);
    
    switch (peerConnection.iceConnectionState) {
        case 'disconnected':
        case 'failed':
            if (!isReconnecting) {
                console.log('Conexão perdida. Tentando reconectar...');
                tentarReconectar();
            }
            break;
        case 'connected':
        case 'completed':
            console.log('Conexão estabelecida/restaurada');
            isReconnecting = false;
            reconnectionAttempts = 0;
            break;
    }
};

// Função para tentar reconexão
async function tentarReconectar() {
    if (reconnectionAttempts >= MAX_RECONNECTION_ATTEMPTS) {
        console.log('Número máximo de tentativas de reconexão atingido');
        alert('Não foi possível reconectar. Por favor, recarregue a página.');
        return;
    }

    isReconnecting = true;
    reconnectionAttempts++;

    try {
        console.log(`Tentativa de reconexão ${reconnectionAttempts}/${MAX_RECONNECTION_ATTEMPTS}`);

        // Fechar conexão antiga
        if (peerConnection) {
            peerConnection.close();
        }

        // Criar nova conexão
        peerConnection = new RTCPeerConnection(configuration);
        
        // Readicionar streams locais
        localStream.getTracks().forEach(track => {
            peerConnection.addTrack(track, localStream);
        });

        // Reconfigurar handlers de eventos
        configurarEventHandlers();

        // Se for o profissional, reiniciar a oferta
        if (tipoUsuario === 'profissional') {
            await criarOfertar();
        }

        // Aguardar antes da próxima tentativa
        setTimeout(() => {
            if (peerConnection.iceConnectionState !== 'connected' && 
                peerConnection.iceConnectionState !== 'completed') {
                tentarReconectar();
            }
        }, RECONNECTION_DELAY);

    } catch (error) {
        console.error('Erro na tentativa de reconexão:', error);
        setTimeout(tentarReconectar, RECONNECTION_DELAY);
    }
}

// Função para configurar handlers de eventos
function configurarEventHandlers() {
    // Handler para vídeo remoto
    peerConnection.ontrack = event => {
        console.log('Track remoto recebido:', event.track.kind);
        const remoteVideo = document.getElementById('remoteVideo');
        remoteVideo.srcObject = event.streams[0];
    };

    // Handler para ICE candidates
    peerConnection.onicecandidate = event => {
        if (event.candidate) {
            console.log('Enviando ICE candidate');
            enviarSinalização({
                type: 'ice-candidate',
                candidate: event.candidate,
                consultaId: consultaId
            });
        }
    };

    // Handler para mudanças de estado da conexão
    peerConnection.onconnectionstatechange = () => {
        console.log('Connection State:', peerConnection.connectionState);
    };
}