<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../api/config/conexao.php';

$basePath    = '../';
$activePage  = 'comunidade';
$sessaoId    = $_SESSION['id'] ?? null;
$usuarioNome = $_SESSION['nome'] ?? 'Membro';
$usuarioAvatar = $_SESSION['avatar_path'] ?? null;

$posts             = [];
$criadoresDestaque = [];
$postsEmAlta       = [];

try {
    $stmtFeed = $pdo->prepare('
        SELECT
            p.id, p.legenda, p.imagem_url, p.data_criacao,
            u.id AS usuario_id, u.nome, u.avatar_path,
            (SELECT COUNT(*) FROM curtida_post WHERE post_id = p.id) AS total_curtidas,
            (SELECT COUNT(*) FROM curtida_post WHERE post_id = p.id AND usuario_id = ?) AS curtiu
        FROM  comunidade_posts p
        JOIN  usuario u ON p.usuario_id = u.id
        ORDER BY p.data_criacao DESC
        LIMIT 20
    ');
    $stmtFeed->execute([$sessaoId]);
    $posts = $stmtFeed->fetchAll(PDO::FETCH_ASSOC);

    $stmtAlta = $pdo->prepare('
        SELECT p.id, p.legenda, p.data_criacao,
               u.nome, u.id AS usuario_id, u.avatar_path,
               COUNT(c.id) AS total_curtidas
        FROM  comunidade_posts p
        JOIN  usuario u ON p.usuario_id = u.id
        LEFT JOIN curtida_post c ON p.id = c.post_id
        GROUP BY p.id
        ORDER BY total_curtidas DESC, p.data_criacao DESC
        LIMIT 5
    ');
    $stmtAlta->execute();
    $postsEmAlta = $stmtAlta->fetchAll(PDO::FETCH_ASSOC);

    $stmtCriadores = $pdo->prepare('
        SELECT u.id, u.nome, u.avatar_path, COUNT(s.id) AS total_seguidores
        FROM  usuario u
        LEFT JOIN seguidor s ON u.id = s.seguido_id
        GROUP BY u.id
        ORDER BY total_seguidores DESC
        LIMIT 5
    ');
    $stmtCriadores->execute();
    $criadoresDestaque = $stmtCriadores->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Erro comunidade: ' . $e->getMessage());
}

function avatarSrc(?string $path): string {
    $base = '../assets/img/user/avatars/';
    return $path ? $base . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') : $base . 'avatar.png';
}

function dataRelativa(string $dataiso): string {
    $diff = time() - strtotime($dataiso);
    if ($diff < 60)    return 'agora';
    if ($diff < 3600)  return (int)($diff / 60) . ' min atrás';
    if ($diff < 86400) return (int)($diff / 3600) . 'h atrás';
    if ($diff < 604800)return (int)($diff / 86400) . 'd atrás';
    return date('d/m/Y', strtotime($dataiso));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Comunidade Float — postagens, criadores e tendências do universo indie." />
    <title>Float | Comunidade</title>
    <link rel="stylesheet" href="../assets/css/global/global.css" />
    <link rel="stylesheet" href="../assets/css/pages/comunidade.css" />
    <link rel="icon" href="../assets/img/favicon/icone.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet" />
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <main class="comunidade-main" id="main-content">

        
        <div class="comunidade-page-header">
            <div class="comunidade-page-header-inner">
                <a href="../index.php" class="back-home">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    Voltar ao início
                </a>
                <span class="comunidade-eyebrow">
                    <i class="fa-solid fa-users" aria-hidden="true"></i>
                    Comunidade
                </span>
                <h1 class="comunidade-page-title">O que está rolando</h1>
                <p class="comunidade-page-subtitle">Postagens reais de criadores e jogadores. Siga, curta e participe.</p>
                <div class="comunidade-tabs" role="tablist" aria-label="Filtros do feed">
                    <button class="comunidade-tab active" data-tab="todos" role="tab" aria-selected="true">
                        <i class="fa-solid fa-globe" aria-hidden="true"></i> Todos
                    </button>
                    <button class="comunidade-tab" data-tab="seguindo" role="tab" aria-selected="false">
                        <i class="fa-solid fa-user-check" aria-hidden="true"></i> Seguindo
                    </button>
                    <button class="comunidade-tab" data-tab="alta" role="tab" aria-selected="false">
                        <i class="fa-solid fa-fire" aria-hidden="true"></i> Em alta
                    </button>
                </div>
            </div>
            <?php if ($sessaoId): ?>
            <button class="btn-criar-post" id="btnCriarPost" type="button" aria-label="Criar nova postagem">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Nova postagem
            </button>
            <?php endif; ?>
        </div>

        
        <div class="comunidade-layout">

            
            <aside class="comunidade-sidebar" aria-label="Tendências e criadores">

                <div class="comunidade-aside-card">
                    <h3 class="aside-title">
                        <i class="fa-solid fa-fire-flame-curved" aria-hidden="true"></i>
                        Posts em alta
                    </h3>
                    <?php if (!empty($postsEmAlta)): ?>
                    <ul class="aside-list" role="list">
                        <?php foreach (array_slice($postsEmAlta, 0, 4) as $idx => $noticia): ?>
                        <li class="aside-list-item">
                            <span class="aside-rank"><?= $idx + 1 ?></span>
                            <div class="aside-item-body">
                                <img src="<?= avatarSrc($noticia['avatar_path']) ?>"
                                     alt="Avatar de <?= htmlspecialchars($noticia['nome'], ENT_QUOTES, 'UTF-8') ?>"
                                     class="aside-avatar" loading="lazy">
                                <div class="aside-item-meta">
                                    <span class="aside-item-user"><?= htmlspecialchars($noticia['nome'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="aside-item-legenda">
                                        <?= htmlspecialchars(mb_substr($noticia['legenda'] ?? '(sem legenda)', 0, 45), ENT_QUOTES, 'UTF-8') ?>…
                                    </span>
                                    <span class="aside-item-stat">
                                        <i class="fa-solid fa-heart" aria-hidden="true"></i>
                                        <?= number_format((int)$noticia['total_curtidas']) ?> curtidas
                                    </span>
                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <p class="aside-empty">Nenhum post ainda.</p>
                    <?php endif; ?>
                </div>

                <div class="comunidade-aside-card">
                    <h3 class="aside-title">
                        <i class="fa-solid fa-star" aria-hidden="true"></i>
                        Criadores em destaque
                    </h3>
                    <?php if (!empty($criadoresDestaque)): ?>
                    <ul class="aside-list" role="list">
                        <?php foreach (array_slice($criadoresDestaque, 0, 5) as $criador): ?>
                        <li class="aside-list-item aside-list-item--criador">
                            <a href="perfil.php?id=<?= $criador['id'] ?>" class="aside-criador-link">
                                <img src="<?= avatarSrc($criador['avatar_path']) ?>"
                                     alt="Avatar de <?= htmlspecialchars($criador['nome'], ENT_QUOTES, 'UTF-8') ?>"
                                     class="aside-avatar aside-avatar--md" loading="lazy">
                                <div class="aside-criador-info">
                                    <span class="aside-item-user"><?= htmlspecialchars($criador['nome'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="aside-item-stat">
                                        <i class="fa-solid fa-users" aria-hidden="true"></i>
                                        <?= number_format((int)$criador['total_seguidores']) ?> seguidores
                                    </span>
                                </div>
                            </a>
                            <?php if ($sessaoId && (int)$criador['id'] !== (int)$sessaoId): ?>
                            <button class="btn-seguir-sidebar"
                                    data-alvo="<?= $criador['id'] ?>"
                                    aria-label="Seguir <?= htmlspecialchars($criador['nome'], ENT_QUOTES, 'UTF-8') ?>">
                                <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
                            </button>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <p class="aside-empty">Nenhum criador ainda.</p>
                    <?php endif; ?>
                </div>

            </aside>

            
            <section class="comunidade-feed" id="feedPosts" aria-label="Feed de postagens" aria-live="polite">

                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post):
                        $curtiu = (int)$post['curtiu'] > 0;
                    ?>
                    <article class="com-card"
                             data-post-id="<?= $post['id'] ?>"
                             data-usuario-id="<?= $post['usuario_id'] ?>">

                        <header class="com-card-header">
                            <a href="perfil.php?id=<?= $post['usuario_id'] ?>" class="com-card-author">
                                <img src="<?= avatarSrc($post['avatar_path']) ?>"
                                     alt="Avatar de <?= htmlspecialchars($post['nome'], ENT_QUOTES, 'UTF-8') ?>"
                                     class="com-card-avatar" loading="lazy">
                                <div>
                                    <span class="com-card-name"><?= htmlspecialchars($post['nome'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <time class="com-card-time" datetime="<?= $post['data_criacao'] ?>">
                                        <?= dataRelativa($post['data_criacao']) ?>
                                    </time>
                                </div>
                            </a>
                            <?php if ($sessaoId && (int)$post['usuario_id'] !== (int)$sessaoId): ?>
                            <button class="btn-report-post"
                                    data-post-id="<?= $post['id'] ?>"
                                    aria-label="Reportar postagem" title="Reportar">
                                <i class="fa-solid fa-flag" aria-hidden="true"></i>
                            </button>
                            <?php endif; ?>
                        </header>

                        <?php if (!empty($post['legenda'])): ?>
                        <p class="com-card-legenda"><?= nl2br(htmlspecialchars($post['legenda'], ENT_QUOTES, 'UTF-8')) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($post['imagem_url'])): ?>
                        <div class="com-card-img-wrapper">
                            <img src="<?= htmlspecialchars($post['imagem_url'], ENT_QUOTES, 'UTF-8') ?>"
                                 alt="Imagem da postagem" class="com-card-img" loading="lazy">
                        </div>
                        <?php endif; ?>

                        <footer class="com-card-footer">
                            <?php if ($sessaoId): ?>
                            <button class="btn-reacao btn-like <?= $curtiu ? 'ativo' : '' ?>"
                                    data-post-id="<?= $post['id'] ?>"
                                    aria-label="Curtir postagem"
                                    aria-pressed="<?= $curtiu ? 'true' : 'false' ?>">
                                <i class="fa-<?= $curtiu ? 'solid' : 'regular' ?> fa-heart" aria-hidden="true"></i>
                                <span class="like-count"><?= number_format((int)$post['total_curtidas']) ?></span>
                            </button>
                            <button class="btn-reacao btn-dislike"
                                    data-post-id="<?= $post['id'] ?>"
                                    aria-label="Não gostei" aria-pressed="false">
                                <i class="fa-regular fa-thumbs-down" aria-hidden="true"></i>
                            </button>
                            <?php else: ?>
                            <span class="btn-reacao btn-like disabled" aria-label="Curtidas">
                                <i class="fa-regular fa-heart" aria-hidden="true"></i>
                                <span class="like-count"><?= number_format((int)$post['total_curtidas']) ?></span>
                            </span>
                            <?php endif; ?>

                            <?php if ($sessaoId && (int)$post['usuario_id'] !== (int)$sessaoId): ?>
                            <button class="btn-reacao btn-seguir-post"
                                    data-alvo="<?= $post['usuario_id'] ?>"
                                    aria-label="Seguir criador">
                                <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
                                Seguir
                            </button>
                            <?php endif; ?>
                        </footer>

                    </article>
                    <?php endforeach; ?>

                <?php else: ?>
                <div class="feed-vazio">
                    <i class="fa-solid fa-satellite-dish" aria-hidden="true"></i>
                    <p>Nenhuma postagem ainda.</p>
                    <?php if ($sessaoId): ?>
                    <p class="feed-vazio-sub">Seja o primeiro a publicar algo!</p>
                    <?php else: ?>
                    <a href="login.php" class="btn-destaque">
                        <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>
                        Entrar para participar
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </section>

        </div>
    </main>

    <?php require_once '../includes/footer.php'; ?>

    
    <?php if ($sessaoId): ?>
    <div class="modal-overlay" id="modalPost" role="dialog" aria-modal="true" aria-labelledby="modalPostTitulo" hidden>
        <div class="modal-box modal-box--post">
            <header class="modal-header">
                <h2 id="modalPostTitulo">
                    <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                    Nova postagem
                </h2>
                <button class="modal-close" aria-label="Fechar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </header>
            <div class="modal-body post-modal-body">
                <div class="post-modal-author">
                  <img src="<?= avatarSrc($usuarioAvatar) ?>" alt="Seu avatar" class="com-card-avatar" id="postModalAvatar">
                    <span class="post-modal-name"><?= htmlspecialchars($usuarioNome, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <textarea id="postLegenda" class="post-textarea" maxlength="500"
                          placeholder="O que você quer compartilhar com a comunidade?"
                          rows="4" aria-label="Texto da postagem"></textarea>
                <p class="modal-hint post-char-hint">
                    <span id="postCharCount">0</span>/500 caracteres
                </p>
                <div class="post-img-row">
                    <label for="postImagemUrl" class="modal-label">
                        <i class="fa-solid fa-image" aria-hidden="true"></i>
                        Imagem (URL, opcional)
                    </label>
                    <div class="upload-url-input-wrapper">
                        <i class="fa-solid fa-link upload-url-icon" aria-hidden="true"></i>
                        <input type="url" id="postImagemUrl" class="modal-input upload-url-input"
                               placeholder="https://..." autocomplete="off">
                    </div>
                </div>
                <div class="post-img-preview" id="postImgPreview" hidden>
                    <img id="postImgPreviewImg" src="" alt="Preview">
                    <button class="post-img-remove" id="btnRemoverImgPost" type="button" aria-label="Remover imagem">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <footer class="modal-footer">
                <button class="btn-modal-cancel modal-close" type="button">Cancelar</button>
                <button class="btn-modal-save" id="btnPublicarPost" type="button">
                    <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                    Publicar
                </button>
            </footer>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if ($sessaoId): ?>
    <div class="modal-overlay" id="modalReportPost" role="dialog" aria-modal="true" aria-labelledby="modalReportPostTitulo" hidden>
        <div class="modal-box">
            <header class="modal-header">
                <h2 id="modalReportPostTitulo">
                    <i class="fa-solid fa-flag" aria-hidden="true"></i>
                    Reportar postagem
                </h2>
                <button class="modal-close" aria-label="Fechar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </header>
            <div class="modal-body">
                <input type="hidden" id="reportPostId" value="">
                <label for="reportMotivo" class="modal-label">Motivo</label>
                <select id="reportMotivo" class="modal-input modal-select">
                    <option value="">Selecione um motivo...</option>
                    <option value="Conteúdo inapropriado">Conteúdo inapropriado</option>
                    <option value="Spam ou flood">Spam ou flood</option>
                    <option value="Discurso de ódio">Discurso de ódio</option>
                    <option value="Violação de direitos autorais">Violação de direitos autorais</option>
                    <option value="Comportamento abusivo">Comportamento abusivo</option>
                    <option value="Outro">Outro</option>
                </select>
                <label for="reportDetalhe" class="modal-label" style="margin-top:12px">Detalhes (opcional)</label>
                <textarea id="reportDetalhe" class="modal-input modal-textarea" maxlength="255" rows="3"
                          placeholder="Descreva brevemente..."></textarea>
            </div>
            <footer class="modal-footer">
                <button class="btn-modal-cancel modal-close" type="button">Cancelar</button>
                <button class="btn-modal-save btn-danger" id="btnEnviarReport" type="button">
                    <i class="fa-solid fa-flag" aria-hidden="true"></i>
                    Reportar
                </button>
            </footer>
        </div>
    </div>
    <?php endif; ?>

    <!-- Modal: Seguir usuário (aberto ao clicar na PFP) -->
    <?php if ($sessaoId): ?>
    <div class="modal-overlay" id="modalSeguir" role="dialog" aria-modal="true" aria-labelledby="modalSeguirTitulo" hidden>
        <div class="modal-box">
            <header class="modal-header">
                <h2 id="modalSeguirTitulo">
                    <i class="fa-solid fa-user" aria-hidden="true"></i>
                    Perfil do criador
                </h2>
                <button class="modal-close" aria-label="Fechar">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </header>
            <div class="modal-body">
                <input type="hidden" id="modalSeguirAlvoId" value="">
                <div class="modal-seguir-perfil">
                    <img id="modalSeguirAvatar"
                         src="../assets/img/user/avatars/avatar.png"
                         alt="Avatar"
                         class="modal-seguir-avatar">
                    <div class="modal-seguir-info">
                        <strong id="modalSeguirNome" class="modal-seguir-nome">—</strong>
                        <span id="modalSeguirSeguidores" class="aside-item-stat">
                            <i class="fa-solid fa-users" aria-hidden="true"></i>
                        </span>
                        <p id="modalSeguirBio" class="modal-seguir-bio"></p>
                    </div>
                </div>
            </div>
            <footer class="modal-footer">
                <a id="btnIrPerfil" href="#" class="btn-modal-cancel">
                    <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                    Ver perfil
                </a>
                <button id="btnConfirmarSeguir" class="btn-modal-save" type="button"
                        data-alvo="" data-seguindo="0" disabled>
                    <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
                    Seguir
                </button>
            </footer>
        </div>
    </div>
    <?php endif; ?>

    <div class="toast-container" id="toastContainer" aria-live="polite" aria-atomic="true"></div>

    <script>
        window.COMUNIDADE = {
            sessaoId:    <?= $sessaoId ? (int)$sessaoId : 'null' ?>,
            usuarioNome: <?= json_encode($usuarioNome, JSON_UNESCAPED_UNICODE) ?>
        };
    </script>
    <script src="../assets/js/global/global.js"></script>
    <script src="../assets/js/pages/comunidade.js"></script>
</body>
</html>