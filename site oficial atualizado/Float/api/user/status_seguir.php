<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensagem' => 'Método não permitido.']);
    exit;
}

if (empty($_SESSION['id'])) {
    echo json_encode(['ok' => true, 'ja_segue' => false]);
    exit;
}

$sessaoId  = (int) $_SESSION['id'];
$alvoId    = (int) ($_GET['alvo_id'] ?? 0);

if ($alvoId <= 0) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensagem' => 'ID inválido.']);
    exit;
}

try {
    // Busca dados do usuário alvo
    $stmtUser = $pdo->prepare('SELECT id, nome, avatar_path, bio FROM usuario WHERE id = ? LIMIT 1');
    $stmtUser->execute([$alvoId]);
    $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        http_response_code(404);
        echo json_encode(['ok' => false, 'mensagem' => 'Usuário não encontrado.']);
        exit;
    }

    // Verifica se já segue
    $stmtCheck = $pdo->prepare('SELECT 1 FROM seguidor WHERE seguidor_id = ? AND seguido_id = ? LIMIT 1');
    $stmtCheck->execute([$sessaoId, $alvoId]);
    $jaSegue = (bool) $stmtCheck->fetch();

    // Total de seguidores do alvo
    $stmtTotal = $pdo->prepare('SELECT COUNT(*) FROM seguidor WHERE seguido_id = ?');
    $stmtTotal->execute([$alvoId]);
    $totalSeguidores = (int) $stmtTotal->fetchColumn();

    echo json_encode([
        'ok'               => true,
        'ja_segue'         => $jaSegue,
        'usuario'          => $usuario,
        'total_seguidores' => $totalSeguidores,
    ]);
} catch (PDOException $e) {
    error_log('Erro status seguir: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensagem' => 'Erro interno.']);
}