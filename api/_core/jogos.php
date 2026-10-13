<?php
/**
 * Float - Núcleo compartilhado de regra de negócio de JOGOS.
 *
 * Fonte única das consultas de jogos: usada pelas páginas do site
 * (public/index.php, public/jogo.php) e pelos endpoints JSON
 * (api/mobile/jogos.php, api/mobile/jogo.php, api/mobile/meusjogos.php).
 */
declare(strict_types=1);

/**
 * Lista de jogos para catálogo.
 *
 * @param array $filtros {
 *     @type string   $busca        Termo opcional (título/tagline/gênero).
 *     @type string   $genero       Gênero exato opcional.
 *     @type int      $limite       0 = sem limite (padrão).
 *     @type int      $offset       Deslocamento para paginação.
 *     @type int|null $usuarioId    Restringe a um dono (null = qualquer um).
 *     @type bool     $apenasPublicos Padrão true. Se false, inclui privados
 *                                    (necessário junto com $usuarioId).
 *     @type bool     $comTags      Anexa tags concatenadas (sep. '|').
 * }
 * @return array Linhas com: jogo.* + dev_nome, media_nota, total_avaliacoes
 *               (+ tags, quando $comTags).
 */
function jogos_listar(PDO $pdo, array $filtros = []): array
{
    $busca        = trim((string) ($filtros['busca'] ?? ''));
    $genero       = trim((string) ($filtros['genero'] ?? ''));
    $limite       = max(0, (int) ($filtros['limite'] ?? 0));
    $offset       = max(0, (int) ($filtros['offset'] ?? 0));
    $usuarioId    = isset($filtros['usuarioId']) ? (int) $filtros['usuarioId'] : null;
    $apenasPublicos = (bool) ($filtros['apenasPublicos'] ?? true);
    $comTags      = !empty($filtros['comTags']);

    $where  = [];
    $params = [];

    if ($apenasPublicos) {
        $where[] = "j.visibilidade = 'publico'";
    }
    if ($usuarioId !== null) {
        $where[]           = 'j.usuario_id = :usuario_id';
        $params[':usuario_id'] = $usuarioId;
    }
    if ($busca !== '') {
        $where[]           = '(j.titulo LIKE :busca OR j.tagline LIKE :busca OR j.genero LIKE :busca)';
        $params[':busca']  = '%' . $busca . '%';
    }
    if ($genero !== '') {
        $where[]             = 'j.genero = :genero';
        $params[':genero']   = $genero;
    }

    $sql = '
        SELECT
            j.*, 
            u.nome AS dev_nome,
            COALESCE(AVG(a.nota), 0) AS media_nota,
            COUNT(a.id)              AS total_avaliacoes' .
            ($comTags ? ", GROUP_CONCAT(t.tag ORDER BY t.tag SEPARATOR '|') AS tags" : '') . '
        FROM   jogo j
        JOIN   usuario u ON u.id = j.usuario_id
        LEFT JOIN avaliacao a ON a.jogo_id = j.id' .
        ($comTags ? ' LEFT JOIN jogo_tags t ON t.jogo_id = j.id' : '') . '
        WHERE  ' . ($where ? implode(' AND ', $where) : '1 = 1') . '
        GROUP BY j.id
        ORDER BY j.criado_em DESC, j.id DESC';

    if ($limite > 0) {
        $sql .= ' LIMIT ' . $limite . ' OFFSET ' . $offset;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Busca um jogo pelo id ou slug.
 *
 * @param int|null    $id
 * @param string|null $slug
 * @param bool        $apenasPublico padrão true (regra do catálogo).
 * @return array|null Linha do jogo ou null se não encontrado.
 */
function jogo_buscar(PDO $pdo, ?int $id, ?string $slug, bool $apenasPublico = true): ?array
{
    if ($id !== null && $id > 0) {
        $sql = 'SELECT * FROM jogo WHERE id = ?' . ($apenasPublico ? " AND visibilidade = 'publico'" : '') . ' LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
    } elseif ($slug !== null && $slug !== '') {
        $sql = 'SELECT * FROM jogo WHERE slug = ?' . ($apenasPublico ? " AND visibilidade = 'publico'" : '') . ' LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$slug]);
    } else {
        return null;
    }

    $jogo = $stmt->fetch();
    return $jogo ?: null;
}

/**
 * Dados complementares de um jogo: tags, screenshots, vídeos, requisitos,
 * nome do dev e média de avaliações. Mesmo contrato para site e app.
 */
function jogo_complementos(PDO $pdo, int $jogoId, int $usuarioId = 0): array
{
    $stmtTags = $pdo->prepare('SELECT tag FROM jogo_tags WHERE jogo_id = ?');
    $stmtTags->execute([$jogoId]);
    $tags = $stmtTags->fetchAll(PDO::FETCH_COLUMN);

    $stmtShots = $pdo->prepare('SELECT caminho FROM jogo_screenshots WHERE jogo_id = ? ORDER BY ordem ASC');
    $stmtShots->execute([$jogoId]);
    $screenshots = $stmtShots->fetchAll(PDO::FETCH_COLUMN);

    $stmtVid = $pdo->prepare('SELECT tipo, url, titulo, ordem FROM jogo_videos WHERE jogo_id = ? ORDER BY ordem ASC');
    $stmtVid->execute([$jogoId]);
    $videos = $stmtVid->fetchAll();

    $stmtReq = $pdo->prepare('SELECT * FROM jogo_requisitos WHERE jogo_id = ?');
    $stmtReq->execute([$jogoId]);
    $reqMin  = null;
    $reqRec  = null;
    while ($req = $stmtReq->fetch()) {
        if ($req['tipo'] === 'minimo')      $reqMin = $req;
        if ($req['tipo'] === 'recomendado') $reqRec = $req;
    }

    $devNome = 'Desconhecido';
    if ($usuarioId > 0) {
        $stmtDev = $pdo->prepare('SELECT nome FROM usuario WHERE id = ? LIMIT 1');
        $stmtDev->execute([$usuarioId]);
        $devNome = $stmtDev->fetchColumn() ?: 'Desconhecido';
    }

    $stmtAv = $pdo->prepare('SELECT COALESCE(AVG(nota),0), COUNT(*) FROM avaliacao WHERE jogo_id = ?');
    $stmtAv->execute([$jogoId]);
    [$mediaNota, $totalAval] = $stmtAv->fetch(PDO::FETCH_NUM);

    return [
        'tags'        => $tags,
        'screenshots' => array_values(array_filter($screenshots)),
        'videos'      => $videos,
        'requisitos'  => [
            'minimo'      => $reqMin,
            'recomendado' => $reqRec,
        ],
        'dev_nome'         => $devNome,
        'media_nota'       => round((float) $mediaNota, 1),
        'total_avaliacoes' => (int) $totalAval,
    ];
}

/**
 * Jogos relacionados (mesmo gênero, exclui o próprio, aleatório).
 * Mesma regra de public/jogo.php.
 *
 * @return array Lista no formato de jogos_listar() (sem tags).
 */
function jogos_relacionados(PDO $pdo, int $jogoId, string $genero, int $limite = 4): array
{
    $genero = trim($genero);
    if ($genero === '') {
        return [];
    }

    $stmt = $pdo->prepare("
        SELECT
            j.*,
            u.nome AS dev_nome,
            COALESCE(AVG(a.nota), 0) AS media_nota,
            COUNT(a.id)              AS total_avaliacoes
        FROM   jogo j
        JOIN   usuario u ON u.id = j.usuario_id
        LEFT JOIN avaliacao a ON a.jogo_id = j.id
        WHERE  j.genero = :genero
          AND  j.id     <> :id
          AND  j.visibilidade = 'publico'
        GROUP BY j.id
        ORDER BY RAND()
        LIMIT " . max(1, $limite)
    );
    $stmt->execute([':genero' => $genero, ':id' => $jogoId]);
    return $stmt->fetchAll();
}

/**
 * "12 de janeiro de 2026" a partir de um datetime (ou null).
 */
function jogo_data_extensa(?string $data): ?string
{
    if (empty($data)) {
        return null;
    }
    $meses = ['janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro'];
    $dt = new DateTime($data);
    return $dt->format('d') . ' de ' . $meses[(int) $dt->format('n') - 1] . ' de ' . $dt->format('Y');
}
