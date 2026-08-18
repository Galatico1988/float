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
$postId        = (int) ($_POST['post_id'] ?? 0);
$motivo        = mb_substr(trim($_POST['motivo'] ?? ''), 0, 255);

if ($postId <= 0) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensagem' => 'Post inválido.']);
    exit;
}

if (mb_strlen($motivo) < 3) {
    echo json_encode(['ok' => false, 'mensagem' => 'Informe um motivo válido.']);
    exit;
}

try {
    $stmtEx = $pdo->prepare('SELECT id FROM comunidade_posts WHERE id = ? LIMIT 1');
    $stmtEx->execute([$postId]);
    if (!$stmtEx->fetch()) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'mensagem' => 'Post não encontrado.']);
        exit;
    }

    $stmtDup = $pdo->prepare('
        SELECT id FROM denuncia
        WHERE  denunciante_id = ?
          AND  tipo           = "post"
          AND  alvo_id        = ?
          AND  criado_em     >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        LIMIT  1
    ');
    $stmtDup->execute([$denuncianteId, $postId]);
    if ($stmtDup->fetch()) {
        echo json_encode(['ok' => false, 'mensagem' => 'Você já reportou esta postagem recentemente.']);
        exit;
    }

    $stmt = $pdo->prepare('
        INSERT INTO denuncia (denunciante_id, tipo, alvo_id, motivo)
        VALUES (?, "post", ?, ?)
    ');
    $stmt->execute([$denuncianteId, $postId, $motivo]);

    echo json_encode([
        'ok'       => true,
        'mensagem' => 'Denúncia registrada. Nossa equipe irá analisar em breve.',
    ]);
} catch (PDOException $e) {
    error_log('Erro ao reportar post: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensagem' => 'Erro ao processar denúncia.']);
}