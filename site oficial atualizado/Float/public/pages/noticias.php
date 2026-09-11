<?php
if (!isset($_SESSION)) {
    session_start();
}
$basePath   = '../';
$activePage = 'noticias';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Float - Últimas notícias do mundo dos jogos indie e da plataforma.">
    <title>Float | Notícias</title>

    <link rel="stylesheet" href="../assets/css/global/global.css" />

    <link rel="stylesheet" href="../assets/css/pages/noticias.css" />

    <link rel="icon" href="../assets/img/favicon/icone.ico" type="image/x-icon" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet" />
</head>

<body>

    <?php require_once '../includes/header.php'; ?>

    <main class="noticias-main">

        
        <header class="noticias-page-header">
            <div class="noticias-page-header-inner">
                <a href="../index.php" class="back-home">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Voltar à Página Inicial
                </a>
                <div class="noticias-page-title-block">
                    <span class="noticias-page-eyebrow"><i class="fa-solid fa-newspaper"></i> Float News</span>
                    <h1 class="noticias-page-title">Notícias &amp; Atualizações</h1>
                    <p class="noticias-page-subtitle">As últimas novidades do universo indie e da plataforma Float.</p>
                </div>

                
                <div class="noticias-filters" role="group" aria-label="Filtrar notícias por categoria">
                    <button class="noticias-filter-btn active" data-filter="all">Todas</button>
                    <button class="noticias-filter-btn" data-filter="atualização">Atualização</button>
                    <button class="noticias-filter-btn" data-filter="lançamento">Lançamento</button>
                    <button class="noticias-filter-btn" data-filter="comunidade">Comunidade</button>
                    <button class="noticias-filter-btn" data-filter="plataforma">Plataforma</button>
                </div>
            </div>
        </header>

        
        <section class="noticias-grid-section">
            <div class="noticias-grid" id="noticiasGrid">

                <article class="noticias-card noticias-card--hero" data-category="atualização">
                    <div class="noticias-card-cover">
                        <img src="../assets/img/pages/index/slides/slide_02.png" alt="Celeste recebe atualização surpresa" loading="lazy">
                        <span class="noticias-badge">Atualização</span>
                    </div>
                    <div class="noticias-card-body">
                        <time class="noticias-date"><i class="fa-regular fa-clock"></i> 8 maio 2026</time>
                        <h2 class="noticias-title">Celeste recebe patch surpresa com novo modo de dificuldade após 3 anos</h2>
                        <p class="noticias-excerpt">Maddy Thorson anunciou uma atualização gratuita que adiciona o "Crystal Heart Mode", revisitando as fases mais icônicas com mecânicas inéditas e uma trilha sonora expandida por Lena Raine.</p>
                        <a href="#" class="noticias-read-more">Ler matéria completa <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>

                <article class="noticias-card" data-category="comunidade">
                    <div class="noticias-card-cover">
                        <img src="../assets/img/pages/index/slides/slide_06.png" alt="Game Jam Float 2026" loading="lazy">
                        <span class="noticias-badge">Comunidade</span>
                    </div>
                    <div class="noticias-card-body">
                        <time class="noticias-date"><i class="fa-regular fa-clock"></i> 5 maio 2026</time>
                        <h2 class="noticias-title">Float anuncia sua primeira Game Jam com prêmio de destaque na plataforma</h2>
                        <p class="noticias-excerpt">Desenvolvedores independentes terão 72 horas para criar um jogo dentro do tema revelado ao vivo. Inscrições abertas.</p>
                        <a href="#" class="noticias-read-more">Ler matéria completa <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>

                <article class="noticias-card" data-category="lançamento">
                    <div class="noticias-card-cover">
                        <img src="../assets/img/pages/index/cards/card_deltarune.png" alt="Deltarune capítulo 3" loading="lazy">
                        <span class="noticias-badge">Lançamento</span>
                    </div>
                    <div class="noticias-card-body">
                        <time class="noticias-date"><i class="fa-regular fa-clock"></i> 1 maio 2026</time>
                        <h2 class="noticias-title">Toby Fox confirma data para os capítulos 3 e 4 de Deltarune</h2>
                        <p class="noticias-excerpt">Em post no blog oficial, o criador de Undertale detalhou o estado do desenvolvimento e surpreendeu fãs com uma revelação inesperada.</p>
                        <a href="#" class="noticias-read-more">Ler matéria completa <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>

                <article class="noticias-card" data-category="atualização">
                    <div class="noticias-card-cover">
                        <img src="../assets/img/pages/index/cards/card_hadesII.png" alt="Hades II atualização" loading="lazy">
                        <span class="noticias-badge">Atualização</span>
                    </div>
                    <div class="noticias-card-body">
                        <time class="noticias-date"><i class="fa-regular fa-clock"></i> 20 abr 2026</time>
                        <h2 class="noticias-title">Hades II recebe grande atualização de Early Access com novo bioma e chefão</h2>
                        <p class="noticias-excerpt">A Supergiant lançou a segunda grande atualização de conteúdo, expandindo a progressão com uma área inédita nas profundezas do submundo.</p>
                        <a href="#" class="noticias-read-more">Ler matéria completa <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>

                <article class="noticias-card" data-category="lançamento">
                    <div class="noticias-card-cover">
                        <img src="../assets/img/pages/index/cards/card_silksong.png" alt="Hollow Knight Silksong" loading="lazy">
                        <span class="noticias-badge">Lançamento</span>
                    </div>
                    <div class="noticias-card-body">
                        <time class="noticias-date"><i class="fa-regular fa-clock"></i> 15 abr 2026</time>
                        <h2 class="noticias-title">Hollow Knight: Silksong finalmente tem janela de lançamento revelada</h2>
                        <p class="noticias-excerpt">A Team Cherry confirmou uma janela de lançamento durante um evento surpresa, encerrando anos de espera da comunidade.</p>
                        <a href="#" class="noticias-read-more">Ler matéria completa <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>

            </div>

            
            <div class="noticias-empty" id="noticiasEmpty" style="display:none;" aria-live="polite">
                <i class="fa-solid fa-newspaper"></i>
                <p>Nenhuma notícia encontrada para esse filtro.</p>
            </div>
        </section>

    </main>

    <?php require_once '../includes/footer.php'; ?>

    <script src="../assets/js/global/global.js"></script>
    <script src="../assets/js/pages/noticias.js"></script>

</body>
</html>


