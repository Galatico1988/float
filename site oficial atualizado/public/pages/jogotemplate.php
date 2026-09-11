<?php
if (!isset($_SESSION)) {
    session_start();
}

/* =========================================================
   Ajuste este caminho conforme a profundidade real do arquivo
   (ex: se ficar em /public/pages/, use '../../')
   ========================================================= */
$basePath   = '../../';
$activePage = 'jogos';

/* =========================================================
   DADOS DO JOGO
   Troque este bloco por dados vindos do banco (ex: via $_GET['slug']).
   Os valores abaixo são de exemplo, reaproveitando os assets
   que já existem no projeto.
   ========================================================= */
$jogo = [
    'titulo'      => 'Hades II',
    'desenvolvedor' => 'Supergiant Games',
    'tagline'     => 'A sequência do premiado roguelite da Supergiant Games chega em Early Access. Mais profundo, mais sombrio e mais viciante do que jamais foi.',
    'descricao'   => 'Empunhe magia e armas duplas na pele de Melinoë, a Princesa do Submundo imortal, em sua missão para derrotar o Titã do Tempo. Explore um Submundo em constante mudança, forje alianças com deuses do Olimpo e enfrente investidas cada vez mais desafiadoras rumo à superfície.',
    'capa'        => '../assets/img/pages/index/cards/card_hadesII.png',
    'backdrop'    => '../assets/img/pages/index/slides/slide_03.png',
    'preco'       => 'R$ 59,99',
    'gratis'      => false,
    'versao'      => 'v1.0.4 (Early Access)',
    'tamanho'     => '4.8 GB',
    'atualizado'  => '10 de maio de 2026',
    'idiomas'     => 'Português, Inglês, Espanhol, Japonês',
    'classificacao' => '14 anos',
    'tags'        => ['Roguelite', 'Early Access', 'Single Player'],
    'plataformas' => ['windows', 'apple'],
    'download_url' => '#',

    'destaques' => [
        ['icone' => 'fa-wand-magic-sparkles', 'texto' => 'Novo sistema de magia com Melinoë'],
        ['icone' => 'fa-users',               'texto' => 'Novos deuses e aliados do Olimpo'],
        ['icone' => 'fa-map',                 'texto' => 'Submundo com layout dinâmico'],
        ['icone' => 'fa-arrows-rotate',       'texto' => 'Atualizações frequentes em Early Access'],
    ],

    'screenshots' => [
        '../assets/img/pages/index/slides/slide_03.png',
        '../assets/img/pages/index/cards/card_hadesII.png',
        '../assets/img/pages/index/slides/slide_02.png',
        '../assets/img/pages/index/slides/slide_01.png',
        '../assets/img/pages/index/slides/slide_05.png',
    ],

    'requisitos' => [
        'minimo' => [
            'os' => 'Windows 10 (64-bit)', 'proc' => 'Intel i3-4160 / AMD FX-6300',
            'mem' => '4 GB RAM', 'gpu' => 'GTX 660 2GB / Radeon HD 7850 2GB',
            'store' => '5 GB disponíveis', 'dx' => 'Versão 11',
            'carga_cpu' => 30, 'carga_gpu' => 35, 'carga_ram' => 25,
        ],
        'recomendado' => [
            'os' => 'Windows 10/11 (64-bit)', 'proc' => 'Intel i5-8400 / AMD Ryzen 5 2600',
            'mem' => '8 GB RAM', 'gpu' => 'GTX 1060 6GB / RX 580 8GB',
            'store' => '5 GB disponíveis (SSD recomendado)', 'dx' => 'Versão 12',
            'carga_cpu' => 60, 'carga_gpu' => 65, 'carga_ram' => 55,
        ],
    ],
];

