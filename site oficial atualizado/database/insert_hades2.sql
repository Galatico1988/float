-- Cadastrar Hades II no banco de dados

-- 1. Inserir o jogo
INSERT INTO `jogo` (`id`, `usuario_id`, `titulo`, `slug`, `tagline`, `descricao`, `thumbnail`, `backdrop`, `genero`, `preco`, `eh_gratis`, `versao`, `tamanho`, `idiomas`, `classificacao`, `plataformas`, `download_url`, `destaques`, `custom_layout`, `visibilidade`, `criado_em`, `atualizado_em`) VALUES
(4, 1, 'Hades II', 'hades-ii', 'A sequência do premiado roguelite da Supergiant Games chega em Early Access.',
'A continuação do aclamado roguelite da Supergiant Games. Explore um mundo mais profundo, mais sombrio e mais viciante do que jamais foi. Enfante novos inimigos, domine novas armas e descubra os segredos do Submundo.',
'card_hadesII.png', 'slide_03.png', 'Roguelite', 'R$ 59,99', 0, '0.1', '10gb', 'Português', '18 anos', '["windows"]', NULL, NULL, '[]', 'publico', NOW(), NOW());

-- 2. Inserir as tags
INSERT INTO `jogo_tags` (`jogo_id`, `tag`) VALUES
(4, 'Roguelite'),
(4, 'Early Access'),
(4, 'Single Player');

-- 3. Inserir requisitos mínimos
INSERT INTO `jogo_requisitos` (`jogo_id`, `tipo`, `os`, `processador`, `memoria`, `video`, `armazenamento`, `directx`, `carga_cpu`, `carga_gpu`, `carga_ram`) VALUES
(4, 'minimo', 'Windows 10', 'Intel Core i3', '8 GB RAM', 'NVIDIA GTX 960', '10 GB', '11', 40, 30, 40);

-- 4. Inserir requisitos recomendados
INSERT INTO `jogo_requisitos` (`jogo_id`, `tipo`, `os`, `processador`, `memoria`, `video`, `armazenamento`, `directx`, `carga_cpu`, `carga_gpu`, `carga_ram`) VALUES
(4, 'recomendado', 'Windows 11', 'Intel Core i7', '16 GB RAM', 'NVIDIA RTX 3060', '10 GB', '12', 70, 60, 70);
