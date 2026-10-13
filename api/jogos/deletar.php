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
$jogoId    = isset($_POST['jogo_id']) ? (int) $_POST['jogo_id'] : 0;

if ($jogoId <= 0) {
    echo json_encode(['ok' => false, 'mensagem' => 'ID do jogo inválido.']);
    exit;
}

/* Verificar se o jogo pertence ao usuário */
$stmt = $pdo->prepare('SELECT id, thumbnail, backdrop FROM jogo WHERE id = ? AND usuario_id = ? LIMIT 1');
$stmt->execute([$jogoId, $usuarioId]);
$jogo = $stmt->fetch();

if (!$jogo) {
    echo json_encode(['ok' => false, 'mensagem' => 'Jogo não encontrado ou sem permissão.']);
    exit;
}

try {
    $pdo->beginTransaction();

    /* Deletar do banco (CASCADE remove tags, screenshots, videos, requisitos, avaliações) */
    $stmt = $pdo->prepare('DELETE FROM jogo WHERE id = ?');
    $stmt->execute([$jogoId]);

    /* Limpar pasta de imagens do jogo */
    $pastaJogo = __DIR__ . '/../../public/assets/img/jogos/' . $jogoId;
    if (is_dir($pastaJogo)) {
        $files = glob($pastaJogo . '/*');
        foreach ($files as $f) {
            if (is_file($f)) @unlink($f);
        }
        @rmdir($pastaJogo);
    }

    /* Limpar pasta de vídeos do jogo */
    $pastaVideos = __DIR__ . '/../../public/assets/videos/jogos/' . $jogoId;
    if (is_dir($pastaVideos)) {
        $files = glob($pastaVideos . '/*');
        foreach ($files as $f) {
            if (is_file($f)) @unlink($f);
        }
        @rmdir($pastaVideos);
    }

    $pdo->commit();

    echo json_encode(['ok' => true, 'mensagem' => 'Jogo excluído com sucesso.']);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('Erro ao deletar jogo: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensagem' => 'Erro ao excluir o jogo. Tente novamente.']);
}
