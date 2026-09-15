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
    echo json_encode(['ok' => false, 'mensagem' => 'Faça login para editar.']);
    exit;
}

$usuarioId = (int) $_SESSION['id'];
$jogoId    = isset($_POST['jogo_id']) ? (int) $_POST['jogo_id'] : 0;

if ($jogoId <= 0) {
    echo json_encode(['ok' => false, 'mensagem' => 'ID do jogo inválido.']);
    exit;
}

/* Verificar propriedade */
$stmt = $pdo->prepare('SELECT id, thumbnail, backdrop FROM jogo WHERE id = ? AND usuario_id = ? LIMIT 1');
$stmt->execute([$jogoId, $usuarioId]);
$jogoAtual = $stmt->fetch();

if (!$jogoAtual) {
    echo json_encode(['ok' => false, 'mensagem' => 'Jogo não encontrado ou sem permissão.']);
    exit;
}

/* =========================================================
   Ler e sanitizar dados do formulário
   ========================================================= */
$titulo    = mb_substr(trim($_POST['titulo'] ?? ''), 0, 100);
$tagline   = mb_substr(trim($_POST['tagline'] ?? ''), 0, 255);
$descricao = trim($_POST['descricao'] ?? '');
$genero    = mb_substr(trim($_POST['genero'] ?? ''), 0, 50);
$preco     = mb_substr(trim($_POST['preco'] ?? 'Gratis'), 0, 20);
$ehGratis  = (int) ($_POST['eh_gratis'] ?? 1);
$versao    = mb_substr(trim($_POST['versao'] ?? ''), 0, 30);
$tamanho   = mb_substr(trim($_POST['tamanho'] ?? ''), 0, 20);
$idiomas   = mb_substr(trim($_POST['idiomas'] ?? 'Portugues'), 0, 255);
$classificacao = mb_substr(trim($_POST['classificacao'] ?? 'Livre'), 0, 10);
$downloadUrl   = trim($_POST['download_url'] ?? '');
$visibilidade  = in_array($_POST['visibilidade'] ?? '', ['publico', 'privado']) ? $_POST['visibilidade'] : 'publico';

$tagsRaw = $_POST['tags'] ?? '';
if (is_string($tagsRaw)) {
    $tags = array_filter(array_map('trim', explode(',', $tagsRaw)));
} elseif (is_array($tagsRaw)) {
    $tags = array_map('trim', $tagsRaw);
} else {
    $tags = [];
}
$tags = array_slice($tags, 0, 20);

$plataformas = json_decode($_POST['plataformas'] ?? '[]', true);
if (!is_array($plataformas)) $plataformas = [];
$plataformas = array_values(array_intersect($plataformas, ['windows', 'apple', 'linux', 'android']));

$destaques = json_decode($_POST['destaques'] ?? '[]', true);
if (!is_array($destaques)) $destaques = [];
$destaques = array_slice($destaques, 0, 10);

$videosLinks = json_decode($_POST['videos_links'] ?? '[]', true);
if (!is_array($videosLinks)) $videosLinks = [];

$reqMin = json_decode($_POST['requisitos_minimo'] ?? '{}', true) ?? [];
$reqRec = json_decode($_POST['requisitos_recomendado'] ?? '{}', true) ?? [];

$customLayout = $_POST['custom_layout'] ?? null;

/* =========================================================
   Validações
   ========================================================= */
$erros = [];
if (mb_strlen($titulo) < 2) $erros[] = 'O título deve ter pelo menos 2 caracteres.';
if (empty($descricao)) $erros[] = 'A descrição é obrigatória.';

if (!empty($erros)) {
    echo json_encode(['ok' => false, 'mensagem' => implode(' | ', $erros)]);
    exit;
}

/* =========================================================
   Upload de novas imagens (se enviadas)
   ========================================================= */
function uploadImagem(array $file, string $pastaDestino, string $prefixo): ?string {
    if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    $maxSize = 8 * 1024 * 1024;
    if ($file['size'] > $maxSize) return null;

    $mimePermitidos = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeReal = $finfo->file($file['tmp_name']);
    if (!in_array($mimeReal, $mimePermitidos, true)) return null;

    $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $ext = $extMap[$mimeReal];

    if (!is_dir($pastaDestino) && !mkdir($pastaDestino, 0755, true)) return null;

    $nomeArquivo = "{$prefixo}.{$ext}";
    $caminhoFinal = $pastaDestino . '/' . $nomeArquivo;

    if (move_uploaded_file($file['tmp_name'], $caminhoFinal)) return $nomeArquivo;
    return null;
}

/* =========================================================
   Atualizar jogo no banco
   ========================================================= */
