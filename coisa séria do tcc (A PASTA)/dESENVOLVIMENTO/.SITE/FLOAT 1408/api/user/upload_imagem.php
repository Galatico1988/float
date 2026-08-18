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
$tipo      = trim($_POST['tipo'] ?? '');

if (!in_array($tipo, ['avatar', 'banner'], true)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'mensagem' => 'Tipo inválido.']);
    exit;
}

if (empty($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'mensagem' => 'Nenhum arquivo recebido ou erro no upload.']);
    exit;
}

$arquivo = $_FILES['imagem'];

const MAX_SIZE = 5 * 1024 * 1024;
if ($arquivo['size'] > MAX_SIZE) {
    echo json_encode(['ok' => false, 'mensagem' => 'Arquivo muito grande. Máximo: 5 MB.']);
    exit;
}

$mimePermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeReal = $finfo->file($arquivo['tmp_name']);

if (!in_array($mimeReal, $mimePermitidos, true)) {
    echo json_encode(['ok' => false, 'mensagem' => 'Formato não suportado. Use JPG, PNG, GIF ou WEBP.']);
    exit;
}

$extMap = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
];
$ext = $extMap[$mimeReal];

$pastasDir = [
    'avatar' => __DIR__ . '/../../public/assets/img/user/avatars/',
    'banner' => __DIR__ . '/../../public/assets/img/user/banners/',
];
$pastaDestino = $pastasDir[$tipo];

if (!is_dir($pastaDestino) && !mkdir($pastaDestino, 0755, true)) {
    error_log("Float upload: falha ao criar diretório $pastaDestino");
    echo json_encode(['ok' => false, 'mensagem' => 'Erro interno ao salvar arquivo.']);
    exit;
}

$nomeArquivo  = "{$tipo}_{$usuarioId}.{$ext}";
$caminhoFinal = $pastaDestino . $nomeArquivo;

foreach ($extMap as $e) {
    $anterior = $pastaDestino . "{$tipo}_{$usuarioId}.{$e}";
    if ($anterior !== $caminhoFinal && file_exists($anterior)) {
        @unlink($anterior);
    }
}

if (!move_uploaded_file($arquivo['tmp_name'], $caminhoFinal)) {
    error_log("Float upload: falha ao mover arquivo para $caminhoFinal");
    echo json_encode(['ok' => false, 'mensagem' => 'Erro ao salvar o arquivo.']);
    exit;
}

$coluna = $tipo === 'avatar' ? 'avatar_path' : 'banner_path';
$stmt   = $pdo->prepare("UPDATE usuario SET {$coluna} = ? WHERE id = ?");
$stmt->execute([$nomeArquivo, $usuarioId]);

if ($tipo === 'avatar') {
    $_SESSION['avatar_path'] = $nomeArquivo;
}

$urlPublica = "../assets/img/user/{$tipo}s/{$nomeArquivo}";

echo json_encode([
    'ok'       => true,
    'mensagem' => 'Imagem atualizada com sucesso.',
    'url'      => $urlPublica,
]);