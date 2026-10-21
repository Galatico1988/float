'use strict';

(function () {
    const DADOS = window.ESTATISTICAS || { labels: [], series: {}, jogos: [], dias: 30 };
    const nf = new Intl.NumberFormat('pt-BR');
    const nfDec = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 });

    const canvasInst = document.getElementById('chartInstalacoes');
    const canvasAtivos = document.getElementById('chartAtivos');
    if (!canvasInst || !canvasAtivos) return;

    let jogoSel = 'todos';
    let rangeDias = DADOS.dias || 30;
    let modoInst = 'acumulado';
    let chartInst = null;
    let chartAtivos = null;

    const CORES = {
        inst: '#a64aff',
        instFill: 'rgba(166, 74, 255, 0.22)',
        ativos: '#ffed4e',
        ativosFill: 'rgba(255, 237, 78, 0.14)',
    };

    function slice(arr, n) {
        return arr.slice(Math.max(0, arr.length - n));
    }

    function acumulado(arr) {
        const out = [];
        let soma = 0;
        for (let i = 0; i < arr.length; i++) {
            soma += arr[i] || 0;
            out.push(soma);
        }
        return out;
    }

    function baseOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(4, 5, 46, 0.96)',
                    borderColor: 'rgba(255, 255, 255, 0.12)',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 8,
                    titleColor: '#fff',
                    bodyColor: '#cfd3e0',
                    displayColors: false,
                    callbacks: {
                        label: (ctx) => `${ctx.dataset.label}: ${nf.format(ctx.parsed.y)}`,
                    },
                },
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.04)' },
                    ticks: { color: '#7c8399', maxTicksLimit: 9, font: { size: 11 } },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.06)' },
                    ticks: {
                        color: '#7c8399',
                        font: { size: 11 },
                        callback: (v) => nf.format(v),
                    },
                },
            },
        };
    }

    function lineDataset(label, data, cor, fill) {
        const ctx = canvasInst.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, fill);
        gradient.addColorStop(1, 'rgba(0, 0, 0, 0)');

        return {
            label,
            data,
            borderColor: cor,
            backgroundColor: gradient,
            borderWidth: 2.5,
            fill: true,
            tension: 0.35,
            pointRadius: 0,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: cor,
        };
    }

    function serieAtual() {
        return DADOS.series[jogoSel] || { instalacoes_dia: [], ativos: [] };
    }

    function renderGraficos() {
        const serie = serieAtual();
        const labels = slice(DADOS.labels, rangeDias);

        const instFull = acumulado(serie.instalacoes_dia);
        const instData = modoInst === 'acumulado'
            ? slice(instFull, rangeDias)
            : slice(serie.instalacoes_dia, rangeDias);
        const instLabel = modoInst === 'acumulado' ? 'Instalações acumuladas' : 'Instalações por dia';

        const ativosData = slice(serie.ativos, rangeDias);

        if (!chartInst) {
            chartInst = new Chart(canvasInst, {
                type: 'line',
                data: {
                    labels,
                    datasets: [lineDataset(instLabel, instData, CORES.inst, CORES.instFill)],
                },
                options: baseOptions(),
            });
        } else {
            chartInst.data.labels = labels;
            chartInst.data.datasets[0].label = instLabel;
            chartInst.data.datasets[0].data = instData;
            chartInst.update();
        }

        if (!chartAtivos) {
            chartAtivos = new Chart(canvasAtivos, {
                type: 'line',
                data: {
                    labels,
                    datasets: [lineDataset('Jogadores ativos', ativosData, CORES.ativos, CORES.ativosFill)],
                },
                options: baseOptions(),
            });
        } else {
            chartAtivos.data.labels = labels;
            chartAtivos.data.datasets[0].data = ativosData;
            chartAtivos.update();
        }
    }

    function atualizarKpis() {
        const serie = serieAtual();
        const inst = slice(serie.instalacoes_dia, rangeDias);
        const ativos = slice(serie.ativos, rangeDias);

        const total = inst.reduce((a, b) => a + b, 0);
        const hoje = ativos[ativos.length - 1] || 0;
        const pico = ativos.length ? Math.max(...ativos) : 0;
        const media = ativos.length ? ativos.reduce((a, b) => a + b, 0) / ativos.length : 0;

        const elTotal = document.getElementById('kpiTotal');
        const elHoje = document.getElementById('kpiHoje');
        const elPico = document.getElementById('kpiPico');
        const elMedia = document.getElementById('kpiMedia');

        if (elTotal) elTotal.textContent = nf.format(total);
        if (elHoje) elHoje.textContent = nf.format(hoje);
        if (elPico) elPico.textContent = nf.format(pico);
        if (elMedia) elMedia.textContent = nfDec.format(media);
    }

    function atualizarTudo() {
        renderGraficos();
        atualizarKpis();
    }

    const selectJogo = document.getElementById('jogoSelect');
    if (selectJogo) {
        selectJogo.addEventListener('change', () => {
            jogoSel = selectJogo.value;
            renderGraficos();
            atualizarKpis();
        });
    }

    document.querySelectorAll('.est-range-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.est-range-btn').forEach((b) => b.classList.remove('active'));
            btn.classList.add('active');
            rangeDias = parseInt(btn.dataset.range, 10) || 30;
            atualizarTudo();
        });
    });

    document.querySelectorAll('.est-mode-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.est-mode-btn').forEach((b) => b.classList.remove('active'));
            btn.classList.add('active');
            modoInst = btn.dataset.modo;
            renderGraficos();
        });
    });

    atualizarTudo();
})();
