'use strict';

const { sessaoId } = window.COMUNIDADE ?? {};

// ─── Utilitários ────────────────────────────────────────────────────────────

function showToast(msg, tipo = 'info') {
    const iconMap = {
        success: 'fa-circle-check',
        error:   'fa-circle-xmark',
        info:    'fa-circle-info',
    };
    const toast = document.createElement('div');
    toast.className = `toast toast-${tipo}`;
    toast.setAttribute('role', 'status');
    toast.innerHTML = `
        <i class="fa-solid ${iconMap[tipo] ?? iconMap.info}" aria-hidden="true"></i>
        <span>${msg}</span>
    `;
    const container = document.getElementById('toastContainer') || document.body;
    container.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('toast-hide');
        toast.addEventListener('transitionend', () => toast.remove(), { once: true });
    }, 3500);
}

async function postForm(url, formData) {
    try {
        const res  = await fetch(url, { method: 'POST', body: formData });
        return await res.json();
    } catch {
        return { ok: false, mensagem: 'Erro de conexão.' };
    }
}

async function getJson(url) {
    try {
        const res = await fetch(url);
        return await res.json();
    } catch {
        return { ok: false, mensagem: 'Erro de conexão.' };
    }
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function dataRelativa(dataiso) {
    const diff = Math.floor((Date.now() - new Date(dataiso).getTime()) / 1000);
    if (diff < 60)     return 'agora';
    if (diff < 3600)   return Math.floor(diff / 60) + ' min atrás';
    if (diff < 86400)  return Math.floor(diff / 3600) + 'h atrás';
    if (diff < 604800) return Math.floor(diff / 86400) + 'd atrás';
    return new Date(dataiso).toLocaleDateString('pt-BR');
}

// ─── Modais genéricos ────────────────────────────────────────────────────────

function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.removeAttribute('hidden');
    document.body.style.overflow = 'hidden';
    const focusable = modal.querySelector('textarea, input, select, button:not(.modal-close)');
    if (focusable) focusable.focus();
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal(id);
    }, { once: true });
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.setAttribute('hidden', '');
    document.body.style.overflow = '';
}

function initModalCloseButtons() {
    document.querySelectorAll('.modal-close').forEach((btn) => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('.modal-overlay');
            if (modal) closeModal(modal.id);
        });
    });
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        const aberto = document.querySelector('.modal-overlay:not([hidden])');
        if (aberto) closeModal(aberto.id);
    });
}

// ─── Tabs de filtro ──────────────────────────────────────────────────────────

let tabAtiva = 'todos';

function initTabs() {
    const tabs = document.querySelectorAll('.comunidade-tab');
    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const novoFiltro = tab.dataset.tab;
            if (novoFiltro === tabAtiva) return;

            tabs.forEach((t) => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');

            tabAtiva = novoFiltro;
            carregarFeed(novoFiltro);
        });
    });
}

async function carregarFeed(filtro) {
    const feed = document.getElementById('feedPosts');
    if (!feed) return;

    feed.innerHTML = `
        <div class="feed-carregando">
            <i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i>
            <p>Carregando posts...</p>
        </div>
    `;

    const data = await getJson('../../api/comunidade/feed.php?filtro=' + encodeURIComponent(filtro));

    if (!data.ok) {
        feed.innerHTML = '<div class="feed-vazio"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i><p>' + escapeHtml(data.mensagem ?? 'Erro ao carregar.') + '</p></div>';
        return;
    }

    if (!data.posts || data.posts.length === 0) {
        const msgs = {
            todos:    'Nenhuma postagem ainda. Seja o primeiro a publicar!',
            seguindo: 'Você ainda não segue ninguém, ou quem você segue ainda não postou.',
            alta:     'Nenhuma postagem em alta no momento.',
        };
        feed.innerHTML = '<div class="feed-vazio"><i class="fa-solid fa-satellite-dish" aria-hidden="true"></i><p>' + (msgs[filtro] ?? 'Nenhuma postagem.') + '</p></div>';
        return;
    }

    feed.innerHTML = '';
    data.posts.forEach((post) => {
        const article = criarCardPost(post);
        feed.appendChild(article);
        bindCardEvents(article);
    });
}

// ─── Criação de card ─────────────────────────────────────────────────────────

