<?php
/**
 * GET /api/mobile/destaques.php
 * Params (opcionais): limite (padrão 0 = todos)
 *
 * Resp: { ok, destaques: [ { id, slug, titulo, descricao, status,
 *                            imagem, capa } ] }
 *
 * Carrossel da home. Mesma fonte e mesma regra de public/index.php:
 * jogos públicos mais recentes, vindo do banco (tabela jogo + jogo_tags).
 * O app usa "id" para abrir a página do jogo (JogoActivity).
 */
declare(strict_types=1);

require_once __DIR__ . '/../_core/helpers.php';
require_once __DIR__ . '/../_core/destaques.php';
require_once __DIR__ . '/../config/conexao.php';

api_inicio();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_erro(405, 'Método não permitido.');
}

$limite = max(0, (int) parametro('limite', '0'));

try {
    $itens = destaques_montar($pdo, $limite);
} catch (PDOException $e) {
    error_log('Erro destaques mobile: ' . $e->getMessage());
    json_erro(500, 'Erro ao carregar os destaques.');
}

$lista = [];
foreach ($itens as $item) {
    $lista[] = [
        'id'         => $item['id'],
        'slug'       => $item['slug'],
        'titulo'     => $item['titulo'],
        'descricao'  => $item['descricao'],
        'status'     => $item['status'],
        'imagem'     => url_asset($item['imagem']),
        'capa'       => url_asset($item['capa']),
    ];
}

json_ok(['destaques' => $lista]);
