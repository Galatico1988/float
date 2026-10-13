<?php
/**
 * Form de cadastro do site (POST com redirect HTML).
 * Usa a mesma regra de negócio do app: api/_core/usuario.php.
 */

require_once __DIR__ . '/../_core/usuario.php';
require_once __DIR__ . '/../config/conexao.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$nome  = trim($_POST['nome']  ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['password']   ?? '';

$erros = usuario_validar_cadastro($nome, $email, $senha);

if (!empty($erros)) {
    $query = http_build_query(['erro' => implode(' | ', $erros)]);
    header('Location: ../../public/pages/cadastro.php?' . $query);
    exit;
}

try {
    $novoId = usuario_criar($pdo, $nome, $email, $senha);
} catch (RuntimeException $e) {
    $query = http_build_query(['erro' => 'Este e-mail já está cadastrado.']);
    header('Location: ../../public/pages/cadastro.php?' . $query);
    exit;
}

/* Mesmo comportamento do cadastro do app: já entra logado. */
usuario_iniciar_sessao([
    'id'    => $novoId,
    'nome'  => $nome,
    'email' => $email,
]);

header('Location: ../../public/pages/login.php?cadastro=sucesso');
exit;
