<?php
/**
 * Float - Núcleo compartilhado de regra de negócio de USUÁRIOS.
 *
 * Fonte única de autenticação/cadastro/perfil: usada pelos endpoints
 * JSON (api/mobile/*) e pelos formulários do site (api/auth/*).
 */
declare(strict_types=1);

/**
 * Valida os campos do formulário de cadastro.
 * @return array Lista de erros (vazia = válido).
 */
function usuario_validar_cadastro(string $nome, string $email, string $senha): array
{
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

    return $erros;
}

/**
 * Busca o usuário pelo e-mail.
 * @return array|null Linha (id, nome, email, senha, avatar_path) ou null.
 */
function usuario_por_email(PDO $pdo, string $email): ?array
{
    $stmt = $pdo->prepare('SELECT id, nome, email, senha, avatar_path FROM usuario WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    return $usuario ?: null;
}

/**
 * Autentica e-mail/senha. Retorna a linha do usuário ou null.
 */
function usuario_autenticar(PDO $pdo, string $email, string $senha): ?array
{
    $usuario = usuario_por_email($pdo, $email);
    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
        return null;
    }
    return $usuario;
}

/**
 * Cria a conta. Retorna o novo id.
 * @throws RuntimeException se o e-mail já existir.
 */
function usuario_criar(PDO $pdo, string $nome, string $email, string $senha): int
{
    if (usuario_por_email($pdo, $email)) {
        throw new RuntimeException('E-mail já cadastrado.');
    }

    $hash  = password_hash($senha, PASSWORD_BCRYPT);
    $stmt  = $pdo->prepare('INSERT INTO usuario (nome, email, senha) VALUES (?, ?, ?)');
    $stmt->execute([$nome, $email, $hash]);

    return (int) $pdo->lastInsertId();
}

/**
 * Inicia a sessão PHP para o usuário (login e pós-cadastro).
 * Grava também as chaves antigas (usuario_id/usuario_nome) por compatibilidade.
 */
function usuario_iniciar_sessao(array $usuario): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    session_regenerate_id(true);

    $_SESSION['id']           = (int) $usuario['id'];
    $_SESSION['nome']         = $usuario['nome'];
    $_SESSION['avatar_path']  = $usuario['avatar_path'] ?? null;
    $_SESSION['usuario_id']   = (int) $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
}

/**
 * Dados básicos do usuário para respostas JSON.
 */
function usuario_publico(array $usuario, ?string $avatarUrl = null): array
{
    return [
        'id'         => (int) $usuario['id'],
        'nome'       => $usuario['nome'],
        'email'      => $usuario['email'] ?? '',
        'avatar_url' => $avatarUrl ?? url_avatar($usuario['avatar_path'] ?? null),
    ];
}

/**
 * Perfil completo (id, nome, email, bio, avatar_url, banner_url,
 * seguidores, seguindo, total_jogos de jogos públicos, desde).
 * @return array|null
 */
function usuario_perfil(PDO $pdo, int $usuarioId): ?array
{
    $stmt = $pdo->prepare('
        SELECT id, nome, email, bio, avatar_path, banner_path, criado_em
        FROM   usuario
        WHERE  id = ?
        LIMIT  1
    ');
    $stmt->execute([$usuarioId]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        return null;
    }

    $stmtSeg = $pdo->prepare('
        SELECT
            (SELECT COUNT(*) FROM seguidor WHERE seguido_id  = ?) AS seguidores,
            (SELECT COUNT(*) FROM seguidor WHERE seguidor_id = ?) AS seguindo
    ');
    $stmtSeg->execute([$usuarioId, $usuarioId]);
    $contagens = $stmtSeg->fetch();

    $stmtJogos = $pdo->prepare("
        SELECT COUNT(*) FROM jogo WHERE usuario_id = ? AND visibilidade = 'publico'
    ");
    $stmtJogos->execute([$usuarioId]);
    $totalJogos = (int) $stmtJogos->fetchColumn();

    return [
        'id'          => (int) $usuario['id'],
        'nome'        => $usuario['nome'],
        'email'       => $usuario['email'],
        'bio'         => $usuario['bio'] ?? '',
        'avatar_url'  => url_avatar($usuario['avatar_path']),
        'banner_url'  => !empty($usuario['banner_path'])
                            ? url_asset('img/user/banners/' . basename($usuario['banner_path']))
                            : null,
        'seguidores'  => (int) $contagens['seguidores'],
        'seguindo'    => (int) $contagens['seguindo'],
        'total_jogos' => $totalJogos,
        'desde'       => date('d/m/Y', strtotime($usuario['criado_em'])),
    ];
}
