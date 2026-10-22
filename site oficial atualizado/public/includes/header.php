<?php
require_once __DIR__ . '/../../config/app.php';
if (!isset($basePath)) {
    $basePath = './';
}
if (!isset($activePage)) {
    $activePage = '';
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$sessionId = $_SESSION['id'] ?? null;
$userName  = isset($_SESSION['nome']) ? htmlspecialchars($_SESSION['nome']) : 'Usuário';
$avatarSrc = float_asset('img/user/avatars/avatar.png');
if (!empty($_SESSION['avatar_path'])) {
    $avatarSrc = float_asset('img/user/avatars/') . htmlspecialchars($_SESSION['avatar_path']);
}
?>
<?= float_js_config() ?>
<nav class="nav-wrapper">
    <div class="nav-content">
        <a href="<?= float_url('index.php') ?>" class="brand-logo" aria-label="Página Inicial"> <!-- Mexa nessa linha depois -->
            <img src="<?= float_asset('img/global/logo.png') ?>" alt="Float Logo" class="logo-image-custom">  <!-- Mexa nessa linha também-->
        </a>

        <ul class="nav-list-center" id="nav-menu">
            <li class="nav-item has-dropdown">
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-compass nav-custom-icon"></i>
                    Descubra
                    <i class="fas fa-chevron-down chevron-icon"></i>
                </a>
                <div class="mega-menu-wrapper">
                    <div class="mega-menu-inner layout-com-midia">
                        <div class="area-conteudo discover-menu-content">
                            <div class="menu-column">
                                <h3><i class="fa-solid fa-fire header-icon"></i> Tendências do Float</h3>
                                <a href="<?= float_url('pages/maisjogados.php') ?>" class="rich-link-item">
                                    <span class="item-title"><i class="fa-solid fa-bolt mini-icon-left"></i> Mais Jogados</span>
                                    <span class="item-desc">Os títulos indie que estão dominando a comunidade nesta semana.</span>
                                </a>
                                <a href="<?= float_url('pages/recempostados.php') ?>" class="rich-link-item">
                                    <span class="item-title"><i class="fa-solid fa-calendar-days mini-icon-left"></i> Recém Postados</span>
                                    <span class="item-desc">Descubra novos talentos e seja o primeiro a comentar nos projetos.</span>
                                </a>
                            </div>
                            <div class="menu-column discover-categories-column">
                                <h3><i class="fa-solid fa-layer-group header-icon"></i> Categorias</h3>
                                <div class="discover-genres-grid">
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-bolt mini-icon-left"></i> Ação</span>
                                        <span class="item-desc">Combates e desafios que exigem reflexos.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-compass mini-icon-left"></i> Aventura</span>
                                        <span class="item-desc">Explore mundos e descubra novas histórias.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-hand-fist mini-icon-left"></i> Beat em' Up</span>
                                        <span class="item-desc">Enfrente grupos de inimigos usando combos.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-clone mini-icon-left"></i> Cartas</span>
                                        <span class="item-desc">Monte baralhos e combine cartas estrategicamente.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-face-smile mini-icon-left"></i> Casual</span>
                                        <span class="item-desc">Partidas simples para jogar sem compromisso.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-flag-checkered mini-icon-left"></i> Corrida</span>
                                        <span class="item-desc">Dispute velocidade em pistas e veículos.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-chess mini-icon-left"></i> Esportes</span>
                                        <span class="item-desc">Competições inspiradas em esportes diversos.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-chess-knight mini-icon-left"></i> Estratégia</span>
                                        <span class="item-desc">Planeje recursos e decisões para vencer.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-crosshairs mini-icon-left"></i> FPS (Tiro em Primeira Pessoa)</span>
                                        <span class="item-desc">Combate com armas visto pelos olhos do personagem.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-arrow-trend-up mini-icon-left"></i> Incremental (Idle)</span>
                                        <span class="item-desc">Acumule recursos e avance continuamente.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-hand-fist mini-icon-left"></i> Luta</span>
                                        <span class="item-desc">Enfrente adversários em duelos e torneios.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-music mini-icon-left"></i> Musical</span>
                                        <span class="item-desc">Interaja com música e acompanhe performances.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-shoe-prints mini-icon-left"></i> Plataforma</span>
                                        <span class="item-desc">Salte obstáculos e atravesse fases.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-arrow-pointer mini-icon-left"></i> Point and Click</span>
                                        <span class="item-desc">Investigue cenários e resolva desafios clicando.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-puzzle-piece mini-icon-left"></i> Quebra-Cabeça</span>
                                        <span class="item-desc">Resolva enigmas com lógica e observação.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-drum mini-icon-left"></i> Ritmo</span>
                                        <span class="item-desc">Acerte os comandos no tempo da música.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-dragon mini-icon-left"></i> RPG</span>
                                        <span class="item-desc">Desenvolva personagens em jornadas narrativas.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-cubes mini-icon-left"></i> Sandbox</span>
                                        <span class="item-desc">Crie, explore e jogue com liberdade.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-gears mini-icon-left"></i> Simulação</span>
                                        <span class="item-desc">Experimente atividades e sistemas simulados.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-campground mini-icon-left"></i> Sobrevivência</span>
                                        <span class="item-desc">Gerencie recursos e resista às ameaças.</span>
                                    </a>
                                    <a href="#" class="rich-link-item">
                                        <span class="item-title"><i class="fa-solid fa-ghost mini-icon-left"></i> Terror</span>
                                        <span class="item-desc">Encare mistérios e situações assustadoras.</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>

            <li class="nav-item has-dropdown">
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-book-bookmark nav-custom-icon"></i>
                    Biblioteca
                    <i class="fas fa-chevron-down chevron-icon"></i>
                </a>
                <div class="mega-menu-wrapper">
                    <div class="mega-menu-inner layout-dividido">
                        <div class="menu-column column-relative">
                            <h3><i class="fa-solid fa-box-open header-icon"></i> Minha Biblioteca</h3>
                            <a href="<?= float_url('pages/meusjogos.php') ?>" class="rich-link-item">
                                <span class="item-title"><i class="fa-solid fa-gamepad mini-icon-left"></i> Meus Jogos</span>
                                <span class="item-desc">Acesse sua lista completa de títulos salvos e baixados.</span>
                            </a>
                            <a href="#" class="rich-link-item">
                                <span class="item-title"><i class="fa-solid fa-heart mini-icon-left"></i> Lista de Desejos</span>
                                <span class="item-desc">Acompanhe o progresso dos jogos que você mais amou.</span>
                            </a>
                            <a href="#" class="rich-link-item">
                                <span class="item-title"><i class="fa-solid fa-clock-rotate-left mini-icon-left"></i> Histórico</span>
                                <span class="item-desc">Veja quais foram as últimas aventuras que você explorou.</span>
                            </a>
                        </div>
                        <div class="menu-column">
                            <h3><i class="fa-solid fa-trophy header-icon"></i> Conquistas</h3>
                            <a href="#" class="rich-link-item">
                                <span class="item-title"><i class="fa-solid fa-medal mini-icon-left"></i> Badges de Membro</span>
                                <span class="item-desc">Ganhe insígnias apoiando desenvolvedores.</span>
                            </a>
                            <a href="#" class="rich-link-item">
                                <span class="item-title"><i class="fa-solid fa-comment-dots mini-icon-left"></i> Top Crítico</span>
                                <span class="item-desc">Suas avaliações ajudam a plataforma crescer.</span>
                            </a>
                        </div>
                    </div>
                </div>
            </li>

            <li class="nav-item has-dropdown">
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-users-viewfinder nav-custom-icon"></i>
                    Comunidade
                    <i class="fas fa-chevron-down chevron-icon"></i>
                </a>
                <div class="mega-menu-wrapper">
                    <div class="mega-menu-inner layout-update-column">
                        <div class="menu-column">
                            <h3><i class="fa-solid fa-bullhorn header-icon"></i> Social</h3>
                            <a href="<?= float_url('pages/comunidade.php') ?>" class="rich-link-item">
                                <span class="item-title"><i class="fa-solid fa-users-line mini-icon-left"></i> Página da Comunidade</span>
                                <span class="item-desc">Acompanhe as últimas notícias e discussões da Float.</span>
                            </a>
                            <a href="#" class="rich-link-item">
                                <span class="item-title"><i class="fa-solid fa-comments mini-icon-left"></i> Fóruns de Discussão</span>
                                <span class="item-desc">Troque ideias e feedbacks com outros jogadores.</span>
                            </a>
                            <a href="#" class="rich-link-item">
                                <span class="item-title"><i class="fa-solid fa-handshake-angle mini-icon-left"></i> Game Jams</span>
                                <span class="item-desc">Participe de competições de criação de jogos.</span>
                            </a>
                        </div>
                    </div>
                </div>
            </li>

            <li class="nav-item has-dropdown">
                <a href="#" class="nav-link">
                    <i class="fa-solid fa-code-merge nav-custom-icon"></i>
                    Projetos
                    <i class="fas fa-chevron-down chevron-icon"></i>
                </a>
                <div class="mega-menu-wrapper">
                    <div class="mega-menu-inner layout-update-column">
                        <div class="menu-column">
                            <h3><i class="fa-solid fa-laptop-code header-icon"></i> Dashboard Dev</h3>
                            <a href="<?= float_url('pages/projetos.php') ?>" class="rich-link-item">
                                <span class="item-title"><i class="fa-solid fa-upload mini-icon-left"></i> Publicar Jogo</span>
                                <span class="item-desc">Envie seu projeto e comece a construir sua audiência.</span>
                            </a>
                               <a href="<?= float_url('pages/estatisticas.php') ?>" class="rich-link-item">
                                <span class="item-title"><i class="fa-solid fa-chart-line mini-icon-left"></i> Estatísticas</span>
                                <span class="item-desc">Veja quantos downloads e visitas seu jogo recebeu.</span>
                            </a>
                        </div>
                    </div>
                </div>
            </li>
        </ul>

        <div class="nav-actions">
            <?php if (!$sessionId): ?>
                <a href="<?= float_url('pages/login.php') ?>" class="btn-destaque">FAZER LOGIN</a>
            <?php else: ?>
                <div class="nav-auth-profile">
                    <button class="profile-avatar-btn" id="profileTrigger">
                        <img src="<?= $avatarSrc ?>" alt="Avatar" class="nav-user-img">
                    </button>
                    <div class="profile-mega-menu" id="profileDropdown">
                        <div class="menu-header">
                            <span class="user-name"><?= $userName ?></span>
                            <span class="user-status"><i class="fas fa-circle"></i> Online</span>
                        </div>
                        <div class="menu-divider"></div>
                        <ul class="menu-list">
                            <li><a href="<?= float_url('pages/perfil.php') ?>"><i class="fas fa-user-circle"></i> <span>Meu Perfil</span></a></li>
                            <li><a href="<?= float_api('logout.php') ?>" class="logout-item"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a></li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <button id="menu-toggle" aria-label="Abrir menu" class="mobile-menu-btn">
        <i class="fas fa-bars"></i>
    </button>
</nav>
