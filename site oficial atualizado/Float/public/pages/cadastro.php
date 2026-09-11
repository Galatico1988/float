<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Float - Crie sua conta e publique seus jogos indie gratuitamente.">
    <title>Float | Cadastro</title>

    <link rel="stylesheet" href="../assets/css/global/global.css">

    <link rel="stylesheet" href="../assets/css/pages/cadastro.css">

    <link rel="icon" href="../assets/img/favicon/icone.ico" type="image/x-icon" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>

<body class="login-page">

    <main class="auth-full-wrapper">
        <div class="auth-container register-mode">

            <div class="auth-form-side">
                <a href="login.php" class="back-home">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Voltar ao Login
                </a>

                <div class="auth-form-box">

                    <header class="auth-header">
                        <img src="../assets/img/global/logo.png" class="auth-logo-central" alt="Float Logo">
                        <h1>Crie sua conta</h1>
                        <p>Junte-se à nossa comunidade indie.</p>
                    </header>

                    <form action="../../api/auth/docadastro.php" method="POST" class="auth-form" novalidate>
                        <div class="input-group">
                            <label for="nome">Nome de Usuário</label>
                            <div class="input-field">
                                <i class="fa-solid fa-user" aria-hidden="true"></i>
                                <input type="text" id="nome" name="nome" placeholder="Como quer ser chamado?"
                                    minlength="3" maxlength="50" required autocomplete="username">
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="email">E-mail</label>
                            <div class="input-field">
                                <i class="fa-regular fa-envelope" aria-hidden="true"></i>
                                <input type="email" id="email" name="email" placeholder="seu@email.com" required
                                    autocomplete="email">
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="password">Senha</label>
                            <div class="input-field input-password-wrapper">
                                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                                <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres"
                                    minlength="8" required autocomplete="new-password">
                                <button type="button" class="btn-toggle-password" aria-label="Mostrar/ocultar senha"
                                    data-target="password">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn-auth-primary">Criar Conta no Float</button>

                        <div class="auth-divider"><span>ou registre-se com</span></div>

                        <div class="social-auth">
                            <a href="#" aria-label="Registrar com Google"><i class="fa-brands fa-google"
                                    aria-hidden="true"></i></a>
                            <a href="#" aria-label="Registrar com Apple"><i class="fa-brands fa-apple"
                                    aria-hidden="true"></i></a>
                            <a href="#" aria-label="Registrar com Discord"><i class="fa-brands fa-discord"
                                    aria-hidden="true"></i></a>
                        </div>

                        <p class="auth-switch">
                            Já tem uma conta? <a href="login.php">Fazer Login</a>
                        </p>
                    </form>
                </div>
            </div>

            <div class="auth-visual-side">
                <img src="../assets/img/pages/cadastro/cadastro.png" alt="Float Visuals">
                <div class="visual-overlay">
                    <div class="visual-content">
                        <i class="fas fa-rocket auth-logo-icon" aria-hidden="true"></i>
                        <h2>Comece a sua <br><span>Jornada Indie</span></h2>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="../assets/js/pages/cadastro.js"></script>

</body>

</html>