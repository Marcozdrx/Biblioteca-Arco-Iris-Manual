
// Função para mostrar modal de adicionar livro
function showAddBookModal() {
    document.getElementById('modalTitle').textContent = 'Adicionar Novo Livro';
    document.getElementById('bookForm').reset();
    document.getElementById('bookId').value = '';
    document.getElementById('capa').required = true;
    document.getElementById('bookModal').style.display = 'block';
}

// Função para fechar modal de livro
function closeBookModal() {
    document.getElementById('bookModal').style.display = 'none';
}

// Atualiza o contador (você pode chamar isso ao carregar a página)
  function atualizarContadorDevolucoes() {
    const contador = document.getElementById("devolucoesCount");
    if (contador && typeof devolucoesPendentes !== 'undefined') {
      contador.textContent = devolucoesPendentes.length;
    }
  }
  
  // Mostra o modal com a lista
  function showDevolucoes() {
    const modal = document.getElementById("modalDevolucoes");
    
    if (!modal) {
        console.error("Modal não encontrado");
        return;
    }
    
    // Apenas abrir o modal - os dados já estão no HTML via PHP
    modal.classList.add("show");
}
  
  // Fecha o modal
  function fecharModal() {
    const modal = document.getElementById("modalDevolucoes");
    modal.classList.remove("show");
  }
  
  // Fecha ao clicar fora
  window.onclick = function(event) {
    const modal = document.getElementById("modalDevolucoes");
    if (event.target == modal) {
      modal.classList.remove("show");
    }
    
    // Também fecha o modal de livro ao clicar fora
    const bookModal = document.getElementById("bookModal");
    if (event.target == bookModal) {
      bookModal.style.display = "none";
    }
  }
  
  // Chamar ao carregar a página
  document.addEventListener("DOMContentLoaded", atualizarContadorDevolucoes);
  

// Exemplo de doações pendentes
const doacoesPendentes = [];
  
  // Atualiza o contador de doações pendentes
  function atualizarContadorDoacoes() {
    const contador = document.getElementById("donationsCount");
    contador.textContent = doacoesPendentes.length;
  }
  
  // Mostra o modal de doações (renomeada para evitar conflito)
  function showDoacoesModal() {
    const modal = document.getElementById("modalDoacoes");
    const lista = document.getElementById("listaDoacoes");
  
    // Limpa e preenche a lista de doações
    lista.innerHTML = "";
    doacoesPendentes.forEach(doacao => {
      const item = document.createElement("li");
      item.textContent = `${doacao.livro} - Doador: ${doacao.doador} (Data: ${doacao.data})`;
      lista.appendChild(item);
    });
  
    modal.style.display = "block";
  }
  
  // Fecha o modal
  function fecharModalDoacoes() {
    document.getElementById("modalDoacoes").style.display = "none";
  }
  
  // Fecha clicando fora do modal
  window.onclick = function(event) {
    const modal = document.getElementById("modalDoacoes");
    if (event.target === modal) {
      modal.style.display = "none";
    }
  }
  
  // Atualiza o contador ao carregar a página
  document.addEventListener("DOMContentLoaded", atualizarContadorDoacoes);
  

function showEditBookModal(button) {
    // Pegar os dados do botão clicado
    const livroData = {
        id: button.dataset.livroId,
        titulo: button.dataset.titulo,
        estoque: button.dataset.estoque,
        autor: button.dataset.autor,
        ano: button.dataset.ano,
        paginas: button.dataset.paginas,
        editora: button.dataset.editora,
        isbn: button.dataset.isbn,
        idioma: button.dataset.idioma,
        categoria: button.dataset.categoria,
        descricao: button.dataset.descricao,
        imagemCapa: button.dataset.imagemCapa // Adicionar este campo
    };
    
    // Popular o formulário
    document.getElementById('idLivroEdit').value = livroData.id;
    document.getElementById('tituloEdit').value = livroData.titulo;
    document.getElementById('estoqueEdit').value = livroData.estoque;
    document.getElementById('autorEdit').value = livroData.autor;
    document.getElementById('dataPublicacaoEdit').value = livroData.ano;
    document.getElementById('numeroPaginasEdit').value = livroData.paginas;
    document.getElementById('editoraEdit').value = livroData.editora;
    document.getElementById('isbnEdit').value = livroData.isbn;
    document.getElementById('idiomaEdit').value = livroData.idioma;
    document.getElementById('categoriaEdit').value = livroData.categoria;
    document.getElementById('descricaoEdit').value = livroData.descricao;
    
    // Mostrar a imagem atual
    if (livroData.imagemCapa && livroData.mimeType) {
        document.getElementById('imagemAtual').value = `data:${livroData.mimeType};base64,${livroData.imagemCapa}`;
    }
    
    // Mostrar o modal
    document.getElementById('editModal').style.display = 'block';
}


