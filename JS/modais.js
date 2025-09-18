function mostrarModal(mensagem) {
    document.getElementById("modalMsg").innerText = mensagem;
    document.getElementById("meuModal").style.display = "flex";
  }
  
  function fecharModal() {
    document.getElementById("meuModal").style.display = "none";
  }
  
// Universal Delete Confirmation Modal Functions
let deleteConfirmCallback = null;
let deleteModalElement = null;

/**
 * Show delete confirmation modal
 * @param {string} title - Modal title
 * @param {string} message - Confirmation message
 * @param {function} onConfirm - Callback function to execute on confirmation
 * @param {string} confirmText - Text for confirm button (optional)
 * @param {string} cancelText - Text for cancel button (optional)
 */
function showDeleteConfirmation(title, message, onConfirm, confirmText = 'Sim, Excluir', cancelText = 'Cancelar') {
    // Create modal if it doesn't exist
    if (!deleteModalElement) {
        createDeleteModal();
    }
    
    // Update modal content
    document.getElementById('deleteModalTitle').textContent = title;
    document.getElementById('deleteModalMessage').textContent = message;
    document.getElementById('deleteConfirmBtn').textContent = confirmText;
    document.getElementById('deleteCancelBtn').textContent = cancelText;
    
    // Store callback
    deleteConfirmCallback = onConfirm;
    
    // Show modal
    deleteModalElement.classList.add('show');
}

/**
 * Hide delete confirmation modal
 */
function hideDeleteConfirmation() {
    if (deleteModalElement) {
        deleteModalElement.classList.remove('show');
    }
    deleteConfirmCallback = null;
}

/**
 * Handle delete confirmation
 */
function confirmDelete() {
    if (deleteConfirmCallback && typeof deleteConfirmCallback === 'function') {
        deleteConfirmCallback();
    }
    hideDeleteConfirmation();
}

/**
 * Handle delete cancellation
 */
function cancelDelete() {
    hideDeleteConfirmation();
}

/**
 * Create delete confirmation modal element
 */
function createDeleteModal() {
    const modal = document.createElement('div');
    modal.id = 'universalDeleteModal';
    modal.className = 'delete-modal';
    modal.innerHTML = `
        <div class="delete-modal-content">
            <h2 id="deleteModalTitle">Confirmar Exclusão</h2>
            <p id="deleteModalMessage">Tem certeza que deseja excluir este item? Esta ação não pode ser desfeita.</p>
            <div class="delete-modal-actions">
                <button class="btn-cancel-delete" id="deleteCancelBtn" onclick="cancelDelete()">Cancelar</button>
                <button class="btn-confirm-delete" id="deleteConfirmBtn" onclick="confirmDelete()">Sim, Excluir</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    deleteModalElement = modal;
    
    // Close on background click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideDeleteConfirmation();
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            hideDeleteConfirmation();
        }
    });
}

/**
 * Initialize delete modal on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    createDeleteModal();
    createEditModal();
});

// Universal Edit Confirmation Modal Functions
let editConfirmCallback = null;
let editModalElement = null;

/**
 * Show edit confirmation modal
 * @param {string} title - Modal title
 * @param {string} message - Confirmation message
 * @param {function} onConfirm - Callback function to execute on confirmation
 * @param {string} confirmText - Text for confirm button (optional)
 * @param {string} cancelText - Text for cancel button (optional)
 */
function showEditConfirmation(title, message, onConfirm, confirmText = 'Sim, Salvar', cancelText = 'Cancelar') {
    // Create modal if it doesn't exist
    if (!editModalElement) {
        createEditModal();
    }
    
    // Update modal content
    document.getElementById('editModalTitle').textContent = title;
    document.getElementById('editModalMessage').textContent = message;
    document.getElementById('editConfirmBtn').textContent = confirmText;
    document.getElementById('editCancelBtn').textContent = cancelText;
    
    // Store callback
    editConfirmCallback = onConfirm;
    
    // Show modal
    editModalElement.classList.add('show');
}

/**
 * Hide edit confirmation modal
 */
function hideEditConfirmation() {
    if (editModalElement) {
        editModalElement.classList.remove('show');
    }
    editConfirmCallback = null;
}

/**
 * Handle edit confirmation
 */
function confirmEdit() {
    if (editConfirmCallback && typeof editConfirmCallback === 'function') {
        editConfirmCallback();
    }
    hideEditConfirmation();
}

/**
 * Handle edit cancellation
 */
function cancelEdit() {
    hideEditConfirmation();
}

/**
 * Create edit confirmation modal element
 */
function createEditModal() {
    const modal = document.createElement('div');
    modal.id = 'universalEditModal';
    modal.className = 'edit-modal';
    modal.innerHTML = `
        <div class="edit-modal-content">
            <h2 id="editModalTitle">Confirmar Edição</h2>
            <p id="editModalMessage">Tem certeza que deseja salvar estas alterações?</p>
            <div class="edit-modal-actions">
                <button class="btn-cancel-edit" id="editCancelBtn" onclick="cancelEdit()">Cancelar</button>
                <button class="btn-confirm-edit" id="editConfirmBtn" onclick="confirmEdit()">Sim, Salvar</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    editModalElement = modal;
    
    // Close on background click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideEditConfirmation();
        }
    });
    
    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            hideEditConfirmation();
        }
    });
}

/**
 * Show success/error message without alert
 * @param {string} message - Message to display
 * @param {string} type - Type: 'success', 'error', 'info'
 * @param {number} duration - Duration in milliseconds (default: 4000)
 */
function showNotification(message, type = 'success', duration = 4000) {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification-toast notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Force reflow
    notification.offsetHeight;
    
    // Show notification
    notification.classList.add('show');
    
    // Auto-remove after duration
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, duration);
}
  