<?php
/**
 * GET /api/mobile/noticias.php
 * Params (opcionais): categoria, limite (padrão 0 = todas), offset
 *
 * Resp: { ok, noticias: [ { id, categoria, titulo, texto, resumo,
 *                           data, data_iso, imagem } ] }
 *
 * Mesmo conteúdo editorial do site (public/pages/noticias.php),
 * vindo da tabela noticia.
 */
declare(strict_types=1);

require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/noticias.php';
require_once __DIR__ . '/../config/conexao.php';

api_inicio();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_erro(405, 'Método não permitido.');
}

try {
    $lista = noticias_publicas($pdo, [
        'categoria' => parametro('categoria', ''),
        'limite'    => min(100, max(0, (int) parametro('limite', '0'))),
        'offset'    => max(0, (int) parametro('offset', '0')),
    ]);
} catch (PDOException $e) {
    error_log('Erro notícias mobile: ' . $e->getMessage());
    json_erro(500, 'Erro ao carregar as notícias.');
}

json_ok(['noticias' => $lista]);
