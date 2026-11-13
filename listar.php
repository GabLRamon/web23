<?php session_start(); if (empty($_SESSION['usuario_id'])) { header('Location: index.html'); exit; } ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listagem - Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="js/app.js" defer></script>
</head>
<body class="container py-4" style="max-width: 800px;">
    <h1 class="mb-3">Listagem</h1>
    <nav class="mb-3 d-flex align-items-center gap-2">
        <a href="listar.php">Listagem</a> | 
        <a href="gerenciar.php">Gerenciar</a> | 
        <button id="sair" type="button" class="btn btn-outline-secondary btn-sm">Sair</button>
    </nav>
    <p id="mensagem" role="status" class="text-danger fw-bold"></p>
    <p id="saudacao" class="fw-semibold"></p>

    <h2 class="h4 mt-4">Pratos</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-light"><tr><th>ID</th><th>Nome do prato</th><th>Preço (R$)</th></tr></thead>
        <tbody id="lista-pratos"></tbody>
    </table>

    <h2 class="h4 mt-4">Meus pedidos</h2>
    <p class="text-muted small">O total é calculado pelo preço atual do prato.</p>
    <table class="table table-bordered table-striped">
        <thead class="table-light"><tr><th>ID</th><th>Nome do prato</th><th>Quantidade</th><th>Total (R$)</th></tr></thead>
        <tbody id="lista-pedidos"></tbody>
    </table>
</body>
</html>