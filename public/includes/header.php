<?php
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
$avatarSrc = $basePath . 'assets/img/user/avatars/avatar.png';
if (!empty($_SESSION['avatar_path'])) {
    $avatarSrc = $basePath . 'assets/img/user/avatars/' . htmlspecialchars($_SESSION['avatar_path']);
}
?>
<nav class="nav-wrapper">
    <div class="nav-content">
        <a href="<?= $basePath ?>index.php" class="brand-logo" aria-label="Página Inicial">
            <img src="<?= $basePath ?>assets/img/global/logo.png" alt="Float Logo" class="logo-image-custom">
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
                        <div class="area-conteudo">
                            <div class="menu-column">
                                <h3><i class="fa-solid fa-fire header-icon"></i> Tendências do Float</h3>
                                <a href="#" class="rich-link-item">
                                    <span class="item-title"><i class="fa-solid fa-bolt mini-icon-left"></i> Mais Jogados</span>
                                    <span class="item-desc">Os títulos indie que estão dominando a comunidade nesta semana.</span>
                                </a>
                                <a href="#" class="rich-link-item">
                                    <span class="item-title"><i class="fa-solid fa-calendar-days mini-icon-left"></i> Recém Postados</span>
                                    <span class="item-desc">Descubra novos talentos e seja o primeiro a comentar nos projetos.</span>
                                </a>
                            </div>
                            <div class="menu-column">
                                <h3><i class="fa-solid fa-layer-group header-icon"></i> Categorias</h3>
                                <a href="#" class="rich-link-item">
                                    <span class="item-title"><i class="fa-solid fa-ghost mini-icon-left"></i> Terror & Suspense</span>
                                    <span class="item-desc">Experiências imersivas criadas por desenvolvedores solo.</span>
                                </a>
                                <a href="#" class="rich-link-item">
                                    <span class="item-title"><i class="fa-solid fa-puzzle-piece mini-icon-left"></i> Quebra-cabeça</span>
                                    <span class="item-desc">Jogos casuais para treinar sua mente entre uma partida e outra.</span>
                                </a>
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
                            <h3><i class="fa-solid fa-box-open header-icon"></i> Meu Acervo</h3>
                            <a href="#" class="rich-link-item">
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
                            <a href="<?= $basePath ?>pages/comunidade.php" class="rich-link-item">
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
                            <a href="#" class="rich-link-item">
                                <span class="item-title"><i class="fa-solid fa-upload mini-icon-left"></i> Publicar Jogo</span>
                                <span class="item-desc">Envie seu projeto e comece a construir sua audiência.</span>
                            </a>
                            <a href="#" class="rich-link-item">
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
                <a href="<?= $basePath ?>pages/login.php" class="btn-destaque">FAZER LOGIN</a>
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
                            <li><a href="<?= $basePath ?>pages/perfil.php"><i class="fas fa-user-circle"></i> <span>Meu Perfil</span></a></li>
                            <li><a href="<?= $basePath ?>../api/logout.php" class="logout-item"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a></li>
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
