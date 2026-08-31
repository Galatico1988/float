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
    echo json_encode(['ok' => false, 'mensagem' => 'Não autenticado.']);
    exit;
}

$usuarioId = (int) $_SESSION['id'];
$postId    = (int) ($_POST['post_id'] ?? 0);

if ($postId <= 0) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensagem' => 'ID do post inválido.']);
    exit;
}

try {
    $stmt = $pdo->prepare('
        SELECT 1 FROM curtida_post 
        WHERE usuario_id = ? AND post_id = ? 
        LIMIT 1
    ');
    $stmt->execute([$usuarioId, $postId]);
    $jaCurtiu = (bool) $stmt->fetch();

    if ($jaCurtiu) {
        $stmt = $pdo->prepare('
            DELETE FROM curtida_post 
            WHERE usuario_id = ? AND post_id = ?
        ');
        $stmt->execute([$usuarioId, $postId]);
        $acao = 'descurtido';
    } else {
        $stmt = $pdo->prepare('
            INSERT INTO curtida_post (usuario_id, post_id) 
            VALUES (?, ?)
        ');
        $stmt->execute([$usuarioId, $postId]);
        $acao = 'curtido';
    }

    $stmt = $pdo->prepare('
        SELECT COUNT(*) as total FROM curtida_post 
        WHERE post_id = ?
    ');
    $stmt->execute([$postId]);
    $resultado = $stmt->fetch();
    $totalCurtidas = (int) $resultado['total'];

    echo json_encode([
        'ok'               => true,
        'mensagem'         => "Post {$acao} com sucesso.",
        'acao'             => $acao,
        'total_curtidas'   => $totalCurtidas,
    ]);
} catch (PDOException $e) {
    error_log("Erro ao curtir post: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensagem' => 'Erro ao processar curtida.']);
}
