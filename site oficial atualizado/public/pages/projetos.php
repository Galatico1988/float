<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require_once '../../api/config/conexao.php';

$basePath   = '../';
$activePage = 'projetos';
$usuarioId  = (int) $_SESSION['id'];

/* Modo edição */
$edicaoJogoId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$modoEdicao   = $edicaoJogoId > 0;
$tituloPagina = $modoEdicao ? 'Editar Jogo' : 'Publicar Jogo';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= $modoEdicao ? 'Edite seu jogo na Float.' : 'Publique seu jogo na Float.' ?>">
    <title>Float | <?= $tituloPagina ?></title>

    <link rel="stylesheet" href="../assets/css/global/global.css" />
    <link rel="stylesheet" href="../assets/css/pages/projetos.css" />
    <link rel="icon" href="../assets/img/favicon/icone.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet" />
</head>

<body>

    <?php require_once '../includes/header.php'; ?>

    <main class="pub-main">

        <div class="pub-header">
            <h1 class="pub-title"><i class="fa-solid fa-rocket"></i> <?= $tituloPagina ?></h1>
            <p class="pub-subtitle">Preencha os dados do seu jogo para publicá-lo na Float.</p>
        </div>

        <!-- Steps indicator -->
        <div class="pub-steps" id="pubSteps">
            <div class="pub-step active" data-step="1">
                <span class="step-num">1</span>
                <span class="step-label">Dados Básicos</span>
            </div>
            <div class="pub-step" data-step="2">
                <span class="step-num">2</span>
                <span class="step-label">Imagens & Vídeos</span>
            </div>
            <div class="pub-step" data-step="3">
                <span class="step-num">3</span>
                <span class="step-label">Tags & Plataformas</span>
            </div>
            <div class="pub-step" data-step="4">
                <span class="step-num">4</span>
                <span class="step-label">Requisitos</span>
            </div>
            <div class="pub-step" data-step="5">
                <span class="step-num">5</span>
                <span class="step-label">Customizar Página</span>
            </div>
        </div>

        <form id="pubForm" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="custom_layout" id="customLayoutInput" value="">

            <!-- ============ STEP 1: Dados Básicos ============ -->
            <div class="pub-panel active" data-panel="1">

                <div class="form-group">
                    <label for="inputTitulo">Título do Jogo <span class="required">*</span></label>
                    <input type="text" id="inputTitulo" name="titulo" maxlength="100" required
                           placeholder="Ex: Meu Jogo Incrível">
                    <span class="slug-preview" id="slugPreview"></span>
                </div>

                <div class="form-group">
                    <label for="inputTagline">Tagline</label>
                    <input type="text" id="inputTagline" name="tagline" maxlength="255"
                           placeholder="Uma frase curta que define seu jogo">
                </div>

                <div class="form-group">
                    <label for="inputDescricao">Descrição <span class="required">*</span></label>
                    <textarea id="inputDescricao" name="descricao" rows="6" required
                              placeholder="Conte tudo sobre o seu jogo..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="inputGenero">Gênero</label>
                        <select id="inputGenero" name="genero">
                            <option value="">Selecione...</option>
                            <option>RPG</option>
                            <option>Aventura</option>
                            <option>Plataforma</option>
                            <option>Roguelike</option>
                            <option>Roguelite</option>
                            <option>Puzzle</option>
                            <option>Estratégia</option>
                            <option>Simulação</option>
                            <option>Terror</option>
                            <option>Corrida</option>
                            <option>Luta</option>
                            <option>Run & Gun</option>
                            <option>Sobrevivência</option>
                            <option>Visual Novel</option>
                            <option>Outro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="inputClassificacao">Classificação Indicativa</label>
                        <select id="inputClassificacao" name="classificacao">
                            <option>Livre</option>
                            <option>10 anos</option>
                            <option>12 anos</option>
                            <option>14 anos</option>
                            <option>16 anos</option>
                            <option>18 anos</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group--radio">
                        <label>Preço</label>
                        <div class="radio-row">
                            <label class="radio-label">
                                <input type="radio" name="eh_gratis" value="1" checked>
                                <span>Grátis</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="eh_gratis" value="0">
                                <span>Pago</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group" id="precoGroup" style="display:none">
                        <label for="inputPreco">Preço</label>
                        <input type="text" id="inputPreco" name="preco" placeholder="Ex: R$ 29,99">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="inputVersao">Versão</label>
                        <input type="text" id="inputVersao" name="versao" maxlength="30" placeholder="Ex: v1.0.0">
                    </div>
                    <div class="form-group">
                        <label for="inputTamanho">Tamanho</label>
                        <input type="text" id="inputTamanho" name="tamanho" maxlength="20" placeholder="Ex: 2.5 GB">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputIdiomas">Idiomas</label>
                    <input type="text" id="inputIdiomas" name="idiomas" value="Português"
                           placeholder="Ex: Português, Inglês">
                </div>

                <div class="form-group">
                    <label for="inputDownloadUrl">Link de Download (opcional)</label>
                    <input type="url" id="inputDownloadUrl" name="download_url"
                           placeholder="https://seu-link-de-download.com">
                </div>

            </div>

            <!-- ============ STEP 2: Imagens & Vídeos ============ -->
            <div class="pub-panel" data-panel="2">

                <!-- Capa -->
                <div class="upload-section">
                    <h3 class="section-title"><i class="fa-solid fa-image"></i> Capa do Jogo</h3>
                    <p class="section-desc">Imagem principal (proporção 3:4 recomendada). Máx 8MB.</p>
                    <div class="upload-area upload-area--cover" id="uploadCapa" data-field="thumbnail">
                        <div class="upload-area-inner">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <p>Clique ou arraste uma imagem</p>
                            <span>PNG, JPG ou WEBP</span>
                        </div>
                        <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp" class="input-hidden">
                    </div>
                    <div class="upload-preview" id="previewCapa" style="display:none">
                        <img src="" alt="Preview da capa">
                        <button type="button" class="btn-remove-img" data-target="previewCapa"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                </div>

                <!-- Backdrop -->
                <div class="upload-section">
                    <h3 class="section-title"><i class="fa-solid fa-panorama"></i> Backdrop (Fundo)</h3>
                    <p class="section-desc">Imagem de fundo da página (proporção 16:9). Máx 8MB.</p>
                    <div class="upload-area upload-area--backdrop" id="uploadBackdrop" data-field="backdrop">
                        <div class="upload-area-inner">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <p>Clique ou arraste uma imagem</p>
                            <span>PNG, JPG ou WEBP</span>
                        </div>
                        <input type="file" name="backdrop" accept="image/jpeg,image/png,image/webp" class="input-hidden">
                    </div>
                    <div class="upload-preview upload-preview--wide" id="previewBackdrop" style="display:none">
                        <img src="" alt="Preview do backdrop">
                        <button type="button" class="btn-remove-img" data-target="previewBackdrop"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                </div>

                <!-- Screenshots -->
                <div class="upload-section">
                    <h3 class="section-title"><i class="fa-solid fa-images"></i> Screenshots (até 8)</h3>
                    <p class="section-desc">Arraste para reordenar.</p>
                    <div class="screenshots-grid" id="screenshotsGrid"></div>
                    <div class="upload-area upload-area--small" id="uploadScreenshot">
                        <div class="upload-area-inner">
                            <i class="fa-solid fa-plus"></i>
                            <p>Adicionar screenshot</p>
                        </div>
                        <input type="file" accept="image/jpeg,image/png,image/webp" class="input-hidden">
                    </div>
                </div>

                <!-- Vídeos -->
                <div class="upload-section">
                    <h3 class="section-title"><i class="fa-solid fa-video"></i> Vídeos</h3>
                    <p class="section-desc">Adicione trailers, gameplay ou outros vídeos.</p>

                    <div class="video-add-row">
                        <div class="form-group form-group--inline">
                            <select id="videoTipo">
                                <option value="youtube">YouTube</option>
                                <option value="link">Link (Vimeo, etc)</option>
                                <option value="arquivo">Upload de arquivo</option>
                            </select>
                        </div>
                        <div class="form-group form-group--inline form-group--grow">
                            <input type="text" id="videoUrlInput" placeholder="URL do vídeo">
                        </div>
                        <div class="form-group form-group--inline" style="max-width:200px">
                            <input type="text" id="videoTituloInput" placeholder="Título (opcional)">
                        </div>
                        <button type="button" class="btn-add-video" id="btnAddVideoLink">
                            <i class="fa-solid fa-plus"></i> Adicionar
                        </button>
                    </div>

                    <!-- Upload de vídeo arquivo -->
                    <div class="upload-area upload-area--small" id="uploadVideoFile" style="margin-top:12px; display:none">
                        <div class="upload-area-inner">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <p>Arraste ou clique para enviar (MP4, WebM, máx 50MB)</p>
                        </div>
                        <input type="file" accept="video/mp4,video/webm" class="input-hidden">
                    </div>

                    <div class="videos-list" id="videosList"></div>
                </div>

            </div>

            <!-- ============ STEP 3: Tags & Plataformas ============ -->
            <div class="pub-panel" data-panel="3">

                <div class="form-group">
                    <label>Tags</label>
                    <p class="section-desc">Pressione Enter ou vírgula para adicionar. Máx 20.</p>
                    <div class="tags-input-wrap" id="tagsInputWrap">
                        <div class="tags-chips" id="tagsChips"></div>
                        <input type="text" id="inputTag" placeholder="Ex: Indie, Roguelite, Pixel Art..."
                               maxlength="50">
                    </div>
                    <input type="hidden" name="tags" id="tagsHidden">
                    <div class="tags-suggestions">
                        <span class="tag-suggestion" data-tag="Indie">Indie</span>
                        <span class="tag-suggestion" data-tag="Roguelite">Roguelite</span>
                        <span class="tag-suggestion" data-tag="RPG">RPG</span>
                        <span class="tag-suggestion" data-tag="Aventura">Aventura</span>
                        <span class="tag-suggestion" data-tag="Pixel Art">Pixel Art</span>
                        <span class="tag-suggestion" data-tag="Plataforma">Plataforma</span>
                        <span class="tag-suggestion" data-tag="Single Player">Single Player</span>
                        <span class="tag-suggestion" data-tag="Early Access">Early Access</span>
                        <span class="tag-suggestion" data-tag="Terror">Terror</span>
                        <span class="tag-suggestion" data-tag="Puzzle">Puzzle</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Plataformas</label>
                    <div class="platform-checks">
                        <label class="check-label">
                            <input type="checkbox" name="plataformas[]" value="windows">
                            <i class="fa-brands fa-windows"></i> Windows
                        </label>
                        <label class="check-label">
                            <input type="checkbox" name="plataformas[]" value="apple">
                            <i class="fa-brands fa-apple"></i> macOS
                        </label>
                        <label class="check-label">
                            <input type="checkbox" name="plataformas[]" value="linux">
                            <i class="fa-brands fa-linux"></i> Linux
                        </label>
                        <label class="check-label">
                            <input type="checkbox" name="plataformas[]" value="android">
                            <i class="fa-brands fa-android"></i> Android
                        </label>
                    </div>
                </div>

            </div>

            <!-- ============ STEP 4: Requisitos de Sistema ============ -->
            <div class="pub-panel" data-panel="4">

                <div class="req-form-grid">
                    <?php foreach (['minimo' => 'Mínimo', 'recomendado' => 'Recomendado'] as $tipo => $label): ?>
                    <div class="req-form-col">
                        <h3 class="req-col-title"><?= $label ?></h3>

                        <div class="form-group">
                            <label>Sistema Operacional</label>
                            <input type="text" name="req_<?= $tipo ?>_os" placeholder="Ex: Windows 10 (64-bit)">
                        </div>
                        <div class="form-group">
                            <label>Processador</label>
                            <input type="text" name="req_<?= $tipo ?>_processador" placeholder="Ex: Intel i5-8400">
                        </div>
                        <div class="form-group">
                            <label>Memória RAM</label>
                            <input type="text" name="req_<?= $tipo ?>_memoria" placeholder="Ex: 8 GB RAM">
                        </div>
                        <div class="form-group">
                            <label>Placa de Vídeo</label>
                            <input type="text" name="req_<?= $tipo ?>_video" placeholder="Ex: GTX 1060 6GB">
                        </div>
                        <div class="form-group">
                            <label>Armazenamento</label>
                            <input type="text" name="req_<?= $tipo ?>_armazenamento" placeholder="Ex: 5 GB">
                        </div>
                        <div class="form-group">
                            <label>DirectX</label>
                            <input type="text" name="req_<?= $tipo ?>_directx" placeholder="Ex: Versão 12">
                        </div>

                        <div class="meters-form">
                            <div class="meter-form">
                                <label>CPU <span id="meterVal_<?= $tipo ?>_cpu">0</span>%</label>
                                <input type="range" name="req_<?= $tipo ?>_carga_cpu" min="0" max="100" value="0"
                                       class="meter-slider" data-display="meterVal_<?= $tipo ?>_cpu">
                            </div>
                            <div class="meter-form">
                                <label>GPU <span id="meterVal_<?= $tipo ?>_gpu">0</span>%</label>
                                <input type="range" name="req_<?= $tipo ?>_carga_gpu" min="0" max="100" value="0"
                                       class="meter-slider" data-display="meterVal_<?= $tipo ?>_gpu">
                            </div>
                            <div class="meter-form">
                                <label>RAM <span id="meterVal_<?= $tipo ?>_ram">0</span>%</label>
                                <input type="range" name="req_<?= $tipo ?>_carga_ram" min="0" max="100" value="0"
                                       class="meter-slider" data-display="meterVal_<?= $tipo ?>_ram">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>

            <!-- ============ STEP 5: Customização Visual ============ -->
            <div class="pub-panel" data-panel="5">
                <p class="section-desc" style="margin-bottom:20px">
                    <i class="fa-solid fa-circle-info"></i>
                    Clique em qualquer texto para editar. Clique nas imagens para trocar. Use os botões de toggle para mostrar/esconder seções.
                </p>

                <!-- Toggles de seções -->
                <div class="customize-toggles">
                    <label class="toggle-label">
                        <input type="checkbox" id="togSobre" checked> Seção "Sobre"
                    </label>
                    <label class="toggle-label">
                        <input type="checkbox" id="togDestaques" checked> Destaques
                    </label>
                    <label class="toggle-label">
                        <input type="checkbox" id="togVideos" checked> Vídeos
                    </label>
                    <label class="toggle-label">
                        <input type="checkbox" id="togRequisitos" checked> Requisitos
                    </label>
                    <label class="toggle-label">
                        <input type="checkbox" id="togRelacionados" checked> Relacionados
                    </label>
                </div>

                <!-- Preview ao vivo da página -->
                <div class="customize-preview-frame" id="customizePreviewFrame">

                    <!-- HERO -->
                    <div class="cp-hero" id="cpHero">
                        <div class="cp-hero-bg" id="cpHeroBg"></div>
                        <div class="cp-hero-scrim"></div>
                        <div class="cp-hero-content">
                            <div class="cp-hero-cover">
                                <img id="cpCapa" src="" alt="Capa">
                                <button class="cp-edit-btn cp-img-btn" data-target="cpCapa" title="Trocar capa">
                                    <i class="fa-solid fa-camera"></i>
                                </button>
                            </div>
                            <div class="cp-hero-info">
                                <div class="cp-hero-tags" id="cpTags"></div>
                                <h1 class="cp-hero-title" contenteditable="true" data-field="titulo"></h1>
                                <p class="cp-hero-dev" contenteditable="true" data-field="tagline"></p>
                                <div class="cp-hero-actions">
                                    <span class="cp-price" contenteditable="true" data-field="preco"></span>
                                    <div class="cp-platforms" id="cpPlatforms"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SOBRE -->
                    <div class="cp-section" id="cpSobre">
                        <h2 class="cp-section-title">Sobre o Jogo</h2>
                        <p class="cp-about-text" contenteditable="true" data-field="descricao"></p>
                        <div class="cp-highlights" id="cpHighlights">
                            <button type="button" class="cp-add-item" id="btnAddDestaque" title="Adicionar destaque">
                                <i class="fa-solid fa-plus"></i> Adicionar destaque
                            </button>
                        </div>
                    </div>

                    <!-- VÍDEOS -->
                    <div class="cp-section" id="cpVideosSection">
                        <h2 class="cp-section-title">Vídeos</h2>
                        <div class="cp-videos-info">
                            <i class="fa-solid fa-circle-info"></i>
                            Vídeos foram adicionados na etapa 2. Eles aparecerão aqui na página final.
                        </div>
                    </div>

                    <!-- REQUISITOS -->
                    <div class="cp-section" id="cpRequisitos">
                        <h2 class="cp-section-title">Requisitos de Sistema</h2>
                        <div class="cp-req-info">
                            <i class="fa-solid fa-circle-info"></i>
                            Requisitos foram configurados na etapa 4. Eles aparecerão aqui na página final.
                        </div>
                    </div>

                    <!-- RELACIONADOS -->
                    <div class="cp-section" id="cpRelacionados">
                        <h2 class="cp-section-title">Mais como este</h2>
                        <p class="cp-rel-info">
                            <i class="fa-solid fa-circle-info"></i>
                            Jogos relacionados são encontrados automaticamente pelo gênero do seu jogo.
                        </p>
                    </div>

                </div>
            </div>

            <!-- ============ NAV BUTTONS ============ -->
            <div class="pub-nav">
                <button type="button" class="btn-pub btn-pub--back" id="btnPrev" style="display:none">
                    <i class="fa-solid fa-arrow-left"></i> Voltar
                </button>
                <button type="button" class="btn-pub btn-pub--next" id="btnNext">
                    Próximo <i class="fa-solid fa-arrow-right"></i>
                </button>
                <button type="submit" class="btn-pub btn-pub--publish" id="btnPublish" style="display:none">
                    <i class="fa-solid fa-rocket"></i> Publicar Jogo
                </button>
            </div>

        </form>

        <!-- Loading overlay -->
        <div class="pub-loading" id="pubLoading" style="display:none">
            <div class="pub-loading-inner">
                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                <p>Publicando seu jogo...</p>
            </div>
        </div>

        <!-- Success overlay -->
        <div class="pub-success" id="pubSuccess" style="display:none">
            <div class="pub-success-inner">
                <i class="fa-solid fa-circle-check"></i>
                <h2>Jogo publicado!</h2>
                <p>Seu jogo está agora disponível na Float.</p>
                <a href="" class="btn-pub btn-pub--success" id="linkJogoPublicado">
                    Ver página do jogo <i class="fa-solid fa-arrow-right"></i>
                </a>
                <a href="perfil.php" class="btn-pub btn-pub--back" style="margin-top:10px">
                    Voltar ao perfil
                </a>
            </div>
        </div>

    </main>

    <?php require_once '../includes/footer.php'; ?>

    <script src="../assets/js/global/global.js"></script>
    <script>
        window.PUBLICAR = {
            usuarioId: <?= $usuarioId ?>,
            apiBase: '/float/api/jogos/',
            jogoId: <?= $edicaoJogoId ?: 'null' ?>
        };
    </script>
    <script src="../assets/js/pages/projetos.js"></script>

</body>
</html>
