<?php
/**
 * GET /api/mobile/meusjogos.php
 * Exige login. Retorna TODOS os jogos do usuário (públicos e privados).
 * Mesmo formato da listagem de jogos.php + campo visibilidade.
 */
declare(strict_types=1);

require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/jogos.php';
require_once __DIR__ . '/../config/conexao.php';

api_inicio();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_erro(405, 'Método não permitido.');
}

$usuarioId = exige_login();

try {
    $jogos = jogos_listar($pdo, [
        'usuarioId'      => $usuarioId,
        'apenasPublicos' => false,
    ]);
} catch (PDOException $e) {
    error_log('Erro meus jogos mobile: ' . $e->getMessage());
    json_erro(500, 'Erro ao carregar seus jogos.');
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
        'visibilidade'     => $j['visibilidade'],
        'media_nota'       => round((float) $j['media_nota'], 1),
        'total_avaliacoes' => (int) $j['total_avaliacoes'],
    ];
}

json_ok(['jogos' => $lista]);
