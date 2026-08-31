<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Float - Acesse sua conta e explore jogos indies.">
    <title>Float | Login</title>

    <link rel="stylesheet" href="../assets/css/global/global.css">

    <link rel="stylesheet" href="../assets/css/pages/login.css">

    <link rel="icon" href="./assets/img/favicon/icone.ico" type="image/x-icon" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>

<body class="login-page">

    <main class="auth-full-wrapper">
        <div class="auth-container">

            <div class="auth-visual-side">
                <img src="../assets/img/pages/login/login.png" alt="Float Visuals">
                <div class="visual-overlay">
                    <div class="visual-content">
                        <i class="fas fa-gamepad auth-logo-icon" aria-hidden="true"></i>
                        <h2>Conecte-se ao <br><span>Universo Indie</span></h2>
                    </div>
                </div>
            </div>

            <div class="auth-form-side">
                <a href="../index.php" class="back-home">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Voltar à Página Inicial
                </a>

                <div class="auth-form-box">
                    <header class="auth-header">
                        <img src="../assets/img/global/logo.png" alt="Float Logo" class="auth-logo-central">
                        <h1>Bem-vindo de volta</h1>
                        <p>Entre com suas credenciais para acessar sua biblioteca.</p>
                    </header>

                    <form action="../../api/auth/dologin.php" method="POST" class="auth-form" novalidate>
                        <div class="input-group">
                            <label for="email">E-mail</label>
                            <div class="input-field">
                                <i class="fa-regular fa-envelope" aria-hidden="true"></i>
                                <input type="email" id="email" name="email" placeholder="nome@email.com" required
                                    autocomplete="email">
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="password">Senha</label>
                            <div class="input-field input-password-wrapper">
                                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                                <input type="password" id="password" name="password" placeholder="Sua senha" required
                                    autocomplete="current-password">
                                <button type="button" class="btn-toggle-password" aria-label="Mostrar/ocultar senha"
                                    data-target="password">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <a href="#" class="forgot-link">Esqueceu a senha?</a>
                        </div>

                        <button type="submit" class="btn-auth-primary">Entrar no Float</button>

                        <div class="auth-divider"><span>ou</span></div>

                        <p class="auth-switch">
                            Não tem uma conta? <a href="cadastro.php">Crie uma agora</a>
                        </p>
                    </form>

                    <footer class="auth-footer">
                        <div class="social-auth">
                            <a href="#" aria-label="Entrar com Google"><i class="fa-brands fa-google"
                                    aria-hidden="true"></i></a>
                            <a href="#" aria-label="Entrar com Apple"><i class="fa-brands fa-apple"
                                    aria-hidden="true"></i></a>
                            <a href="#" aria-label="Entrar com Discord"><i class="fa-brands fa-discord"
                                    aria-hidden="true"></i></a>
                        </div>
                    </footer>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="../assets/js/pages/login.js"></script>

</body>

</html>