function criarCardPost(post) {
    const avatarBase = '../assets/img/user/avatars/';
    const avatarSrc  = post.avatar_path
        ? avatarBase + escapeHtml(post.avatar_path)
        : avatarBase + 'avatar.png';
    const tempo      = dataRelativa(post.data_criacao);
    const eDoUsuario = sessaoId && (parseInt(post.usuario_id, 10) === parseInt(sessaoId, 10));
    const curtiu     = post.curtiu === true || post.curtiu === 1;

    const article = document.createElement('article');
    article.className = 'com-card';
    article.dataset.postId    = post.id;
    article.dataset.usuarioId = post.usuario_id;

    const pfpHtml = eDoUsuario
        ? '<a href="perfil.php?id=' + escapeHtml(String(post.usuario_id)) + '" class="com-card-author"><img src="' + avatarSrc + '" alt="Avatar de ' + escapeHtml(post.nome) + '" class="com-card-avatar" loading="lazy"><div><span class="com-card-name">' + escapeHtml(post.nome) + '</span><time class="com-card-time" datetime="' + escapeHtml(post.data_criacao) + '">' + tempo + '</time></div></a>'
        : '<button class="com-card-author com-card-author--btn" data-alvo-id="' + escapeHtml(String(post.usuario_id)) + '" aria-label="Ver perfil de ' + escapeHtml(post.nome) + '"><img src="' + avatarSrc + '" alt="Avatar de ' + escapeHtml(post.nome) + '" class="com-card-avatar" loading="lazy"><div><span class="com-card-name">' + escapeHtml(post.nome) + '</span><time class="com-card-time" datetime="' + escapeHtml(post.data_criacao) + '">' + tempo + '</time></div></button>';

    const reportBtn = (sessaoId && !eDoUsuario)
        ? '<button class="btn-report-post" data-post-id="' + post.id + '" aria-label="Reportar postagem" title="Reportar"><i class="fa-solid fa-flag" aria-hidden="true"></i></button>'
        : '';

    const legendaHtml = post.legenda
        ? '<p class="com-card-legenda">' + escapeHtml(post.legenda).replace(/\n/g, '<br>') + '</p>'
        : '';

    const imagemHtml = post.imagem_url
        ? '<div class="com-card-img-wrapper"><img src="' + escapeHtml(post.imagem_url) + '" alt="Imagem da postagem" class="com-card-img" loading="lazy"></div>'
        : '';

    let footerHtml = '';
    if (sessaoId) {
        const likeClass = curtiu ? 'btn-like ativo' : 'btn-like';
        const likeIcon  = curtiu ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
        const likeCount = Number(post.total_curtidas).toLocaleString('pt-BR');
        footerHtml  = '<button class="btn-reacao ' + likeClass + '" data-post-id="' + post.id + '" aria-label="Curtir postagem" aria-pressed="' + (curtiu ? 'true' : 'false') + '"><i class="' + likeIcon + '" aria-hidden="true"></i><span class="like-count">' + likeCount + '</span></button>';
        footerHtml += '<button class="btn-reacao btn-dislike" data-post-id="' + post.id + '" aria-label="Não gostei" aria-pressed="false"><i class="fa-regular fa-thumbs-down" aria-hidden="true"></i></button>';
        if (!eDoUsuario) {
            footerHtml += '<button class="btn-reacao btn-seguir-post" data-alvo="' + post.usuario_id + '" aria-label="Seguir criador"><i class="fa-solid fa-user-plus" aria-hidden="true"></i> Seguir</button>';
        }
    } else {
        footerHtml = '<span class="btn-reacao btn-like disabled" aria-label="Curtidas"><i class="fa-regular fa-heart" aria-hidden="true"></i><span class="like-count">' + Number(post.total_curtidas).toLocaleString('pt-BR') + '</span></span>';
    }

    article.innerHTML =
        '<header class="com-card-header">' + pfpHtml + reportBtn + '</header>' +
        legendaHtml + imagemHtml +
        '<footer class="com-card-footer">' + footerHtml + '</footer>';

    return article;
}

