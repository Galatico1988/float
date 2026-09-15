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

});
