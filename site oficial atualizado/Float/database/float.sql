








SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


;
;
;
;




CREATE DATABASE IF NOT EXISTS `float` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `float`;









DROP TABLE IF EXISTS `avaliacao`;
CREATE TABLE `avaliacao` (
  `id` int(10) UNSIGNED NOT NULL,
  `jogo_id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `nota` tinyint(3) UNSIGNED NOT NULL COMMENT '1 a 5 estrelas',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


















DROP TABLE IF EXISTS `curtida_post`;
DROP TABLE IF EXISTS `comunidade_posts`;
CREATE TABLE `comunidade_posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `legenda` text DEFAULT NULL,
  `imagem_url` varchar(2048) DEFAULT NULL,
  `data_criacao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;











INSERT INTO `comunidade_posts` (`id`, `usuario_id`, `legenda`, `imagem_url`, `data_criacao`) VALUES
(1, 1, 'Olá, comunidade Float! Primeiro post da plataforma 🎮', NULL, '2026-05-11 16:12:40');







CREATE TABLE `curtida_post` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;















DROP TABLE IF EXISTS `denuncia`;
CREATE TABLE `denuncia` (
  `id` int(10) UNSIGNED NOT NULL,
  `denunciante_id` int(10) UNSIGNED NOT NULL,
  `tipo` enum('conta','jogo') NOT NULL,
  `alvo_id` int(10) UNSIGNED NOT NULL COMMENT 'ID de usuario ou jogo, conforme tipo',
  `motivo` varchar(255) NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;















DROP TABLE IF EXISTS `jogo`;
CREATE TABLE `jogo` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL COMMENT 'Caminho da imagem de capa',
  `genero` varchar(50) DEFAULT NULL,
  `visibilidade` enum('publico','privado') NOT NULL DEFAULT 'publico',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;















DROP TABLE IF EXISTS `seguidor`;
CREATE TABLE `seguidor` (
  `seguidor_id` int(10) UNSIGNED NOT NULL COMMENT 'Quem segue',
  `seguido_id` int(10) UNSIGNED NOT NULL COMMENT 'Quem é seguido',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


















DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `banner_path` varchar(255) DEFAULT NULL,
  `banner_pos_x` tinyint(3) UNSIGNED NOT NULL DEFAULT 50,
  `banner_pos_y` tinyint(3) UNSIGNED NOT NULL DEFAULT 50,
  `banner_zoom` tinyint(3) UNSIGNED NOT NULL DEFAULT 100,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;









INSERT INTO `usuario` (`id`, `nome`, `email`, `senha`, `bio`, `avatar_path`, `banner_path`, `banner_pos_x`, `banner_pos_y`, `banner_zoom`, `criado_em`) VALUES
(1, 'Hoshimi Miyabi', 'Voidhunter@etec.sp.gov.br', '$2y$10$Cqx8OGIMC9elqY5AEn20OOwGmfcjaDMi2ySGaVJ4gk6FVcTUXRqzy', NULL, NULL, NULL, 50, 50, 100, '2026-05-11 15:55:36');








ALTER TABLE `avaliacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_avaliacao` (`jogo_id`,`usuario_id`),
  ADD KEY `fk_aval_usuario` (`usuario_id`);




ALTER TABLE `comunidade_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);




ALTER TABLE `curtida_post`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_curtida` (`post_id`,`usuario_id`),
  ADD KEY `fk_curtida_usuario` (`usuario_id`);




ALTER TABLE `denuncia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_denuncia_denunciante` (`denunciante_id`),
  ADD KEY `idx_alvo` (`tipo`,`alvo_id`);




ALTER TABLE `jogo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_jogo_usuario` (`usuario_id`),
  ADD KEY `idx_visibilidade` (`visibilidade`);




ALTER TABLE `seguidor`
  ADD PRIMARY KEY (`seguidor_id`,`seguido_id`),
  ADD KEY `idx_seguido` (`seguido_id`);




ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);








ALTER TABLE `avaliacao`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;




ALTER TABLE `comunidade_posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;




ALTER TABLE `curtida_post`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;




ALTER TABLE `denuncia`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;




ALTER TABLE `jogo`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;




ALTER TABLE `usuario`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;








ALTER TABLE `avaliacao`
  ADD CONSTRAINT `fk_aval_jogo` FOREIGN KEY (`jogo_id`) REFERENCES `jogo` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_aval_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;




ALTER TABLE `comunidade_posts`
  ADD CONSTRAINT `comunidade_posts_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;




ALTER TABLE `curtida_post`
  ADD CONSTRAINT `fk_curtida_post` FOREIGN KEY (`post_id`) REFERENCES `comunidade_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_curtida_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;




ALTER TABLE `denuncia`
  ADD CONSTRAINT `fk_denuncia_denunciante` FOREIGN KEY (`denunciante_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;




ALTER TABLE `jogo`
  ADD CONSTRAINT `fk_jogo_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;




ALTER TABLE `seguidor`
  ADD CONSTRAINT `fk_seguido_usuario` FOREIGN KEY (`seguido_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_seguidor_usuario` FOREIGN KEY (`seguidor_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;





USE ``;phpmyadmin




























SET FOREIGN_KEY_CHECKS=1;
COMMIT;

;
;
;
