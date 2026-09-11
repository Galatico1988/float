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

$seguidorId = (int) $_SESSION['id'];
$seguidoId  = (int) ($_POST['usuario_id'] ?? 0);

if ($seguidoId <= 0 || $seguidoId === $seguidorId) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensagem' => 'ID do usuário inválido.']);
    exit;
}

try {
    $stmt = $pdo->prepare('
        SELECT 1 FROM seguidor 
        WHERE seguidor_id = ? AND seguido_id = ? 
        LIMIT 1
    ');
    $stmt->execute([$seguidorId, $seguidoId]);
    $jaSegue = (bool) $stmt->fetch();

    if ($jaSegue) {
        $stmt = $pdo->prepare('
            DELETE FROM seguidor 
            WHERE seguidor_id = ? AND seguido_id = ?
        ');
        $stmt->execute([$seguidorId, $seguidoId]);
        $acao = 'deixou de seguir';
    } else {
        $stmt = $pdo->prepare('
            INSERT INTO seguidor (seguidor_id, seguido_id) 
            VALUES (?, ?)
        ');
        $stmt->execute([$seguidorId, $seguidoId]);
        $acao = 'seguindo';
    }

    $stmt = $pdo->prepare('
        SELECT COUNT(*) as total FROM seguidor 
        WHERE seguido_id = ?
    ');
    $stmt->execute([$seguidoId]);
    $resultado = $stmt->fetch();
    $totalSeguidores = (int) $resultado['total'];

    echo json_encode([
        'ok'               => true,
        'mensagem'         => "Você {$acao}.",
        'acao'             => $acao,
        'total_seguidores' => $totalSeguidores,
    ]);
} catch (PDOException $e) {
    error_log("Erro ao seguir usuário: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensagem' => 'Erro ao processar seguimento.']);
}
