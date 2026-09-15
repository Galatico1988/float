-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 11/09/2026 às 06:54
-- Versão do servidor: 8.4.7
-- Versão do PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `float`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacao`
--

DROP TABLE IF EXISTS `avaliacao`;
CREATE TABLE IF NOT EXISTS `avaliacao` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `jogo_id` int UNSIGNED NOT NULL,
  `usuario_id` int UNSIGNED NOT NULL,
  `nota` tinyint UNSIGNED NOT NULL COMMENT '1 a 5 estrelas',
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_avaliacao` (`jogo_id`,`usuario_id`),
  KEY `fk_aval_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `comunidade_posts`
--

DROP TABLE IF EXISTS `comunidade_posts`;
CREATE TABLE IF NOT EXISTS `comunidade_posts` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` int UNSIGNED NOT NULL,
  `legenda` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `imagem_url` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_criacao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `curtida_post`
--

DROP TABLE IF EXISTS `curtida_post`;
CREATE TABLE IF NOT EXISTS `curtida_post` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` int UNSIGNED NOT NULL,
  `usuario_id` int UNSIGNED NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_curtida` (`post_id`,`usuario_id`),
  KEY `fk_curtida_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `denuncia`
--

DROP TABLE IF EXISTS `denuncia`;
CREATE TABLE IF NOT EXISTS `denuncia` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `denunciante_id` int UNSIGNED NOT NULL,
  `tipo` enum('conta','jogo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alvo_id` int UNSIGNED NOT NULL COMMENT 'ID de usuario ou jogo, conforme tipo',
  `motivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_denuncia_denunciante` (`denunciante_id`),
  KEY `idx_alvo` (`tipo`,`alvo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `jogo`
--

DROP TABLE IF EXISTS `jogo`;
CREATE TABLE IF NOT EXISTS `jogo` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` int UNSIGNED NOT NULL,
  `titulo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tagline` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `thumbnail` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `backdrop` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `genero` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preco` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Gratis',
  `eh_gratis` tinyint(1) NOT NULL DEFAULT '1',
  `versao` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tamanho` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idiomas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Portugues',
  `classificacao` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Livre',
  `plataformas` json DEFAULT NULL,
  `download_url` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destaques` json DEFAULT NULL,
  `custom_layout` json DEFAULT NULL,
  `visibilidade` enum('publico','privado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'publico',
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`),
  KEY `idx_jogo_usuario` (`usuario_id`),
  KEY `idx_visibilidade` (`visibilidade`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `jogo`
--

INSERT INTO `jogo` (`id`, `usuario_id`, `titulo`, `slug`, `tagline`, `descricao`, `thumbnail`, `backdrop`, `genero`, `preco`, `eh_gratis`, `versao`, `tamanho`, `idiomas`, `classificacao`, `plataformas`, `download_url`, `destaques`, `custom_layout`, `visibilidade`, `criado_em`, `atualizado_em`) VALUES
(1, 1, 'ADM FODA', 'adm-foda', 'Incremental', 'fds', 'thumbnail.png', 'backdrop.webp', 'Simulação', 'Gratis', 1, '0.1', '100mb', 'Português', 'Livre', '[\"windows\", \"android\"]', NULL, NULL, '[]', 'publico', '2026-09-11 03:27:56', '2026-09-11 03:27:56'),
(2, 1, 'ADM FODA2', 'adm-foda2', 'Incremental', '67', NULL, NULL, 'Run & Gun', '67', 0, '1', '10mb', 'Português', '18 anos', NULL, NULL, NULL, '[]', 'publico', '2026-09-11 03:40:07', '2026-09-11 03:40:07'),
(3, 1, 'ADM FODA2', 'adm-foda2-bcbfd2', 'Incremental', 'jk', 'thumbnail.jpg', 'backdrop.jpg', 'Visual Novel', '67', 0, '0.1', '100mb', 'Português', '18 anos', '[\"windows\", \"android\"]', NULL, NULL, '[{\"icone\": \"fa-wand-magic-sparkles\", \"texto\": \"Novo destaque\"}]', 'publico', '2026-09-11 03:44:06', '2026-09-11 03:44:06');

-- --------------------------------------------------------

--
-- Estrutura para tabela `jogo_requisitos`
--

DROP TABLE IF EXISTS `jogo_requisitos`;
CREATE TABLE IF NOT EXISTS `jogo_requisitos` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `jogo_id` int UNSIGNED NOT NULL,
  `tipo` enum('minimo','recomendado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `os` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `processador` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `memoria` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `armazenamento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `directx` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carga_cpu` tinyint UNSIGNED DEFAULT '0',
  `carga_gpu` tinyint UNSIGNED DEFAULT '0',
  `carga_ram` tinyint UNSIGNED DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_jogo_tipo` (`jogo_id`,`tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `jogo_requisitos`
--

INSERT INTO `jogo_requisitos` (`id`, `jogo_id`, `tipo`, `os`, `processador`, `memoria`, `video`, `armazenamento`, `directx`, `carga_cpu`, `carga_gpu`, `carga_ram`) VALUES
(1, 1, 'minimo', '', '', '', '', '', '', 0, 0, 0),
(2, 1, 'recomendado', '', '', '', '', '', '', 0, 0, 0),
(3, 2, 'minimo', '', '', '', '', '', '', 0, 0, 0),
(4, 2, 'recomendado', '', '', '', '', '', '', 0, 0, 0),
(5, 3, 'minimo', '', '', '', '', '', '', 0, 0, 0),
(6, 3, 'recomendado', '', '', '', '', '', '', 0, 0, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `jogo_screenshots`
--

DROP TABLE IF EXISTS `jogo_screenshots`;
CREATE TABLE IF NOT EXISTS `jogo_screenshots` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `jogo_id` int UNSIGNED NOT NULL,
  `caminho` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ordem` tinyint UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_screenshots_jogo` (`jogo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `jogo_screenshots`
--

INSERT INTO `jogo_screenshots` (`id`, `jogo_id`, `caminho`, `ordem`) VALUES
(1, 1, 'screenshot_1789108076_eace730c.jpg', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `jogo_tags`
--

DROP TABLE IF EXISTS `jogo_tags`;
CREATE TABLE IF NOT EXISTS `jogo_tags` (
  `jogo_id` int UNSIGNED NOT NULL,
  `tag` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`jogo_id`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `jogo_tags`
--

INSERT INTO `jogo_tags` (`jogo_id`, `tag`) VALUES
(1, 'Early Access'),
(1, 'Indie'),
(1, 'Single Player'),
(3, 'Indie'),
(3, 'RPG');

-- --------------------------------------------------------

--
-- Estrutura para tabela `jogo_videos`
--

DROP TABLE IF EXISTS `jogo_videos`;
CREATE TABLE IF NOT EXISTS `jogo_videos` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `jogo_id` int UNSIGNED NOT NULL,
  `tipo` enum('youtube','link','arquivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordem` tinyint UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_videos_jogo` (`jogo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `seguidor`
--

DROP TABLE IF EXISTS `seguidor`;
CREATE TABLE IF NOT EXISTS `seguidor` (
  `seguidor_id` int UNSIGNED NOT NULL COMMENT 'Quem segue',
  `seguido_id` int UNSIGNED NOT NULL COMMENT 'Quem é seguido',
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`seguidor_id`,`seguido_id`),
  KEY `idx_seguido` (`seguido_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `avatar_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner_pos_x` tinyint UNSIGNED NOT NULL DEFAULT '50',
  `banner_pos_y` tinyint UNSIGNED NOT NULL DEFAULT '50',
  `banner_zoom` tinyint UNSIGNED NOT NULL DEFAULT '100',
  `criado_em` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `nome`, `email`, `senha`, `bio`, `avatar_path`, `banner_path`, `banner_pos_x`, `banner_pos_y`, `banner_zoom`, `criado_em`) VALUES
(1, 'Naja', 'ivan.lucas0404@gmail.com', '$2y$10$/0aiUa16R4pZjrnIwZIskue2QfDYhWbFtkijoeaWVhmLW46DjATw2', NULL, NULL, NULL, 50, 50, 100, '2026-09-11 03:26:39');

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `avaliacao`
--
ALTER TABLE `avaliacao`
  ADD CONSTRAINT `fk_aval_jogo` FOREIGN KEY (`jogo_id`) REFERENCES `jogo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aval_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `comunidade_posts`
--
ALTER TABLE `comunidade_posts`
  ADD CONSTRAINT `comunidade_posts_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `curtida_post`
--
ALTER TABLE `curtida_post`
  ADD CONSTRAINT `fk_curtida_post` FOREIGN KEY (`post_id`) REFERENCES `comunidade_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_curtida_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `denuncia`
--
ALTER TABLE `denuncia`
  ADD CONSTRAINT `fk_denuncia_denunciante` FOREIGN KEY (`denunciante_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `jogo`
--
ALTER TABLE `jogo`
  ADD CONSTRAINT `fk_jogo_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `jogo_requisitos`
--
ALTER TABLE `jogo_requisitos`
  ADD CONSTRAINT `fk_requisitos_jogo` FOREIGN KEY (`jogo_id`) REFERENCES `jogo` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `jogo_screenshots`
--
ALTER TABLE `jogo_screenshots`
  ADD CONSTRAINT `fk_screenshots_jogo` FOREIGN KEY (`jogo_id`) REFERENCES `jogo` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `jogo_tags`
--
ALTER TABLE `jogo_tags`
  ADD CONSTRAINT `fk_tags_jogo` FOREIGN KEY (`jogo_id`) REFERENCES `jogo` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `jogo_videos`
--
ALTER TABLE `jogo_videos`
  ADD CONSTRAINT `fk_videos_jogo` FOREIGN KEY (`jogo_id`) REFERENCES `jogo` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `seguidor`
--
ALTER TABLE `seguidor`
  ADD CONSTRAINT `fk_seguido_usuario` FOREIGN KEY (`seguido_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_seguidor_usuario` FOREIGN KEY (`seguidor_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
