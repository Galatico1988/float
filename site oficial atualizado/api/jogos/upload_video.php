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

if (empty($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'mensagem' => 'Nenhum arquivo recebido ou erro no upload.']);
    exit;
}

$arquivo = $_FILES['video'];

/* Máximo 50MB para vídeos */
const MAX_SIZE = 50 * 1024 * 1024;
if ($arquivo['size'] > MAX_SIZE) {
    echo json_encode(['ok' => false, 'mensagem' => 'Arquivo muito grande. Máximo: 50 MB.']);
    exit;
}

/* Validar MIME type */
$mimePermitidos = ['video/mp4', 'video/webm'];
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeReal = $finfo->file($arquivo['tmp_name']);

if (!in_array($mimeReal, $mimePermitidos, true)) {
    echo json_encode(['ok' => false, 'mensagem' => 'Formato não suportado. Use MP4 ou WebM.']);
    exit;
}

$extMap = [
    'video/mp4'  => 'mp4',
    'video/webm' => 'webm',
];
$ext = $extMap[$mimeReal];

/* Criar pasta de vídeos do jogo se não existir */
$pastaJogo = __DIR__ . '/../../public/assets/videos/jogos/' . $jogoId;
if (!is_dir($pastaJogo) && !mkdir($pastaJogo, 0755, true)) {
    echo json_encode(['ok' => false, 'mensagem' => 'Erro interno ao criar diretório.']);
    exit;
}

/* Gerar nome único */
$ordem = (int) ($_POST['ordem'] ?? 0);
$titulo = mb_substr(trim($_POST['titulo'] ?? ''), 0, 100);
$nomeArquivo  = "video_" . time() . "_" . bin2hex(random_bytes(4)) . ".{$ext}";
$caminhoFinal = $pastaJogo . '/' . $nomeArquivo;

if (!move_uploaded_file($arquivo['tmp_name'], $caminhoFinal)) {
    error_log("Float upload_video: falha ao mover arquivo para $caminhoFinal");
    echo json_encode(['ok' => false, 'mensagem' => 'Erro ao salvar o arquivo.']);
    exit;
}

/* Inserir no banco */
$urlPublica = '/float/public/assets/videos/jogos/' . $jogoId . '/' . $nomeArquivo;
$stmt = $pdo->prepare('INSERT INTO jogo_videos (jogo_id, tipo, url, titulo, ordem) VALUES (?, ?, ?, ?, ?)');
$stmt->execute([$jogoId, 'arquivo', $urlPublica, $titulo !== '' ? $titulo : null, $ordem]);

$videoId = (int) $pdo->lastInsertId();

echo json_encode([
    'ok'       => true,
    'mensagem' => 'Vídeo adicionado.',
    'id'       => $videoId,
    'tipo'     => 'arquivo',
    'url'      => $urlPublica,
    'titulo'   => $titulo,
]);
