<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'mensagem' => 'Método não permitido.']);
    exit;
}

$sessaoId = !empty($_SESSION['id']) ? (int) $_SESSION['id'] : null;
$filtro   = $_GET['filtro'] ?? 'todos'; // todos | seguindo | alta

try {
    switch ($filtro) {
        case 'seguindo':
            if (!$sessaoId) {
                echo json_encode(['ok' => true, 'posts' => [], 'mensagem' => 'Faça login para ver posts de quem você segue.']);
                exit;
            }
            $stmt = $pdo->prepare('
                SELECT
                    p.id, p.legenda, p.imagem_url, p.data_criacao,
                    u.id AS usuario_id, u.nome, u.avatar_path,
                    (SELECT COUNT(*) FROM curtida_post WHERE post_id = p.id) AS total_curtidas,
                    (SELECT COUNT(*) FROM curtida_post WHERE post_id = p.id AND usuario_id = :uid_curtiu) AS curtiu
                FROM  comunidade_posts p
                JOIN  usuario u ON p.usuario_id = u.id
                WHERE p.usuario_id IN (
                    SELECT seguido_id FROM seguidor WHERE seguidor_id = :uid_seg
                )
                ORDER BY p.data_criacao DESC
                LIMIT 30
            ');
            $stmt->execute([':uid_curtiu' => $sessaoId, ':uid_seg' => $sessaoId]);
            break;

        case 'alta':
            $stmt = $pdo->prepare('
                SELECT
                    p.id, p.legenda, p.imagem_url, p.data_criacao,
                    u.id AS usuario_id, u.nome, u.avatar_path,
                    COUNT(c.id) AS total_curtidas,
                    (SELECT COUNT(*) FROM curtida_post WHERE post_id = p.id AND usuario_id = :uid_curtiu) AS curtiu
                FROM  comunidade_posts p
                JOIN  usuario u ON p.usuario_id = u.id
                LEFT JOIN curtida_post c ON p.id = c.post_id
                GROUP BY p.id, p.legenda, p.imagem_url, p.data_criacao, u.id, u.nome, u.avatar_path
                ORDER BY total_curtidas DESC, p.data_criacao DESC
                LIMIT 20
            ');
            $stmt->execute([':uid_curtiu' => $sessaoId ?? 0]);
            break;

        default: // todos
            $stmt = $pdo->prepare('
                SELECT
                    p.id, p.legenda, p.imagem_url, p.data_criacao,
                    u.id AS usuario_id, u.nome, u.avatar_path,
                    (SELECT COUNT(*) FROM curtida_post WHERE post_id = p.id) AS total_curtidas,
                    (SELECT COUNT(*) FROM curtida_post WHERE post_id = p.id AND usuario_id = :uid_curtiu) AS curtiu
                FROM  comunidade_posts p
                JOIN  usuario u ON p.usuario_id = u.id
                ORDER BY p.data_criacao DESC
                LIMIT 30
            ');
            $stmt->execute([':uid_curtiu' => $sessaoId ?? 0]);
            break;
    }

    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Normaliza tipos
    foreach ($posts as &$post) {
        $post['total_curtidas'] = (int) $post['total_curtidas'];
        $post['curtiu']         = (int) $post['curtiu'] > 0;
    }
    unset($post);

    echo json_encode(['ok' => true, 'posts' => $posts]);
} catch (PDOException $e) {
    error_log('Erro feed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensagem' => 'Erro ao carregar o feed.']);
}