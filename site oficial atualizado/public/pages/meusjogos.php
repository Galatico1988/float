<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($basePath)) {
    $basePath = './';
}
$activePage = 'biblioteca';

require_once __DIR__ . '/../../api/config/conexao.php';

$usuarioId = $_SESSION['id'] ?? null;
$jogosBaixados = [];
$jogosSalvos = [];

if ($usuarioId) {
    // Buscar jogos do usuário (jogos que ele publicou)
    $stmt = $pdo->prepare('SELECT id, titulo, slug, thumbnail, genero, preco, eh_gratis, tagline FROM jogo WHERE usuario_id = ? AND visibilidade = ? ORDER BY criado_em DESC');
    $stmt->execute([$usuarioId, 'publico']);
    $jogosBaixados = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Float | Meus Jogos</title>

    <link rel="stylesheet" href="/float/public/assets/css/global/global.css" />
    <link rel="stylesheet" href="/float/public/assets/css/pages/meusjogos.css" />
    <link rel="icon" href="/float/public/assets/img/favicon/icone.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet" />
</head>

<body>

    <?php require_once __DIR__ . '/../includes/header.php'; ?>

    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper">
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>

    <main class="meus-jogos-page">

        <!-- HEADER DA PÁGINA -->
        <section class="mj-page-header">
            <div class="mj-header-content">
                <div class="mj-header-icon">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
                <div class="mj-header-text">
                    <h1>Meus Jogos</h1>
                    <p>Acesse sua lista completa de títulos salvos e baixados.</p>
                </div>
            </div>
        </section>

        <!-- TABS -->
        <div class="mj-tabs-container">
            <div class="mj-tabs">
                <button class="mj-tab active" data-tab="baixados">
                    <i class="fa-solid fa-download"></i> Jogos Baixados
                </button>
                <button class="mj-tab" data-tab="salvos">
                    <i class="fa-regular fa-bookmark"></i> Jogos Salvos
                </button>
            </div>
        </div>

        <!-- CONTEÚDO: BAIXADOS -->
        <section class="mj-content active" id="tab-baixados">
            <?php if (empty($jogosBaixados)): ?>
                <div class="mj-empty-state">
                    <div class="mj-empty-icon">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <h2>Nenhum jogo baixado</h2>
                    <p>Quando você publicar ou baixar jogos, eles aparecerão aqui.</p>
                    <a href="/float/public/index.php" class="mj-btn-explore">
                        <i class="fa-solid fa-compass"></i> Explorar Jogos
                    </a>
                </div>
            <?php else: ?>
                <div class="mj-games-grid">
                    <?php foreach ($jogosBaixados as $jogo): ?>
                        <a href="/float/public/jogos/<?= htmlspecialchars($jogo['slug']) ?>/" class="mj-game-card">
                            <div class="mj-game-cover">
                                <?php if ($jogo['thumbnail']): ?>
                                    <img src="/float/public/assets/img/jogos/<?= $jogo['id'] ?>/<?= htmlspecialchars($jogo['thumbnail']) ?>" alt="<?= htmlspecialchars($jogo['titulo']) ?>" loading="lazy">
                                <?php else: ?>
                                    <img src="/float/public/assets/img/pages/index/cards/card_undertale.png" alt="<?= htmlspecialchars($jogo['titulo']) ?>" loading="lazy">
                                <?php endif; ?>
                                <div class="mj-game-overlay">
                                    <i class="fa-solid fa-play"></i>
                                </div>
                            </div>
                            <div class="mj-game-info">
                                <h3><?= htmlspecialchars($jogo['titulo']) ?></h3>
                                <div class="mj-game-meta">
                                    <?php if ($jogo['genero']): ?>
                                        <span class="mj-game-genre"><?= htmlspecialchars($jogo['genero']) ?></span>
                                    <?php endif; ?>
                                    <span class="mj-game-price"><?= $jogo['eh_gratis'] ? 'Grátis' : htmlspecialchars($jogo['preco']) ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- CONTEÚDO: SALVOS -->
        <section class="mj-content" id="tab-salvos">
            <div class="mj-empty-state">
                <div class="mj-empty-icon">
                    <i class="fa-regular fa-bookmark"></i>
                </div>
                <h2>Nenhum jogo salvo</h2>
                <p>Adicione jogos à sua lista de desejos para acessá-los facilmente depois.</p>
                <a href="/float/public/index.php" class="mj-btn-explore">
                    <i class="fa-solid fa-compass"></i> Explorar Jogos
                </a>
            </div>
        </section>

    </main>

    <?php require_once __DIR__ . '/../includes/footer.php'; ?>

    <script src="/float/public/assets/js/global/global.js"></script>
    <script src="/float/public/assets/js/pages/meusjogos.js"></script>

</body>

</html>
