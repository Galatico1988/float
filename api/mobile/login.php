<?php
/**
 * POST /api/mobile/login.php
 * Body: email, password
 * Resp: { ok, mensagem, usuario: { id, nome, email, avatar_url } }
 */
declare(strict_types=1);

require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/usuario.php';
require_once __DIR__ . '/../config/conexao.php';

api_inicio();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_erro(405, 'Método não permitido.');
}

$email = parametro('email', '');
$senha = (string) ($_POST['password'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_erro(422, 'E-mail inválido.');
}

if ($senha === '') {
    json_erro(422, 'Informe a senha.');
}

try {
    $usuario = usuario_autenticar($pdo, $email, $senha);
} catch (PDOException $e) {
    error_log('Erro login mobile: ' . $e->getMessage());
    json_erro(500, 'Erro interno ao tentar entrar.');
}

if ($usuario === null) {
    json_erro(401, 'E-mail ou senha incorretos.');
}

usuario_iniciar_sessao($usuario);

json_ok([
    'mensagem' => 'Login realizado com sucesso!',
    'usuario'  => usuario_publico($usuario),
]);
