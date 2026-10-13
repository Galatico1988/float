'use strict';

(function () {
    const tabs = document.querySelectorAll('.mjp-tab');
    const panels = document.querySelectorAll('.mjp-panel');

    if (!tabs.length) return;

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const alvo = tab.dataset.panel;

            tabs.forEach((t) => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');

            panels.forEach((p) => {
                const ativo = p.id === 'panel-' + alvo;
                p.classList.toggle('active', ativo);
                p.toggleAttribute('hidden', !ativo);
            });
        });
    });
})();