function bindCardEvents(article) {
    const btnLike = article.querySelector('.btn-like');
    if (btnLike) bindLikeButton(btnLike);

    const btnSeguirPost = article.querySelector('.btn-seguir-post');
    if (btnSeguirPost) bindSeguirButton(btnSeguirPost);

    const pfpBtn = article.querySelector('.com-card-author--btn');
    if (pfpBtn) {
        pfpBtn.addEventListener('click', () => {
            abrirModalSeguir(parseInt(pfpBtn.dataset.alvoId, 10));
        });
    }
}

// ─── Criar post ──────────────────────────────────────────────────────────────

function initCriarPost() {
    const btnAbrir    = document.getElementById('btnCriarPost');
    const btnPublicar = document.getElementById('btnPublicarPost');
    const textarea    = document.getElementById('postLegenda');
    const charCount   = document.getElementById('postCharCount');
    const inputUrl    = document.getElementById('postImagemUrl');
    const preview     = document.getElementById('postImgPreview');
    const previewImg  = document.getElementById('postImgPreviewImg');
    const btnRemover  = document.getElementById('btnRemoverImgPost');

    if (!btnAbrir) return;

    btnAbrir.addEventListener('click', () => openModal('modalPost'));

    if (textarea && charCount) {
        textarea.addEventListener('input', () => { charCount.textContent = textarea.value.length; });
    }

    if (inputUrl && preview && previewImg) {
        let debounce;
        inputUrl.addEventListener('input', () => {
            clearTimeout(debounce);
            const url = inputUrl.value.trim();
            if (!url) { preview.hidden = true; return; }
            debounce = setTimeout(() => {
                const img = new Image();
                img.onload  = () => { previewImg.src = url; preview.hidden = false; };
                img.onerror = () => { preview.hidden = true; };
                img.src = url;
            }, 600);
        });
    }

    if (btnRemover && preview && inputUrl) {
        btnRemover.addEventListener('click', () => {
            preview.hidden = true;
            inputUrl.value = '';
            if (previewImg) previewImg.src = '';
        });
    }

    if (btnPublicar) {
        btnPublicar.addEventListener('click', async () => {
            const legenda   = textarea ? textarea.value.trim() : '';
            const imagemUrl = inputUrl  ? inputUrl.value.trim()  : '';

            if (legenda.length < 3 && !imagemUrl) {
                showToast('Escreva algo ou adicione uma imagem antes de publicar.', 'error');
                return;
            }

            btnPublicar.disabled = true;
            btnPublicar.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Publicando...';

            const fd = new FormData();
            fd.append('legenda', legenda);
            fd.append('imagem_url', imagemUrl);

            const data = await postForm('../../api/comunidade/post.php', fd);

            if (data.ok && data.post) {
                closeModal('modalPost');

                if (tabAtiva === 'todos' || tabAtiva === 'seguindo') {
                    inserirCardNoTopo(data.post);
                }

                showToast('Postagem publicada!', 'success');

                if (textarea)   { textarea.value = ''; if (charCount) charCount.textContent = '0'; }
                if (inputUrl)   inputUrl.value = '';
                if (preview)    preview.hidden = true;
                if (previewImg) previewImg.src = '';
            } else {
                showToast(data.mensagem ?? 'Erro ao publicar.', 'error');
            }

            btnPublicar.disabled = false;
            btnPublicar.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Publicar';
        });
    }
}

function inserirCardNoTopo(post) {
    const feed = document.getElementById('feedPosts');
    if (!feed) return;
    feed.querySelector('.feed-vazio')?.remove();
    post.curtiu         = false;
    post.total_curtidas = post.total_curtidas ?? 0;
    const article = criarCardPost(post);
    feed.insertBefore(article, feed.firstChild);
    bindCardEvents(article);
}

// ─── Like ────────────────────────────────────────────────────────────────────

