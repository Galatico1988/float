











'use strict';

const { ehDono, alvoId, sessaoId } = window.PERFIL ?? {};







function showToast(msg, tipo = 'info') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const iconMap = {
        success: 'fa-circle-check',
        error: 'fa-circle-xmark',
        info: 'fa-circle-info',
    };

    const toast = document.createElement('div');
    toast.className = `toast toast-${tipo}`;
    toast.setAttribute('role', 'status');
    toast.innerHTML = `
        <i class="fa-solid ${iconMap[tipo] ?? iconMap.info}" aria-hidden="true"></i>
        <span>${msg}</span>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('toast-hide');
        toast.addEventListener('transitionend', () => toast.remove(), { once: true });
    }, 3500);
}







async function postForm(url, formData) {
    try {
        const res = await fetch(url, { method: 'POST', body: formData });
        const text = await res.text();
        try {
            return JSON.parse(text);
        } catch {
            console.error('Resposta não-JSON do servidor:', text);
            return { ok: false, mensagem: `Erro do servidor (${res.status}). Verifique o console.` };
        }
    } catch (err) {
        console.error('Erro de rede:', err);
        return { ok: false, mensagem: 'Erro de conexão com o servidor.' };
    }
}






function openModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;

    modal.removeAttribute('hidden');
    const focusable = modal.querySelector('input, textarea, select, button:not(.modal-close)');
    if (focusable) focusable.focus();
    document.body.style.overflow = 'hidden';

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


function initTabs() {
    const btns = document.querySelectorAll('.tab-btn');
    const panels = document.querySelectorAll('.perfil-panel');

    btns.forEach((btn) => {
        btn.addEventListener('click', () => {
            const alvo = btn.dataset.tab;

            btns.forEach((b) => {
                b.classList.remove('active');
                b.setAttribute('aria-selected', 'false');
            });
            btn.classList.add('active');
            btn.setAttribute('aria-selected', 'true');

            panels.forEach((p) => {
                const show = p.id === `panel-${alvo}`;
                p.toggleAttribute('hidden', !show);
                if (show) p.classList.add('active');
                else p.classList.remove('active');
            });
        });
    });
}


function initSeguir() {
    const btn = document.getElementById('btnSeguir');
    if (!btn) return;

    btn.addEventListener('click', async () => {
        btn.disabled = true;

        const fd = new FormData();
        fd.append('alvo_id', alvoId);

        const data = await postForm('../../api/user/seguir.php', fd);

        if (data.ok) {
            const seguindo = data.acao === 'seguindo';
            btn.classList.toggle('seguindo', seguindo);
            btn.setAttribute('aria-pressed', seguindo ? 'true' : 'false');

            const icon = btn.querySelector('i');
            const span = btn.querySelector('span');

            if (icon) icon.className = `fa-solid ${seguindo ? 'fa-user-check' : 'fa-user-plus'}`;
            if (span) span.textContent = seguindo ? 'Seguindo' : 'Seguir';

            const contadores = document.querySelectorAll('.perfil-counters strong');
            if (contadores[0] && data.total_seguidores !== undefined) {
                contadores[0].textContent = Number(data.total_seguidores).toLocaleString('pt-BR');
            }

            showToast(data.mensagem, 'success');
        } else {
            showToast(data.mensagem ?? 'Erro ao processar.', 'error');
        }

        btn.disabled = false;
    });
}


function initReportar() {
    const btnReport = document.getElementById('btnReport');
    if (btnReport) {
        btnReport.addEventListener('click', () => {
            document.getElementById('reportTipo').value = btnReport.dataset.tipo;
            document.getElementById('reportAlvoId').value = btnReport.dataset.alvo;
            openModal('modalReport');
        });
    }

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.jogo-report-btn');
        if (!btn) return;
        document.getElementById('reportTipo').value = btn.dataset.tipo;
        document.getElementById('reportAlvoId').value = btn.dataset.alvo;
        openModal('modalReport');
    });

    const btnEnviar = document.getElementById('btnEnviarReport');
    if (!btnEnviar) return;

    btnEnviar.addEventListener('click', async () => {
        const motivo = document.getElementById('selectMotivo').value.trim();
        const detalhe = document.getElementById('detalheReport').value.trim();
        const tipo = document.getElementById('reportTipo').value;
        const alvoRep = document.getElementById('reportAlvoId').value;

        if (!motivo) {
            showToast('Selecione um motivo antes de enviar.', 'error');
            return;
        }

        btnEnviar.disabled = true;

        const fd = new FormData();
        fd.append('tipo', tipo);
        fd.append('alvo_id', alvoRep);
        fd.append('motivo', detalhe ? `${motivo} — ${detalhe}` : motivo);

        const data = await postForm('../../api/user/reportar.php', fd);

        showToast(data.mensagem ?? (data.ok ? 'Denúncia enviada.' : 'Erro.'), data.ok ? 'success' : 'error');
        closeModal('modalReport');

        document.getElementById('selectMotivo').value = '';
        document.getElementById('detalheReport').value = '';
        btnEnviar.disabled = false;
    });
}


function initAjuda() {
    const btn = document.getElementById('btnHelp');
    if (!btn) return;
    btn.addEventListener('click', () => openModal('modalHelp'));
}


function initEdicaoTexto() {
    if (!ehDono) return;

    const btnEditNome = document.getElementById('btnEditNome');
    const btnSalvNome = document.getElementById('btnSalvarNome');
    const inputNome = document.getElementById('inputNome');
    const spanNome = document.getElementById('perfilNome');

    if (btnEditNome) {
        btnEditNome.addEventListener('click', () => {
            inputNome.value = spanNome.textContent.trim();
            openModal('modalNome');
        });
    }

    if (btnSalvNome) {
        btnSalvNome.addEventListener('click', async () => {
            const novoNome = inputNome.value.trim();

            if (novoNome.length < 3 || novoNome.length > 50) {
                showToast('O nome deve ter entre 3 e 50 caracteres.', 'error');
                return;
            }

            btnSalvNome.disabled = true;

            const fd = new FormData();
            fd.append('campo', 'nome');
            fd.append('valor', novoNome);

            const data = await postForm('../../api/user/atualizacao.php', fd);

            if (data.ok) {
                spanNome.textContent = novoNome;
                const navNome = document.querySelector('.user-name');
                if (navNome) navNome.textContent = novoNome;
                closeModal('modalNome');
                showToast('Nome atualizado!', 'success');
            } else {
                showToast(data.mensagem ?? 'Erro ao salvar.', 'error');
            }

            btnSalvNome.disabled = false;
        });
    }

    const btnEditBio = document.getElementById('btnEditBio');
    const btnSalvBio = document.getElementById('btnSalvarBio');
    const inputBio = document.getElementById('inputBio');
    const spanBio = document.getElementById('perfilBio');
    const bioCount = document.getElementById('bioCharCount');

    if (btnEditBio) {
        btnEditBio.addEventListener('click', () => {
            const texto = spanBio.querySelector('em') ? '' : spanBio.textContent.trim();
            inputBio.value = texto;
            if (bioCount) bioCount.textContent = texto.length;
            openModal('modalBio');
        });
    }

    if (inputBio && bioCount) {
        inputBio.addEventListener('input', () => {
            bioCount.textContent = inputBio.value.length;
        });
    }

    if (btnSalvBio) {
        btnSalvBio.addEventListener('click', async () => {
            const novaBio = inputBio.value.trim();

            if (novaBio.length > 300) {
                showToast('Máximo de 300 caracteres.', 'error');
                return;
            }

            btnSalvBio.disabled = true;

            const fd = new FormData();
            fd.append('campo', 'bio');
            fd.append('valor', novaBio);

            const data = await postForm('../../api/user/atualizacao.php', fd);

            if (data.ok) {
                spanBio.innerHTML = novaBio
                    ? escapeHtml(novaBio)
                    : '<em class="bio-placeholder">Adicione uma bio...</em>';
                closeModal('modalBio');
                showToast('Bio atualizada!', 'success');
            } else {
                showToast(data.mensagem ?? 'Erro ao salvar.', 'error');
            }

            btnSalvBio.disabled = false;
        });
    }
}


function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}


let cropperInstance = null;
let cropTipo = null;


function openUploadModal(tipo) {
    cropTipo = tipo;

    const tituloTexto = document.getElementById('modalUploadTituloTexto');
    const hintTipo = document.getElementById('uploadHintTipo');
    const inputModal = document.getElementById('inputUploadModal');

    if (tituloTexto) tituloTexto.textContent = tipo === 'avatar' ? 'Alterar foto de perfil' : 'Alterar banner';
    if (hintTipo) hintTipo.textContent = tipo === 'banner'
        ? 'PNG, JPG, WEBP ou GIF • até 5 MB'
        : 'PNG, JPG ou WEBP • até 5 MB (sem GIF para avatar)';
    if (inputModal) {
        inputModal.accept = tipo === 'banner' ? 'image/*, image/gif' : 'image/png, image/jpeg, image/webp';
        inputModal.value = '';
    }

    const preview = document.getElementById('uploadUrlPreview');
    const inputUrl = document.getElementById('inputUrlImagem');
    if (preview) preview.hidden = true;
    if (inputUrl) inputUrl.value = '';

    openModal('modalUploadImagem');
}

function initUploads() {
    if (!ehDono) return;

    const btnAvatar = document.getElementById('btnEditAvatar');
    if (btnAvatar) {
        btnAvatar.addEventListener('click', () => openUploadModal('avatar'));
    }

    const btnBanner = document.getElementById('btnEditBanner');
    if (btnBanner) {
        btnBanner.addEventListener('click', () => openUploadModal('banner'));
    }

    const inputModal = document.getElementById('inputUploadModal');
    const dropzone = document.getElementById('uploadDropzone');

    if (dropzone) {
        dropzone.addEventListener('click', () => inputModal && inputModal.click());
        dropzone.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                inputModal && inputModal.click();
            }
        });

        ['dragenter', 'dragover'].forEach((ev) => {
            dropzone.addEventListener(ev, (e) => {
                e.preventDefault();
                dropzone.classList.add('drag-over');
            });
        });

        ['dragleave', 'drop'].forEach((ev) => {
            dropzone.addEventListener(ev, (e) => {
                if (ev === 'drop') {
                    e.preventDefault();
                    const file = e.dataTransfer?.files?.[0];
                    if (file) processarArquivoSelecionado(file);
                }
                dropzone.classList.remove('drag-over');
            });
        });
    }

    if (inputModal) {
        inputModal.addEventListener('change', (e) => {
            const file = e.target.files?.[0];
            if (file) processarArquivoSelecionado(file);
        });
    }

    const btnCarregarUrl = document.getElementById('btnCarregarUrl');
    if (btnCarregarUrl) {
        btnCarregarUrl.addEventListener('click', carregarImagemPorUrl);
    }
    const inputUrl = document.getElementById('inputUrlImagem');
    if (inputUrl) {
        inputUrl.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') carregarImagemPorUrl();
        });
    }

    const btnUsarUrl = document.getElementById('btnUsarUrlImagem');
    if (btnUsarUrl) {
        btnUsarUrl.addEventListener('click', async () => {
            const url = document.getElementById('inputUrlImagem')?.value?.trim();
            if (!url) return;

            btnUsarUrl.disabled = true;
            closeModal('modalUploadImagem');

            try {
                const res = await fetch(url);
                const blob = await res.blob();
                const file = new File([blob], `url_image.${blob.type.split('/')[1] || 'jpg'}`, { type: blob.type });
                processarArquivoSelecionado(file);
            } catch {
                showToast('Não foi possível carregar a imagem da URL.', 'error');
            } finally {
                btnUsarUrl.disabled = false;
            }
        });
    }

    const btnConfirmar = document.getElementById('btnConfirmarCrop');
    if (btnConfirmar) {
        btnConfirmar.addEventListener('click', confirmarCrop);
    }
}

function initDragAndDropUploads() {
    if (!ehDono) return;

    const banner = document.getElementById('perfilBanner');
    const overlay = document.getElementById('bannerDropOverlay');
    if (!banner || !overlay) return;

    const permitido = ['image/png', 'image/jpeg', 'image/webp', 'image/gif'];

    ['dragenter', 'dragover'].forEach((ev) => {
        banner.addEventListener(ev, (e) => {
            e.preventDefault();
            overlay.classList.add('active');
        });
    });

    ['dragleave', 'drop'].forEach((ev) => {
        banner.addEventListener(ev, (e) => {
            if (ev === 'drop') {
                e.preventDefault();
                overlay.classList.remove('active');
                const file = e.dataTransfer?.files?.[0];
                if (!file) { showToast('Nenhuma imagem detectada.', 'error'); return; }
                if (!permitido.includes(file.type)) { showToast('Formato não suportado.', 'error'); return; }
                cropTipo = 'banner';
                processarArquivoSelecionado(file);
            } else {
                overlay.classList.remove('active');
            }
        });
    });
}

async function carregarImagemPorUrl() {
    const input = document.getElementById('inputUrlImagem');
    const preview = document.getElementById('uploadUrlPreview');
    const img = document.getElementById('uploadUrlPreviewImg');
    const btn = document.getElementById('btnCarregarUrl');

    const url = input?.value?.trim();
    if (!url) { showToast('Cole uma URL válida.', 'error'); return; }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

    try {
        const testImg = new Image();
        testImg.crossOrigin = 'anonymous';
        await new Promise((resolve, reject) => {
            testImg.onload = resolve;
            testImg.onerror = reject;
            testImg.src = url;
        });

        if (img) img.src = url;
        if (preview) preview.hidden = false;
    } catch {
        showToast('Não foi possível carregar a imagem desta URL.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-arrow-right"></i>';
    }
}





function processarArquivoSelecionado(file) {
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        showToast('Selecione uma imagem válida (PNG, JPG, GIF ou WEBP).', 'error');
        return;
    }

    if (file.type === 'image/gif' && cropTipo === 'avatar') {
        showToast('Avatar não suporta GIF. Escolha PNG, JPG ou WEBP.', 'error');
        return;
    }

    closeModal('modalUploadImagem');

    if (file.type === 'image/gif') {
        enviarImagem(file);
        return;
    }

    handleImageSelect(file);
}

function applyBannerFrame() {
    const banner = document.getElementById('perfilBanner');
    if (!banner) return;

    const zoom = banner.dataset.bannerZoom || '100';
    const x = banner.dataset.bannerPosX || '50';
    const y = banner.dataset.bannerPosY || '50';

    banner.style.backgroundSize = `${zoom}%`;
    banner.style.backgroundPosition = `${x}% ${y}%`;
}





function handleImageSelect(file) {
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        const img = document.getElementById('cropperImg');
        if (!img) return;

        img.src = e.target.result;

        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }

        openModal('modalCropper');

        requestAnimationFrame(() => {
            cropperInstance = new Cropper(img, {
                aspectRatio: cropTipo === 'avatar' ? 1 : 3,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.8,
                responsive: true,
                restore: false,
                guides: false,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });
        });
    };

    reader.readAsDataURL(file);
}


async function confirmarCrop() {
    if (!cropperInstance) return;

    const btn = document.getElementById('btnConfirmarCrop');
    btn.disabled = true;

    const canvas = cropperInstance.getCroppedCanvas({
        width: cropTipo === 'avatar' ? 256 : 1200,
        height: cropTipo === 'avatar' ? 256 : 400,
        imageSmoothingQuality: 'high',
    });

    canvas.toBlob(async (blob) => {
        const file = new File([blob], `${cropTipo}.jpg`, { type: 'image/jpeg' });
        closeModal('modalCropper');
        await enviarImagem(file);
        btn.disabled = false;
    }, 'image/jpeg', 0.88);
}





async function enviarImagem(file) {
    const fd = new FormData();
    fd.append('tipo', cropTipo);
    fd.append('imagem', file);

    const data = await postForm('../../api/user/upload_imagem.php', fd);

    if (data.ok) {
        if (cropTipo === 'avatar') {
            const imgs = document.querySelectorAll('#perfilAvatarImg, .nav-user-img');
            imgs.forEach((img) => { img.src = data.url + '?t=' + Date.now(); });
        } else {
            const banner = document.getElementById('perfilBanner');
            if (banner) banner.style.backgroundImage = `url('${data.url}?t=${Date.now()}')`;
        }
        showToast('Imagem atualizada com sucesso!', 'success');

        ['inputUploadModal', 'inputAvatar', 'inputBanner'].forEach((id) => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });

        if (cropperInstance) {
            cropperInstance.destroy();
            cropperInstance = null;
        }
    } else {
        showToast(data.mensagem ?? 'Erro ao enviar imagem.', 'error');
    }
}


document.addEventListener('DOMContentLoaded', () => {
    initModalCloseButtons();
    initTabs();
    initSeguir();
    initReportar();
    initAjuda();
    initEdicaoTexto();
    initUploads();
});