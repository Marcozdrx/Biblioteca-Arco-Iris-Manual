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

// Função para carregar livros dinamicamente
function loadBooks() {
    fetch('../PHP/get_books.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('booksContainer');
            if (container) {
                container.innerHTML = '';
                data.forEach(book => {
                    const bookCard = createBookCard(book);
                    container.appendChild(bookCard);
                });
            }
        })
        .catch(error => {
            console.error('Erro ao carregar livros:', error);
        });
}

// Função para criar card de livro
function createBookCard(book) {
    const card = document.createElement('div');
    card.className = 'book-card';
    card.innerHTML = `
        <img src="${book.imagem_capa || '../IMG/default-avatar.svg'}" alt="${book.titulo}" class="book-cover">
        <div class="book-title">${book.titulo}</div>
        <div class="book-author">${book.nome_autor || 'Autor não informado'}</div>
        <div class="book-stock">Estoque: ${book.estoque || 0}</div>
        <button class="ver-mais-btn" onclick="viewBook(${book.id})">Ver mais</button>
    `;
    return card;
}

// Função para visualizar detalhes do livro
function viewBook(bookId) {
    // Implementar modal ou redirecionamento para detalhes do livro
    console.log('Visualizar livro:', bookId);
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
