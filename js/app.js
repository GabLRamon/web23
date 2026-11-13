const mensagem = document.getElementById('mensagem');
const gerenciar = document.getElementById('usuario');
function dinheiro(valor) {
    return Number(valor).toFixed(2).replace('.', ',');
}

async function api(acao, dados) {
    let url = 'api/index.php';
    const opcoes = {method: 'GET'};
    if (dados === undefined) {
        url += '?acao=' + acao;
    } else {
        opcoes.method = 'POST';
        opcoes.body = new URLSearchParams(dados);
        opcoes.body.set('acao', acao);
    }

    const resposta = await fetch(url, opcoes);
    const resultado = await resposta.json();
    if (!resposta.ok) {
        if (resposta.status === 401 && document.getElementById('sair')) location.href = 'index.html';
        throw new Error(resultado.erro || 'Não foi possível concluir.');
    }
    return resultado;
}

function linha(tabela, valores) {
    const tr = tabela.insertRow();
    for (const valor of valores) tr.insertCell().textContent = valor;
    return tr;
}

function acoes(tr, registro, entidade) {
    const celula = tr.insertCell();
    celula.innerHTML = '<button>Editar</button> <button>Excluir</button>';
    celula.children[0].onclick = function () {
        const form = document.getElementById(entidade);
        for (const campo of form.elements) {
            if (campo.name && registro[campo.name] !== undefined) campo.value = registro[campo.name];
        }
    };
    celula.children[1].onclick = async function () {
        try {
            const resultado = await api('excluir_' + entidade, {id: registro.id});
            document.getElementById(entidade).reset();
            await carregar();
            mensagem.textContent = resultado.mensagem;
        } catch (erro) { mensagem.textContent = erro.message; }
    };
}

async function carregar() {
    const dados = await api('listar');
    const pratos = document.getElementById('lista-pratos');
    const pedidos = document.getElementById('lista-pedidos');
    pratos.replaceChildren();
    pedidos.replaceChildren();
    for (const prato of dados.pratos) {
        const tr = linha(pratos, [prato.id, prato.nome, dinheiro(prato.valor)]);
        if (gerenciar) acoes(tr, prato, 'prato');
    }
    for (const pedido of dados.pedidos) {
        const tr = linha(pedidos, [pedido.id, pedido.nome, pedido.quantidade, dinheiro(pedido.total)]);
        if (gerenciar) acoes(tr, pedido, 'pedido');
    }
    if (!dados.pratos.length) linha(pratos, ['Nenhum prato cadastrado.']);
    if (!dados.pedidos.length) linha(pedidos, ['Nenhum pedido cadastrado.']);
    if (gerenciar) {
        const form = document.getElementById('usuario');
        form.elements.nome.value = dados.usuario.nome;
        const select = document.getElementById('pedido').elements.prato_id;
        select.replaceChildren(new Option('Selecione', ''));
        for (const prato of dados.pratos) select.add(new Option(prato.nome + ' - R$ ' + dinheiro(prato.valor), prato.id));
    } else {
        document.getElementById('saudacao').textContent = 'Usuário: ' + dados.usuario.nome;
    }
}

function formulario(id, acao, destino) {
    const form = document.getElementById(id);
    if (!form) return;
    form.onsubmit = async function (evento) {
        evento.preventDefault();
        try {
            const resultado = await api(acao, new FormData(form));
            if (destino) { location.href = destino; return; }
            form.reset();
            if (document.getElementById('sair')) await carregar();
            mensagem.textContent = resultado.mensagem;
        } catch (erro) { mensagem.textContent = erro.message; }
    };
}

formulario('login', 'login', 'listar.php');
formulario('cadastro', 'cadastrar');
formulario('usuario', 'salvar_usuario');
formulario('prato', 'salvar_prato');
formulario('pedido', 'salvar_pedido');

const sair = document.getElementById('sair');

if (sair) {
    sair.onclick = async () => {
        try { await api('sair', {}); location.href = 'index.html'; }
        catch (erro) { mensagem.textContent = erro.message; }
    };
    carregar().catch(erro => { mensagem.textContent = erro.message; });
}

const excluirConta = document.getElementById('excluir-conta');
if (excluirConta) excluirConta.onclick = async () => {
    try { await api('excluir_usuario', {}); location.href = 'index.html'; }
    catch (erro) { mensagem.textContent = erro.message; }
};
