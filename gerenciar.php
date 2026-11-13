<?php session_start(); if (empty($_SESSION['usuario_id'])) { header('Location: index.html'); exit; } ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerenciamento - Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="js/app.js" defer></script>
</head>
<body class="container py-4" style="max-width: 800px;">
    <h1 class="mb-3">Gerenciamento</h1>
    <nav class="mb-3 d-flex align-items-center gap-2">
        <a href="listar.php">Listagem</a> | 
        <a href="gerenciar.php">Gerenciar</a> | 
        <button id="sair" type="button" class="btn btn-outline-secondary btn-sm">Sair</button>
    </nav>
    <p id="mensagem" role="status" class="text-danger fw-bold"></p>

    <h2 class="h4 mt-4">Meu usuário</h2>
    <form id="usuario" class="card p-3 mb-4">
        <div class="mb-3">
            <label class="form-label">Nome de usuário</label>
            <input name="nome" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nova senha <small class="text-muted">(deixe vazio para manter)</small></label>
            <input type="password" name="senha" class="form-control" autocomplete="new-password">
        </div>
        <div>
            <button class="btn btn-primary">Salvar usuário</button>
            <button type="button" id="excluir-conta" class="btn btn-danger">Excluir minha conta</button>
        </div>
    </form>

    <h2 class="h4 mt-4">Pratos</h2>
    <form id="prato" class="card p-3 mb-3">
        <input type="hidden" name="id">
        <div class="mb-3">
            <label class="form-label">Nome do prato</label>
            <input name="nome" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Preço (R$)</label>
            <input type="number" name="valor" min="0.01" step="0.01" class="form-control" required>
        </div>
        <div>
            <button class="btn btn-success">Salvar prato</button>
            <button type="reset" class="btn btn-secondary">Limpar / novo</button>
        </div>
    </form>
    <table class="table table-bordered table-striped mb-4">
        <thead class="table-light"><tr><th>ID</th><th>Nome do prato</th><th>Preço (R$)</th><th>Ações</th></tr></thead>
        <tbody id="lista-pratos"></tbody>
    </table>

    <h2 class="h4 mt-4">Meus pedidos</h2>
    <form id="pedido" class="card p-3 mb-3">
        <input type="hidden" name="id">
        <div class="mb-3">
            <label class="form-label">Prato</label>
            <select name="prato_id" class="form-select" required></select>
        </div>
        <div class="mb-3">
            <label class="form-label">Quantidade</label>
            <input type="number" name="quantidade" min="1" step="1" class="form-control" required>
        </div>
        <div>
            <button class="btn btn-success">Salvar pedido</button>
            <button type="reset" class="btn btn-secondary">Limpar / novo</button>
        </div>
    </form>
    <table class="table table-bordered table-striped">
        <thead class="table-light"><tr><th>ID</th><th>Nome do prato</th><th>Quantidade</th><th>Total (R$)</th><th>Ações</th></tr></thead>
        <tbody id="lista-pedidos"></tbody>
    </table>
</body>
</html>