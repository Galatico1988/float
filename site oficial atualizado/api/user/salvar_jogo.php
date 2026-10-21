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
    echo json_encode(['ok' => false, 'mensagem' => 'Faça login para salvar jogos.']);
    exit;
}

$usuarioId = (int) $_SESSION['id'];
$jogoId    = (int) ($_POST['jogo_id'] ?? 0);

if ($jogoId <= 0) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensagem' => 'ID do jogo inválido.']);
    exit;
}

try {
    $stmtExiste = $pdo->prepare('SELECT id FROM jogo WHERE id = ? AND visibilidade = ? LIMIT 1');
    $stmtExiste->execute([$jogoId, 'publico']);
    if (!$stmtExiste->fetch()) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'mensagem' => 'Jogo não encontrado.']);
        exit;
    }

    $stmtCheck = $pdo->prepare('SELECT 1 FROM jogo_salvo WHERE usuario_id = ? AND jogo_id = ? LIMIT 1');
    $stmtCheck->execute([$usuarioId, $jogoId]);
    $jaSalvo = (bool) $stmtCheck->fetch();

    if ($jaSalvo) {
        $pdo->prepare('DELETE FROM jogo_salvo WHERE usuario_id = ? AND jogo_id = ?')
            ->execute([$usuarioId, $jogoId]);
        $acao = 'removido';
    } else {
        $pdo->prepare('INSERT INTO jogo_salvo (usuario_id, jogo_id) VALUES (?, ?)')
            ->execute([$usuarioId, $jogoId]);
        $acao = 'salvo';
    }

    echo json_encode([
        'ok'       => true,
        'acao'     => $acao,
        'mensagem' => $acao === 'salvo' ? 'Jogo adicionado à sua lista de desejos.' : 'Jogo removido da sua lista de desejos.',
    ]);
} catch (PDOException $e) {
    error_log('Erro ao salvar jogo: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensagem' => 'Erro ao processar a lista de desejos.']);
}
