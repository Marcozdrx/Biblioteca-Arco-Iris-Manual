// Arquivo JavaScript comum para funcionalidades básicas
// Biblioteca Arco-Íris

// Função para alternar visibilidade da senha
function togglePassword(button) {
    const input = button.previousElementSibling;
    if (input.type === 'password') {
        input.type = 'text';
        button.textContent = '🙈';
    } else {
        input.type = 'password';
        button.textContent = '👁️';
    }
}

// Função para formatar CPF
function formatCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
}

// Função para formatar telefone
function formatPhone(phone) {
    phone = phone.replace(/\D/g, '');
    if (phone.length === 11) {
        return phone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (phone.length === 10) {
        return phone.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    }
    return phone;
}

// Função para validar email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Função para validar CPF
function validateCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    if (cpf.length !== 11) return false;
    
    // Verificar se todos os dígitos são iguais
    if (/^(\d)\1{10}$/.test(cpf)) return false;
    
    // Validar dígitos verificadores
    let sum = 0;
    for (let i = 0; i < 9; i++) {
        sum += parseInt(cpf.charAt(i)) * (10 - i);
    }
    let remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.charAt(9))) return false;
    
    sum = 0;
    for (let i = 0; i < 10; i++) {
        sum += parseInt(cpf.charAt(i)) * (11 - i);
    }
    remainder = (sum * 10) % 11;
    if (remainder === 10 || remainder === 11) remainder = 0;
    if (remainder !== parseInt(cpf.charAt(10))) return false;
    
    return true;
}

// Função para mostrar mensagens de erro
function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.cssText = 'color: red; text-align: center; margin: 10px 0; padding: 10px; background: #ffe6e6; border-radius: 5px;';
    errorDiv.textContent = message;
    
    const form = document.querySelector('form');
    if (form) {
        form.insertBefore(errorDiv, form.firstChild);
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }
}

// Função para mostrar mensagens de sucesso
function showSuccess(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.style.cssText = 'color: green; text-align: center; margin: 10px 0; padding: 10px; background: #e6ffe6; border-radius: 5px;';
    successDiv.textContent = message;
    
    const form = document.querySelector('form');
    if (form) {
        form.insertBefore(successDiv, form.firstChild);
        setTimeout(() => {
            successDiv.remove();
        }, 5000);
    }
}





// Função para visualizar detalhes do livro
function viewBook(bookId) {
    window.location.href = `detalhes-livro.php?id=${bookId}`;
}

// Função para fazer empréstimo
function fazerEmprestimo(bookId) {
    // Verificar limite antes de fazer empréstimo
    fetch('../PHP/verificarLimiteEmprestimos.php')
        .then(response => response.json())
        .then(data => {
            if (data.pode_emprestar) {
                window.location.href = `../PHP/registrarEmprestimo.php?livro_id=${bookId}`;
            } else {
                alert(data.mensagem);
            }
        })
        .catch(error => {
            console.error('Erro ao verificar limite:', error);
            alert('Erro ao verificar disponibilidade para empréstimo');
        });
}

// Função para pesquisar livros
function searchBooks(query) {
    const cards = document.querySelectorAll('.book-card');
    cards.forEach(card => {
        const title = card.querySelector('.book-title').textContent.toLowerCase();
        const author = card.querySelector('.book-author').textContent.toLowerCase();
        const searchTerm = query.toLowerCase();
        
        if (title.includes(searchTerm) || author.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Adicionar estilos CSS para os novos elementos
const additionalStyles = document.createElement('style');
additionalStyles.textContent = `
    .book-buttons {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }
    
    .ver-mais-btn, .emprestar-btn {
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }
    
    .ver-mais-btn {
        background: white !important;
        background-color: white !important;
        color: #ff6600 !important;
    }
    
    .ver-mais-btn:hover {
        background: #f0f0f0 !important;
        background-color: #f0f0f0 !important;
    }
    
    .emprestar-btn {
        background: #4CAF50;
        color: white;
    }
    
    .emprestar-btn:hover:not(:disabled) {
        background: #388E3C;
    }
    
    .emprestar-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
    
    .book-card {
        background: #ff9000 !important;
        border-radius: 10px;
        padding: 18px;
        margin: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        min-width: 180px;
        max-width: 220px;
    }
    
    .book-card:hover {
        transform: translateY(-2px);
    }
    
    .book-cover {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    
    .book-title {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 5px;
    }
    
    .book-author {
        color: #666;
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .book-stock {
        color: #4CAF50;
        font-weight: bold;
        margin-bottom: 10px;
    }
`;

document.head.appendChild(additionalStyles);

// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar event listeners para campos de busca
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchBooks(this.value);
        });
    }
    
    // Carregar livros se estivermos na página do usuário
    if (document.getElementById('booksContainer')) {
        loadBooks();
    }
    
    // Formatar campos de CPF e telefone automaticamente
    const cpfInputs = document.querySelectorAll('input[name="cpf"], input[name="cpfCnpj"]');
    cpfInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = formatCPF(this.value);
        });
    });
    
    const phoneInputs = document.querySelectorAll('input[name="telefone"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = formatPhone(this.value);
        });
    });
});
