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
$campo     = trim($_POST['campo'] ?? '');
$valor     = trim($_POST['valor'] ?? '');

$camposPermitidos = [
    'nome' => ['min' => 3, 'max' => 50,  'col' => 'nome'],
    'bio'  => ['min' => 0, 'max' => 300, 'col' => 'bio'],
];

if (!array_key_exists($campo, $camposPermitidos)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensagem' => 'Campo inválido.']);
    exit;
}

$regra = $camposPermitidos[$campo];
$len   = mb_strlen($valor);

if ($len < $regra['min'] || $len > $regra['max']) {
    echo json_encode([
        'ok'       => false,
        'mensagem' => "O campo deve ter entre {$regra['min']} e {$regra['max']} caracteres.",
    ]);
    exit;
}

if ($campo === 'nome') {
    $stmtCheck = $pdo->prepare('SELECT id FROM usuario WHERE nome = ? AND id <> ? LIMIT 1');
    $stmtCheck->execute([$valor, $usuarioId]);
    if ($stmtCheck->fetch()) {
        echo json_encode(['ok' => false, 'mensagem' => 'Este nome já está em uso.']);
        exit;
    }
}

$coluna = $regra['col'];
$stmt   = $pdo->prepare("UPDATE usuario SET {$coluna} = ? WHERE id = ?");
$stmt->execute([$valor !== '' ? $valor : null, $usuarioId]);

if ($campo === 'nome') {
    $_SESSION['nome'] = $valor;
}

echo json_encode(['ok' => true, 'mensagem' => 'Salvo com sucesso.']);