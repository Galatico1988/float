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
    echo json_encode(['ok' => false, 'mensagem' => 'Faça login para publicar.']);
    exit;
}

$usuarioId = (int) $_SESSION['id'];
$legenda   = mb_substr(trim($_POST['legenda'] ?? ''), 0, 500);
$imagemUrl = trim($_POST['imagem_url'] ?? '');

if (mb_strlen($legenda) < 3 && empty($imagemUrl)) {
    echo json_encode(['ok' => false, 'mensagem' => 'A postagem precisa ter texto ou imagem.']);
    exit;
}

if ($imagemUrl !== '') {
    if (!filter_var($imagemUrl, FILTER_VALIDATE_URL)) {
        echo json_encode(['ok' => false, 'mensagem' => 'URL de imagem inválida.']);
        exit;
    }
    $imagemUrl = htmlspecialchars($imagemUrl, ENT_QUOTES, 'UTF-8');
}

try {
    $stmt = $pdo->prepare('
        INSERT INTO comunidade_posts (usuario_id, legenda, imagem_url)
        VALUES (?, ?, ?)
    ');
    $stmt->execute([
        $usuarioId,
        $legenda !== '' ? $legenda : null,
        $imagemUrl !== '' ? $imagemUrl : null,
    ]);

    $postId = (int) $pdo->lastInsertId();

    $stmtPost = $pdo->prepare('
        SELECT p.id, p.legenda, p.imagem_url, p.data_criacao,
               u.id AS usuario_id, u.nome, u.avatar_path
        FROM   comunidade_posts p
        JOIN   usuario u ON p.usuario_id = u.id
        WHERE  p.id = ?
        LIMIT  1
    ');
    $stmtPost->execute([$postId]);
    $post = $stmtPost->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'ok'       => true,
        'mensagem' => 'Postagem publicada!',
        'post'     => $post,
    ]);
} catch (PDOException $e) {
    error_log('Erro ao criar post: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensagem' => 'Erro ao publicar a postagem.']);
}