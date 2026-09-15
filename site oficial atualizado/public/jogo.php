<?php
if (!isset($_SESSION)) {
    session_start();
}

require_once __DIR__ . '/../api/config/conexao.php';

/* =========================================================
   Recebe slug pela URL amigável (/jogos/{slug}/)
   ou por query string (?slug=...) como fallback
   ========================================================= */
$slug = $_GET['slug'] ?? null;

if (empty($slug)) {
    http_response_code(404);
    echo '<p>Jogo não encontrado.</p>';
    exit;
}

/* =========================================================
   Buscar jogo pelo slug
   ========================================================= */
$stmt = $pdo->prepare('SELECT * FROM jogo WHERE slug = ? AND visibilidade = ? LIMIT 1');
$stmt->execute([$slug, 'publico']);
$jogoRow = $stmt->fetch();

if (!$jogoRow) {
    http_response_code(404);
    echo '<p>Jogo não encontrado.</p>';
    exit;
}

$jogoId = (int) $jogoRow['id'];

/* =========================================================
   Buscar tags
   ========================================================= */
$stmtTags = $pdo->prepare('SELECT tag FROM jogo_tags WHERE jogo_id = ?');
$stmtTags->execute([$jogoId]);
$jogoTags = $stmtTags->fetchAll(PDO::FETCH_COLUMN);

/* =========================================================
   Buscar screenshots (ordenados)
   ========================================================= */
$stmtShots = $pdo->prepare('SELECT caminho FROM jogo_screenshots WHERE jogo_id = ? ORDER BY ordem ASC');
$stmtShots->execute([$jogoId]);
$Screenshots = $stmtShots->fetchAll(PDO::FETCH_COLUMN);

/* =========================================================
   Buscar vídeos (ordenados)
   ========================================================= */
$stmtVideos = $pdo->prepare('SELECT tipo, url, titulo FROM jogo_videos WHERE jogo_id = ? ORDER BY ordem ASC');
$stmtVideos->execute([$jogoId]);
$jogoVideos = $stmtVideos->fetchAll();

/* =========================================================
   Buscar requisitos (mínimo e recomendado)
   ========================================================= */
$stmtReqs = $pdo->prepare('SELECT * FROM jogo_requisitos WHERE jogo_id = ?');
$stmtReqs->execute([$jogoId]);
$Requisitos = [];
while ($req = $stmtReqs->fetch()) {
    $Requisitos[$req['tipo']] = $req;
}

/* =========================================================
   Buscar desenvolvedor (nome do dono)
   ========================================================= */
$stmtDev = $pdo->prepare('SELECT nome FROM usuario WHERE id = ? LIMIT 1');
$stmtDev->execute([$jogoRow['usuario_id']]);
$devNome = $stmtDev->fetchColumn() ?: 'Desconhecido';

/* =========================================================
   Montar array $jogo no formato que o template espera
   ========================================================= */
$jogo = [
    'titulo'        => $jogoRow['titulo'],
    'slug'          => $jogoRow['slug'],
    'desenvolvedor' => $devNome,
    'tagline'       => $jogoRow['tagline'] ?? '',
    'descricao'     => $jogoRow['descricao'] ?? '',
    'capa'          => $jogoRow['thumbnail'] ? '/float/public/assets/img/jogos/' . $jogoId . '/' . $jogoRow['thumbnail'] : '/float/public/assets/img/pages/index/cards/card_undertale.png',
    'backdrop'      => $jogoRow['backdrop'] ? '/float/public/assets/img/jogos/' . $jogoId . '/' . $jogoRow['backdrop'] : '/float/public/assets/img/pages/index/slides/slide_03.png',
    'preco'         => $jogoRow['preco'] ?? 'Gratis',
    'gratis'        => (bool) ($jogoRow['eh_gratis'] ?? 1),
    'versao'        => $jogoRow['versao'] ?? '',
    'tamanho'       => $jogoRow['tamanho'] ?? '',
    'atualizado'    => date('d \d\e F \d\e Y', strtotime($jogoRow['atualizado_em'])),
    'idiomas'       => $jogoRow['idiomas'] ?? 'Portugues',
    'classificacao' => $jogoRow['classificacao'] ?? 'Livre',
    'tags'          => $jogoTags,
    'plataformas'   => json_decode($jogoRow['plataformas'] ?? '[]', true),
    'download_url'  => $jogoRow['download_url'] ?? '#',
    'destaques'     => json_decode($jogoRow['destaques'] ?? '[]', true),
    'screenshots'   => array_map(fn($c) => '/float/public/assets/img/jogos/' . $jogoId . '/' . $c, $Screenshots),
    'videos'        => $jogoVideos,
    'requisitos'    => [
        'minimo'      => isset($Requisitos['minimo']) ? $Requisitos['minimo'] : [
            'os' => '', 'processador' => '', 'memoria' => '', 'video' => '',
            'armazenamento' => '', 'directx' => '', 'carga_cpu' => 0, 'carga_gpu' => 0, 'carga_ram' => 0,
        ],
        'recomendado' => isset($Requisitos['recomendado']) ? $Requisitos['recomendado'] : [
            'os' => '', 'processador' => '', 'memoria' => '', 'video' => '',
            'armazenamento' => '', 'directx' => '', 'carga_cpu' => 0, 'carga_gpu' => 0, 'carga_ram' => 0,
        ],
    ],
];

/* =========================================================
   Buscar jogos relacionados (mesmo gênero, exceto este)
   ========================================================= */
if (!empty($jogoRow['genero'])) {
    $stmtRel = $pdo->prepare('
        SELECT j.titulo, j.genero, j.preco, j.thumbnail, j.slug
        FROM jogo j
        WHERE j.genero = ? AND j.id != ? AND j.visibilidade = ?
        ORDER BY RAND() LIMIT 4
    ');
    $stmtRel->execute([$jogoRow['genero'], $jogoId, 'publico']);
    $relRows = $stmtRel->fetchAll();

    $relacionados = array_map(fn($r) => [
        'titulo' => $r['titulo'],
        'tipo'   => $r['genero'],
        'preco'  => $r['preco'] ?? 'Gratis',
        'img'    => $r['thumbnail'] ? '/float/public/assets/img/jogos/' . $r['id'] . '/' . $r['thumbnail'] : '/float/public/assets/img/pages/index/cards/card_undertale.png',
        'slug'   => $r['slug'],
    ], $relRows);
} else {
    $relacionados = [];
}

/* =========================================================
   Incluir o template
   ========================================================= */
require_once __DIR__ . '/pages/jogotemplate.php';
