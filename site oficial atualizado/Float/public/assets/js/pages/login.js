document.addEventListener('DOMContentLoaded', () => {
    iniciarToggleSenha();
    exibirToast();
});

function exibirToast() {
    const params = new URLSearchParams(window.location.search);

    const erro = params.get('erro');
    const cadastro = params.get('cadastro');
    const login = params.get('login');

    if (cadastro === 'sucesso') {
        Toastify({
            text: 'Conta criada com sucesso!',
            duration: 3000,
            gravity: 'top',
            position: 'right',
            stopOnFocus: true,
            style: {
                background: '#22c55e'
            }
        }).showToast();
    }

    if (erro) {
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
    }

    if (login === 'sucesso') {
        Toastify({
            text: 'Login realizado com sucesso!',
            duration: 3000,
            gravity: 'top',
            position: 'right',
            stopOnFocus: true,
            style: {
                background: '#22c55e'
            }
        }).showToast();
    }

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