/* Jogos relacionados (reaproveita o padrão .cf-card do index.css) */
$relacionados = [
    ['titulo' => 'Hollow Knight: Silksong', 'tipo' => 'Jogo Base', 'preco' => 'Grátis', 'img' => '../assets/img/pages/index/cards/card_silksong.png'],
    ['titulo' => 'Dead Cells',              'tipo' => 'Roguelike', 'preco' => 'R$ 49,99', 'img' => '../assets/img/pages/index/cards/card_dead_cells.png'],
    ['titulo' => 'Undertale',               'tipo' => 'RPG',       'preco' => 'R$ 19,99', 'img' => '../assets/img/pages/index/cards/card_undertale.png'],
    ['titulo' => 'Cuphead',                 'tipo' => 'Run & Gun', 'preco' => 'R$ 44,99', 'img' => '../assets/img/pages/index/cards/card_cuphead.png'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= htmlspecialchars($jogo['titulo']) ?> - Baixe agora na Float." />
    <title>Float | <?= htmlspecialchars($jogo['titulo']) ?></title>

    <link rel="stylesheet" href="../assets/css/global/global.css" />
    <link rel="stylesheet" href="../assets/css/pages/jogotemplate.css">

    <link rel="icon" href="../assets/img/favicon/icone.ico" type="image/x-icon" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />]

    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet" />
</head>
<body>

    <?php require_once __DIR__ . '/../includes/header.php'; ?>

    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>

    <!-- Barra fixa de download (aparece ao rolar) -->
    <div class="sticky-download-bar" id="stickyDownloadBar">
        <div class="sdb-inner">
            <img src="<?= $jogo['capa'] ?>" alt="" class="sdb-thumb">
            <span class="sdb-title"><?= htmlspecialchars($jogo['titulo']) ?></span>
            <span class="sdb-price"><?= $jogo['gratis'] ? 'Grátis' : htmlspecialchars($jogo['preco']) ?></span>
            <a href="<?= htmlspecialchars($jogo['download_url']) ?>" class="btn-buy sdb-btn">
                <i class="fa-solid fa-download"></i> Baixar
            </a>
        </div>
    </div>

    <main>

        <!-- ===================== HERO ===================== -->
        <section class="game-hero-section" style="--game-backdrop: url('<?= $jogo['backdrop'] ?>');">
            <div class="game-hero-bg"></div>
            <div class="game-hero-scrim"></div>

            <div class="game-hero-content">
                <div class="game-hero-cover reveal-hidden">
                    <img src="<?= $jogo['capa'] ?>" alt="Capa de <?= htmlspecialchars($jogo['titulo']) ?>">
                </div>

                <div class="game-hero-info reveal-hidden">
                    <div class="game-hero-tags">
                        <?php foreach ($jogo['tags'] as $tag): ?>
                            <span class="promo-tag"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>

                    <h1 class="game-hero-title"><?= htmlspecialchars($jogo['titulo']) ?></h1>
                    <p class="game-hero-dev">por <?= htmlspecialchars($jogo['desenvolvedor']) ?></p>
                    <p class="game-hero-tagline"><?= htmlspecialchars($jogo['tagline']) ?></p>

                    <div class="game-hero-actions">
                        <a href="<?= htmlspecialchars($jogo['download_url']) ?>" class="btn-buy game-download-btn">
                            <i class="fa-solid fa-download"></i>
                            <?= $jogo['gratis'] ? 'Baixar Grátis' : 'Baixar Agora — ' . htmlspecialchars($jogo['preco']) ?>
                        </a>
                        <button class="btn-wishlist" title="Lista de Desejos">
                            <i class="fa-regular fa-bookmark"></i>
                        </button>
                        <div class="game-platforms">
                            <?php foreach ($jogo['plataformas'] as $plat): ?>
                                <i class="fa-brands fa-<?= $plat ?>" title="<?= ucfirst($plat) ?>"></i>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="game-body-grid">

            <!-- ===================== COLUNA PRINCIPAL ===================== -->
            <div class="game-main-col">

                <!-- Galeria -->
                <section class="game-gallery-section reveal-hidden">
                    <div class="game-gallery-main">
                        <img src="<?= $jogo['screenshots'][0] ?>" alt="Screenshot principal" id="galleryMain">
                    </div>
                    <div class="game-gallery-thumbs" id="galleryThumbs">
                        <?php foreach ($jogo['screenshots'] as $i => $shot): ?>
                            <div class="gallery-thumb <?= $i === 0 ? 'active' : '' ?>" data-src="<?= $shot ?>">
                                <img src="<?= $shot ?>" alt="Screenshot <?= $i + 1 ?>" loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Sobre -->
                <section class="game-about-section reveal-hidden">
                    <h2 class="store-title">Sobre o Jogo <i class="fa-solid fa-arrow-right"></i></h2>
                    <p class="game-about-text"><?= htmlspecialchars($jogo['descricao']) ?></p>

                    <ul class="game-highlights">
                        <?php foreach ($jogo['destaques'] as $item): ?>
                            <li>
                                <i class="fa-solid <?= $item['icone'] ?>"></i>
                                <span><?= htmlspecialchars($item['texto']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <!-- Requisitos / Painel de compatibilidade -->
                <section class="game-requirements-section reveal-hidden">
                    <h2 class="store-title">Diagnóstico de Compatibilidade <i class="fa-solid fa-microchip"></i></h2>

                    <div class="req-grid">
                        <?php foreach (['minimo' => 'Mínimo', 'recomendado' => 'Recomendado'] as $key => $label): $r = $jogo['requisitos'][$key]; ?>
                            <div class="req-col">
                                <span class="req-col-label"><?= $label ?></span>

                                <ul class="req-spec-list">
                                    <li><span>Sistema</span><strong><?= htmlspecialchars($r['os']) ?></strong></li>
                                    <li><span>Processador</span><strong><?= htmlspecialchars($r['proc']) ?></strong></li>
                                    <li><span>Memória</span><strong><?= htmlspecialchars($r['mem']) ?></strong></li>
                                    <li><span>Vídeo</span><strong><?= htmlspecialchars($r['gpu']) ?></strong></li>
                                    <li><span>Armazenamento</span><strong><?= htmlspecialchars($r['store']) ?></strong></li>
                                    <li><span>DirectX</span><strong><?= htmlspecialchars($r['dx']) ?></strong></li>
                                </ul>

                                <div class="req-meters">
                                    <div class="req-meter">
                                        <span>CPU</span>
                                        <div class="req-meter-track"><div class="req-meter-fill" data-value="<?= $r['carga_cpu'] ?>"></div></div>
                                    </div>
                                    <div class="req-meter">
                                        <span>GPU</span>
                                        <div class="req-meter-track"><div class="req-meter-fill" data-value="<?= $r['carga_gpu'] ?>"></div></div>
                                    </div>
                                    <div class="req-meter">
                                        <span>RAM</span>
                                        <div class="req-meter-track"><div class="req-meter-fill" data-value="<?= $r['carga_ram'] ?>"></div></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

            </div>

            <!-- ===================== SIDEBAR ===================== -->
            <aside class="game-side-col">
                <div class="game-info-card reveal-hidden">
                    <a href="<?= htmlspecialchars($jogo['download_url']) ?>" class="btn-buy game-download-btn full-width">
                        <i class="fa-solid fa-download"></i>
                        <?= $jogo['gratis'] ? 'Baixar Grátis' : 'Baixar — ' . htmlspecialchars($jogo['preco']) ?>
                    </a>
                    <button class="btn-wishlist full-width">
                        <i class="fa-regular fa-bookmark"></i> Adicionar à Lista de Desejos
                    </button>

                    <div class="game-info-divider"></div>

                    <ul class="game-info-list">
                        <li><span>Desenvolvedor</span><strong><?= htmlspecialchars($jogo['desenvolvedor']) ?></strong></li>
                        <li><span>Versão</span><strong><?= htmlspecialchars($jogo['versao']) ?></strong></li>
                        <li><span>Tamanho</span><strong><?= htmlspecialchars($jogo['tamanho']) ?></strong></li>
                        <li><span>Atualizado em</span><strong><?= htmlspecialchars($jogo['atualizado']) ?></strong></li>
                        <li><span>Idiomas</span><strong><?= htmlspecialchars($jogo['idiomas']) ?></strong></li>
                        <li><span>Classificação</span><strong><?= htmlspecialchars($jogo['classificacao']) ?></strong></li>
                    </ul>
                </div>
            </aside>
        </div>

        <!-- ===================== RELACIONADOS ===================== -->
        <section class="store-section">
            <div class="store-header">
                <h2 class="store-title">Mais como este <i class="fa-solid fa-arrow-right"></i></h2>
            </div>
            <div class="coverflow-track-wrapper">
                <div class="coverflow-track cf-static">
                    <?php foreach ($relacionados as $rel): ?>
                        <div class="cf-card cf-active">
                            <div class="cf-card-cover">
                                <img src="<?= $rel['img'] ?>" alt="<?= htmlspecialchars($rel['titulo']) ?>" loading="lazy">
                                <div class="cf-card-overlay"><i class="fa-solid fa-play"></i></div>
                            </div>
                            <div class="cf-card-info">
                                <span class="cf-game-type"><?= htmlspecialchars($rel['tipo']) ?></span>
                                <h3 class="cf-game-title"><?= htmlspecialchars($rel['titulo']) ?></h3>
                                <p class="cf-game-price"><?= htmlspecialchars($rel['preco']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</html>