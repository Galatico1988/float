<?php
/**
 * GET /api/mobile/jogo.php?id=123  ou  ?slug=meu-jogo
 * Detalhe público de um jogo (mesma regra de public/jogo.php).
 *
 * Resp: { ok, jogo: {...}, tags: [], screenshots: [urls], videos: [], requisitos: {...} }
 */
declare(strict_types=1);

require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/jogos.php';
require_once __DIR__ . '/../config/conexao.php';

api_inicio();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_erro(405, 'Método não permitido.');
}

$idJogo = (int) parametro('id', '0');
$slug   = parametro('slug', '');

if ($idJogo <= 0 && $slug === '') {
    json_erro(422, 'Informe o id ou o slug do jogo.');
}

try {
    $j = jogo_buscar($pdo, $idJogo > 0 ? $idJogo : null, $slug !== '' ? $slug : null);

    if (!$j) {
        json_erro(404, 'Jogo não encontrado.');
    }

    $jogoId = (int) $j['id'];
    $extras = jogo_complementos($pdo, $jogoId, (int) $j['usuario_id']);

    $screenshots = [];
    foreach ($extras['screenshots'] as $caminho) {
        $screenshots[] = url_imagem_jogo($jogoId, $caminho);
    }

    json_ok([
        'jogo' => [
            'id'            => $jogoId,
            'titulo'        => $j['titulo'],
            'slug'          => $j['slug'],
            'tagline'       => $j['tagline'] ?? '',
            'descricao'     => $j['descricao'] ?? '',
            'genero'        => $j['genero'] ?? '',
            'preco'         => $j['preco'] ?? 'Gratis',
            'eh_gratis'     => (int) ($j['eh_gratis'] ?? 1),
            'versao'        => $j['versao'] ?? '',
            'tamanho'       => $j['tamanho'] ?? '',
            'idiomas'       => $j['idiomas'] ?? 'Português',
            'classificacao' => $j['classificacao'] ?? 'Livre',
            'plataformas'   => json_decode($j['plataformas'] ?? '[]', true) ?: [],
            'download_url'  => $j['download_url'] ?? '',
            'thumbnail_url' => url_imagem_jogo($jogoId, $j['thumbnail'] ?? null),
            'backdrop_url'  => url_imagem_jogo($jogoId, $j['backdrop'] ?? null),
            'destaques'     => json_decode($j['destaques'] ?? '[]', true) ?: [],
            'dev_nome'      => $extras['dev_nome'],
            'atualizado'    => jogo_data_extensa($j['atualizado_em'] ?? null),
            'media_nota'    => $extras['media_nota'],
            'total_avaliacoes' => $extras['total_avaliacoes'],
        ],
        'tags'        => $extras['tags'],
        'screenshots' => array_values(array_filter($screenshots)),
        'videos'      => $extras['videos'],
        'requisitos'  => $extras['requisitos'],
    ]);
} catch (PDOException $e) {
    error_log('Erro detalhe mobile: ' . $e->getMessage());
    json_erro(500, 'Erro ao carregar o jogo.');
}
