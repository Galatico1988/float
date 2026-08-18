<?php
if (!isset($_SESSION)) {
    session_start();
}
$basePath   = './';
$activePage = 'game-page'; // Identificador para a página atual

// ==========================================
// CONFIGURAÇÕES DO TEMPLATE DO JOGO (FRONTEND)
// ==========================================
$game_title       = "Título do Seu Jogo";
$game_tagline     = "Sua frase de efeito ou descrição curta aqui.";
$game_price       = "R$ 0,00"; // Use "Grátis" ou o valor desejado
$game_type        = "Jogo Base"; // Tipo: Indie, RPG, Roguelite, etc.
$developer_name   = "Nome do Dev";

$game_description = "Escreva aqui toda a descrição detalhada do seu jogo. Conte a história, descreva as mecânicas de gameplay, adicione os controles ou qualquer outra informação essencial para atrair o jogador.";

// Mídias do Carrossel (A primeira será exibida no topo ao carregar a página)
$media_carousel = [
    ["type" => "video", "url" => "https://youtube.com"],
    ["type" => "image", "url" => "https://placeholder.com"],
    ["type" => "image", "url" => "https://placeholder.com"],
    ["type" => "image", "url" => "https://placeholder.com"],
];

$game_details = [
    "Status"     => "Lançado",
    "Plataformas"=> "Windows, Mac",
    "Gênero"     => "Aventura, Indie",
    "Linguagens" => "Português (BR)",
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?php echo htmlspecialchars($game_title); ?> no Float - Explore, jogue e publique seus projetos.">
    <title>Float | <?php echo htmlspecialchars($game_title); ?></title>

    <!-- Arquivos de Estilo Globais do Projeto Float -->
    <link rel="stylesheet" href="./assets/css/global/global.css" />
    
    <!-- Ícones e Fontes da Identidade Float -->
    <link rel="icon" href="./assets/img/favicon/icone.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://googleapis.com" rel="stylesheet" />

    <style type="text/css">
        /* Estilos específicos da página interna alinhados à home do Float */
        main {
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
            font-family: 'Inter', sans-serif;
        }

        /* Título e Tags superiores */
        .game-header {
            margin-bottom: 25px;
        }

        .game-header h1 {
            font-family: 'Rajdhani', sans-serif;
            font-size: 2.8rem;
            font-weight: 700;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .game-meta-tags {
            display: flex;
            gap: 15px;
            align-items: center;
            font-size: 0.9rem;
            color: #aaa;
        }

        .game-type-badge {
            background: rgba(255, 255, 255, 0.1);
            padding: 3px 10px;
            border-radius: 4px;
            font-weight: 600;
            color: #fff;
        }

        /* Layout de Grade Igual ao Herói da Home */
        .game-media-container {
            display: grid;
            grid-template-columns: 2.5fr 1fr;
            gap: 20px;
            margin-bottom: 40px;
        }

        @media (max-width: 992px) {
            .game-media-container {
                grid-template-columns: 1fr;
            }
        }

        /* Área do Visualizador Principal */
        .media-main-display {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            background: #0b0b0c;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
        }

        .media-slide {
            display: none;
            width: 100%;
            height: 100%;
        }

        .media-slide.active {
            display: block;
        }

        .media-slide img, .media-slide iframe {
            width: 100%;
            height: 100%;
            border: none;
            object-fit: contain;
        }

        /* Barra Lateral de Seleção (Estilo a lista lateral da Home) */
        .media-side-thumbs {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 100%;
            overflow-y: auto;
        }

        @media (max-width: 992px) {
            .media-side-thumbs {
                flex-direction: row;
                overflow-x: auto;
            }
        }

        .thumb-card {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        @media (max-width: 992px) {
            .thumb-card {
                flex: 0 0 140px;
                flex-direction: column;
                text-align: center;
            }
        }

        .thumb-card:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .thumb-card.active {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .thumb-card img {
            width: 80px;
            aspect-ratio: 16 / 9;
            object-fit: cover;
            border-radius: 4px;
        }

        .thumb-info {
            font-size: 0.85rem;
            font-weight: 600;
            color: #ccc;
        }

        .thumb-card.active .thumb-info {
            color: #fff;
        }

        /* Seções de Texto e Ficha Técnica */
        .game-info-sections {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
        }

        @media (max-width: 768px) {
            .game-info-sections {
                grid-template-columns: 1fr;
            }
        }

        .about-section h2 {
            font-family: 'Rajdhani', sans-serif;
            font-size: 1.8rem;
            margin-top: 0;
            text-transform: uppercase;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 8px;
        }

        .about-section p {
            line-height: 1.7;
            color: #ccc;
            font-size: 1.05rem;
        }

        /* Caixa de Compra e Detalhes Direita */
        .sidebar-panel {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .checkout-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 24px;
            border-radius: 8px;
        }

        .price-tag {
            font-size: 1.6rem;
            font-weight: 900;
            margin-bottom: 15px;
        }

        /* Alinhamento de Botões Padrão da Home */
        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-buy {
            flex: 1;
            background: #fff;
            color: #000;
            border: none;
            padding: 14px;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 6px;
            cursor: pointer;
            text-transform: uppercase;
            transition: opacity 0.2s;
            text-align: center;
            text-decoration: none;
        }

        .btn-buy:hover {
            opacity: 0.9;
        }

        .btn-wishlist {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 50px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            transition: background 0.2s;
        }

        .btn-wishlist:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Tabela Detalhes Ficha Técnica */
        .specs-card {
            background: rgba(255, 255, 255, 0.01);
            padding: 20px;
            border-radius: 8px;
        }

        .specs-table {
            width: 100%;
            border-collapse: collapse;
        }

        .specs-table td {
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.9rem;
        }

        .specs-table td:first-child {
            color: #888;
            font-weight: 600;
            width: 40%;
        }

        .specs-table td:last-child {
            color: #fff;
            text-align: right;
        }
    </style>
</head>

<body>

    <!-- Inclusão Automática do Header do seu projeto Float -->
    <?php require_once 'includes/header.php'; ?>

    <main>
        <!-- Cabeçalho do Jogo -->
        <div class="game-header">
            <h1><?php echo htmlspecialchars($game_title); ?></h1>
            <div class="game-meta-tags">
                <span class="game-type-badge"><?php echo htmlspecialchars($game_type); ?></span>
                <span>Por <strong><?php echo htmlspecialchars($developer_name); ?></strong></span>
            </div>
        </div>

        <!-- Estrutura de Mídia do Jogo (Herói) -->
        <section class="game-media-container">
            
            <!-- Visualizador Principal -->
            <div class="media-main-display">
                <?php foreach ($media_carousel as $index => $media){ ?>
                    <div class="media-slide <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index;} ?>">
                    </div>
            </div>
    </body>
</html>