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
    echo json_encode(['ok' => false, 'mensagem' => 'Faça login para reportar.']);
    exit;
}

$denuncianteId = (int) $_SESSION['id'];
$tipo          = trim($_POST['tipo']    ?? '');
$alvoId        = (int) ($_POST['alvo_id'] ?? 0);
$motivo        = mb_substr(trim($_POST['motivo'] ?? ''), 0, 255);

if (!in_array($tipo, ['conta', 'jogo'], true)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensagem' => 'Tipo inválido.']);
    exit;
}

if ($alvoId <= 0) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensagem' => 'Alvo inválido.']);
    exit;
}

if (mb_strlen($motivo) < 3) {
    echo json_encode(['ok' => false, 'mensagem' => 'Informe um motivo válido.']);
    exit;
}

if ($tipo === 'conta' && $alvoId === $denuncianteId) {
    echo json_encode(['ok' => false, 'mensagem' => 'Você não pode reportar a si mesmo.']);
    exit;
}

if ($tipo === 'conta') {
    $stmtEx = $pdo->prepare('SELECT id FROM usuario WHERE id = ? LIMIT 1');
} else {
    $stmtEx = $pdo->prepare('SELECT id FROM jogo WHERE id = ? AND visibilidade = \'publico\' LIMIT 1');
}
$stmtEx->execute([$alvoId]);
if (!$stmtEx->fetch()) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'mensagem' => 'Alvo não encontrado.']);
    exit;
}

$stmtDup = $pdo->prepare('
    SELECT id FROM denuncia
    WHERE  denunciante_id = ?
      AND  tipo           = ?
      AND  alvo_id        = ?
      AND  criado_em     >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    LIMIT  1
');
$stmtDup->execute([$denuncianteId, $tipo, $alvoId]);
if ($stmtDup->fetch()) {
    echo json_encode(['ok' => false, 'mensagem' => 'Você já reportou este item recentemente.']);
    exit;
}

$stmt = $pdo->prepare('
    INSERT INTO denuncia (denunciante_id, tipo, alvo_id, motivo)
    VALUES (?, ?, ?, ?)
');
$stmt->execute([$denuncianteId, $tipo, $alvoId, $motivo]);

echo json_encode([
    'ok'       => true,
    'mensagem' => 'Denúncia registrada. Nossa equipe irá analisar em breve.',
]);