<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensagem' => 'Método não permitido.']);
    exit;
}

if (empty($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'mensagem' => 'Faça login para publicar.']);
    exit;
}

$usuarioId = (int) $_SESSION['id'];

/* =========================================================
   Ler e sanitizar dados do formulário
   ========================================================= */
$titulo    = mb_substr(trim($_POST['titulo'] ?? ''), 0, 100);
$tagline   = mb_substr(trim($_POST['tagline'] ?? ''), 0, 255);
$descricao = trim($_POST['descricao'] ?? '');
$genero    = mb_substr(trim($_POST['genero'] ?? ''), 0, 50);
$preco     = mb_substr(trim($_POST['preco'] ?? 'Gratis'), 0, 20);
$ehGratis  = (int) ($_POST['eh_gratis'] ?? 1);
$versao    = mb_substr(trim($_POST['versao'] ?? ''), 0, 30);
$tamanho   = mb_substr(trim($_POST['tamanho'] ?? ''), 0, 20);
$idiomas   = mb_substr(trim($_POST['idiomas'] ?? 'Portugues'), 0, 255);
$classificacao = mb_substr(trim($_POST['classificacao'] ?? 'Livre'), 0, 10);
$downloadUrl   = trim($_POST['download_url'] ?? '');
$visibilidade  = in_array($_POST['visibilidade'] ?? '', ['publico', 'privado']) ? $_POST['visibilidade'] : 'publico';

/* Tags (JSON array ou separadas por vírgula) */
$tagsRaw = $_POST['tags'] ?? '';
if (is_string($tagsRaw)) {
    $tags = array_filter(array_map('trim', explode(',', $tagsRaw)));
} elseif (is_array($tagsRaw)) {
    $tags = array_map('trim', $tagsRaw);
} else {
    $tags = [];
}
$tags = array_slice($tags, 0, 20); /* máx 20 tags */

/* Plataformas (JSON array) */
$plataformas = json_decode($_POST['plataformas'] ?? '[]', true);
if (!is_array($plataformas)) {
    $plataformas = [];
}
$plataformas = array_values(array_intersect($plataformas, ['windows', 'apple', 'linux', 'android']));

/* Destaques (JSON array de {icone, texto}) */
$destaques = json_decode($_POST['destaques'] ?? '[]', true);
if (!is_array($destaques)) {
    $destaques = [];
}
$destaques = array_slice($destaques, 0, 10);

/* Vídeos links (JSON array de {tipo, url, titulo}) */
$videosLinks = json_decode($_POST['videos_links'] ?? '[]', true);
if (!is_array($videosLinks)) {
    $videosLinks = [];
}

/* Requisitos */
$reqMin = json_decode($_POST['requisitos_minimo'] ?? '{}', true) ?? [];
$reqRec = json_decode($_POST['requisitos_recomendado'] ?? '{}', true) ?? [];

/* Custom layout */
$customLayout = $_POST['custom_layout'] ?? null;

/* =========================================================
   Validações
   ========================================================= */
$erros = [];

if (mb_strlen($titulo) < 2) {
    $erros[] = 'O título deve ter pelo menos 2 caracteres.';
}
if (mb_strlen($titulo) > 100) {
    $erros[] = 'O título deve ter no máximo 100 caracteres.';
}
if (empty($descricao)) {
    $erros[] = 'A descrição é obrigatória.';
}

if (!empty($erros)) {
    echo json_encode(['ok' => false, 'mensagem' => implode(' | ', $erros)]);
    exit;
}

/* =========================================================
   Gerar slug único a partir do título
   ========================================================= */
function gerarSlug(string $titulo): string {
    $slug = strtolower($titulo);
    $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

$slug = gerarSlug($titulo);

/* Verificar unicidade do slug */
$stmtSlug = $pdo->prepare('SELECT id FROM jogo WHERE slug = ? LIMIT 1');
$stmtSlug->execute([$slug]);
if ($stmtSlug->fetch()) {
    $slug .= '-' . bin2hex(random_bytes(3));
}

/* =========================================================
   Upload de capa e backdrop (se enviados)
   ========================================================= */
$pastaJogo = __DIR__ . '/../../public/assets/img/jogos/temp';

function uploadImagem(array $file, string $pastaDestino, string $prefixo): ?string {
    if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $maxSize = 8 * 1024 * 1024; /* 8MB */
    if ($file['size'] > $maxSize) {
        return null;
    }

    $mimePermitidos = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeReal = $finfo->file($file['tmp_name']);

    if (!in_array($mimeReal, $mimePermitidos, true)) {
        return null;
    }

    $extMap = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];
    $ext = $extMap[$mimeReal];

    if (!is_dir($pastaDestino) && !mkdir($pastaDestino, 0755, true)) {
        return null;
    }

    $nomeArquivo = "{$prefixo}.{$ext}";
    $caminhoFinal = $pastaDestino . '/' . $nomeArquivo;

    if (move_uploaded_file($file['tmp_name'], $caminhoFinal)) {
        return $nomeArquivo;
    }

    return null;
}

