<?php
/**
 * Configuração central da aplicação Float.
 *
 * Descobre em tempo de execução o prefixo de URL do projeto para que nenhum
 * arquivo precise fixar caminhos absolutos (ex.: "/float/public/...").
 * Assim o app continua funcionando se a pasta for renomeada, movida,
 * publicada em um VirtualHost ou servida em um subdiretório diferente.
 *
 * Constantes expostas:
 *   FLOAT_ROOT_URL -> "/float"         raiz do projeto na URL
 *   FLOAT_BASE_URL -> "/float/public"  document root da aplicação
 *   FLOAT_API_URL  -> "/float/api"     endpoints JSON consumidos pelo front
 *
 * Helpers:
 *   float_url($path)        URL dentro de public/
 *   float_asset($path)      URL de arquivos estáticos de public/assets
 *   float_api($path)        URL da API
 *   float_game_url($slug)   URL amigável de um jogo (/jogos/{slug}/)
 *   float_js_config()       <script> que expõe as URLs para o JavaScript
 *   float_redirect($url)    redireciona e encerra a requisição
 */

declare(strict_types=1);

if (defined('FLOAT_APP_CONFIG_LOADED')) {
    return;
}
define('FLOAT_APP_CONFIG_LOADED', true);

define('FLOAT_APP_DIR', dirname(__DIR__));
define('FLOAT_PUBLIC_DIR', FLOAT_APP_DIR . DIRECTORY_SEPARATOR . 'public');
define('FLOAT_API_DIR', FLOAT_APP_DIR . DIRECTORY_SEPARATOR . 'api');

/**
 * Normaliza um caminho de disco (Windows ou Unix) e remove a barra final.
 */
function float_fs_path(string $path): string
{
    return rtrim(str_replace('\\', '/', $path), '/');
}

/**
 * Converte um caminho físico em URL relativa ao DocumentRoot do Apache.
 * Retorna string vazia quando o caminho está fora do DocumentRoot.
 */
function float_url_from_fs(string $fsPath, string $docRoot): string
{
    $fsPath  = float_fs_path(realpath($fsPath) ?: $fsPath);
    $docRoot = float_fs_path(realpath($docRoot) ?: $docRoot);

    if ($fsPath === '' || $docRoot === '') {
        return '';
    }

    if (strcasecmp($fsPath, $docRoot) === 0) {
        return '';
    }

    $prefix = $docRoot . '/';
    if (strncasecmp($fsPath, $prefix, strlen($prefix)) === 0) {
        return '/' . substr($fsPath, strlen($prefix));
    }

    return '';
}

/**
 * Junta base e caminho garantindo uma única barra entre as partes.
 */
function float_join_url(string $base, string $path): string
{
    $base = rtrim(str_replace('\\', '/', $base), '/');
    $path = ltrim(str_replace('\\', '/', $path), '/');

    if ($path === '') {
        return $base === '' ? '/' : $base;
    }

    return $base . '/' . $path;
}

/* ------------------------------------------------------------------ *
 * Detecção automática das URLs
 * ------------------------------------------------------------------ */

$floatDocRoot = (string) ($_SERVER['DOCUMENT_ROOT'] ?? '');

$floatRootUrl = float_url_from_fs(FLOAT_APP_DIR, $floatDocRoot);
$floatBaseUrl = float_url_from_fs(FLOAT_PUBLIC_DIR, $floatDocRoot);
$floatApiUrl  = float_url_from_fs(FLOAT_API_DIR, $floatDocRoot);

// A API pode estar fora do DocumentRoot (deploy com VirtualHost apontando
// direto para a pasta public). Nesse caso deriva o prefixo a partir da base.
if ($floatApiUrl === '') {
    $floatApiUrl = float_join_url(rtrim(str_replace('\\', '/', dirname($floatBaseUrl)), '/'), 'api');
}

// Permite sobrescrever a detecção (deploy, testes) criando config/local.php.
$floatLocalConfig = FLOAT_APP_DIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'local.php';
if (is_file($floatLocalConfig)) {
    require_once $floatLocalConfig;
}

defined('FLOAT_ROOT_URL') || define('FLOAT_ROOT_URL', $floatRootUrl);
defined('FLOAT_BASE_URL') || define('FLOAT_BASE_URL', $floatBaseUrl);
defined('FLOAT_API_URL')  || define('FLOAT_API_URL',  $floatApiUrl);

/* ------------------------------------------------------------------ *
 * Helpers de URL
 * ------------------------------------------------------------------ */

/** URL de um arquivo/página dentro de public/. */
function float_url(string $path = ''): string
{
    return float_join_url(FLOAT_BASE_URL, $path);
}

/** URL de um arquivo estático dentro de public/assets. */
function float_asset(string $path = ''): string
{
    return float_join_url(FLOAT_BASE_URL . '/assets', $path);
}

/** URL de um endpoint da API. */
function float_api(string $path = ''): string
{
    return float_join_url(FLOAT_API_URL, $path);
}

/** URL amigável da página de um jogo: /jogos/{slug}/ */
function float_game_url(string $slug): string
{
    return float_url('jogos/' . rawurlencode((string) $slug) . '/');
}

/** URL da capa/backdrop de um jogo a partir do id e do arquivo no banco. */
function float_game_image(int|string $jogoId, ?string $arquivo): string
{
    if (!$arquivo) {
        return float_asset('img/pages/index/cards/card_undertale.png');
    }

    return float_asset('img/jogos/' . (int) $jogoId . '/' . rawurlencode(basename($arquivo)));
}

/** Expondo as URLs para o JavaScript do front-end. */
function float_js_config(): string
{
    $payload = json_encode(
        [
            'base' => FLOAT_BASE_URL,
            'api'  => FLOAT_API_URL,
            'root' => FLOAT_ROOT_URL,
        ],
        JSON_UNESCAPED_SLASHES
    );

    return '<script>window.FLOAT = Object.assign({}, window.FLOAT, ' . $payload . ');</script>';
}

/** Redireciona e encerra a requisição. */
function float_redirect(string $url, int $status = 302): void
{
    if (!headers_sent()) {
        header('Location: ' . $url, true, $status);
    }
    exit;
}
