
// Função para mostrar modal de adicionar livro
function showAddBookModal() {
    document.getElementById('modalTitle').textContent = 'Adicionar Novo Livro';
    document.getElementById('bookForm').reset();
    document.getElementById('bookId').value = '';
    document.getElementById('bookModal').style.display = 'block';
}

// Função para mostrar modal de editar livro
function showEditBookModal(bookId) {
    // Esta função será chamada quando o PHP gerar os botões de editar
    // O PHP deve preencher os campos do formulário antes de chamar esta função
    document.getElementById('modalTitle').textContent = 'Editar Livro';
    document.getElementById('bookModal').style.display = 'block';
}


// Função para alternar painel de doações
function toggleDonationsPanel() {
    const panel = document.getElementById('donationsPanel');
    if (panel.style.display === 'block') {
        panel.style.display = 'none';
    } else {
        panel.style.display = 'block';
    }
}

// Função para alternar painel de devoluções
function toggleDevolucoesPanel() {
    const panel = document.getElementById('devolucoesPanel');
    if (panel.style.display === 'block') {
        panel.style.display = 'none';
    } else {
        panel.style.display = 'block';
    }
}

// Função para deletar livro (será chamada pelo PHP)
function deleteBook(bookId) {
    if (confirm('Tem certeza que deseja excluir este livro?')) {
        // Redirecionar para uma página PHP que fará a exclusão
        window.location.href = `delete_book.php?id=${bookId}`;
    }
}

// Função para aceitar doação (será chamada pelo PHP)
function acceptDonation(donationId) {
    if (confirm('Tem certeza que deseja aceitar esta doação?')) {
        // Redirecionar para uma página PHP que fará a aceitação
        window.location.href = `accept_donation.php?id=${donationId}`;
    }
}

// Função para recusar doação (será chamada pelo PHP)
function rejectDonation(donationId) {
    if (confirm('Tem certeza que deseja recusar esta doação?')) {
        // Redirecionar para uma página PHP que fará a recusa
        window.location.href = `reject_donation.php?id=${donationId}`;
    }
}

// Função para confirmar devolução (será chamada pelo PHP)
function confirmarDevolucao(devolucaoId) {
    if (confirm('Tem certeza que deseja confirmar esta devolução?')) {
        // Redirecionar para uma página PHP que fará a confirmação
        window.location.href = `confirm_devolucao.php?id=${devolucaoId}`;
    }
}


// Fechar modal ao pressionar ESC
document.addEventListener('keydown', function(event) {
if (event.key === 'Escape') {
closeModal();
}
});

function openModal() {
document.getElementById("bookModal").style.display = "flex";
}

function closeModal() {
document.getElementById("bookModal").style.display = "none";
}
