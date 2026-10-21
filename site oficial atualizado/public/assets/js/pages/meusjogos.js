document.addEventListener('DOMContentLoaded', () => {

    /* ======== TABS ======== */
    const tabs = document.querySelectorAll('.mj-tab');
    const contents = document.querySelectorAll('.mj-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;

            // Remover active de todas as tabs
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            // Mostrar conteúdo correspondente
            contents.forEach(c => c.classList.remove('active'));
            document.getElementById(`tab-${target}`).classList.add('active');
        });
    });

    // Abre automaticamente a aba informada na URL (?tab=salvos)
    const tabParam = new URLSearchParams(window.location.search).get('tab');
    if (tabParam) {
        const initialTab = document.querySelector(`.mj-tab[data-tab="${tabParam}"]`);
        if (initialTab) initialTab.click();
    }

    /* ======== REMOVER JOGO SALVO ======== */
    const savedGrid = document.getElementById('savedGrid');

    if (savedGrid) {
        savedGrid.addEventListener('click', async (e) => {
            const btn = e.target.closest('.mj-remove-saved');
            if (!btn) return;

            e.preventDefault();
            const jogoId = btn.dataset.jogoId;
            btn.disabled = true;

            try {
                const resp = await fetch('/float/api/user/salvar_jogo.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'jogo_id=' + encodeURIComponent(jogoId)
                });

                if (resp.status === 401) {
                    window.location.href = '/float/public/pages/login.php';
                    return;
                }

                const data = await resp.json();
                if (!data.ok) throw new Error(data.mensagem || 'Erro ao remover.');

                const card = btn.closest('.mj-game-card-wrap');
                if (card) card.remove();

                if (savedGrid.querySelectorAll('.mj-game-card-wrap').length === 0) {
                    const tpl = document.getElementById('savedEmptyTemplate');
                    if (tpl) savedGrid.replaceWith(tpl.content.cloneNode(true));
                }
            } catch (err) {
                alert('Não foi possível remover o jogo. Tente novamente.');
                btn.disabled = false;
            }
        });
    }

});
