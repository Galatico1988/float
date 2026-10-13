'use strict';

(function () {
    const botoes = document.querySelectorAll('.rp-filter');
    const cards = document.querySelectorAll('.rp-card');
    const empty = document.getElementById('rpEmpty');
    const emptyText = document.getElementById('rpEmptyText');

    if (!botoes.length || !cards.length) return;

    const nomes = {};
    botoes.forEach((b) => {
        nomes[b.dataset.cat] = b.querySelector('.rp-filter-count')
            ? b.textContent.replace(/\s*\d+\s*$/, '').trim()
            : b.textContent.trim();
    });

    function filtrar(cat) {
        let visiveis = 0;

        cards.forEach((card) => {
            const cats = (card.dataset.cats || '').split(' ');
            const mostrar = cats.includes(cat);
            card.hidden = !mostrar;
            if (mostrar) visiveis++;
        });

        if (empty) {
            empty.hidden = visiveis > 0;
            if (emptyText && visiveis === 0) {
                const nome = nomes[cat] || 'nesta categoria';
                emptyText.textContent = `Nenhum jogo encontrado em "${nome}".`;
            }
        }
    }

    botoes.forEach((btn) => {
        btn.addEventListener('click', () => {
            botoes.forEach((b) => {
                b.classList.remove('active');
                b.setAttribute('aria-selected', 'false');
            });
            btn.classList.add('active');
            btn.setAttribute('aria-selected', 'true');
            filtrar(btn.dataset.cat);
        });
    });
})();
