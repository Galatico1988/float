<?php
/**
 * Float - Núcleo compartilhado de utilitários da API.
 *
 * Usado pelo site (páginas PHP), pelos endpoints JSON (api/*) e pelo
 * app Android (api/mobile/*). Todo endpoint deve fazer require_once
 * deste arquivo (direta ou via api/mobile/comum.php).
 */
declare(strict_types=1);

/** Inicia a sessão e define o cabeçalho JSON padrão. */
function api_inicio(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    header('Content-Type: application/json; charset=utf-8');
}

/**
 * Monta a URL absoluta da raiz do site (ex.: http://localhost/float).
 * Derivada do caminho do script atual (.../api/...).
 */
function raiz_url(): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '/float/api/_core/helpers.php';
    $pos    = strpos($script, '/api/');

    if ($pos !== false) {
        $raiz = substr($script, 0, $pos);
    } elseif (($posPub = strpos($script, '/public/')) !== false) {
        /* Página do site (public/...): a raiz é a pasta antes de /public. */
        $raiz = substr($script, 0, $posPub);
    } else {
        $raiz = '';
    }

    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $https  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    return ($https ? 'https' : 'http') . '://' . $host . $raiz;
}

/** Monta URL absoluta para arquivos em public/assets/... */
function url_asset(string $caminhoRelativo): ?string
{
    $caminhoRelativo = trim($caminhoRelativo);
    if ($caminhoRelativo === '') {
        return null;
    }
    if (preg_match('#^https?://#i', $caminhoRelativo)) {
        return $caminhoRelativo;
    }
    return raiz_url() . '/public/assets/' . ltrim($caminhoRelativo, '/');
}

/** URL pública da capa/backdrop de um jogo. */
function url_imagem_jogo(int $jogoId, ?string $arquivo): ?string
{
    if (empty($arquivo)) {
        return null;
    }
    if (str_contains($arquivo, '/')) {
        return url_asset('img/jogos/' . ltrim($arquivo, '/'));
    }
    return url_asset('img/jogos/' . $jogoId . '/' . $arquivo);
}

/** URL pública do avatar de um usuário. */
function url_avatar(?string $avatarPath): string
{
    if (empty($avatarPath)) {
        return url_asset('img/user/avatars/avatar.png');
    }
    return url_asset('img/user/avatars/' . basename($avatarPath));
}

/** Resposta de sucesso padrão. */
function json_ok(array $dados = []): void
{
    echo json_encode(['ok' => true] + $dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/** Resposta de erro padrão. */
function json_erro(int $codigo, string $mensagem): void
{
    http_response_code($codigo);
    echo json_encode(['ok' => false, 'mensagem' => $mensagem], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/** ID do usuário logado na sessão atual (ou null). */
function usuario_logado(): ?int
{
    return !empty($_SESSION['id']) ? (int) $_SESSION['id'] : null;
}

/** Exige usuário logado; responde 401 e encerra caso contrário. */
function exige_login(): int
{
    $id = usuario_logado();
    if ($id === null) {
        json_erro(401, 'Faça login para continuar.');
    }
    return $id;
}

/** Lê um parâmetro GET/POST como string trimada. */
function parametro(string $chave, ?string $padrao = null): ?string
{
    $valor = $_POST[$chave] ?? $_GET[$chave] ?? $padrao;
    if ($valor === null) {
        return null;
    }
    return trim((string) $valor);
}
