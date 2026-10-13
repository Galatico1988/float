<?php
/**
 * Float - Núcleo compartilhado de RECAROUSEL / DESTAQUES.
 *
 * Fonte única do carrossel da home: usada por public/index.php (site)
 * e por api/mobile/destaques.php (app Android).
 *
 * As duas telas montam a URL da imagem a partir do caminho relativo
 * devolvido por destaque_caminho_imagem(): site -> FLOAT_PUBLIC_URL,
 * app -> url_asset().
 */
declare(strict_types=1);

/**
 * Caminho da imagem de um jogo relativo a public/assets/, com fallback
 * para a capa padrão quando o arquivo não existe.
 *
 * @param array  $jogo  Linha com id, thumbnail e backdrop.
 * @param bool   $usarBackdrop Tenta backdrop antes da thumbnail.
 * @return string Ex.: "img/jogos/3/backdrop.jpg"
 */
function destaque_caminho_imagem(array $jogo, bool $usarBackdrop = true): string
{
    $id = (int) ($jogo['id'] ?? 0);
    $arquivo = $usarBackdrop
        ? (($jogo['backdrop'] ?? '') ?: ($jogo['thumbnail'] ?? ''))
        : (($jogo['thumbnail'] ?? '') ?: ($jogo['backdrop'] ?? ''));

    if (!empty($arquivo)) {
        $relativo = str_contains($arquivo, '/')
            ? 'img/jogos/' . ltrim($arquivo, '/')
            : 'img/jogos/' . $id . '/' . $arquivo;

        $absoluto = dirname(__DIR__, 2) . '/public/assets/' . $relativo;
        if (is_file($absoluto)) {
            return $relativo;
        }
    }

    return 'img/pages/index/cards/card_undertale.png';
}

/**
 * Lista os jogos em destaque (carrossel da home).
 *
 * Mesma regra de public/index.php: jogos públicos, mais recentes primeiro,
 * com tags concatenadas por "|" para montar o "status" do slide.
 *
 * @return array Linhas de jogo + coluna "tags" (string com "|" ou '').
 */
function destaques_listar(PDO $pdo, int $limite = 0): array
{
    $sql = "
        SELECT
            j.*,
            GROUP_CONCAT(t.tag ORDER BY t.tag SEPARATOR '|') AS tags
        FROM   jogo j
        LEFT   JOIN jogo_tags t ON t.jogo_id = j.id
        WHERE  j.visibilidade = 'publico'
        GROUP  BY j.id
        ORDER  BY j.criado_em DESC, j.id DESC";

    if ($limite > 0) {
        $sql .= ' LIMIT ' . $limite;
    }

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

/**
 * Monta um item do carrossel no formato compartilhado site/app.
 *
 * Retorna:
 *  - id       : id do jogo (navegação do app)
 *  - slug     : slug do jogo (navegação do site)
 *  - titulo   : título exibido
 *  - descricao: tagline -> descrição -> texto padrão
 *  - status   : primeira tag do jogo ou "Disponível"
 *  - imagem   : caminho relativo a public/assets/
 *
 * @param array $jogo Linha vinda de destaques_listar().
 */
function destaque_montar(array $jogo): array
{
    $tags = array_values(array_filter(array_map('trim', explode('|', (string) ($jogo['tags'] ?? '')))));

    $descricao = trim((string) ($jogo['tagline'] ?? ''));
    if ($descricao === '') {
        $descricao = trim((string) ($jogo['descricao'] ?? ''));
    }
    if ($descricao === '') {
        $descricao = 'Explore este título na Float.';
    }

    return [
        'id'         => (int) $jogo['id'],
        'slug'       => (string) $jogo['slug'],
        'titulo'     => (string) $jogo['titulo'],
        'descricao'  => $descricao,
        'status'     => $tags[0] ?? 'Disponível',
        'tags'       => $tags,
        'imagem'     => destaque_caminho_imagem($jogo, true),
        'capa'       => destaque_caminho_imagem($jogo, false),
    ];
}

/**
 * Lista de itens prontos para o carrossel.
 *
 * @return array<int, array> Cada item no formato de destaque_montar().
 */
function destaques_montar(PDO $pdo, int $limite = 0): array
{
    $itens = [];
    foreach (destaques_listar($pdo, $limite) as $jogo) {
        $itens[] = destaque_montar($jogo);
    }
    return $itens;
}
