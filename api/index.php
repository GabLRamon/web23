<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

# queria fazer com put e delete :c

try {
    $conn = mysqli_connect('localhost:3307', 'restaurante', 'Restaurante123!', 'restaurante');

    $metodo = $_SERVER['REQUEST_METHOD'];
    if ($metodo !== 'GET' && $metodo !== 'POST') {
        http_response_code(405);
        echo json_encode(['erro' => 'Método inválido.']);
        exit;
    }

    $dados = $_POST;
    $id = (int) ($dados['id'] ?? 0);
    if ($metodo === 'GET') {
        $acao = $_GET['acao'] ?? '';
    } else {
        $acao = $dados['acao'] ?? '';
    }

    $usuarioId = $_SESSION['usuario_id'] ?? null;
    $publica = $metodo === 'POST' && ($acao === 'login' || $acao === 'cadastrar');
    if (!$publica && !$usuarioId) {
        http_response_code(401);
        echo json_encode(['erro' => 'Faça login para continuar.']);
        exit;
    }
    
    $nome = trim($dados['nome'] ?? '');
    $senha = (string) ($dados['senha'] ?? '');
    if (in_array($acao, ['login', 'cadastrar', 'salvar_usuario'])) {
        if ($nome === '') {
            http_response_code(422);
            echo json_encode(['erro' => 'Informe o nome de usuário.']);
            exit;
        }
        if ($acao !== 'salvar_usuario' && $senha === '') {
            http_response_code(422);
            echo json_encode(['erro' => 'Informe a senha.']);
            exit;
        }
    }

    if ($metodo === 'GET') {
        if ($acao === 'listar') {
            $consulta = $conn->prepare('SELECT id, nome FROM usuarios WHERE id = ?');
            $consulta->bind_param('i', $usuarioId);
            $consulta->execute();
            $usuario = $consulta->get_result()->fetch_assoc();

            $consulta = $conn->prepare('SELECT * FROM pratos ORDER BY nome');
            $consulta->execute();
            $pratos = $consulta->get_result()->fetch_all(MYSQLI_ASSOC);

            $consulta = $conn->prepare('SELECT p.id, p.prato_id, s.nome, s.valor, p.quantidade, s.valor * p.quantidade AS total FROM pedidos p JOIN pratos s ON s.id = p.prato_id WHERE p.usuario_id = ? ORDER BY p.id DESC');
            $consulta->bind_param('i', $usuarioId);
            $consulta->execute();
            $pedidos = $consulta->get_result()->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['usuario' => $usuario, 'pratos' => $pratos, 'pedidos' => $pedidos]);
            exit;
        }
    }

    #dor

    if ($metodo === 'POST') {
        if ($acao === 'cadastrar') {
            $consulta = $conn->prepare('INSERT INTO usuarios (nome, senha) VALUES (?, ?)');
            $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);
            $consulta->bind_param('ss', $nome, $senhaCriptografada);
            $consulta->execute();
            echo json_encode(['mensagem' => 'Cadastro concluído. Faça login.']);
            exit;
        }

        if ($acao === 'login') {
            $consulta = $conn->prepare('SELECT * FROM usuarios WHERE nome = ?');
            $consulta->bind_param('s', $nome);
            $consulta->execute();
            $usuario = $consulta->get_result()->fetch_assoc();
            if (!$usuario || !password_verify($senha, $usuario['senha'])) {
                http_response_code(401);
                echo json_encode(['erro' => 'Usuário ou senha incorretos.']);
                exit;
            }
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $usuario['id'];
            echo json_encode(['mensagem' => 'Operação concluída.']);
            exit;
        }

        if ($acao === 'sair') {
            $_SESSION = [];
            session_destroy();
            echo json_encode(['mensagem' => 'Operação concluída.']);
            exit;
        }

        if ($acao === 'salvar_prato') {
            $nome = trim($dados['nome'] ?? '');
            $valor = $dados['valor'] ?? '';
            if ($nome === '') {
                http_response_code(422);
                echo json_encode(['erro' => 'Informe o nome do prato.']);
                exit;
            }
            if (!is_numeric($valor) || $valor <= 0) {
                http_response_code(422);
                echo json_encode(['erro' => 'Informe um preço maior que zero.']);
                exit;
            }
            if ($id === 0) {
                $consulta = $conn->prepare('INSERT INTO pratos (nome, valor) VALUES (?, ?)');
                $consulta->bind_param('sd', $nome, $valor);
                $consulta->execute();
            } else {
                $consulta = $conn->prepare('UPDATE pratos SET nome = ?, valor = ? WHERE id = ?');
                $consulta->bind_param('sdi', $nome, $valor, $id);
                $consulta->execute();
            }
            echo json_encode(['mensagem' => 'Operação concluída.']);
            exit;
        }

        if ($acao === 'salvar_pedido') {
            $prato = (int) ($dados['prato_id'] ?? 0);
            $quantidade = filter_var($dados['quantidade'] ?? '', FILTER_VALIDATE_INT);
            if ($prato <= 0 || $quantidade === false || $quantidade <= 0) {
                http_response_code(422);
                echo json_encode(['erro' => 'Selecione um prato e informe uma quantidade inteira maior que zero.']);
                exit;
            }
            if ($id === 0) {
                $consulta = $conn->prepare('INSERT INTO pedidos (usuario_id, prato_id, quantidade) VALUES (?, ?, ?)');
                $consulta->bind_param('iii', $usuarioId, $prato, $quantidade);
                $consulta->execute();
            } else {
                $consulta = $conn->prepare('UPDATE pedidos SET prato_id = ?, quantidade = ? WHERE id = ? AND usuario_id = ?');
                $consulta->bind_param('iiii', $prato, $quantidade, $id, $usuarioId);
                $consulta->execute();
            }
            echo json_encode(['mensagem' => 'Operação concluída.']);
            exit;
        }

        if ($acao === 'salvar_usuario') {
            $consulta = $conn->prepare('UPDATE usuarios SET nome = ? WHERE id = ?');
            $consulta->bind_param('si', $nome, $usuarioId);
            $consulta->execute();
            if ($senha !== '') {
                $consulta = $conn->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
                $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);
                $consulta->bind_param('si', $senhaCriptografada, $usuarioId);
                $consulta->execute();
            }
            echo json_encode(['mensagem' => 'Operação concluída.']);
            exit;
        }

        if ($acao === 'excluir_usuario') {
            $consulta = $conn->prepare('DELETE FROM usuarios WHERE id = ?');
            $consulta->bind_param('i', $usuarioId);
            $consulta->execute();
            $_SESSION = [];
            session_destroy();
            echo json_encode(['mensagem' => 'Conta excluída.']);
            exit;
        }

        if ($acao === 'excluir_prato') {
            $consulta = $conn->prepare('DELETE FROM pratos WHERE id = ?');
            $consulta->bind_param('i', $id);
            $consulta->execute();
            echo json_encode(['mensagem' => 'Operação concluída.']);
            exit;
        }

        if ($acao === 'excluir_pedido') {
            $consulta = $conn->prepare('DELETE FROM pedidos WHERE id = ? AND usuario_id = ?');
            $consulta->bind_param('ii', $id, $usuarioId);
            $consulta->execute();
            echo json_encode(['mensagem' => 'Operação concluída.']);
            exit;
        }
    }

    http_response_code(404);
    echo json_encode(['erro' => 'Ação inválida.']);
    exit;
} catch (Throwable $erro) {
    error_log($erro->getMessage());
    http_response_code(400);
    echo json_encode(['erro' => 'Não foi possível concluir. Confira os dados, nomes de usuário repetidos, pedidos vinculados e a conexão com o banco.']);
}
