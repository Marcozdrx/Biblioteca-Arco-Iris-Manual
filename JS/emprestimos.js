// Arquivo JavaScript para gerenciar empréstimos
// Biblioteca Arco-Íris

// Carregar empréstimos quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    loadEmprestimos();
    setupModalEvents();
});

// Função para carregar empréstimos
function loadEmprestimos() {
    const container = document.getElementById('emprestimosContainer');
    
    fetch('../PHP/get_emprestimos.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showMessage(data.error, 'error');
                return;
            }
            
            if (data.length === 0) {
                container.innerHTML = `
                    <div class="no-emprestimos">
                        <h2>Nenhum empréstimo encontrado</h2>
                        <p>Você ainda não possui empréstimos ativos.</p>
                        <a href="usuario.php" class="btn-voltar">Voltar ao Catálogo</a>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = '';
            data.forEach(emprestimo => {
                const card = createEmprestimoCard(emprestimo);
                container.appendChild(card);
            });
        })
        .catch(error => {
        });
}

// Função para criar card de empréstimo
function createEmprestimoCard(emprestimo) {
    const card = document.createElement('div');
    card.className = 'emprestimo-card';
    
    const statusClass = getStatusClass(emprestimo.status);
    const statusText = getStatusText(emprestimo.status);
    
    let multaHtml = '';
    if (emprestimo.dias_atraso > 0) {
        multaHtml = `
            <div class="multa-info">
                <span class="multa-alert">⚠️ ${emprestimo.dias_atraso} dias de atraso</span>
                <span class="multa-valor">Multa: R$ ${emprestimo.multa_calculada.toFixed(2)}</span>
                ${!emprestimo.multa_paga ? '<button class="btn-pagar-multa" onclick="pagarMulta(' + emprestimo.id + ')">Pagar Multa</button>' : ''}
            </div>
        `;
    }
    
    let renovacaoHtml = '';
    if (emprestimo.status === 'emprestado' && !emprestimo.renovado) {
        renovacaoHtml = `
            <button class="btn-renovar" onclick="renovarEmprestimo(${emprestimo.id})">
                Renovar Empréstimo
            </button>
        `;
    }
    
    card.innerHTML = `
        <img src="${emprestimo.imagem_capa || '../IMG/default-avatar.svg'}" alt="${emprestimo.titulo_livro}" class="book-thumb">
        <div class="book-info">
            <h3>${emprestimo.titulo_livro}</h3>
            <p><strong>Autor:</strong> ${emprestimo.nome_autor || 'Não informado'}</p>
            <p><strong>Categoria:</strong> ${emprestimo.categoria || 'Não informada'}</p>
            <p><strong>Data do Empréstimo:</strong> ${emprestimo.data_emprestimo_formatada}</p>
            <p><strong>Data de Devolução:</strong> ${emprestimo.data_devolucao_prevista_formatada}</p>
            ${emprestimo.data_devolucao_real_formatada ? `<p><strong>Devolvido em:</strong> ${emprestimo.data_devolucao_real_formatada}</p>` : ''}
            <div class="status-badge ${statusClass}">${statusText}</div>
            ${multaHtml}
            ${renovacaoHtml}
        </div>
    `;
    
    return card;
}

// Função para obter classe CSS do status
function getStatusClass(status) {
    switch (status) {
        case 'emprestado': return 'status-emprestado';
        case 'devolvido': return 'status-devolvido';
        case 'atrasado': return 'status-atrasado';
        default: return 'status-default';
    }
}

// Função para obter texto do status
function getStatusText(status) {
    switch (status) {
        case 'emprestado': return 'Emprestado';
        case 'devolvido': return 'Devolvido';
        case 'atrasado': return 'Em Atraso';
        default: return 'Status Desconhecido';
    }
}

// Função para renovar empréstimo
function renovarEmprestimo(emprestimoId) {
    const formData = new FormData();
    formData.append('emprestimo_id', emprestimoId);
    
    fetch('../PHP/renovarEmprestimo.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            loadEmprestimos(); // Recarregar lista
        } else {
            showMessage(data.error, 'error');
        }
    })
    .catch(error => {
        console.error('Erro ao renovar empréstimo:', error);
        showMessage('Erro ao renovar empréstimo', 'error');
    });
}

// Função para pagar multa
function pagarMulta(emprestimoId) {
    const modal = document.getElementById('modalMulta');
    const multaInfo = document.getElementById('multaInfo');
    
    // Buscar informações da multa
    fetch('../PHP/get_emprestimos.php')
        .then(response => response.json())
        .then(data => {
            const emprestimo = data.find(e => e.id == emprestimoId);
            if (emprestimo) {
                multaInfo.innerHTML = `
                    <p><strong>Livro:</strong> ${emprestimo.titulo_livro}</p>
                    <p><strong>Dias de Atraso:</strong> ${emprestimo.dias_atraso}</p>
                    <p><strong>Valor da Multa:</strong> R$ ${emprestimo.multa_calculada.toFixed(2)}</p>
                `;
                modal.style.display = 'block';
                
                // Armazenar ID do empréstimo para uso posterior
                modal.dataset.emprestimoId = emprestimoId;
            }
        })
        .catch(error => {
            console.error('Erro ao buscar informações da multa:', error);
            showMessage('Erro ao carregar informações da multa', 'error');
        });
}

// Configurar eventos do modal
function setupModalEvents() {
    const modal = document.getElementById('modalMulta');
    const closeBtn = document.getElementById('closeModalMulta');
    const cancelBtn = document.getElementById('cancelarPagamento');
    const confirmBtn = document.getElementById('confirmarPagamento');
    const qrcodeContainer = document.getElementById('qrcodeContainer');
    const closeQrcodeBtn = document.getElementById('closeQrcode');
    
    // Opções de pagamento
    const paymentOptions = document.querySelectorAll('.payment-option');
    let selectedMethod = null;
    
    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            paymentOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            selectedMethod = this.dataset.method;
        });
    });
    
    // Fechar modal
    closeBtn.onclick = function() {
        modal.style.display = 'none';
        qrcodeContainer.style.display = 'none';
    }
    
    cancelBtn.onclick = function() {
        modal.style.display = 'none';
        qrcodeContainer.style.display = 'none';
    }
    
    // Confirmar pagamento
    confirmBtn.onclick = function() {
        if (!selectedMethod) {
            showMessage('Selecione um método de pagamento', 'error');
            return;
        }
        
        const emprestimoId = modal.dataset.emprestimoId;
        const formData = new FormData();
        formData.append('emprestimo_id', emprestimoId);
        formData.append('metodo_pagamento', selectedMethod);
        
        fetch('../PHP/pagarMulta.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                modal.style.display = 'none';
                qrcodeContainer.style.display = 'none';
                loadEmprestimos(); // Recarregar lista
            } else {
                showMessage(data.error, 'error');
            }
        })
        .catch(error => {
            console.error('Erro ao processar pagamento:', error);
            showMessage('Erro ao processar pagamento', 'error');
        });
    }
    
    // Fechar QR Code
    closeQrcodeBtn.onclick = function() {
        qrcodeContainer.style.display = 'none';
    }
    
    // Fechar modal ao clicar fora
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
            qrcodeContainer.style.display = 'none';
        }
    }
}

// Função para mostrar mensagens
function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    messageDiv.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    
    if (type === 'success') {
        messageDiv.style.backgroundColor = '#4CAF50';
    } else if (type === 'error') {
        messageDiv.style.backgroundColor = '#f44336';
    }
    
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            messageDiv.remove();
        }, 300);
    }, 3000);
}

// Adicionar estilos CSS para animações
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    
    .no-emprestimos {
        text-align: center;
        padding: 40px;
        background: white;
        border-radius: 10px;
        margin: 20px;
    }
    
    .btn-voltar {
        display: inline-block;
        background: #ff9100;
        color: white;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        margin-top: 20px;
    }
    
    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: bold;
        margin-top: 10px;
    }
    
    .status-emprestado {
        background: #4CAF50;
        color: white;
    }
    
    .status-devolvido {
        background: #2196F3;
        color: white;
    }
    
    .status-atrasado {
        background: #f44336;
        color: white;
    }
    
    .multa-info {
        margin-top: 10px;
        padding: 10px;
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 5px;
    }
    
    .multa-alert {
        color: #856404;
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }
    
    .multa-valor {
        color: #856404;
        display: block;
        margin-bottom: 10px;
    }
    
    .btn-pagar-multa, .btn-renovar {
        background: #ff9100;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        margin-right: 10px;
    }
    
    .btn-pagar-multa:hover, .btn-renovar:hover {
        background: #e67e00;
    }
    
    .payment-option {
        display: inline-block;
        margin: 10px;
        padding: 15px;
        border: 2px solid #ddd;
        border-radius: 10px;
        cursor: pointer;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .payment-option.selected {
        border-color: #ff9100;
        background: #fff3cd;
    }
    
    .payment-option img {
        width: 40px;
        height: 40px;
        margin-bottom: 5px;
    }
`;
document.head.appendChild(style);
