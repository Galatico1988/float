<?php
if (!isset($_SESSION)) {
    session_start();
}
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

          <main>    
             <section class="epic-hero-section">
            <div class="epic-hero-container">

                <div class="hero-main-display">
                    <img src="./assets/img/pages/index/slides/slide_01.png"
                         alt="Deltarune"
                         class="hero-main-img">

                    <div class="hero-gradient-overlay"></div>

                    <div class="hero-overlay-content">
                        <span class="hero-status">Última Atualização</span>
                        <h2 class="hero-game-name">Deltarune</h2>
                        <p class="hero-description">Mergulhe em um novo capítulo do multiverso de Toby Fox. Uma narrativa emocionante e subversiva que redefine os limites do RPG indie com carisma e mistério.</p>
                        <div class="hero-actions">
                            <button class="btn-buy">Jogue Agora</button>
                            <button class="btn-wishlist" title="Lista de Desejos">
                                <i class="fa-regular fa-bookmark"></i>
                            </button>
                        </div>
                    </div>

                    <div class="hero-dots">
                        <span class="hero-dot active"></span>
                        <span class="hero-dot"></span>
                        <span class="hero-dot"></span>
                         <span class="hero-dot"></span>
                          <span class="hero-dot"></span>
                    </div>

                    <div class="hero-progress-bar">
                        <div class="hero-progress-bar-fill"></div>
                    </div>
                </div>

                <aside class="hero-side-list">
                    <div class="side-item active">
                        <img src="./assets/img/pages/index/slides/slide_01.png" alt="Deltarune">
                        <div class="side-item-info">
                            <span class="side-item-name">Deltarune</span>
                            <span class="side-item-tag"></span>
                        </div>
                    </div>
                    <div class="side-item">
                        <img src="./assets/img/pages/index/slides/slide_02.png" alt="Celeste">
                        <div class="side-item-info">
                            <span class="side-item-name">Celeste</span>
                            <span class="side-item-tag"></span>
                        </div>
                    </div>
                    <div class="side-item">
                        <img src="./assets/img/pages/index/slides/slide_03.png" alt="Hades II">
                        <div class="side-item-info">
                            <span class="side-item-name">Hades II</span>
                            <span class="side-item-tag"></span>
                        </div>
                    </div>
                    <div class="side-item">
                        <img src="./assets/img/pages/index/slides/slide_04.png" alt="Journey">
                        <div class="side-item-info">
                            <span class="side-item-name">Journey</span>
                            <span class="side-item-tag"></span>
                        </div>
                    </div>
                    <div class="side-item">
                        <img src="./assets/img/pages/index/slides/slide_05.png" alt="Stardew Valley">
                        <div class="side-item-info">
                            <span class="side-item-name">Stardew Valley</span>
                            <span class="side-item-tag"></span>
                        </div>
                    </div>
                </aside>

            </div>
        </section>

        <section class="store-section">
            <div class="store-header">
                <h2 class="store-title">Descubra algo novo  <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></h2>
                <div class="store-nav">
                    <button class="nav-arrow" id="coverflow-prev" aria-label="Anterior"><i class="fa-solid fa-chevron-left"></i></button>
                    <button class="nav-arrow" id="coverflow-next" aria-label="Próximo"><i class="fa-solid fa-chevron-right"></i></button>
                </div>
            </div>

            <div class="coverflow-track-wrapper">
                <div class="coverflow-track" id="coverflowTrack">

                    <div class="cf-card" data-title="Hollow Knight: Silksong" data-type="Jogo Base" data-price="Grátis">
                        <div class="cf-card-cover">
                            <img src="./assets/img/pages/index/cards/card_silksong.png" alt="Hollow Knight: Silksong" loading="lazy">
                            <div class="cf-card-overlay"><i class="fa-solid fa-play"></i></div>
                        </div>
                        <div class="cf-card-info">
                            <span class="cf-game-type">Jogo Base</span>
                            <h3 class="cf-game-title">Hollow Knight: Silksong</h3>
                            <p class="cf-game-price">Grátis</p>
                        </div>
                    </div>

                    <div class="cf-card" data-title="Celeste" data-type="Indie" data-price="R$ 36,99">
                        <div class="cf-card-cover">
                            <img src="./assets/img/pages/index/cards/card_celeste.png" alt="Celeste" loading="lazy">
                            <div class="cf-card-overlay"><i class="fa-solid fa-play"></i></div>
                        </div>
                        <div class="cf-card-info">
                            <span class="cf-game-type">Indie</span>
                            <h3 class="cf-game-title">Celeste</h3>
                            <p class="cf-game-price">R$ 36,99</p>
                        </div>
                    </div>

                    <div class="cf-card" data-title="Deltarune" data-type="RPG" data-price="Grátis">
                        <div class="cf-card-cover">
                            <img src="./assets/img/pages/index/cards/card_deltarune.png" alt="Deltarune" loading="lazy">
                            <div class="cf-card-overlay"><i class="fa-solid fa-play"></i></div>
                        </div>
                        <div class="cf-card-info">
                            <span class="cf-game-type">RPG</span>
                            <h3 class="cf-game-title">Deltarune</h3>
                            <p class="cf-game-price">Grátis</p>
                        </div>
                    </div>

                    <div class="cf-card" data-title="Hades II" data-type="Roguelite" data-price="R$ 59,99">
                        <div class="cf-card-cover">
                            <img src="./assets/img/pages/index/cards/card_hadesII.png" alt="Hades II" loading="lazy">
                            <div class="cf-card-overlay"><i class="fa-solid fa-play"></i></div>
                        </div>
                        <div class="cf-card-info">
                            <span class="cf-game-type">Roguelite</span>
                            <h3 class="cf-game-title">Hades II</h3>
                            <p class="cf-game-price">R$ 59,99</p>
                        </div>
                    </div>

                    <div class="cf-card" data-title="Journey" data-type="Aventura" data-price="R$ 29,99">
                        <div class="cf-card-cover">
                            <img src="./assets/img/pages/index/cards/card_journey.png" alt="Journey" loading="lazy">
                            <div class="cf-card-overlay"><i class="fa-solid fa-play"></i></div>
                        </div>
                        <div class="cf-card-info">
                            <span class="cf-game-type">Aventura</span>
                            <h3 class="cf-game-title">Journey</h3>
                            <p class="cf-game-price">R$ 29,99</p>
                        </div>
                    </div>

                    <div class="cf-card" data-title="Undertale" data-type="RPG" data-price="R$ 19,99">
                        <div class="cf-card-cover">
                            <img src="./assets/img/pages/index/cards/card_undertale.png" alt="Undertale" loading="lazy">
                            <div class="cf-card-overlay"><i class="fa-solid fa-play"></i></div>
                        </div>
                        <div class="cf-card-info">
                            <span class="cf-game-type">RPG</span>
                            <h3 class="cf-game-title">Undertale</h3>
                            <p class="cf-game-price">R$ 19,99</p>
                        </div>
                    </div>

                    <div class="cf-card" data-title="Shovel Knight" data-type="Plataforma" data-price="R$ 39,99">
                        <div class="cf-card-cover">
                            <img src="./assets/img/pages/index/cards/card_shovel_knight.png" alt="Shovel Knight" loading="lazy">
                            <div class="cf-card-overlay"><i class="fa-solid fa-play"></i></div>
                        </div>
                        <div class="cf-card-info">
                            <span class="cf-game-type">Plataforma</span>
                            <h3 class="cf-game-title">Shovel Knight</h3>
                            <p class="cf-game-price">R$ 39,99</p>
                        </div>
                    </div>

                    <div class="cf-card" data-title="Stardew Valley" data-type="Simulação" data-price="R$ 27,99">
                        <div class="cf-card-cover">
                            <img src="./assets/img/pages/index/cards/card_stardew_valley.png" alt="Stardew Valley" loading="lazy">
                            <div class="cf-card-overlay"><i class="fa-solid fa-play"></i></div>
                        </div>
                        <div class="cf-card-info">
                            <span class="cf-game-type">Simulação</span>
                            <h3 class="cf-game-title">Stardew Valley</h3>
                            <p class="cf-game-price">R$ 27,99</p>
                        </div>
                    </div>

                    <div class="cf-card" data-title="Dead Cells" data-type="Roguelike" data-price="R$ 49,99">
                        <div class="cf-card-cover">
                            <img src="./assets/img/pages/index/cards/card_dead_cells.png" alt="Dead Cells" loading="lazy">
                            <div class="cf-card-overlay"><i class="fa-solid fa-play"></i></div>
                        </div>
                        <div class="cf-card-info">
                            <span class="cf-game-type">Roguelike</span>
                            <h3 class="cf-game-title">Dead Cells</h3>
                            <p class="cf-game-price">R$ 49,99</p>
                        </div>
                    </div>

                    <div class="cf-card" data-title="Cuphead" data-type="Run &amp; Gun" data-price="R$ 44,99">
                        <div class="cf-card-cover">
                            <img src="./assets/img/pages/index/cards/card_cuphead.png" alt="Cuphead" loading="lazy">
                            <div class="cf-card-overlay"><i class="fa-solid fa-play"></i></div>
                        </div>
                        <div class="cf-card-info">
                            <span class="cf-game-type">Run &amp; Gun</span>
                            <h3 class="cf-game-title">Cuphead</h3>
                            <p class="cf-game-price">R$ 44,99</p>
                        </div>
                    </div>

                </div>
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
                            <span class="promo-tag"><i class="fa-solid fa-bolt"></i> Early Access</span>
                            <span class="promo-tag"><i class="fa-solid fa-users"></i> Single Player</span>
                        </div>
                        <div class="promo-actions">
                            <a href="../public/pages/jogotemplate.php"><button class="btn-promo-primary"><i class="fa-solid fa-play"></i> Jogar Agora</button></a>
                            <button class="btn-promo-secondary"><i class="fa-regular fa-bookmark"></i> Lista de Desejos</button>
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

            </div>
        </section>

    </main>

    <?php require_once 'includes/footer.php'; ?>

    <script src="./assets/js/global/global.js"></script>
    <script src="./assets/js/pages/index.js"></script>

</body>

</html>



