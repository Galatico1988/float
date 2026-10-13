<?php
/**
 * GET /api/mobile/jogos.php
 * Params (opcionais): busca, genero, limite (padrão 30), offset (padrão 0)
 * Resp: { ok, jogos: [ { id, titulo, slug, tagline, genero, preco, eh_gratis,
 *                        thumbnail_url, backdrop_url, dev_nome, media_nota, total_avaliacoes } ] }
 * Lista pública de jogos (não exige login). Mesma regra de catálogo do site.
 */
declare(strict_types=1);

require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/jogos.php';
require_once __DIR__ . '/../config/conexao.php';

api_inicio();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_erro(405, 'Método não permitido.');
}

try {
    $jogos = jogos_listar($pdo, [
        'busca'  => parametro('busca', ''),
        'genero' => parametro('genero', ''),
        'limite' => min(100, max(1, (int) parametro('limite', '30'))),
        'offset' => max(0, (int) parametro('offset', '0')),
    ]);
} catch (PDOException $e) {
    error_log('Erro listagem mobile: ' . $e->getMessage());
    json_erro(500, 'Erro ao carregar os jogos.');
}

$lista = [];
foreach ($jogos as $j) {
    $lista[] = [
        'id'               => (int) $j['id'],
        'titulo'           => $j['titulo'],
        'slug'             => $j['slug'],
        'tagline'          => $j['tagline'] ?? '',
        'genero'           => $j['genero'] ?? '',
        'preco'            => $j['preco'] ?? 'Gratis',
        'eh_gratis'        => (int) ($j['eh_gratis'] ?? 1),
        'thumbnail_url'    => url_imagem_jogo((int) $j['id'], $j['thumbnail'] ?? null),
        'backdrop_url'     => url_imagem_jogo((int) $j['id'], $j['backdrop'] ?? null),
        'dev_nome'         => $j['dev_nome'],
        'media_nota'       => round((float) $j['media_nota'], 1),
        'total_avaliacoes' => (int) $j['total_avaliacoes'],
    ];
}

json_ok(['jogos' => $lista]);
