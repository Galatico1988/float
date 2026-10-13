<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require_once '../../api/config/conexao.php';

$basePath   = '../';
$activePage = 'estatisticas';

$sessaoId = (int) $_SESSION['id'];
$alvoId   = isset($_GET['id']) ? (int) $_GET['id'] : $sessaoId;

$stmtUsuario = $pdo->prepare('SELECT id, nome FROM usuario WHERE id = ? LIMIT 1');
$stmtUsuario->execute([$alvoId]);
$usuario = $stmtUsuario->fetch();

if (!$usuario) {
    http_response_code(404);
    echo '<p>Usuário não encontrado.</p>';
    exit;
}

$ehDono = ($sessaoId === $alvoId);

$stmtJogos = $pdo->prepare('
    SELECT j.id, j.titulo, j.slug, j.thumbnail, j.genero,
           COALESCE(SUM(e.instalacoes), 0)      AS total_inst,
           COALESCE(MAX(e.jogadores_ativos), 0) AS pico_ativos,
           COALESCE(ROUND(AVG(e.jogadores_ativos), 1), 0) AS media_ativos
    FROM   jogo j
    LEFT JOIN estatistica_jogo e ON e.jogo_id = j.id
    WHERE  j.usuario_id   = ?
      AND  j.visibilidade = \'publico\'
    GROUP BY j.id
    ORDER BY total_inst DESC, j.titulo ASC
');
$stmtJogos->execute([$alvoId]);
$jogos = $stmtJogos->fetchAll();

$dias   = 30;
$labels = [];
$mapa   = [];
$hoje   = new DateTime('today');

for ($i = $dias - 1; $i >= 0; $i--) {
    $d = (clone $hoje)->modify("-$i day");
    $mapa[$d->format('Y-m-d')] = count($labels);
    $labels[] = $d->format('d/m');
}

$series = [
    'todos' => [
        'instalacoes_dia' => array_fill(0, $dias, 0),
        'ativos'          => array_fill(0, $dias, 0),
    ],
];

foreach ($jogos as $j) {
    $series[$j['id']] = [
        'instalacoes_dia' => array_fill(0, $dias, 0),
        'ativos'          => array_fill(0, $dias, 0),
    ];
}

$stmtSerie = $pdo->prepare('
    SELECT e.jogo_id, e.data, e.instalacoes, e.jogadores_ativos
    FROM   estatistica_jogo e
    JOIN   jogo j ON j.id = e.jogo_id
    WHERE  j.usuario_id = ?
      AND  e.data >= DATE_SUB(CURDATE(), INTERVAL ' . ($dias - 1) . ' DAY)
    ORDER  BY e.data ASC
');
$stmtSerie->execute([$alvoId]);

foreach ($stmtSerie as $linha) {
    $chave = $linha['data'];
    if (!isset($mapa[$chave])) {
        continue;
    }

    $idx  = $mapa[$chave];
    $jid  = (int) $linha['jogo_id'];
    $inst = (int) $linha['instalacoes'];
    $act  = (int) $linha['jogadores_ativos'];

    if (isset($series[$jid])) {
        $series[$jid]['instalacoes_dia'][$idx] += $inst;
        $series[$jid]['ativos'][$idx]          += $act;
    }

    $series['todos']['instalacoes_dia'][$idx] += $inst;
    $series['todos']['ativos'][$idx]          += $act;
}

$totalInst   = array_sum($series['todos']['instalacoes_dia']);
$ativosHoje  = $series['todos']['ativos'][$dias - 1] ?? 0;
$picoAtivos  = max($series['todos']['ativos']);
$mediaAtivos = round(array_sum($series['todos']['ativos']) / $dias, 1);

$jogosJson = [];
foreach ($jogos as $j) {
    $jogosJson[] = ['id' => (int) $j['id'], 'titulo' => $j['titulo']];
}

$payload = [
    'labels' => $labels,
    'series' => $series,
    'jogos'  => $jogosJson,
    'dias'   => $dias,
];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Estatísticas dos projetos de <?= htmlspecialchars($usuario['nome']) ?> na Float.">
    <title>Float | Estatísticas</title>

    <link rel="stylesheet" href="/float/public/assets/css/global/global.css" />
    <link rel="stylesheet" href="/float/public/assets/css/pages/estatisticas.css" />
    <link rel="icon" href="/float/public/assets/img/favicon/icone.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet" />
</head>

<body>

    <?php require_once '../includes/header.php'; ?>

    <main class="est-page">

        <section class="est-page-header">
            <div class="est-header-content">
                <div class="est-header-icon">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="est-header-text">
                    <h1>Estatísticas</h1>
                    <p>
                        <?= $ehDono
                            ? 'Acompanhe instalações e jogadores ativos dos seus projetos.'
                            : 'Desempenho dos projetos de ' . htmlspecialchars($usuario['nome']) . '.' ?>
                    </p>
                </div>
                <a href="perfil.php?id=<?= $alvoId ?>" class="est-back-link">
                    <i class="fa-solid fa-arrow-left"></i> Voltar ao perfil
                </a>
            </div>
        </section>

        <?php if (empty($jogos)): ?>
        <div class="est-empty">
            <i class="fa-solid fa-chart-bar fa-3x" aria-hidden="true"></i>
            <h2>Sem dados por enquanto</h2>
            <p>Publique um jogo para começar a acompanhar suas estatísticas.</p>
            <?php if ($ehDono): ?>
            <a href="projetos.php?acao=novo" class="est-btn-primary">
                <i class="fa-solid fa-plus"></i> Publicar jogo
            </a>
            <?php endif; ?>
        </div>
        <?php else: ?>

        <div class="est-toolbar">
            <div class="est-filter">
                <label for="jogoSelect"><i class="fa-solid fa-gamepad"></i> Jogo</label>
                <select id="jogoSelect">
                    <option value="todos">Todos os jogos</option>
                    <?php foreach ($jogos as $j): ?>
                    <option value="<?= (int) $j['id'] ?>"><?= htmlspecialchars($j['titulo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="est-range" role="group" aria-label="Período">
                <button type="button" class="est-range-btn" data-range="7">7 dias</button>
                <button type="button" class="est-range-btn" data-range="14">14 dias</button>
                <button type="button" class="est-range-btn active" data-range="30">30 dias</button>
            </div>
        </div>

        <section class="est-chart-card">
            <div class="est-chart-head">
                <div>
                    <h2><i class="fa-solid fa-download"></i> Instalações</h2>
                    <p>Quantas pessoas instalaram <?= count($jogos) > 1 ? 'seus jogos' : 'seu jogo' ?> ao longo do tempo.</p>
                </div>
                <div class="est-mode" role="group" aria-label="Modo do gráfico de instalações">
                    <button type="button" class="est-mode-btn active" data-modo="acumulado">Acumulado</button>
                    <button type="button" class="est-mode-btn" data-modo="dia">Por dia</button>
                </div>
            </div>
            <div class="est-chart-wrap">
                <canvas id="chartInstalacoes" aria-label="Gráfico de instalações" role="img"></canvas>
            </div>
        </section>

        <section class="est-chart-card">
            <div class="est-chart-head">
                <div>
                    <h2><i class="fa-solid fa-gamepad"></i> Jogadores ativos</h2>
                    <p>Pessoas jogando ativamente a cada dia.</p>
                </div>
            </div>
            <div class="est-chart-wrap">
                <canvas id="chartAtivos" aria-label="Gráfico de jogadores ativos" role="img"></canvas>
            </div>
        </section>

        <section class="est-kpis">
            <div class="est-kpi">
                <i class="fa-solid fa-download est-kpi-icon icon-purple"></i>
                <div>
                    <span class="est-kpi-label">Total de instalações</span>
                    <strong class="est-kpi-value" id="kpiTotal"><?= number_format($totalInst, 0, ',', '.') ?></strong>
                </div>
            </div>
            <div class="est-kpi">
                <i class="fa-solid fa-circle-play est-kpi-icon icon-green"></i>
                <div>
                    <span class="est-kpi-label">Ativos hoje</span>
                    <strong class="est-kpi-value" id="kpiHoje"><?= number_format($ativosHoje, 0, ',', '.') ?></strong>
                </div>
            </div>
            <div class="est-kpi">
                <i class="fa-solid fa-arrow-trend-up est-kpi-icon icon-yellow"></i>
                <div>
                    <span class="est-kpi-label">Pico de jogadores</span>
                    <strong class="est-kpi-value" id="kpiPico"><?= number_format($picoAtivos, 0, ',', '.') ?></strong>
                </div>
            </div>
            <div class="est-kpi">
                <i class="fa-solid fa-chart-simple est-kpi-icon icon-blue"></i>
                <div>
                    <span class="est-kpi-label">Média diária de ativos</span>
                    <strong class="est-kpi-value" id="kpiMedia"><?= number_format($mediaAtivos, 1, ',', '.') ?></strong>
                </div>
            </div>
        </section>

        <section class="est-ranking">
            <h2><i class="fa-solid fa-ranking-star"></i> Desempenho por jogo</h2>
            <div class="est-table-wrap">
                <table class="est-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jogo</th>
                            <th>Gênero</th>
                            <th class="num">Instalações</th>
                            <th class="num">Pico de ativos</th>
                            <th class="num">Média de ativos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jogos as $i => $j):
                            $thumbSrc = $j['thumbnail']
                                ? '/float/public/assets/img/jogos/' . $j['id'] . '/' . htmlspecialchars($j['thumbnail'])
                                : '/float/public/assets/img/pages/index/cards/card_undertale.png';
                            $jogoSlug = $j['slug'] ?? $j['id'];
                        ?>
                        <tr>
                            <td class="est-rank"><?= $i + 1 ?></td>
                            <td>
                                <a href="/float/public/jogos/<?= htmlspecialchars($jogoSlug) ?>/" class="est-game-cell">
                                    <img src="<?= $thumbSrc ?>" alt="" loading="lazy">
                                    <span><?= htmlspecialchars($j['titulo']) ?></span>
                                </a>
                            </td>
                            <td class="est-genre"><?= htmlspecialchars($j['genero'] ?: '—') ?></td>
                            <td class="num"><?= number_format((int) $j['total_inst'], 0, ',', '.') ?></td>
                            <td class="num"><?= number_format((int) $j['pico_ativos'], 0, ',', '.') ?></td>
                            <td class="num"><?= number_format((float) $j['media_ativos'], 1, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <?php endif; ?>

    </main>

    <?php require_once '../includes/footer.php'; ?>

    <script>
        window.ESTATISTICAS = <?= json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="/float/public/assets/js/global/global.js"></script>
    <script src="/float/public/assets/js/pages/estatisticas.js"></script>

</body>

</html>
