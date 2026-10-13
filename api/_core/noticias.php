<?php
/**
 * Float - Núcleo compartilhado de NOTÍCIAS.
 *
 * Fonte única do conteúdo editorial: usada por public/index.php,
 * public/pages/noticias.php (site) e por api/mobile/noticias.php (app).
 */
declare(strict_types=1);

/* url_asset()/raiz_url() vivem em helpers.php; as páginas do site não o
 * incluem por conta própria, então o núcleo garante a dependência. */
require_once __DIR__ . '/helpers.php';

/**
 * Lista notícias ativas.
 *
 * @param array $filtros {
 *     @type string $categoria Categoria exata opcional.
 *     @type int    $limite    0 = sem limite (padrão).
 *     @type int    $offset    Deslocamento.
 * }
 * @return array Linhas da tabela noticia (imagem ainda relativa).
 */
function noticias_listar(PDO $pdo, array $filtros = []): array
{
    $categoria = trim((string) ($filtros['categoria'] ?? ''));
    $limite    = max(0, (int) ($filtros['limite'] ?? 0));
    $offset    = max(0, (int) ($filtros['offset'] ?? 0));

    $where  = ['n.ativo = 1'];
    $params = [];

    if ($categoria !== '') {
        $where[]             = 'n.categoria = :categoria';
        $params[':categoria'] = $categoria;
    }

    $sql = 'SELECT n.* FROM noticia n
            WHERE  ' . implode(' AND ', $where) . '
            ORDER  BY n.data_publicacao DESC, n.id DESC';

    if ($limite > 0) {
        $sql .= ' LIMIT ' . $limite . ' OFFSET ' . $offset;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * URL pública da imagem de uma notícia.
 * A coluna "imagem" guarda o caminho relativo a public/assets/.
 */
function noticia_url_imagem(?string $caminhoRelativo): string
{
    $caminhoRelativo = trim((string) $caminhoRelativo);
    if ($caminhoRelativo === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $caminhoRelativo)) {
        return $caminhoRelativo;
    }
    return url_asset($caminhoRelativo);
}

/**
 * Data por extenso curto: "8 maio 2026".
 * Mesmo formato usado pelas páginas do site e pelo app.
 */
function noticia_data_curta(?string $data): string
{
    if (empty($data)) {
        return '';
    }

    /* Mesmo formato do conteúdo original do site:
     * "8 maio 2026", "20 abr 2026", "15 abr 2026". */
    $meses = [
        'jan', 'fev', 'mar', 'abr', 'maio', 'jun',
        'jul', 'ago', 'set', 'out', 'nov', 'dez',
    ];

    $dt = DateTime::createFromFormat('Y-m-d', substr($data, 0, 10));
    if (!$dt) {
        return (string) $data;
    }

    return (int) $dt->format('j') . ' ' . $meses[(int) $dt->format('n') - 1] . ' ' . $dt->format('Y');
}

/**
 * Normaliza uma notícia para o contrato JSON compartilhado site/app.
 *
 * Retorna: id, categoria, titulo, texto, resumo, data, imagem (URL absoluta).
 */
function noticia_publica(array $noticia): array
{
    return [
        'id'        => (int) $noticia['id'],
        'categoria' => (string) $noticia['categoria'],
        'titulo'    => (string) $noticia['titulo'],
        'texto'     => (string) $noticia['texto'],
        'resumo'    => (string) ($noticia['resumo'] ?? ''),
        'data'      => noticia_data_curta($noticia['data_publicacao'] ?? null),
        'data_iso'  => (string) ($noticia['data_publicacao'] ?? ''),
        'imagem'    => noticia_url_imagem($noticia['imagem'] ?? ''),
    ];
}

/**
 * Lista de notícias já no formato público (URLs absolutas).
 *
 * @return array<int, array>
 */
function noticias_publicas(PDO $pdo, array $filtros = []): array
{
    $lista = [];
    foreach (noticias_listar($pdo, $filtros) as $noticia) {
        $lista[] = noticia_publica($noticia);
    }
    return $lista;
}
