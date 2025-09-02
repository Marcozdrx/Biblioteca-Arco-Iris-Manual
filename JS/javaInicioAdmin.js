
// Função para mostrar modal de adicionar livro
function showAddBookModal() {
    document.getElementById('modalTitle').textContent = 'Adicionar Novo Livro';
    document.getElementById('bookForm').reset();
    document.getElementById('bookId').value = '';
    document.getElementById('capa').required = true; // Tornar campo de imagem obrigatório
    document.getElementById('bookModal').style.display = 'block';
}

function showEditBookModal() {
    document.getElementById('modalTitle').textContent = 'Editar Livro';
    document.getElementById('capa').required = true; // Tornar campo de imagem obrigatório
    document.getElementById('editModal').style.display = 'block';
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

// Função para deletar livro
function deleteBook(bookId) {
    // Armazenar o ID do livro para confirmação
    window.currentBookId = bookId;
    
    // Mostrar modal de confirmação
    document.getElementById('deleteModal').style.display = 'block';
}

// Função para confirmar exclusão
function confirmDelete() {
    const bookId = window.currentBookId;
    
    if (!bookId) {
        alert('Erro: ID do livro não encontrado');
        return;
    }
    
    // Fazer requisição AJAX para excluir o livro
    const formData = new FormData();
    formData.append('bookId', bookId);
    
    fetch('../PHP/delete_book.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Livro excluído com sucesso!');
            location.reload(); // Recarregar a página para atualizar a lista
        } else {
            alert('Erro ao excluir livro: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao excluir livro');
    })
    .finally(() => {
        closeDeleteModal();
    });
}

// Função para fechar modal de exclusão
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    window.currentBookId = null;
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

function closeBookModal() {
document.getElementById("bookModal").style.display = "none";
}
function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
    }
