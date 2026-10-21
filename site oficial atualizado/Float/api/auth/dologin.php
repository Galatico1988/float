<?php

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


$stmt = $pdo->prepare('SELECT id, nome, senha, avatar_path FROM usuario WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($senha, $usuario['senha'])) {
    $query = http_build_query(['erro' => 'E-mail ou senha incorretos.']);
    header('Location: ../../public/pages/login.php?' . $query);
    exit;
}


session_regenerate_id(true);

$_SESSION['id']          = (int) $usuario['id'];
$_SESSION['nome']        = $usuario['nome'];
$_SESSION['avatar_path'] = $usuario['avatar_path'];


header('Location: ../../public/index.php?login=sucesso');
exit;