// Função para alternar painel de doações
function toggleDonationsPanel() {
    const panel = document.getElementById('donationsPanel');
    const overlay = document.getElementById('panelOverlay');
    
    if (panel.classList.contains('show')) {
        panel.classList.remove('show');
        overlay.classList.remove('show');
    } else {
        // Fechar outros painéis primeiro
        closeAllPanels();
        panel.classList.add('show');
        overlay.classList.add('show');
    }
}

// Função para alternar painel de devoluções
function toggleDevolucoesPanel() {
    const panel = document.getElementById('devolucoesPanel');
    const overlay = document.getElementById('panelOverlay');
    
    if (panel.classList.contains('show')) {
        panel.classList.remove('show');
        overlay.classList.remove('show');
    } else {
        // Fechar outros painéis primeiro
        closeAllPanels();
        panel.classList.add('show');
        overlay.classList.add('show');
    }
}

// Função para fechar todos os painéis
function closeAllPanels() {
    const donationsPanel = document.getElementById('donationsPanel');
    const devolucoesPanel = document.getElementById('devolucoesPanel');
    const overlay = document.getElementById('panelOverlay');
    
    donationsPanel.classList.remove('show');
    devolucoesPanel.classList.remove('show');
    overlay.classList.remove('show');
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

// Função para aceitar doação
function aceitarDoacao(doacaoId) {
    if (confirm('Tem certeza que deseja aceitar esta doação?')) {
        const formData = new FormData();
        formData.append('doacao_id', doacaoId);
        formData.append('acao', 'aceitar');
        
        fetch('../PHP/processarDoacao.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Doação aceita com sucesso!');
                location.reload();
            } else {
                alert('Erro ao aceitar doação: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao processar doação');
        });
    }
}

// Função para recusar doação
function recusarDoacao(doacaoId) {
    if (confirm('Tem certeza que deseja recusar esta doação?')) {
        const formData = new FormData();
        formData.append('doacao_id', doacaoId);
        formData.append('acao', 'recusar');
        
        fetch('../PHP/processarDoacao.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Doação recusada com sucesso!');
                location.reload();
            } else {
                alert('Erro ao recusar doação: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao processar doação');
        });
    }
}

// Função para confirmar devolução
function confirmarDevolucao(emprestimoId) {
    if (confirm('Tem certeza que deseja confirmar esta devolução?')) {
        const formData = new FormData();
        formData.append('emprestimo_id', emprestimoId);
        formData.append('acao', 'confirmar');
        
        fetch('../PHP/processarDevolucao.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Devolução confirmada com sucesso!');
                location.reload();
            } else {
                alert('Erro ao confirmar devolução: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao processar devolução');
        });
    }
}

// Função para deletar doação
function deletarDoacao(doacaoId) {
    if (confirm('Tem certeza que deseja deletar esta doação? Esta ação não pode ser desfeita.')) {
        const formData = new FormData();
        formData.append('doacao_id', doacaoId);
        
        fetch('../PHP/deletarDoacao.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Doação deletada com sucesso!');
                location.reload();
            } else {
                alert('Erro ao deletar doação: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao deletar doação');
        });
    }
}

// Função para enviar lembrete
function enviarLembrete(emprestimoId) {
    if (confirm('Deseja enviar um lembrete para este usuário?')) {
        const formData = new FormData();
        formData.append('emprestimo_id', emprestimoId);
        formData.append('acao', 'lembrete');
        
        fetch('../PHP/processarDevolucao.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Lembrete enviado com sucesso!');
            } else {
                alert('Erro ao enviar lembrete: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao enviar lembrete');
        });
    }
}


// Fechar modal ao pressionar ESC
document.addEventListener('keydown', function(event) {
if (event.key === 'Escape') {
closeBookModal();
closeEditModal();
}
});

function openModal() {
document.getElementById("bookModal").style.display = "flex";
}

function closeEditModal() {
    document.getElementById("editModal").style.display = "none";
    }
