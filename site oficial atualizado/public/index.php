<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once __DIR__ . '/../api/config/conexao.php';

$stmtJogos = $pdo->query("
    SELECT j.id, j.titulo, j.slug, j.tagline, j.descricao, j.thumbnail, j.backdrop,
           j.genero, u.nome AS desenvolvedor
    FROM jogo j
    LEFT JOIN usuario u ON u.id = j.usuario_id
    WHERE j.visibilidade = 'publico'
    ORDER BY RAND()
    LIMIT 10
");
$jogos = $stmtJogos->fetchAll();
$jogosSlider = array_slice($jogos, 0, 5);

$imagemJogo = static function (array $jogo, bool $usarBackdrop = false): string {
    $arquivos = $usarBackdrop
        ? [$jogo['backdrop'], $jogo['thumbnail']]
        : [$jogo['thumbnail']];

    foreach ($arquivos as $arquivo) {
        if (!$arquivo) {
            continue;
        }

        $arquivo = basename($arquivo);
        $caminho = __DIR__ . '/assets/img/jogos/' . (int) $jogo['id'] . '/' . $arquivo;
        if (is_file($caminho)) {
            return './assets/img/jogos/' . (int) $jogo['id'] . '/' . rawurlencode($arquivo);
        }
    }

    return './assets/img/pages/index/cards/card_undertale.png';
};

$descricaoJogo = static function (array $jogo): string {
    $descricao = trim((string) ($jogo['tagline'] ?: strip_tags((string) $jogo['descricao'])));
    return mb_substr($descricao ?: 'Conheça este jogo indie publicado pela comunidade Float.', 0, 220);
};

$basePath   = './';
$activePage = 'home';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Float - O maior repositório de jogos indie. Explore, jogue e publique seus projetos.">
    <title>Float | Página Inicial</title>

    <link rel="stylesheet" href="./assets/css/global/global.css" />

    <link rel="stylesheet" href="./assets/css/pages/index.css">

    <link rel="icon" href="./assets/img/favicon/icone.ico" type="image/x-icon" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet" />
</head>
<body>

      <?php require_once 'includes/header.php'; ?>

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

          <main>
             <section class="home-brand-intro" aria-label="Float">
                <img src="./assets/img/global/logo.png" alt="Float" class="home-brand-logo">
                <p>Conectando ideias, elevando o jogo.</p>
             </section>
             <?php if ($jogosSlider): ?>
             <?php $jogoPrincipal = $jogosSlider[0]; ?>
             <?php $descricaoPrincipal = $descricaoJogo($jogoPrincipal); ?>
             <section class="epic-hero-section">
                 <div class="epic-hero-container">

                     <div class="hero-main-display">
                         <img src="<?= htmlspecialchars($imagemJogo($jogoPrincipal, true), ENT_QUOTES, 'UTF-8') ?>"
                              alt="<?= htmlspecialchars($jogoPrincipal['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                              class="hero-main-img">

                         <div class="hero-gradient-overlay"></div>

                         <div class="hero-overlay-content">
                             <span class="hero-status"><?= htmlspecialchars($jogoPrincipal['genero'] ?: 'Jogo em destaque', ENT_QUOTES, 'UTF-8') ?></span>
                             <h2 class="hero-game-name"><?= htmlspecialchars($jogoPrincipal['titulo'], ENT_QUOTES, 'UTF-8') ?></h2>
                             <p class="hero-description"><?= htmlspecialchars($descricaoPrincipal, ENT_QUOTES, 'UTF-8') ?></p>
                             <div class="hero-actions">
                                 <a href="/float/public/jogos/<?= rawurlencode($jogoPrincipal['slug']) ?>/" class="btn-buy" id="heroPlayBtn">Jogue Agora</a>
                                 <button class="btn-wishlist" id="heroWishlistBtn" data-jogo-id="<?= (int) $jogoPrincipal['id'] ?>" title="Lista de Desejos">
                                     <i class="fa-regular fa-bookmark"></i>
                                 </button>
                             </div>
                         </div>

                         <div class="hero-dots">
                             <?php foreach ($jogosSlider as $indice => $jogo): ?>
                             <span class="hero-dot <?= $indice === 0 ? 'active' : '' ?>"></span>
                             <?php endforeach; ?>
                         </div>

                    <div class="hero-progress-bar">
                        <div class="hero-progress-bar-fill"></div>
                    </div>
                </div>

                <aside class="hero-side-list">
                    <?php foreach ($jogosSlider as $indice => $jogo): ?>
                    <div class="side-item <?= $indice === 0 ? 'active' : '' ?>"
                         data-image="<?= htmlspecialchars($imagemJogo($jogo, true), ENT_QUOTES, 'UTF-8') ?>"
                         data-title="<?= htmlspecialchars($jogo['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                         data-description="<?= htmlspecialchars($descricaoJogo($jogo), ENT_QUOTES, 'UTF-8') ?>"
                         data-status="<?= htmlspecialchars($jogo['genero'] ?: 'Jogo em destaque', ENT_QUOTES, 'UTF-8') ?>"
                         data-jogo-id="<?= (int) $jogo['id'] ?>"
                         data-slug="<?= htmlspecialchars($jogo['slug'], ENT_QUOTES, 'UTF-8') ?>">
                        <img src="<?= htmlspecialchars($imagemJogo($jogo), ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= htmlspecialchars($jogo['titulo'], ENT_QUOTES, 'UTF-8') ?>">
                        <div class="side-item-info">
                            <span class="side-item-name"><?= htmlspecialchars($jogo['titulo'], ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="side-item-tag"><?= htmlspecialchars($jogo['genero'] ?: '', ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </aside>

            </div>
        </section>
        <?php endif; ?>

        <section class="store-section">
            <div class="store-header">
                <h2 class="store-title">Descubra Algo Novo <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></h2>
                <div class="store-nav">
                    <button class="nav-arrow" id="coverflow-prev" aria-label="Anterior"><i class="fa-solid fa-chevron-left"></i></button>
                    <button class="nav-arrow" id="coverflow-next" aria-label="Próximo"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>

            <div class="coverflow-track-wrapper">
                <?php if ($jogos): ?>
                <div class="coverflow-track" id="coverflowTrack">
                    <?php foreach ($jogos as $jogo): ?>
                    <div class="cf-card">
                        <div class="cf-card-cover">
                            <img src="<?= htmlspecialchars($imagemJogo($jogo), ENT_QUOTES, 'UTF-8') ?>"
                                 alt="<?= htmlspecialchars('Capa de ' . $jogo['titulo'], ENT_QUOTES, 'UTF-8') ?>"
                                 loading="lazy">
                            <div class="cf-card-overlay"><i class="fa-solid fa-play"></i></div>
                        </div>
                        <div class="cf-card-info">
                            <span class="cf-game-type"><?= htmlspecialchars($jogo['genero'] ?: 'Jogo indie', ENT_QUOTES, 'UTF-8') ?></span>
                            <h3 class="cf-game-title"><?= htmlspecialchars($jogo['titulo'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p class="cf-game-developer">Desenvolvido por <?= htmlspecialchars($jogo['desenvolvedor'] ?: 'Desenvolvedor independente', ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="coverflow-empty">Ainda não há jogos publicados para descobrir. Volte em breve!</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="promo-spotlight-section">
            <div class="promo-spotlight-inner">
                <div class="promo-spotlight-bg">
                    <img src="./assets/img/pages/index/slides/slide_03.png" alt="" aria-hidden="true" class="promo-bg-img">
                    <div class="promo-bg-overlay"></div>
                </div>
                <div class="promo-spotlight-content">
                    <div class="promo-text-side">
                        <span class="promo-badge"><i class="fa-solid fa-star"></i> Em Destaque</span>
                        <h2 class="promo-title">Hades II</h2>
                        <p class="promo-desc">A sequência do premiado roguelite da Supergiant Games chega em Early Access. Mais profundo, mais sombrio e mais viciante do que jamais foi.</p>
                        <div class="promo-meta">
                            <span class="promo-tag"><i class="fa-solid fa-layer-group"></i> Roguelite</span>
                            <span class="promo-tag"><i class="fa-solid fa-bolt"></i> Acesso Antecipado</span>
                            <span class="promo-tag"><i class="fa-solid fa-users"></i> Single Player</span>
                        </div>
                        <div class="promo-actions">
                            <a href="/float/public/jogos/hades-ii/" class="btn-promo-primary"><i class="fa-solid fa-play"></i> Jogar Agora</a>
                            <button class="btn-promo-secondary" id="promoWishlistBtn" data-jogo-id="4"><i class="fa-regular fa-bookmark"></i> <span class="btn-wishlist-label">Lista de Desejos</span></button>
                        </div>
                    </div>
                    <div class="promo-cover-side">
                        <div class="promo-cover-frame">
                            <img src="./assets/img/pages/index/cards/card_hadesII.png" alt="Hades II" class="promo-cover-img">
                            <div class="promo-cover-glow"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="news-section">
            <div class="news-header">
                <h2 class="store-title">Últimas Notícias <i class="fa-solid fa-newspaper"></i></h2>
                <a href="./pages/noticias.php" class="back-home news-see-all">
                    Ver todas as notícias  <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            <div class="news-grid">

                <article class="news-card news-card--featured">
                    <div class="news-card-cover">
                        <img src="./assets/img/pages/index/slides/slide_02.png" alt="Celeste recebe atualização surpresa" loading="lazy">
                        <span class="news-category">Atualização</span>
                    </div>
                    <div class="news-card-body">
                        <time class="news-date"><i class="fa-regular fa-clock"></i> 8 maio 2026</time>
                        <h3 class="news-title">Celeste recebe patch surpresa com novo modo de dificuldade após 3 anos</h3>
                        <p class="news-excerpt">Maddy Thorson lançou o inédito Crystal Heart Mode. O patch gratuito revisita fases icônicas com mecânicas e desafios novos.</p>
                        <a href="./pages/noticias.php" class="news-read-more">Ler mais <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>

                <article class="news-card">
                    <div class="news-card-cover">
                        <img src="./assets/img/pages/index/slides/slide_06.png" alt="Game Jam Float 2026" loading="lazy">
                        <span class="news-category">Comunidade</span>
                    </div>
                    <div class="news-card-body">
                        <time class="news-date"><i class="fa-regular fa-clock"></i> 5 maio 2026</time>
                        <h3 class="news-title">Float anuncia sua primeira Game Jam com prêmio de visibilidade na plataforma</h3>
                        <div class="news-excerpt">Talentos independentes terão 72 horas para criar jogos sob um tema secreto. O grande vencedor ganhará destaque global, mentoria exclusiva e total suporte de nossa comunidade.</div>
                        <a href="./pages/noticias.php" class="news-read-more">Ler mais <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>

                <article class="news-card">
                    <div class="news-card-cover">
                        <img src="./assets/img/pages/index/slides/slide_01.png" alt="Deltarune capítulo 3" loading="lazy">
                        <span class="news-category">Lançamento</span>
                    </div>
                    <div class="news-card-body">
                        <time class="news-date"><i class="fa-regular fa-clock"></i> 1 maio 2026</time>
                        <h3 class="news-title">Toby Fox confirma data para os capítulos 3 e 4 de Deltarune</h3>
                        <p class="news-excerpt">Os novos capítulos já estão disponíveis para os fãs. A expansão traz mistérios profundos, trilha sonora épica e o humor único de Undertale em uma jornada que redefine todo o gênero de RPG.</p>
                        <a href="./pages/noticias.php" class="news-read-more">Ler mais <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>

                <article class="news-card">
                    <div class="news-card-cover">
                        <img src="./assets/img/pages/index/cards/card_hadesII.png" alt="Hades II atualização" loading="lazy">
                        <span class="news-category">Atualização</span>
                    </div>
                    <div class="news-card-body">
                        <time class="news-date"><i class="fa-regular fa-clock"></i> 20 abr 2026</time>
                        <h3 class="news-title">Hades II recebe grande atualização de Early Access com novo bioma e chefão</h3>
                        <p class="news-excerpt">A Supergiant lançou a segunda grande atualização de conteúdo, expandindo a progressão com uma área inédita nas profundezas do submundo.</p>
                        <a href="./pages/noticias.php" class="news-read-more">Ler mais <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>

                <article class="news-card">
                    <div class="news-card-cover">
                        <img src="./assets/img/pages/index/cards/card_silksong.png" alt="Hollow Knight Silksong" loading="lazy">
                        <span class="news-category">Lançamento</span>
                    </div>
                    <div class="news-card-body">
                        <time class="news-date"><i class="fa-regular fa-clock"></i> 15 abr 2026</time>
                        <h3 class="news-title">Hollow Knight: Silksong finalmente tem janela de lançamento revelada</h3>
                        <p class="news-excerpt">A Team Cherry confirmou uma janela de lançamento durante um evento surpresa, encerrando anos de espera da comunidade.</p>
                        <a href="./pages/noticias.php" class="news-read-more">Ler mais <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>

                <article class="news-card">
                    <div class="news-card-cover">
                        <img src="./assets/img/pages/index/cards/card_undertale.png" alt="Arte ilustrativa de jogo indie em pixel art" loading="lazy">
                        <span class="news-category">Lançamento</span>
                    </div>
                    <div class="news-card-body">
                        <time class="news-date"><i class="fa-regular fa-clock"></i> 27 julho 2026</time>
                        <h3 class="news-title">Endacopia chega para quem busca uma nova aventura de terror indie</h3>
                        <p class="news-excerpt">Explore um mundo inquietante, descubra seus mistérios e acompanhe o lançamento de Endacopia, uma experiência independente de terror e exploração.</p>
                        <a href="./pages/noticias.php" class="news-read-more">Ler mais <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>

                <article class="news-card">
                    <div class="news-card-cover">
                        <img src="./assets/img/pages/index/cards/card_hadesII.png" alt="Hades II" loading="lazy">
                        <span class="news-category">Reviews de Jogos</span>
                    </div>
                    <div class="news-card-body">
                        <time class="news-date"><i class="fa-regular fa-clock"></i> 12 outubro 2026</time>
                        <h3 class="news-title">Review: Hades II combina ação veloz e progressão envolvente</h3>
                        <p class="news-excerpt">Analisamos o combate, a variedade de builds e a direção de arte da jornada de Melinoë pelo Submundo.</p>
                        <a href="./pages/noticias.php" class="news-read-more">Ler mais <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>

            </div>
        </section>

    </main>

    <?php require_once 'includes/footer.php'; ?>

    <script src="./assets/js/global/global.js"></script>
    <script src="./assets/js/pages/index.js"></script>

</body>

</html>
