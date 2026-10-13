<?php
/**
 * POST /api/mobile/cadastro.php
 * Body: nome, email, password
 * Resp: { ok, mensagem, usuario: { id, nome, email, avatar_url } }
 * Ao final já cria a sessão (mesmo comportamento do site).
 */
declare(strict_types=1);

require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/usuario.php';
require_once __DIR__ . '/../config/conexao.php';

api_inicio();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_erro(405, 'Método não permitido.');
}

$nome  = parametro('nome', '');
$email = parametro('email', '');
$senha = (string) ($_POST['password'] ?? '');

$erros = usuario_validar_cadastro($nome, $email, $senha);

if (!empty($erros)) {
    json_erro(422, implode(' ', $erros));
}

try {
    $novoId = usuario_criar($pdo, $nome, $email, $senha);
} catch (RuntimeException $e) {
    json_erro(409, $e->getMessage());
} catch (PDOException $e) {
    error_log('Erro cadastro mobile: ' . $e->getMessage());
    json_erro(500, 'Erro interno ao criar a conta.');
}

usuario_iniciar_sessao([
    'id'    => $novoId,
    'nome'  => $nome,
    'email' => $email,
]);

json_ok([
    'mensagem' => 'Conta criada com sucesso!',
    'usuario'  => [
        'id'         => $novoId,
        'nome'       => $nome,
        'email'      => $email,
        'avatar_url' => url_avatar(null),
    ],
]);
