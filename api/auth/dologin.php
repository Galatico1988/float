<?php
/**
 * Form de login do site (POST com redirect HTML).
 * Usa a mesma regra de negócio do app: api/_core/usuario.php.
 */

require_once __DIR__ . '/../_core/usuario.php';
require_once __DIR__ . '/../config/conexao.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$email = trim($_POST['email']  ?? '');
$senha = $_POST['password']    ?? '';

$erros = [];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'E-mail inválido.';
}

if (empty($senha)) {
    $erros[] = 'Informe a senha.';
}

if (!empty($erros)) {
    $query = http_build_query(['erro' => implode(' | ', $erros)]);
    header('Location: ../../public/pages/login.php?' . $query);
    exit;
}

$usuario = usuario_autenticar($pdo, $email, $senha);

if ($usuario === null) {
    $query = http_build_query(['erro' => 'E-mail ou senha incorretos.']);
    header('Location: ../../public/pages/login.php?' . $query);
    exit;
}

usuario_iniciar_sessao($usuario);

header('Location: ../../public/index.php?login=sucesso');
exit;
