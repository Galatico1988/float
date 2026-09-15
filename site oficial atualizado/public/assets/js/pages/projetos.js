document.addEventListener('DOMContentLoaded', () => {

  const API = window.PUBLICAR?.apiBase || '/float/api/jogos/';
  const jogoId = window.PUBLICAR?.jogoId || null;
  let currentStep = 1;
  const totalSteps = 5;

  /* ===========================================================
     MULTI-STEP NAVIGATION
     =========================================================== */
  const panels  = document.querySelectorAll('.pub-panel');
  const steps   = document.querySelectorAll('.pub-step');
  const btnNext = document.getElementById('btnNext');
  const btnPrev = document.getElementById('btnPrev');
  const btnPub  = document.getElementById('btnPublish');

  function goToStep(n) {
    if (n < 1 || n > totalSteps) return;
    panels.forEach(p => p.classList.remove('active'));
    steps.forEach(s => {
      const sn = parseInt(s.dataset.step);
      s.classList.remove('active', 'done');
      if (sn < n) s.classList.add('done');
      if (sn === n) s.classList.add('active');
    });
    document.querySelector(`.pub-panel[data-panel="${n}"]`).classList.add('active');
    btnPrev.style.display = n > 1 ? '' : 'none';
    btnNext.style.display = n < totalSteps ? '' : 'none';
    btnPub.style.display  = n === totalSteps ? '' : 'none';
    currentStep = n;
    if (n === 5 && typeof syncPreview === 'function') syncPreview();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  btnNext.addEventListener('click', () => {
    if (currentStep === 1 && !validateStep1()) return;
    goToStep(currentStep + 1);
  });
  btnPrev.addEventListener('click', () => goToStep(currentStep - 1));

  function validateStep1() {
    const titulo = document.getElementById('inputTitulo').value.trim();
    const desc   = document.getElementById('inputDescricao').value.trim();
    if (titulo.length < 2) {
      alert('O título deve ter pelo menos 2 caracteres.');
      return false;
    }
    if (!desc) {
      alert('A descrição é obrigatória.');
      return false;
    }
    return true;
  }

  /* ===========================================================
     STEP 1 — SLUG PREVIEW + PRECO TOGGLE
     =========================================================== */
  const inputTitulo = document.getElementById('inputTitulo');
  const slugPreview = document.getElementById('slugPreview');

  inputTitulo.addEventListener('input', () => {
    const slug = inputTitulo.value
      .toLowerCase()
      .normalize('NFD').replace(/[̀-ͯ]/g, '')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-|-$/g, '');
    slugPreview.textContent = slug ? `/jogos/${slug}/` : '';
  });

  document.querySelectorAll('input[name="eh_gratis"]').forEach(radio => {
    radio.addEventListener('change', () => {
      document.getElementById('precoGroup').style.display =
        radio.value === '0' && radio.checked ? '' : 'none';
    });
  });

  /* ===========================================================
     STEP 2 — FILE UPLOADS (capa, backdrop)
     =========================================================== */
  function setupUploadArea(areaId, previewId) {
    const area = document.getElementById(areaId);
    const preview = document.getElementById(previewId);
    if (!area) return;
    const input = area.querySelector('input[type="file"]');

    area.addEventListener('click', () => input.click());
    area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('dragover'); });
    area.addEventListener('dragleave', () => area.classList.remove('dragover'));
    area.addEventListener('drop', e => {
      e.preventDefault();
      area.classList.remove('dragover');
      if (e.dataTransfer.files.length) {
        input.files = e.dataTransfer.files;
        input.dispatchEvent(new Event('change'));
      }
    });

    input.addEventListener('change', () => {
      const file = input.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = e => {
        preview.querySelector('img').src = e.target.result;
        preview.style.display = '';
        area.style.display = 'none';
      };
      reader.readAsDataURL(file);
    });

    const removeBtn = preview.querySelector('.btn-remove-img');
    if (removeBtn) {
      removeBtn.addEventListener('click', () => {
        input.value = '';
        preview.style.display = 'none';
        area.style.display = '';
      });
    }
  }

  setupUploadArea('uploadCapa', 'previewCapa');
  setupUploadArea('uploadBackdrop', 'previewBackdrop');

  /* ===========================================================
     STEP 2 — SCREENSHOTS (upload + drag reorder + local store)
     =========================================================== */
  const screenshotsGrid = document.getElementById('screenshotsGrid');
  const uploadScreenshot = document.getElementById('uploadScreenshot');
  const screenshotInput = uploadScreenshot?.querySelector('input[type="file"]');
  let screenshotsFiles = [];

  if (uploadScreenshot && screenshotInput) {
    uploadScreenshot.addEventListener('click', () => {
      if (screenshotsFiles.length >= 8) { alert('Máximo de 8 screenshots.'); return; }
      screenshotInput.click();
    });

    screenshotInput.addEventListener('change', () => {
      const file = screenshotInput.files[0];
      if (!file || screenshotsFiles.length >= 8) return;
      screenshotsFiles.push(file);
      renderScreenshots();
      screenshotInput.value = '';
    });
  }

  function renderScreenshots() {
    screenshotsGrid.innerHTML = '';
    screenshotsFiles.forEach((file, i) => {
      const div = document.createElement('div');
      div.className = 'screenshot-item';
      div.draggable = true;
      div.dataset.index = i;

      /* Imagem já existente no banco (edit mode) */
      if (file._existing) {
        div.innerHTML = `
          <img src="${file.src}" alt="Screenshot ${i + 1}">
          <button type="button" class="btn-remove-img" data-idx="${i}"><i class="fa-solid fa-xmark"></i></button>
        `;
        div.querySelector('.btn-remove-img').addEventListener('click', () => {
          screenshotsFiles.splice(i, 1);
          renderScreenshots();
        });
      } else {
        const reader = new FileReader();
        reader.onload = e => {
          div.innerHTML = `
            <img src="${e.target.result}" alt="Screenshot ${i + 1}">
            <button type="button" class="btn-remove-img" data-idx="${i}"><i class="fa-solid fa-xmark"></i></button>
          `;
          div.querySelector('.btn-remove-img').addEventListener('click', () => {
            screenshotsFiles.splice(i, 1);
            renderScreenshots();
          });
        };
        reader.readAsDataURL(file);
      }

      div.addEventListener('dragstart', e => {
        div.classList.add('dragging');
        e.dataTransfer.setData('text/plain', i);
      });
      div.addEventListener('dragend', () => div.classList.remove('dragging'));
      div.addEventListener('dragover', e => e.preventDefault());
      div.addEventListener('drop', e => {
        e.preventDefault();
        const from = parseInt(e.dataTransfer.getData('text/plain'));
        const to = i;
        if (from !== to) {
          const [moved] = screenshotsFiles.splice(from, 1);
          screenshotsFiles.splice(to, 0, moved);
          renderScreenshots();
        }
      });

      screenshotsGrid.appendChild(div);
    });
  }

  /* ===========================================================
     STEP 2 — VIDEOS
     =========================================================== */
  const videosList = document.getElementById('videosList');
  const videoTipo = document.getElementById('videoTipo');
  const videoUrlInput = document.getElementById('videoUrlInput');
  const videoTituloInput = document.getElementById('videoTituloInput');
  const btnAddVideoLink = document.getElementById('btnAddVideoLink');
  const uploadVideoFile = document.getElementById('uploadVideoFile');
  const videoFileInput = uploadVideoFile?.querySelector('input[type="file"]');
  let videosData = [];

  if (videoTipo) {
    videoTipo.addEventListener('change', () => {
      const isFile = videoTipo.value === 'arquivo';
      document.querySelector('.video-add-row').style.display = isFile ? 'none' : '';
      uploadVideoFile.style.display = isFile ? '' : 'none';
    });
  }

  if (btnAddVideoLink) {
    btnAddVideoLink.addEventListener('click', () => {
      const url = videoUrlInput.value.trim();
      const titulo = videoTituloInput.value.trim();
      if (!url) { alert('Informe a URL do vídeo.'); return; }

      let tipo = 'link';
      if (/(youtube\.com|youtu\.be)/.test(url)) tipo = 'youtube';

      videosData.push({ tipo, url, titulo, arquivo: null });
      renderVideos();
      videoUrlInput.value = '';
      videoTituloInput.value = '';
    });
  }

  if (uploadVideoFile && videoFileInput) {
    uploadVideoFile.addEventListener('click', () => videoFileInput.click());
    uploadVideoFile.addEventListener('dragover', e => { e.preventDefault(); uploadVideoFile.classList.add('dragover'); });
    uploadVideoFile.addEventListener('dragleave', () => uploadVideoFile.classList.remove('dragover'));
    uploadVideoFile.addEventListener('drop', e => {
      e.preventDefault();
      uploadVideoFile.classList.remove('dragover');
      if (e.dataTransfer.files.length) handleVideoFile(e.dataTransfer.files[0]);
    });

    videoFileInput.addEventListener('change', () => {
      if (videoFileInput.files[0]) handleVideoFile(videoFileInput.files[0]);
      videoFileInput.value = '';
    });
  }

  function handleVideoFile(file) {
    if (file.size > 50 * 1024 * 1024) { alert('Máximo 50MB.'); return; }
    const titulo = prompt('Título do vídeo (opcional):') || '';
    videosData.push({ tipo: 'arquivo', url: '', titulo, arquivo: file });
    renderVideos();
  }

  function renderVideos() {
    videosList.innerHTML = '';
    videosData.forEach((v, i) => {
      const div = document.createElement('div');
      div.className = 'video-item';

      const iconClass = v.tipo;
      const iconFA = v.tipo === 'youtube' ? 'fa-youtube' :
                     v.tipo === 'arquivo' ? 'fa-file-video' : 'fa-link';
      const displayUrl = v.tipo === 'arquivo' ? v.arquivo?.name || 'Arquivo local' : v.url;

      div.innerHTML = `
        <div class="video-item-icon ${iconClass}"><i class="fa-solid ${iconFA}"></i></div>
        <div class="video-item-info">
          <div class="video-item-titulo">${escHtml(v.titulo || displayUrl)}</div>
          <div class="video-item-url">${escHtml(displayUrl)}</div>
        </div>
        <button type="button" class="btn-remove-img" data-idx="${i}"><i class="fa-solid fa-xmark"></i></button>
      `;
      div.querySelector('.btn-remove-img').addEventListener('click', () => {
        videosData.splice(i, 1);
        renderVideos();
      });
      videosList.appendChild(div);
    });
  }

  /* ===========================================================
     STEP 3 — TAGS
     =========================================================== */
  const inputTag = document.getElementById('inputTag');
  const tagsChips = document.getElementById('tagsChips');
  const tagsHidden = document.getElementById('tagsHidden');
  let tagsList = [];

  function renderTags() {
    tagsChips.innerHTML = '';
    tagsList.forEach((tag, i) => {
      const chip = document.createElement('span');
      chip.className = 'tag-chip';
      chip.innerHTML = `${escHtml(tag)} <button type="button" data-idx="${i}">&times;</button>`;
      chip.querySelector('button').addEventListener('click', () => {
        tagsList.splice(i, 1);
        renderTags();
      });
      tagsChips.appendChild(chip);
    });
    tagsHidden.value = tagsList.join(',');
  }

  function addTag(tag) {
    tag = tag.trim();
    if (!tag || tagsList.length >= 20 || tagsList.includes(tag)) return;
    tagsList.push(tag);
    renderTags();
  }

  if (inputTag) {
    inputTag.addEventListener('keydown', e => {
      if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        addTag(inputTag.value);
        inputTag.value = '';
      }
      if (e.key === 'Backspace' && !inputTag.value && tagsList.length) {
        tagsList.pop();
        renderTags();
      }
    });
  }

  document.querySelectorAll('.tag-suggestion').forEach(el => {
    el.addEventListener('click', () => {
      addTag(el.dataset.tag);
    });
  });

  /* ===========================================================
     STEP 4 — METER SLIDERS
     =========================================================== */
  document.querySelectorAll('.meter-slider').forEach(slider => {
    const display = document.getElementById(slider.dataset.display);
    slider.addEventListener('input', () => {
      if (display) display.textContent = slider.value;
    });
  });

  /* ===========================================================
     STEP 5 — CUSTOMIZATION INLINE EDITING
     =========================================================== */
  const layoutData = { destaques: [], custom_layout: [] };
  let destaquesCounter = 0;

  /* --- Sync fields from earlier steps into preview --- */
  function syncPreview() {
    /* Title */
    const tituloEl = document.querySelector('[data-field="titulo"]');
    if (tituloEl) tituloEl.textContent = document.getElementById('inputTitulo').value.trim() || 'Título do Jogo';

    /* Tagline */
    const taglineEl = document.querySelector('[data-field="tagline"]');
    if (taglineEl) taglineEl.textContent = document.getElementById('inputTagline').value.trim() || 'por Desenvolvedor';

    /* Price */
    const precoEl = document.querySelector('[data-field="preco"]');
    if (precoEl) {
      const isFree = document.querySelector('input[name="eh_gratis"]:checked')?.value === '1';
      precoEl.textContent = isFree ? 'Baixar Grátis' : (document.getElementById('inputPreco').value.trim() || 'R$ 0,00');
    }

    /* Description */
    const descEl = document.querySelector('[data-field="descricao"]');
    if (descEl) descEl.textContent = document.getElementById('inputDescricao').value.trim() || 'Adicione uma descrição do seu jogo...';

    /* Cover image */
    const capaInput = document.querySelector('#uploadCapa input[type="file"]');
    const cpCapa = document.getElementById('cpCapa');
    if (cpCapa && capaInput?.files[0]) {
      const reader = new FileReader();
      reader.onload = e => cpCapa.src = e.target.result;
      reader.readAsDataURL(capaInput.files[0]);
    } else if (cpCapa && !cpCapa.src) {
      cpCapa.src = '../assets/img/pages/index/cards/card_undertale.png';
    }

    /* Backdrop */
    const backdropInput = document.querySelector('#uploadBackdrop input[type="file"]');
    const cpBg = document.getElementById('cpHeroBg');
    if (cpBg && backdropInput?.files[0]) {
      const reader = new FileReader();
      reader.onload = e => cpBg.style.backgroundImage = `url('${e.target.result}')`;
      reader.readAsDataURL(backdropInput.files[0]);
    }

    /* Tags */
    const cpTags = document.getElementById('cpTags');
    if (cpTags) {
      cpTags.innerHTML = '';
      tagsList.forEach(tag => {
        const span = document.createElement('span');
        span.className = 'promo-tag';
        span.textContent = tag;
        cpTags.appendChild(span);
      });
    }

    /* Platforms */
    const cpPlat = document.getElementById('cpPlatforms');
    if (cpPlat) {
      cpPlat.innerHTML = '';
      document.querySelectorAll('input[name="plataformas[]"]:checked').forEach(c => {
        const i = document.createElement('i');
        i.className = `fa-brands fa-${c.value}`;
        i.title = c.value;
        cpPlat.appendChild(i);
      });
    }

    /* Highlights */
    renderHighlights();
  }

  function renderHighlights() {
    const container = document.getElementById('cpHighlights');
    if (!container) return;
    /* Remove old items but keep the add button */
    container.querySelectorAll('.cp-highlight-item').forEach(el => el.remove());
    const addBtn = document.getElementById('btnAddDestaque');

    layoutData.destaques.forEach((d, i) => {
      const div = document.createElement('div');
      div.className = 'cp-highlight-item';
      div.innerHTML = `
        <i class="fa-solid ${escHtml(d.icone)}"></i>
        <span contenteditable="true">${escHtml(d.texto)}</span>
        <button type="button" class="cp-remove-highlight" data-idx="${i}" title="Remover"><i class="fa-solid fa-xmark"></i></button>
      `;
      div.querySelector('[contenteditable]').addEventListener('blur', e => {
        layoutData.destaques[i].texto = e.target.textContent.trim();
      });
      div.querySelector('.cp-remove-highlight').addEventListener('click', () => {
        layoutData.destaques.splice(i, 1);
        renderHighlights();
      });
      container.insertBefore(div, addBtn);
    });
  }

  if (document.getElementById('btnAddDestaque')) {
    document.getElementById('btnAddDestaque').addEventListener('click', () => {
      const icons = ['fa-wand-magic-sparkles','fa-users','fa-map','fa-gamepad','fa-bolt','fa-trophy','fa-heart','fa-star'];
      const icone = icons[destaquesCounter % icons.length];
      destaquesCounter++;
      layoutData.destaques.push({ icone, texto: 'Novo destaque' });
      renderHighlights();
    });
  }

  /* --- Toggles --- */
  ['togSobre','togDestaques','togVideos','togRequisitos','togRelacionados'].forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    const sectionMap = {
      togSobre: 'cpSobre',
      togDestaques: null, /* toggles highlights inside cpSobre */
      togVideos: 'cpVideosSection',
      togRequisitos: 'cpRequisitos',
      togRelacionados: 'cpRelacionados',
    };
    el.addEventListener('change', () => {
      if (id === 'togDestaques') {
        const highlights = document.getElementById('cpHighlights');
        if (highlights) highlights.style.display = el.checked ? '' : 'none';
      } else {
        const sec = document.getElementById(sectionMap[id]);
        if (sec) sec.classList.toggle('hidden', !el.checked);
      }
    });
  });

  /* --- Contenteditable sync back to fields --- */
  document.querySelectorAll('#customizePreviewFrame [contenteditable]').forEach(el => {
    el.addEventListener('blur', () => {
      const field = el.dataset.field;
      const val = el.textContent.trim();
      /* Só sobrescreve se o contenteditable tem conteúdo — evita apagar valores reais */
      if (field === 'titulo' && val) document.getElementById('inputTitulo').value = val;
      if (field === 'tagline' && val) document.getElementById('inputTagline').value = val;
      if (field === 'preco' && val) document.getElementById('inputPreco').value = val;
      if (field === 'descricao' && val) document.getElementById('inputDescricao').value = val;
    });
  });

  /* --- Image edit buttons --- */
  document.querySelectorAll('.cp-img-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const url = prompt('Cole a URL da imagem:');
      if (url) {
        const target = document.getElementById(btn.dataset.target);
        if (target) target.src = url;
      }
    });
  });

  /* ===========================================================
     FORM SUBMIT
     =========================================================== */
  const form = document.getElementById('pubForm');
  const pubLoading = document.getElementById('pubLoading');
  const pubSuccess = document.getElementById('pubSuccess');
  const linkJogo = document.getElementById('linkJogoPublicado');

  form.addEventListener('submit', async e => {
    e.preventDefault();
    console.log('Titulo:', inputTitulo?.value, 'Desc:', document.getElementById('inputDescricao')?.value);
    if (!validateStep1()) return;

    pubLoading.style.display = '';

    try {
      const fd = new FormData();

      /* Step 1 */
      fd.append('titulo', inputTitulo.value.trim());
      fd.append('tagline', document.getElementById('inputTagline').value.trim());
      fd.append('descricao', document.getElementById('inputDescricao').value.trim());
      fd.append('genero', document.getElementById('inputGenero').value);
      fd.append('classificacao', document.getElementById('inputClassificacao').value);
      fd.append('eh_gratis', document.querySelector('input[name="eh_gratis"]:checked').value);
      fd.append('preco', document.getElementById('inputPreco').value.trim() || 'Gratis');
      fd.append('versao', document.getElementById('inputVersao').value.trim());
      fd.append('tamanho', document.getElementById('inputTamanho').value.trim());
      fd.append('idiomas', document.getElementById('inputIdiomas').value.trim());
      fd.append('download_url', document.getElementById('inputDownloadUrl').value.trim());
      fd.append('visibilidade', 'publico');

      /* Step 2 — images */
      const capaInput = document.querySelector('#uploadCapa input[type="file"]');
      if (capaInput.files[0]) fd.append('thumbnail', capaInput.files[0]);

      const backdropInput = document.querySelector('#uploadBackdrop input[type="file"]');
      if (backdropInput.files[0]) fd.append('backdrop', backdropInput.files[0]);

      /* Step 3 */
      fd.append('tags', tagsList.join(','));
      const plataformas = [];
      document.querySelectorAll('input[name="plataformas[]"]:checked').forEach(c => plataformas.push(c.value));
      fd.append('plataformas', JSON.stringify(plataformas));

      /* Step 4 — requisitos */
      const reqMin = {};
      const reqRec = {};
      ['os','processador','memoria','video','armazenamento','directx'].forEach(campo => {
        reqMin[campo] = document.querySelector(`[name="req_minimo_${campo}"]`)?.value || '';
        reqRec[campo] = document.querySelector(`[name="req_recomendado_${campo}"]`)?.value || '';
      });
      ['carga_cpu','carga_gpu','carga_ram'].forEach(campo => {
        reqMin[campo] = parseInt(document.querySelector(`[name="req_minimo_${campo}"]`)?.value || 0);
        reqRec[campo] = parseInt(document.querySelector(`[name="req_recomendado_${campo}"]`)?.value || 0);
      });
      fd.append('requisitos_minimo', JSON.stringify(reqMin));
      fd.append('requisitos_recomendado', JSON.stringify(reqRec));

      /* Step 5 — custom layout */
      fd.append('custom_layout', JSON.stringify(layoutData.destaques));

      /* Videos links (não arquivos — arquivos são feitos depois) */
      const videosLinks = videosData
        .filter(v => v.tipo !== 'arquivo')
        .map(v => ({ tipo: v.tipo, url: v.url, titulo: v.titulo }));
      fd.append('videos_links', JSON.stringify(videosLinks));

      /* Publicar ou Editar */
      const endpoint = jogoId ? 'editar.php' : 'publicar.php';
      if (jogoId) fd.append('jogo_id', jogoId);
      const resp = await fetch(API + endpoint, { method: 'POST', body: fd });
      const data = await resp.json();

      if (!data.ok) {
        pubLoading.style.display = 'none';
        alert(data.mensagem || 'Erro ao publicar.');
        return;
      }

      const novoJogoId = data.jogo_id;

      /* Upload de screenshots (só os novos) */
      for (let i = 0; i < screenshotsFiles.length; i++) {
        if (screenshotsFiles[i]._existing) continue;
        const sfd = new FormData();
        sfd.append('jogo_id', novoJogoId);
        sfd.append('ordem', i);
        sfd.append('imagem', screenshotsFiles[i]);
        await fetch(API + 'upload_screenshot.php', { method: 'POST', body: sfd });
      }

      /* Upload de vídeos arquivo (só os novos) */
      for (let i = 0; i < videosData.length; i++) {
        if (videosData[i]._existing) continue;
        if (videosData[i].tipo === 'arquivo' && videosData[i].arquivo) {
          const vfd = new FormData();
          vfd.append('jogo_id', novoJogoId);
          vfd.append('ordem', i);
          vfd.append('titulo', videosData[i].titulo);
          vfd.append('video', videosData[i].arquivo);
          await fetch(API + 'upload_video.php', { method: 'POST', body: vfd });
        }
      }

      /* Sucesso! */
      pubLoading.style.display = 'none';
      pubSuccess.style.display = '';
      linkJogo.href = data.url;

    } catch (err) {
      pubLoading.style.display = 'none';
      console.error(err);
      alert('Erro de conexão. Tente novamente.');
    }
  });

  /* ===========================================================
     UTILITY
     =========================================================== */
  function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
  }

  /* ===========================================================
     MODO EDIÇÃO — Prefill dos campos
     =========================================================== */
  if (jogoId) {
    (async () => {
      try {
        const resp = await fetch(API + 'buscar.php?id=' + jogoId);
        const data = await resp.json();
        if (!data.ok) { alert(data.mensagem); return; }

        const j = data.jogo;

        /* Step 1 */
        document.getElementById('inputTitulo').value = j.titulo || '';
        document.getElementById('inputTagline').value = j.tagline || '';
        document.getElementById('inputDescricao').value = j.descricao || '';
        document.getElementById('inputGenero').value = j.genero || '';
        document.getElementById('inputClassificacao').value = j.classificacao || 'Livre';
        document.getElementById('inputVersao').value = j.versao || '';
        document.getElementById('inputTamanho').value = j.tamanho || '';
        document.getElementById('inputIdiomas').value = j.idiomas || 'Portugues';
        document.getElementById('inputDownloadUrl').value = j.download_url || '';

        /* Preço */
        const isFree = j.eh_gratis == 1;
        document.querySelectorAll('input[name="eh_gratis"]').forEach(r => {
          r.checked = (r.value === (isFree ? '1' : '0'));
        });
        document.getElementById('precoGroup').style.display = isFree ? 'none' : '';
        document.getElementById('inputPreco').value = isFree ? '' : (j.preco || '');

        /* Slug preview */
        if (slugPreview) slugPreview.textContent = `/jogos/${j.slug}/`;

        /* Step 2 — Capa/Backdrop previews */
        if (j.thumbnail) {
          const previewCapa = document.getElementById('previewCapa');
          const uploadCapa = document.getElementById('uploadCapa');
          if (previewCapa && uploadCapa) {
            previewCapa.querySelector('img').src = '/float/public/assets/img/jogos/' + j.id + '/' + j.thumbnail;
            previewCapa.style.display = '';
            uploadCapa.style.display = 'none';
          }
        }
        if (j.backdrop) {
          const previewBd = document.getElementById('previewBackdrop');
          const uploadBd = document.getElementById('uploadBackdrop');
          if (previewBd && uploadBd) {
            previewBd.querySelector('img').src = '/float/public/assets/img/jogos/' + j.id + '/' + j.backdrop;
            previewBd.style.display = '';
            uploadBd.style.display = 'none';
          }
        }

        /* Step 2 — Screenshots existentes */
        if (data.screenshots && data.screenshots.length) {
          data.screenshots.forEach(s => {
            screenshotsFiles.push({ _existing: true, src: '/float/public/assets/img/jogos/' + j.id + '/' + s.caminho, id: s.id });
          });
          renderScreenshots();
        }

        /* Step 2 — Vídeos existentes */
        if (data.videos && data.videos.length) {
          data.videos.forEach(v => {
            videosData.push({ tipo: v.tipo, url: v.url, titulo: v.titulo || '', arquivo: null, _existing: true, id: v.id });
          });
          renderVideos();
        }

        /* Step 3 — Tags */
        if (data.tags && data.tags.length) {
          data.tags.forEach(t => addTag(t));
        }

        /* Step 3 — Plataformas */
        if (j.plataformas && j.plataformas.length) {
          document.querySelectorAll('input[name="plataformas[]"]').forEach(c => {
            c.checked = j.plataformas.includes(c.value);
          });
        }

        /* Step 4 — Requisitos */
        if (data.requisitos?.minimo) {
          const rm = data.requisitos.minimo;
          ['os','processador','memoria','video','armazenamento','directx'].forEach(c => {
            const el = document.querySelector(`[name="req_minimo_${c}"]`);
            if (el) el.value = rm[c] || '';
          });
          ['carga_cpu','carga_gpu','carga_ram'].forEach(c => {
            const el = document.querySelector(`[name="req_minimo_${c}"]`);
            if (el) { el.value = rm[c] || 0; const disp = document.getElementById(el.dataset.display); if (disp) disp.textContent = rm[c] || 0; }
          });
        }
        if (data.requisitos?.recomendado) {
          const rr = data.requisitos.recomendado;
          ['os','processador','memoria','video','armazenamento','directx'].forEach(c => {
            const el = document.querySelector(`[name="req_recomendado_${c}"]`);
            if (el) el.value = rr[c] || '';
          });
          ['carga_cpu','carga_gpu','carga_ram'].forEach(c => {
            const el = document.querySelector(`[name="req_recomendado_${c}"]`);
            if (el) { el.value = rr[c] || 0; const disp = document.getElementById(el.dataset.display); if (disp) disp.textContent = rr[c] || 0; }
          });
        }

        /* Step 5 — Destaques */
        if (j.destaques && j.destaques.length) {
          layoutData.destaques = j.destaques;
          renderHighlights();
        }

      } catch (err) {
        console.error('Erro ao carregar jogo:', err);
        alert('Erro ao carregar dados do jogo.');
      }
    })();
  }

});
