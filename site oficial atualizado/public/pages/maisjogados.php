<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../api/config/conexao.php';

$basePath   = '../';
$activePage = 'maisjogados';

$stmtJogos = $pdo->query("SELECT id, titulo, slug, thumbnail, genero FROM jogo WHERE visibilidade = 'publico'");
$jogos = [];
foreach ($stmtJogos as $row) {
    $jogos[(int) $row['id']] = $row;
}

$stmtStats = $pdo->query("
    SELECT e.jogo_id, e.data, e.jogadores_ativos
    FROM   estatistica_jogo e
    JOIN   jogo j ON j.id = e.jogo_id
    WHERE  j.visibilidade = 'publico'
    ORDER  BY e.data ASC
");

$porDia = [];
foreach ($stmtStats as $r) {
    $porDia[$r['data']][(int) $r['jogo_id']] = (int) $r['jogadores_ativos'];
}

$datas = array_keys($porDia);
sort($datas);

$refData = !empty($datas) ? end($datas) : null;
$ult7    = array_slice($datas, -7);
$ult30   = array_slice($datas, -30);
$total30 = count($ult30);

$dados = [];
foreach ($jogos as $id => $g) {
    $hoje   = $refData !== null ? ($porDia[$refData][$id] ?? 0) : 0;
    $semana = 0;
    foreach ($ult7 as $d) {
        $semana += $porDia[$d][$id] ?? 0;
    }
    $mes = 0;
    foreach ($ult30 as $d) {
        $mes += $porDia[$d][$id] ?? 0;
    }

    $dados[$id] = [
        'jogo'   => $g,
        'hoje'   => $hoje,
        'semana' => $semana,
        'mes'    => $mes,
        'top3'   => 0,
    ];
}

foreach ($ult30 as $d) {
    $linha = $porDia[$d] ?? [];
    arsort($linha);
    $i = 0;
    foreach ($linha as $jid => $v) {
        if ($i >= 3) {
            break;
        }
        if (isset($dados[$jid])) {
            $dados[$jid]['top3']++;
        }
        $i++;
    }
}

function ordenarRanking(array $dados, string $campo, string $desempate): array
{
    $arr = array_values($dados);
    usort($arr, function ($a, $b) use ($campo, $desempate) {
        if ($b[$campo] === $a[$campo]) {
            return $b[$desempate] <=> $a[$desempate];
        }
        return $b[$campo] <=> $a[$campo];
    });
    return $arr;
}

$listaHoje   = ordenarRanking($dados, 'hoje', 'semana');
$listaSemana = ordenarRanking($dados, 'semana', 'mes');
$listaMes    = ordenarRanking($dados, 'mes', 'semana');
$listaSempre = ordenarRanking($dados, 'top3', 'mes');

function maxCampo(array $lista, string $campo): int
{
    $max = 0;
    foreach ($lista as $item) {
        if ($item[$campo] > $max) {
            $max = $item[$campo];
        }
    }
    return $max;
}

function renderRanking(array $lista, string $campo, string $unidade, int $max): void
{
    if (empty($lista)) {
        echo '<p class="mjp-vazio">Sem dados para este período.</p>';
        return;
    }

    echo '<ol class="mjp-rank">';
    foreach ($lista as $i => $item) {
        $j      = $item['jogo'];
        $val    = $item[$campo];
        $pos    = $i + 1;
        $larg   = $max > 0 ? round($val / $max * 100, 1) : 0;
        $thumb  = $j['thumbnail']
            ? '/float/public/assets/img/jogos/' . $j['id'] . '/' . htmlspecialchars($j['thumbnail'])
            : '/float/public/assets/img/pages/index/cards/card_undertale.png';

        echo '<li class="mjp-item' . ($pos <= 3 ? ' mjp-top mjp-top-' . $pos : '') . '">';
        echo '  <span class="mjp-pos">' . $pos . '</span>';
        echo '  <a class="mjp-game" href="/float/public/jogos/' . htmlspecialchars($j['slug']) . '/">';
        echo '      <img src="' . $thumb . '" alt="" loading="lazy">';
        echo '      <span class="mjp-game-info">';
        echo '          <strong>' . htmlspecialchars($j['titulo']) . '</strong>';
        echo '          <span class="mjp-genre">' . htmlspecialchars($j['genero'] ?: '—') . '</span>';
        echo '      </span>';
        echo '  </a>';
        echo '  <div class="mjp-metric">';
        echo '      <strong>' . number_format($val, 0, ',', '.') . '</strong>';
        echo '      <span>' . $unidade . '</span>';
        echo '      <div class="mjp-bar"><span style="width:' . $larg . '%"></span></div>';
        echo '  </div>';
        echo '</li>';
    }
    echo '</ol>';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Os jogos mais jogados da Float: hoje, semana, mês e sempre.">
    <title>Float | Mais Jogados</title>

    <link rel="stylesheet" href="/float/public/assets/css/global/global.css" />
    <link rel="stylesheet" href="/float/public/assets/css/pages/maisjogados.css" />
    <link rel="icon" href="/float/public/assets/img/favicon/icone.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet" />
</head>

<body>

    <?php require_once '../includes/header.php'; ?>

    <main class="mjp-page">

        <section class="mjp-page-header">
            <div class="mjp-header-content">
                <div class="mjp-header-icon">
                    <i class="fa-solid fa-fire"></i>
                </div>
                <div class="mjp-header-text">
                    <h1>Mais Jogados</h1>
                    <p>Os jogos que a comunidade mais está jogando — por dia, semana, mês e os que nunca saem de moda.</p>
                </div>
            </div>
        </section>

        <?php if (empty($jogos)): ?>
        <div class="mjp-empty">
            <i class="fa-solid fa-fire fa-3x" aria-hidden="true"></i>
            <h2>Nada por aqui ainda</h2>
            <p>Assim que houver jogadores ativos, o ranking aparecerá aqui.</p>
        </div>
        <?php else: ?>

        <div class="mjp-toolbar">
            <div class="mjp-tabs" role="tablist" aria-label="Período do ranking">
                <button type="button" class="mjp-tab active" role="tab" aria-selected="true" data-panel="hoje">
                    <i class="fa-solid fa-sun"></i> Hoje
                </button>
                <button type="button" class="mjp-tab" role="tab" aria-selected="false" data-panel="semana">
                    <i class="fa-solid fa-calendar-week"></i> Semana
                </button>
                <button type="button" class="mjp-tab" role="tab" aria-selected="false" data-panel="mes">
                    <i class="fa-solid fa-calendar-days"></i> Mês
                </button>
                <button type="button" class="mjp-tab" role="tab" aria-selected="false" data-panel="sempre">
                    <i class="fa-solid fa-infinity"></i> Sempre Jogados
                </button>
            </div>
        </div>

        <section class="mjp-panel active" id="panel-hoje" role="tabpanel">
            <div class="mjp-panel-head">
                <h2><i class="fa-solid fa-sun"></i> Mais jogados hoje</h2>
                <span>Jogadores ativos hoje<?= $refData ? ' · ' . date('d/m/Y', strtotime($refData)) : '' ?></span>
            </div>
            <?php renderRanking($listaHoje, 'hoje', 'ativos hoje', maxCampo($listaHoje, 'hoje')); ?>
        </section>

        <section class="mjp-panel" id="panel-semana" role="tabpanel" hidden>
            <div class="mjp-panel-head">
                <h2><i class="fa-solid fa-calendar-week"></i> Mais jogados na semana</h2>
                <span>Soma de jogadores ativos nos últimos 7 dias</span>
            </div>
            <?php renderRanking($listaSemana, 'semana', 'ativos na semana', maxCampo($listaSemana, 'semana')); ?>
        </section>

        <section class="mjp-panel" id="panel-mes" role="tabpanel" hidden>
            <div class="mjp-panel-head">
                <h2><i class="fa-solid fa-calendar-days"></i> Mais jogados no mês</h2>
                <span>Soma de jogadores ativos nos últimos 30 dias</span>
            </div>
            <?php renderRanking($listaMes, 'mes', 'ativos no mês', maxCampo($listaMes, 'mes')); ?>
        </section>

        <section class="mjp-panel" id="panel-sempre" role="tabpanel" hidden>
            <div class="mjp-panel-head">
                <h2><i class="fa-solid fa-infinity"></i> Sempre jogados</h2>
                <span>Jogos que mais aparecem entre os 3 mais ativos (últimos <?= $total30 ?> dias)</span>
            </div>
            <?php renderRanking($listaSempre, 'top3', 'dias no top 3', $total30 > 0 ? $total30 : 1); ?>
        </section>

        <?php endif; ?>

    </main>

    <?php require_once '../includes/footer.php'; ?>

    <script src="/float/public/assets/js/global/global.js"></script>
    <script src="/float/public/assets/js/pages/maisjogados.js"></script>

</body>

</html>
