<?php
if (!isset($_SESSION)) {
    session_start();
}

/* =========================================================
   Este template é incluído por jogo.php (via /jogos/{slug}/)
   ou pode ser acessado diretamente para desenvolvimento.
   As variáveis $jogo e $relacionados devem estar definidas
   antes deste include.
   ========================================================= */
$basePath   = '../../';
$activePage = 'jogos';

/* Se acessado diretamente (debug), usa dados de exemplo */
if (!isset($jogo)) {
    $jogo = [
        'titulo' => 'Jogo não encontrado', 'slug' => '', 'desenvolvedor' => '',
        'tagline' => '', 'descricao' => '', 'capa' => '', 'backdrop' => '',
        'preco' => 'Gratis', 'gratis' => true, 'versao' => '', 'tamanho' => '',
        'atualizado' => '', 'idiomas' => '', 'classificacao' => 'Livre',
        'tags' => [], 'plataformas' => [], 'download_url' => '#',
        'destaques' => [], 'screenshots' => [], 'videos' => [],
        'requisitos' => [
            'minimo' => ['os'=>'','processador'=>'','memoria'=>'','video'=>'','armazenamento'=>'','directx'=>'','carga_cpu'=>0,'carga_gpu'=>0,'carga_ram'=>0],
            'recomendado' => ['os'=>'','processador'=>'','memoria'=>'','video'=>'','armazenamento'=>'','directx'=>'','carga_cpu'=>0,'carga_gpu'=>0,'carga_ram'=>0],
        ],
    ];
}
if (!isset($relacionados)) {
    $relacionados = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= htmlspecialchars($jogo['titulo']) ?> - Baixe agora na Float." />
    <title>Float | <?= htmlspecialchars($jogo['titulo']) ?></title>

    <link rel="stylesheet" href="/float/public/assets/css/global/global.css" />
    <link rel="stylesheet" href="/float/public/assets/css/pages/jogotemplate.css">

    <link rel="icon" href="/float/public/assets/img/favicon/icone.ico" type="image/x-icon" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

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
                <?php if (!empty($jogo['screenshots'])): ?>
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
                <?php endif; ?>

                <!-- Sobre -->
                <section class="game-about-section reveal-hidden">
                    <h2 class="store-title">Sobre o Jogo <i class="fa-solid fa-arrow-right"></i></h2>
                    <p class="game-about-text"><?= htmlspecialchars($jogo['descricao']) ?></p>

                    <?php if (!empty($jogo['destaques'])): ?>
                    <ul class="game-highlights">
                        <?php foreach ($jogo['destaques'] as $item): ?>
                            <li>
                                <i class="fa-solid <?= $item['icone'] ?>"></i>
                                <span><?= htmlspecialchars($item['texto']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </section>

                <!-- Vídeos / Trailers -->
                <?php if (!empty($jogo['videos'])): ?>
                <section class="game-videos-section reveal-hidden">
                    <h2 class="store-title">Vídeos <i class="fa-solid fa-play"></i></h2>
                    <div class="game-videos-grid">
                        <?php foreach ($jogo['videos'] as $vid): ?>
                            <div class="game-video-card">
                                <?php if ($vid['tipo'] === 'youtube'):
                                    preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $vid['url'], $m);
                                    $ytId = $m[1] ?? '';
                                ?>
                                    <div class="video-embed">
                                        <iframe src="https://www.youtube.com/embed/<?= $ytId ?>"
                                                title="<?= htmlspecialchars($vid['titulo'] ?? 'Vídeo') ?>"
                                                frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen></iframe>
                                    </div>
                                <?php elseif ($vid['tipo'] === 'link'):
                                    preg_match('/vimeo\.com\/(\d+)/', $vid['url'], $m);
                                    $vimeoId = $m[1] ?? '';
                                ?>
                                    <?php if ($vimeoId): ?>
                                    <div class="video-embed">
                                        <iframe src="https://player.vimeo.com/video/<?= $vimeoId ?>"
                                                title="<?= htmlspecialchars($vid['titulo'] ?? 'Vídeo') ?>"
                                                frameborder="0"
                                                allow="autoplay; fullscreen; picture-in-picture"
                                                allowfullscreen></iframe>
                                    </div>
                                    <?php else: ?>
                                    <a href="<?= htmlspecialchars($vid['url']) ?>" target="_blank" rel="noopener" class="video-link-externo">
                                        <i class="fa-solid fa-up-right-from-square"></i>
                                        <?= htmlspecialchars($vid['titulo'] ?? 'Ver vídeo') ?>
                                    </a>
                                    <?php endif; ?>
                                <?php elseif ($vid['tipo'] === 'arquivo'): ?>
                                    <div class="video-embed">
                                        <video controls preload="metadata"
                                               poster="<?= !empty($jogo['screenshots'][0]) ? $jogo['screenshots'][0] : '' ?>">
                                            <source src="<?= htmlspecialchars($vid['url']) ?>" type="video/mp4">
                                            Seu navegador não suporta vídeo.
                                        </video>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($vid['titulo'])): ?>
                                    <p class="game-video-title"><?= htmlspecialchars($vid['titulo']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- Requisitos / Painel de compatibilidade -->
                <section class="game-requirements-section reveal-hidden">
                    <h2 class="store-title">Diagnóstico de Compatibilidade <i class="fa-solid fa-microchip"></i></h2>

                    <div class="req-grid">
                        <?php foreach (['minimo' => 'Mínimo', 'recomendado' => 'Recomendado'] as $key => $label): $r = $jogo['requisitos'][$key]; ?>
                            <div class="req-col">
                                <span class="req-col-label"><?= $label ?></span>

                                <ul class="req-spec-list">
                                    <li><span>Sistema</span><strong><?= htmlspecialchars($r['os']) ?></strong></li>
                                    <li><span>Processador</span><strong><?= htmlspecialchars($r['processador']) ?></strong></li>
                                    <li><span>Memória</span><strong><?= htmlspecialchars($r['memoria']) ?></strong></li>
                                    <li><span>Vídeo</span><strong><?= htmlspecialchars($r['video']) ?></strong></li>
                                    <li><span>Armazenamento</span><strong><?= htmlspecialchars($r['armazenamento']) ?></strong></li>
                                    <li><span>DirectX</span><strong><?= htmlspecialchars($r['directx']) ?></strong></li>
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

    <script src="/float/public/assets/js/global/global.js"></script>
    <script src="/float/public/assets/js/pages/jogotemplate.js"></script>

</body>
</html>