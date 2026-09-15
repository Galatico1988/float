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
$stmt = $pdo->prepare('SELECT id FROM jogo WHERE id = ? AND usuario_id = ? LIMIT 1');
$stmt->execute([$jogoId, $usuarioId]);
if (!$stmt->fetch()) {
    echo json_encode(['ok' => false, 'mensagem' => 'Jogo não encontrado ou sem permissão.']);
    exit;
}

if (empty($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'mensagem' => 'Nenhum arquivo recebido ou erro no upload.']);
    exit;
}

$arquivo = $_FILES['imagem'];

/* Máximo 10MB para screenshots */
const MAX_SIZE = 10 * 1024 * 1024;
if ($arquivo['size'] > MAX_SIZE) {
    echo json_encode(['ok' => false, 'mensagem' => 'Arquivo muito grande. Máximo: 10 MB.']);
    exit;
}

/* Validar MIME type */
$mimePermitidos = ['image/jpeg', 'image/png', 'image/webp'];
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeReal = $finfo->file($arquivo['tmp_name']);

if (!in_array($mimeReal, $mimePermitidos, true)) {
    echo json_encode(['ok' => false, 'mensagem' => 'Formato não suportado. Use JPG, PNG ou WEBP.']);
    exit;
}

$extMap = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
];
$ext = $extMap[$mimeReal];

/* Criar pasta do jogo se não existir */
$pastaJogo = __DIR__ . '/../../public/assets/img/jogos/' . $jogoId;
if (!is_dir($pastaJogo) && !mkdir($pastaJogo, 0755, true)) {
    echo json_encode(['ok' => false, 'mensagem' => 'Erro interno ao criar diretório.']);
    exit;
}

/* Gerar nome único */
$ordem = (int) ($_POST['ordem'] ?? 0);
$nomeArquivo  = "screenshot_" . time() . "_" . bin2hex(random_bytes(4)) . ".{$ext}";
$caminhoFinal = $pastaJogo . '/' . $nomeArquivo;

if (!move_uploaded_file($arquivo['tmp_name'], $caminhoFinal)) {
    error_log("Float upload_screenshot: falha ao mover arquivo para $caminhoFinal");
    echo json_encode(['ok' => false, 'mensagem' => 'Erro ao salvar o arquivo.']);
    exit;
}

/* Inserir no banco */
$stmt = $pdo->prepare('INSERT INTO jogo_screenshots (jogo_id, caminho, ordem) VALUES (?, ?, ?)');
$stmt->execute([$jogoId, $nomeArquivo, $ordem]);

$screenshotId = (int) $pdo->lastInsertId();

$urlPublica = '/float/public/assets/img/jogos/' . $jogoId . '/' . $nomeArquivo;

echo json_encode([
    'ok'       => true,
    'mensagem' => 'Screenshot adicionado.',
    'id'       => $screenshotId,
    'caminho'  => $nomeArquivo,
    'url'      => $urlPublica,
]);