/* =========================================================
   Inserir jogo no banco
   ========================================================= */
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare('
        INSERT INTO jogo
            (usuario_id, titulo, slug, tagline, descricao, genero,
             preco, eh_gratis, versao, tamanho, idiomas, classificacao,
             plataformas, download_url, destaques, custom_layout, visibilidade)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([
        $usuarioId,
        $titulo,
        $slug,
        $tagline !== '' ? $tagline : null,
        $descricao,
        $genero !== '' ? $genero : null,
        $preco,
        $ehGratis,
        $versao !== '' ? $versao : null,
        $tamanho !== '' ? $tamanho : null,
        $idiomas,
        $classificacao,
        !empty($plataformas) ? json_encode($plataformas) : null,
        $downloadUrl !== '' ? $downloadUrl : null,
        !empty($destaques) ? json_encode($destaques) : null,
        $customLayout,
        $visibilidade,
    ]);

    $jogoId = (int) $pdo->lastInsertId();

    /* =========================================================
       Mover arquivos temporários para pasta final do jogo
       ========================================================= */
    $pastaFinal = __DIR__ . '/../../public/assets/img/jogos/' . $jogoId;
    if (!is_dir($pastaFinal)) {
        mkdir($pastaFinal, 0755, true);
    }

    /* Mover capa e backdrop se existirem */
    $thumbnail = null;
    $backdrop  = null;

    if (!empty($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $tempPasta = __DIR__ . '/../../public/assets/img/jogos/temp';
        $thumbFiles = glob($tempPasta . '/thumbnail.*');
        if (!empty($thumbFiles)) {
            $ext = pathinfo($thumbFiles[0], PATHINFO_EXTENSION);
            $thumbnail = "thumbnail.{$ext}";
            rename($thumbFiles[0], $pastaFinal . '/' . $thumbnail);
        } else {
            $thumbnail = uploadImagem($_FILES['thumbnail'], $pastaFinal, 'thumbnail');
        }
    }

    if (!empty($_FILES['backdrop']) && $_FILES['backdrop']['error'] === UPLOAD_ERR_OK) {
        $backdropFiles = glob((__DIR__ . '/../../public/assets/img/jogos/temp') . '/backdrop.*');
        if (!empty($backdropFiles)) {
            $ext = pathinfo($backdropFiles[0], PATHINFO_EXTENSION);
            $backdrop = "backdrop.{$ext}";
            rename($backdropFiles[0], $pastaFinal . '/' . $backdrop);
        } else {
            $backdrop = uploadImagem($_FILES['backdrop'], $pastaFinal, 'backdrop');
        }
    }

    /* Atualizar caminhos de capa e backdrop */
    if ($thumbnail || $backdrop) {
        $updates = [];
        $params  = [];
        if ($thumbnail) {
            $updates[] = 'thumbnail = ?';
            $params[]  = $thumbnail;
        }
        if ($backdrop) {
            $updates[] = 'backdrop = ?';
            $params[]  = $backdrop;
        }
        $params[] = $jogoId;
        $pdo->prepare('UPDATE jogo SET ' . implode(', ', $updates) . ' WHERE id = ?')->execute($params);
    }

    /* =========================================================
       Inserir tags
       ========================================================= */
    if (!empty($tags)) {
        $stmtTag = $pdo->prepare('INSERT INTO jogo_tags (jogo_id, tag) VALUES (?, ?)');
        foreach ($tags as $tag) {
            $stmtTag->execute([$jogoId, $tag]);
        }
    }

    /* =========================================================
       Inserir vídeos links (YouTube, Vimeo, etc.)
       ========================================================= */
    if (!empty($videosLinks)) {
        $stmtVideo = $pdo->prepare('INSERT INTO jogo_videos (jogo_id, tipo, url, titulo, ordem) VALUES (?, ?, ?, ?, ?)');
        foreach ($videosLinks as $i => $vid) {
            $tipo = 'link';
            if (preg_match('/(youtube\.com|youtu\.be)/', $vid['url'] ?? '')) {
                $tipo = 'youtube';
            }
            $stmtVideo->execute([
                $jogoId,
                $tipo,
                $vid['url'] ?? '',
                mb_substr($vid['titulo'] ?? '', 0, 100),
                $i,
            ]);
        }
    }

    /* =========================================================
       Inserir requisitos de sistema
       ========================================================= */
    $stmtReq = $pdo->prepare('
        INSERT INTO jogo_requisitos
            (jogo_id, tipo, os, processador, memoria, video, armazenamento, directx, carga_cpu, carga_gpu, carga_ram)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    $camposReq = ['os', 'processador', 'memoria', 'video', 'armazenamento', 'directx'];
    $camposCarga = ['carga_cpu', 'carga_gpu', 'carga_ram'];

    foreach (['minimo' => $reqMin, 'recomendado' => $reqRec] as $tipo => $req) {
        $params = [$jogoId, $tipo];
        foreach ($camposReq as $campo) {
            $params[] = mb_substr($req[$campo] ?? '', 0, 100);
        }
        foreach ($camposCarga as $campo) {
            $params[] = min(100, max(0, (int) ($req[$campo] ?? 0)));
        }
        $stmtReq->execute($params);
    }

    /* Limpar pasta temp */
    $tempFiles = glob((__DIR__ . '/../../public/assets/img/jogos/temp') . '/*');
    foreach ($tempFiles as $f) {
        if (is_file($f)) {
            @unlink($f);
        }
    }

    $pdo->commit();

    echo json_encode([
        'ok'       => true,
        'mensagem' => 'Jogo publicado com sucesso!',
        'jogo_id'  => $jogoId,
        'slug'     => $slug,
        'url'      => '/jogos/' . $slug . '/',
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('Erro ao publicar jogo: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensagem' => 'Erro ao publicar o jogo. Tente novamente.']);
}
