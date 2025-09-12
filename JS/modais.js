function mostrarModal(mensagem) {
    document.getElementById("modalMsg").innerText = mensagem;
    document.getElementById("meuModal").style.display = "flex";
  }
  
  function fecharModal() {
    document.getElementById("meuModal").style.display = "none";
  }
  