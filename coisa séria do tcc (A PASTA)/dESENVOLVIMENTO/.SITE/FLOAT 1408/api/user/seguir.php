<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensagem' => 'Método não permitido.']);
    exit;
}

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'mensagem' => 'Faça login para seguir usuários.']);
    exit;
}

$seguidorId = (int) $_SESSION['id'];
$seguidoId  = (int) ($_POST['alvo_id'] ?? 0);

if ($seguidoId <= 0 || $seguidoId === $seguidorId) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensagem' => 'Alvo inválido.']);
    exit;
}

$stmtExiste = $pdo->prepare('SELECT id FROM usuario WHERE id = ? LIMIT 1');
$stmtExiste->execute([$seguidoId]);
if (!$stmtExiste->fetch()) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'mensagem' => 'Usuário não encontrado.']);
    exit;
}

$stmtCheck = $pdo->prepare('SELECT 1 FROM seguidor WHERE seguidor_id = ? AND seguido_id = ? LIMIT 1');
$stmtCheck->execute([$seguidorId, $seguidoId]);
$jaSegue = (bool) $stmtCheck->fetch();

if ($jaSegue) {
    $pdo->prepare('DELETE FROM seguidor WHERE seguidor_id = ? AND seguido_id = ?')
        ->execute([$seguidorId, $seguidoId]);
    $acao = 'removido';
} else {
    $pdo->prepare('INSERT INTO seguidor (seguidor_id, seguido_id) VALUES (?, ?)')
        ->execute([$seguidorId, $seguidoId]);
    $acao = 'seguindo';
}

$stmtTotal = $pdo->prepare('SELECT COUNT(*) FROM seguidor WHERE seguido_id = ?');
$stmtTotal->execute([$seguidoId]);
$totalSeguidores = (int) $stmtTotal->fetchColumn();

echo json_encode([
    'ok'               => true,
    'acao'             => $acao,
    'mensagem'         => $acao === 'seguindo' ? 'Você está seguindo este usuário.' : 'Você deixou de seguir.',
    'total_seguidores' => $totalSeguidores,
]);