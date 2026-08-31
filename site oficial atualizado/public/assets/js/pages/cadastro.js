document.addEventListener('DOMContentLoaded', () => {
    iniciarToggleSenha();
    exibirToastCadastro();
});

function exibirToastCadastro() {
    const params = new URLSearchParams(window.location.search);
    const erro = params.get('erro');

    if (!erro) return;

    Toastify({
        text: decodeURIComponent(erro),
        duration: 3500,
        gravity: 'top',
        position: 'right',
        stopOnFocus: true,
        style: {
            background: '#ff4747'
        }
    }).showToast();

    window.history.replaceState({}, '', window.location.pathname);
}

function iniciarToggleSenha() {
    document.querySelectorAll('.btn-toggle-password').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.dataset.target;
            const input = document.getElementById(targetId);
            const icon = btn.querySelector('i');

            if (!input) return;

            const mostrandoSenha = input.type === 'text';

            input.type = mostrandoSenha ? 'password' : 'text';

            icon.className = mostrandoSenha
                ? 'fa-regular fa-eye'
                : 'fa-regular fa-eye-slash';
        });
    });
}