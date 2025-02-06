document.addEventListener('DOMContentLoaded', function () {
    // Verificação de login
    fetch('verificar_login.php')
        .then(response => response.json())
        .then(data => {
            if (!data.logado) {
                window.location.href = '../login/index.html';
                return;
            }
        })
        .catch(error => {
            console.error('Erro ao verificar login:', error);
            window.location.href = '../login/index.html';
        });

    // Obter parâmetros da URL
    const urlParams = new URLSearchParams(window.location.search);
    const plano = urlParams.get('plano');
    const planoId = urlParams.get('IdSubscricao');
    const preco = urlParams.get('preco');
    const nome = urlParams.get('nome');
    const ciclo = urlParams.get('ciclo');

    // Preencher informações do plano
    document.getElementById('planoNome').textContent = decodeURIComponent(nome) || 'Plano não especificado';
    document.getElementById('planoPreco').textContent = preco ? `€${preco}` : '€0.00';
    document.getElementById('planoCiclo').textContent = `Ciclo: ${ciclo || '-'}`;
    document.getElementById('totalAmount').textContent = preco ? `€${preco}` : '€0.00';

    // Manipular seleção de método de pagamento
    const metodoPagamentoRadios = document.querySelectorAll('input[name="metodoPagamento"]');
    const cartaoDetails = document.getElementById('cartaoDetails');
    const mbwayDetails = document.getElementById('mbwayDetails');

    metodoPagamentoRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            // Resetar detalhes de pagamento
            cartaoDetails.style.display = 'none';
            mbwayDetails.style.display = 'none';

            // Exibir detalhes com base no método selecionado
            if (this.value === 'cartao_credito') {
                cartaoDetails.style.display = 'block';
            } else if (this.value === 'mbway') {
                mbwayDetails.style.display = 'block';
            }
        });
    });

    // Validação e envio do formulário
    const form = document.getElementById('paymentForm');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const metodoPagamento = document.querySelector('input[name="metodoPagamento"]:checked');
        if (!metodoPagamento) {
            alert('Por favor, selecione um método de pagamento');
            return;
        }

        // Criar dados para envio
        const dados = new URLSearchParams();
        dados.append('planoId', planoId);
        dados.append('valor', preco);
        dados.append('metodoPagamento', metodoPagamento.value);

        // Debug - verificar dados antes do envio
        console.log('Dados a enviar:', {
            planoId: dados.get('planoId'),
            valor: dados.get('valor'),
            metodoPagamento: dados.get('metodoPagamento')
        });

        // Enviar usando fetch
        fetch('subscrever.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: dados.toString()
        })
            .then(response => response.json())
            .then(data => {
                console.log('Resposta do servidor:', data);
                if (data.success) {
                    window.location.href = '../dashboard/settings/index.php';
                } else {
                    const errorMessage = data.message +
                        (data.errors ? '\n\nDetalhes:\n' + data.errors.join('\n') : '') +
                        '\n\nDados enviados:\n' + dados.toString();
                    console.error('Erro detalhado:', {
                        message: data.message,
                        errors: data.errors,
                        sentData: dados.toString()
                    });
                    alert(errorMessage);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao processar o pagamento. Por favor, tente novamente.');
            });
    });
});