function bindLikeButton(btn) {
    btn.addEventListener('click', async () => {
        if (!sessaoId) { showToast('Faça login para curtir.', 'info'); return; }

        const postId    = btn.dataset.postId;
        const curtido   = btn.classList.contains('ativo');
        const likeCount = btn.querySelector('.like-count');
        const icon      = btn.querySelector('i');
        const count     = parseInt(likeCount?.textContent?.replace(/\D/g, '') || '0', 10);

        btn.classList.toggle('ativo', !curtido);
        btn.setAttribute('aria-pressed', curtido ? 'false' : 'true');
        if (icon)      icon.className = curtido ? 'fa-regular fa-heart' : 'fa-solid fa-heart';
        if (likeCount) likeCount.textContent = (curtido ? count - 1 : count + 1).toLocaleString('pt-BR');

        btn.disabled = true;
        const fd = new FormData();
        fd.append('post_id', postId);
        const data = await postForm('../../api/user/like.php', fd);

        if (data.ok) {
            if (likeCount && data.total_curtidas !== undefined) {
                likeCount.textContent = Number(data.total_curtidas).toLocaleString('pt-BR');
            }
        } else {
            btn.classList.toggle('ativo', curtido);
            btn.setAttribute('aria-pressed', curtido ? 'true' : 'false');
            if (icon)      icon.className = curtido ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
            if (likeCount) likeCount.textContent = count.toLocaleString('pt-BR');
            showToast(data.mensagem ?? 'Erro ao curtir.', 'error');
        }

        btn.disabled = false;
    });
}

// ─── Seguir ──────────────────────────────────────────────────────────────────

function bindSeguirButton(btn) {
    btn.addEventListener('click', async () => {
        if (!sessaoId) { showToast('Faça login para seguir.', 'info'); return; }
        btn.disabled = true;

        const fd = new FormData();
        fd.append('alvo_id', btn.dataset.alvo);
        const data = await postForm('../../api/user/seguir.php', fd);

        if (data.ok) {
            atualizarBotoesSeguir(btn.dataset.alvo, data.acao === 'seguindo');
            showToast(data.mensagem, 'success');
        } else {
            showToast(data.mensagem ?? 'Erro.', 'error');
        }

        btn.disabled = false;
    });
}

function atualizarBotoesSeguir(alvoId, seguindo) {
    // Botões inline (no card) e sidebar
    document.querySelectorAll('.btn-seguir-post[data-alvo="' + alvoId + '"], .btn-seguir-sidebar[data-alvo="' + alvoId + '"]').forEach((b) => {
        if (seguindo) {
            b.classList.add('seguindo');
            if (b.classList.contains('btn-seguir-post')) {
                b.innerHTML = '<i class="fa-solid fa-user-check" aria-hidden="true"></i> Seguindo';
            } else {
                b.innerHTML = '<i class="fa-solid fa-user-check" aria-hidden="true"></i>';
            }
        } else {
            b.classList.remove('seguindo');
            if (b.classList.contains('btn-seguir-post')) {
                b.innerHTML = '<i class="fa-solid fa-user-plus" aria-hidden="true"></i> Seguir';
            } else {
                b.innerHTML = '<i class="fa-solid fa-user-plus" aria-hidden="true"></i>';
            }
        }
    });

    // Botão do modal
    const btnModal = document.getElementById('btnConfirmarSeguir');
    const modalAlvoId = document.getElementById('modalSeguirAlvoId');
    if (btnModal && modalAlvoId && modalAlvoId.value === String(alvoId)) {
        btnModal.dataset.seguindo = seguindo ? '1' : '0';
        btnModal.innerHTML = seguindo
            ? '<i class="fa-solid fa-user-minus" aria-hidden="true"></i> Deixar de seguir'
            : '<i class="fa-solid fa-user-plus" aria-hidden="true"></i> Seguir';
    }
}

function initSeguirButtonsSidebar() {
    document.querySelectorAll('.btn-seguir-sidebar').forEach(bindSeguirButton);
}

// ─── Modal de seguir ao clicar na PFP ────────────────────────────────────────