try {
    $pdo->beginTransaction();

    $pastaFinal = __DIR__ . '/../../public/assets/img/jogos/' . $jogoId;
    if (!is_dir($pastaFinal)) mkdir($pastaFinal, 0755, true);

    /* Upload de novas imagens */
    $thumbnail = $jogoAtual['thumbnail'];
    $backdrop  = $jogoAtual['backdrop'];

    if (!empty($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $novo = uploadImagem($_FILES['thumbnail'], $pastaFinal, 'thumbnail');
        if ($novo) $thumbnail = $novo;
    }
    if (!empty($_FILES['backdrop']) && $_FILES['backdrop']['error'] === UPLOAD_ERR_OK) {
        $novo = uploadImagem($_FILES['backdrop'], $pastaFinal, 'backdrop');
        if ($novo) $backdrop = $novo;
    }

    /* UPDATE jogo */
    $stmt = $pdo->prepare('
        UPDATE jogo SET
            titulo = ?, tagline = ?, descricao = ?, genero = ?,
            preco = ?, eh_gratis = ?, versao = ?, tamanho = ?,
            idiomas = ?, classificacao = ?, plataformas = ?,
            download_url = ?, destaques = ?, custom_layout = ?,
            thumbnail = ?, backdrop = ?, visibilidade = ?,
            atualizado_em = NOW()
        WHERE id = ?
    ');
    $stmt->execute([
        $titulo, $tagline !== '' ? $tagline : null, $descricao,
        $genero !== '' ? $genero : null,
        $preco, $ehGratis,
        $versao !== '' ? $versao : null,
        $tamanho !== '' ? $tamanho : null,
        $idiomas, $classificacao,
        !empty($plataformas) ? json_encode($plataformas) : null,
        $downloadUrl !== '' ? $downloadUrl : null,
        !empty($destaques) ? json_encode($destaques) : null,
        $customLayout,
        $thumbnail, $backdrop, $visibilidade,
        $jogoId,
    ]);

    /* Tags — delete antigos e reinsere */
    $pdo->prepare('DELETE FROM jogo_tags WHERE jogo_id = ?')->execute([$jogoId]);
    if (!empty($tags)) {
        $stmtTag = $pdo->prepare('INSERT INTO jogo_tags (jogo_id, tag) VALUES (?, ?)');
        foreach ($tags as $tag) $stmtTag->execute([$jogoId, $tag]);
    }

    /* Vídeos links — delete antigos e reinsere (mantém os que já existem no banco) */
    $pdo->prepare('DELETE FROM jogo_videos WHERE jogo_id = ? AND tipo != "arquivo"')->execute([$jogoId]);
    if (!empty($videosLinks)) {
        $stmtVideo = $pdo->prepare('INSERT INTO jogo_videos (jogo_id, tipo, url, titulo, ordem) VALUES (?, ?, ?, ?, ?)');
        foreach ($videosLinks as $i => $vid) {
            $tipo = 'link';
            if (preg_match('/(youtube\.com|youtu\.be)/', $vid['url'] ?? '')) $tipo = 'youtube';
            $stmtVideo->execute([$jogoId, $tipo, $vid['url'] ?? '', mb_substr($vid['titulo'] ?? '', 0, 100), $i]);
        }
    }

    /* Requisitos — delete antigos e reinsere */
    $pdo->prepare('DELETE FROM jogo_requisitos WHERE jogo_id = ?')->execute([$jogoId]);
    $stmtReq = $pdo->prepare('
        INSERT INTO jogo_requisitos
            (jogo_id, tipo, os, processador, memoria, video, armazenamento, directx, carga_cpu, carga_gpu, carga_ram)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $camposReq = ['os', 'processador', 'memoria', 'video', 'armazenamento', 'directx'];
    $camposCarga = ['carga_cpu', 'carga_gpu', 'carga_ram'];

    foreach (['minimo' => $reqMin, 'recomendado' => $reqRec] as $tipo => $req) {
        $params = [$jogoId, $tipo];
        foreach ($camposReq as $campo) $params[] = mb_substr($req[$campo] ?? '', 0, 100);
        foreach ($camposCarga as $campo) $params[] = min(100, max(0, (int) ($req[$campo] ?? 0)));
        $stmtReq->execute($params);
    }

    $pdo->commit();

    echo json_encode([
        'ok'       => true,
        'mensagem' => 'Jogo atualizado com sucesso!',
        'jogo_id'  => $jogoId,
        'slug'     => $jogoAtual['slug'] ?? '',
        'url'      => '/jogos/' . ($jogoAtual['slug'] ?? '') . '/',
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('Erro ao editar jogo: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensagem' => 'Erro ao atualizar o jogo. Tente novamente.']);
}
