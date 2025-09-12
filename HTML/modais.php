<?php?>


        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
            <link rel="stylesheet" href="../CSS/modais.css">
        </head>
        <body>
            <div id="meuModal" class="modal">
            <div class="modal-content">
            <p id="modalMsg"></p>
            <button class="close-btn" onclick="fecharModal()">Fechar</button>
            </div>
        </div>

        <button onclick="mostrarModal('Abrindo manualmente!')">Abrir Modal</button>

        <script src="../JS/modais.js"></script>

        <?php if (!empty($mensagem)) { ?>
            <script>
            mostrarModal("<?= $mensagem ?>");
            </script>
        <?php } ?>
        </body>
        </html>
