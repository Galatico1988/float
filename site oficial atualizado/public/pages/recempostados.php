<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../api/config/conexao.php';

$basePath   = '../';
$activePage = 'recempostados';

$categorias = [
    'geral'    => ['nome' => 'Geral',         'icone' => 'fa-solid fa-border-all',    'generos' => []],
    'terror'   => ['nome' => 'Terror',        'icone' => 'fa-solid fa-ghost',         'generos' => ['Terror', 'Suspense']],
    'aventura' => ['nome' => 'Aventura',      'icone' => 'fa-solid fa-compass',       'generos' => ['Aventura', 'RPG', 'Visual Novel']],
    'quebra'   => ['nome' => 'Quebra-cabeça', 'icone' => 'fa-solid fa-puzzle-piece',  'generos' => ['Puzzle', 'Quebra-cabeça']],
    'acao'     => ['nome' => 'Ação',          'icone' => 'fa-solid fa-crosshairs',    'generos' => ['Ação', 'Run & Gun', 'Roguelite', 'Roguelike', 'Luta']],
];

$stmtJogos = $pdo->prepare('
    SELECT   j.id, j.titulo, j.slug, j.descricao, j.thumbnail, j.genero,
             j.preco, j.eh_gratis, j.criado_em,
             COALESCE(AVG(a.nota), 0) AS media_nota,
             COUNT(a.id)              AS total_aval
    FROM     jogo j
    LEFT JOIN avaliacao a ON a.jogo_id = j.id
    WHERE    j.visibilidade = \'publico\'
    GROUP BY j.id
    ORDER BY j.criado_em DESC
');
$stmtJogos->execute();
$jogos = $stmtJogos->fetchAll();

$contagens = array_fill_keys(array_keys($categorias), 0);

foreach ($jogos as &$jogo) {
    $cats = ['geral'];
    $contagens['geral']++;

    foreach ($categorias as $chave => $cat) {
        if ($chave === 'geral') {
            continue;
        }
        if ($jogo['genero'] !== null && in_array($jogo['genero'], $cat['generos'], true)) {
            $cats[] = $chave;
            $contagens[$chave]++;
        }
    }

    $jogo['cats'] = $cats;
}
unset($jogo);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Descubra os jogos recém postados na Float.">
    <title>Float | Recém Postados</title>

    <link rel="stylesheet" href="<?= float_asset('css/global/global.css') ?>" />
    <link rel="stylesheet" href="<?= float_asset('css/pages/recempostados.css') ?>" />
    <link rel="icon" href="<?= float_asset('img/favicon/icone.ico') ?>" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet" />
</head>

<body>

    <?php require_once '../includes/header.php'; ?>

    <main class="rp-page">

        <section class="rp-page-header">
            <div class="rp-header-content">
                <div class="rp-header-icon">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div class="rp-header-text">
                    <h1>Recém Postados</h1>
                    <p>Os jogos mais recentes publicados pela comunidade Float.</p>
                </div>
            </div>
        </section>

        <div class="rp-toolbar">
            <div class="rp-filters" role="tablist" aria-label="Filtrar por categoria">
                <?php foreach ($categorias as $chave => $cat): ?>
                <button type="button"
                        class="rp-filter <?= $chave === 'geral' ? 'active' : '' ?>"
                        role="tab"
                        aria-selected="<?= $chave === 'geral' ? 'true' : 'false' ?>"
                        data-cat="<?= $chave ?>">
                    <i class="<?= $cat['icone'] ?>"></i>
                    <?= htmlspecialchars($cat['nome']) ?>
                    <span class="rp-filter-count"><?= $contagens[$chave] ?></span>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($jogos)): ?>
        <div class="rp-empty">
            <i class="fa-solid fa-ghost fa-3x" aria-hidden="true"></i>
            <h2>Nenhum jogo por aqui ainda</h2>
            <p>Assim que novos jogos forem publicados, eles aparecerão nesta página.</p>
        </div>
        <?php else: ?>
        <div class="rp-grid" id="rpGrid">
            <?php foreach ($jogos as $jogo):
                $thumbSrc = $jogo['thumbnail']
                    ? float_asset('img/jogos/') . $jogo['id'] . '/' . htmlspecialchars($jogo['thumbnail'])
                    : float_asset('img/pages/index/cards/card_undertale.png');
                $estrelas = round($jogo['media_nota']);
                $jogoSlug = $jogo['slug'] ?? $jogo['id'];
            ?>
            <a href="<?= float_game_url($jogoSlug) ?>"
               class="rp-card"
               data-cats="<?= htmlspecialchars(implode(' ', $jogo['cats'])) ?>">
                <div class="rp-card-thumb">
                    <img src="<?= $thumbSrc ?>"
                         alt="Capa de <?= htmlspecialchars($jogo['titulo']) ?>"
                         loading="lazy">
                    <?php if ($jogo['genero']): ?>
                    <span class="rp-badge-genero"><?= htmlspecialchars($jogo['genero']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="rp-card-info">
                    <h3 class="rp-titulo"><?= htmlspecialchars($jogo['titulo']) ?></h3>
                    <?php if ($jogo['descricao']): ?>
                    <p class="rp-desc"><?= htmlspecialchars(mb_substr($jogo['descricao'], 0, 90)) ?>...</p>
                    <?php endif; ?>
                    <div class="rp-footer">
                        <div class="rp-estrelas" aria-label="Avaliação: <?= $estrelas ?> de 5 estrelas">
                            <?php for ($s = 1; $s <= 5; $s++): ?>
                            <i class="fa-<?= $s <= $estrelas ? 'solid' : 'regular' ?> fa-star" aria-hidden="true"></i>
                            <?php endfor; ?>
                            <span class="rp-total-aval">(<?= $jogo['total_aval'] ?>)</span>
                        </div>
                        <time class="rp-data" datetime="<?= $jogo['criado_em'] ?>">
                            <?= date('d/m/Y', strtotime($jogo['criado_em'])) ?>
                        </time>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="rp-empty" id="rpEmpty" hidden>
            <i class="fa-solid fa-ghost fa-3x" aria-hidden="true"></i>
            <h2>Nada por aqui</h2>
            <p id="rpEmptyText">Nenhum jogo encontrado nesta categoria.</p>
        </div>
        <?php endif; ?>

    </main>

    <?php require_once '../includes/footer.php'; ?>

    <script src="<?= float_asset('js/global/global.js') ?>"></script>
    <script src="<?= float_asset('js/pages/recempostados.js') ?>"></script>

</body>

</html>
