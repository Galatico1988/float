<?php

require_once __DIR__ . '/../config/conexao.php';

session_start();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$nome  = trim($_POST['nome']  ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['password']   ?? '';


$erros = [];

if (mb_strlen($nome) < 3 || mb_strlen($nome) > 50) {
    $erros[] = 'O nome de usuário deve ter entre 3 e 50 caracteres.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'E-mail inválido.';
}

if (mb_strlen($senha) < 8) {
    $erros[] = 'A senha deve ter no mínimo 8 caracteres.';
}

if (!empty($erros)) {
    $query = http_build_query(['erro' => implode(' | ', $erros)]);
    header('Location: ../../public/pages/cadastro.php?' . $query);
    exit;
}


$stmt = $pdo->prepare('SELECT id FROM usuario WHERE email = ? LIMIT 1');
$stmt->execute([$email]);

if ($stmt->fetch()) {
    $query = http_build_query(['erro' => 'Este e-mail já está cadastrado.']);
    header('Location: ../../public/pages/cadastro.php?' . $query);
    exit;
}


$hash = password_hash($senha, PASSWORD_BCRYPT);
$stmt = $pdo->prepare('INSERT INTO usuario (nome, email, senha) VALUES (?, ?, ?)');
$stmt->execute([$nome, $email, $hash]);

$_SESSION['usuario_id'] = (int) $pdo->lastInsertId();
$_SESSION['usuario_nome'] = $nome;

header('Location: ../../public/pages/login.php?cadastro=sucesso');
exit;
