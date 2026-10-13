<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'mensagem' => 'Não autenticado.']);
    exit;
}

$usuarioId = (int) $_SESSION['id'];
$jogoId    = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($jogoId <= 0) {
    echo json_encode(['ok' => false, 'mensagem' => 'ID do jogo inválido.']);
    exit;
}

/* Buscar jogo (só se pertence ao usuário) */
$stmt = $pdo->prepare('SELECT * FROM jogo WHERE id = ? AND usuario_id = ? LIMIT 1');
$stmt->execute([$jogoId, $usuarioId]);
$jogo = $stmt->fetch();

if (!$jogo) {
    echo json_encode(['ok' => false, 'mensagem' => 'Jogo não encontrado ou sem permissão.']);
    exit;
}

/* Tags */
$stmtTags = $pdo->prepare('SELECT tag FROM jogo_tags WHERE jogo_id = ?');
$stmtTags->execute([$jogoId]);
$tags = $stmtTags->fetchAll(PDO::FETCH_COLUMN);

/* Screenshots */
$stmtShots = $pdo->prepare('SELECT id, caminho, ordem FROM jogo_screenshots WHERE jogo_id = ? ORDER BY ordem ASC');
$stmtShots->execute([$jogoId]);
$screenshots = $stmtShots->fetchAll();

/* Vídeos */
$stmtVid = $pdo->prepare('SELECT id, tipo, url, titulo, ordem FROM jogo_videos WHERE jogo_id = ? ORDER BY ordem ASC');
$stmtVid->execute([$jogoId]);
$videos = $stmtVid->fetchAll();

/* Requisitos */
$stmtReq = $pdo->prepare('SELECT * FROM jogo_requisitos WHERE jogo_id = ?');
$stmtReq->execute([$jogoId]);
$reqMin = null;
$reqRec = null;
while ($req = $stmtReq->fetch()) {
    if ($req['tipo'] === 'minimo') $reqMin = $req;
    if ($req['tipo'] === 'recomendado') $reqRec = $req;
}

echo json_encode([
    'ok' => true,
    'jogo' => [
        'id'            => (int) $jogo['id'],
        'titulo'        => $jogo['titulo'],
        'slug'          => $jogo['slug'],
        'tagline'       => $jogo['tagline'] ?? '',
        'descricao'     => $jogo['descricao'] ?? '',
        'genero'        => $jogo['genero'] ?? '',
        'preco'         => $jogo['preco'] ?? 'Gratis',
        'eh_gratis'     => (int) ($jogo['eh_gratis'] ?? 1),
        'versao'        => $jogo['versao'] ?? '',
        'tamanho'       => $jogo['tamanho'] ?? '',
        'idiomas'       => $jogo['idiomas'] ?? 'Portugues',
        'classificacao' => $jogo['classificacao'] ?? 'Livre',
        'plataformas'   => json_decode($jogo['plataformas'] ?? '[]', true),
        'download_url'  => $jogo['download_url'] ?? '',
        'thumbnail'     => $jogo['thumbnail'] ?? null,
        'backdrop'      => $jogo['backdrop'] ?? null,
        'destaques'     => json_decode($jogo['destaques'] ?? '[]', true),
    ],
    'tags'        => $tags,
    'screenshots' => $screenshots,
    'videos'      => $videos,
    'requisitos'  => [
        'minimo' => $reqMin,
        'recomendado' => $reqRec,
    ],
]);
