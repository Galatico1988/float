<?php
/**
 * GET  /api/mobile/sessao.php          -> verifica se há sessão ativa
 * POST /api/mobile/sessao.php          -> encerra a sessão (logout)
 *
 * Resp: { ok, logado: bool, usuario?: { id, nome, email, avatar_url } }
 */
declare(strict_types=1);

require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/usuario.php';
require_once __DIR__ . '/../config/conexao.php';

api_inicio();

/* Logout */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    json_ok(['logado' => false, 'mensagem' => 'Sessão encerrada.']);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_erro(405, 'Método não permitido.');
}

$usuarioId = usuario_logado();

if ($usuarioId === null) {
    json_ok(['logado' => false]);
}

try {
    $stmt = $pdo->prepare('SELECT id, nome, email, avatar_path FROM usuario WHERE id = ? LIMIT 1');
    $stmt->execute([$usuarioId]);
    $usuario = $stmt->fetch();
} catch (PDOException $e) {
    json_erro(500, 'Erro interno.');
}

if (!$usuario) {
    json_ok(['logado' => false]);
}

json_ok([
    'logado'  => true,
    'usuario' => usuario_publico($usuario),
]);
