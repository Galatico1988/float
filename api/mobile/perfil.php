<?php
/**
 * GET /api/mobile/perfil.php
 * Resp: { ok, usuario: { id, nome, email, bio, avatar_url, banner_url,
 *                        seguidores, seguindo, total_jogos, desde } }
 */
declare(strict_types=1);

require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/usuario.php';
require_once __DIR__ . '/../config/conexao.php';

api_inicio();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_erro(405, 'Método não permitido.');
}

$usuarioId = exige_login();

try {
    $perfil = usuario_perfil($pdo, $usuarioId);
} catch (PDOException $e) {
    error_log('Erro perfil mobile: ' . $e->getMessage());
    json_erro(500, 'Erro ao carregar o perfil.');
}

if (!$perfil) {
    json_erro(404, 'Usuário não encontrado.');
}

json_ok(['usuario' => $perfil]);