async function abrirModalSeguir(alvoId) {
    if (!sessaoId) {
        showToast('Faça login para interagir.', 'info');
        return;
    }

    const modalAlvoId  = document.getElementById('modalSeguirAlvoId');
    const modalAvatar  = document.getElementById('modalSeguirAvatar');
    const modalNome    = document.getElementById('modalSeguirNome');
    const modalBio     = document.getElementById('modalSeguirBio');
    const modalSeg     = document.getElementById('modalSeguirSeguidores');
    const btnConfirmar = document.getElementById('btnConfirmarSeguir');
    const btnPerfil    = document.getElementById('btnIrPerfil');

    if (!document.getElementById('modalSeguir')) return;

    if (modalAlvoId)  modalAlvoId.value   = alvoId;
    if (modalNome)    modalNome.textContent = 'Carregando...';
    if (modalBio)     modalBio.textContent  = '';
    if (modalSeg)     modalSeg.textContent  = '';
    if (btnConfirmar) btnConfirmar.disabled = true;

    openModal('modalSeguir');

    const data = await getJson('../../api/user/status_seguir.php?alvo_id=' + alvoId);

    if (!data.ok) {
        showToast(data.mensagem ?? 'Erro ao carregar perfil.', 'error');
        closeModal('modalSeguir');
        return;
    }

    const u         = data.usuario;
    const avatarSrc = u.avatar_path
        ? '../assets/img/user/avatars/' + u.avatar_path
        : '../assets/img/user/avatars/avatar.png';

    if (modalAvatar)  { modalAvatar.src = avatarSrc; modalAvatar.alt = 'Avatar de ' + u.nome; }
    if (modalNome)    modalNome.textContent = u.nome;
    if (modalBio)     modalBio.textContent  = u.bio || 'Sem bio ainda.';
    if (modalSeg)     modalSeg.textContent  = Number(data.total_seguidores).toLocaleString('pt-BR') + ' seguidores';
    if (btnPerfil)    btnPerfil.href        = 'perfil.php?id=' + alvoId;

    if (btnConfirmar) {
        btnConfirmar.dataset.alvo     = alvoId;
        btnConfirmar.dataset.seguindo = data.ja_segue ? '1' : '0';
        btnConfirmar.disabled         = false;
        btnConfirmar.innerHTML        = data.ja_segue
            ? '<i class="fa-solid fa-user-minus" aria-hidden="true"></i> Deixar de seguir'
            : '<i class="fa-solid fa-user-plus" aria-hidden="true"></i> Seguir';
    }
}

function initModalSeguir() {
    const btnConfirmar = document.getElementById('btnConfirmarSeguir');
    if (!btnConfirmar) return;

    btnConfirmar.addEventListener('click', async () => {
        const alvoId = btnConfirmar.dataset.alvo;
        btnConfirmar.disabled = true;

        const fd = new FormData();
        fd.append('alvo_id', alvoId);
        const data = await postForm('../../api/user/seguir.php', fd);

        if (data.ok) {
            atualizarBotoesSeguir(alvoId, data.acao === 'seguindo');
            showToast(data.mensagem, 'success');
            closeModal('modalSeguir');
        } else {
            showToast(data.mensagem ?? 'Erro.', 'error');
            btnConfirmar.disabled = false;
        }
    });
}

// ─── Reportar post ───────────────────────────────────────────────────────────

function initReportarPost() {
    const modalReport = document.getElementById('modalReportPost');
    if (!modalReport) return;

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-report-post');
        if (!btn) return;
        document.getElementById('reportPostId').value = btn.dataset.postId;
        openModal('modalReportPost');
    });

    const btnEnviar = document.getElementById('btnEnviarReport');
    if (!btnEnviar) return;

    btnEnviar.addEventListener('click', async () => {
        const postId  = document.getElementById('reportPostId').value;
        const motivo  = document.getElementById('reportMotivo').value.trim();
        const detalhe = document.getElementById('reportDetalhe').value.trim();

        if (!motivo) { showToast('Selecione um motivo.', 'error'); return; }

        btnEnviar.disabled = true;
        const fd = new FormData();
        fd.append('post_id', postId);
        fd.append('motivo', detalhe ? motivo + ' — ' + detalhe : motivo);
        const data = await postForm('../../api/comunidade/reportar_post.php', fd);

        showToast(data.mensagem ?? (data.ok ? 'Denúncia enviada.' : 'Erro.'), data.ok ? 'success' : 'error');
        closeModal('modalReportPost');
        document.getElementById('reportMotivo').value  = '';
        document.getElementById('reportDetalhe').value = '';
        btnEnviar.disabled = false;
    });
}

// ─── Bootstrap ───────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {
    initModalCloseButtons();
    initCriarPost();
    initReportarPost();
    initSeguirButtonsSidebar();
    initModalSeguir();
    initTabs();
    // Feed inicial carregado via JS para garantir consistência com as tabs
    carregarFeed('todos');
});