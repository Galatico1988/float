-- Cadastrar os jogos em destaque do hero da home (Deltarune, Celeste, Journey, Stardew Valley)
-- O Hades II ja existe no banco com id = 4.

INSERT INTO `jogo` (`id`, `usuario_id`, `titulo`, `slug`, `tagline`, `descricao`, `thumbnail`, `backdrop`, `genero`, `preco`, `eh_gratis`, `versao`, `tamanho`, `idiomas`, `classificacao`, `plataformas`, `download_url`, `destaques`, `custom_layout`, `visibilidade`, `criado_em`, `atualizado_em`) VALUES
(5, 1, 'Deltarune', 'deltarune', 'Um novo capitulo do multiverso de Toby Fox.', 'Mergulhe em um novo capitulo do multiverso de Toby Fox. Uma narrativa emocionante e subversiva que redefine os limites do RPG indie com carisma e misterio.', 'thumbnail.png', 'backdrop.png', 'RPG', 'Gratis', 1, '0.1', '500mb', 'Portugues', 'Livre', '["windows"]', NULL, NULL, '[]', 'publico', NOW(), NOW()),
(6, 1, 'Celeste', 'celeste', 'Uma obra-prima de plataforma sobre autodescoberta.', 'Uma obra-prima de plataforma sobre autodescoberta. Supere montanhas fisicas e metaforicas em uma jornada sensivel, precisa e visualmente deslumbrante.', 'thumbnail.png', 'backdrop.png', 'Plataforma', 'R$ 29,99', 0, '1.0', '1gb', 'Portugues', 'Livre', '["windows"]', NULL, NULL, '[]', 'publico', NOW(), NOW()),
(7, 1, 'Journey', 'journey', 'Uma odisseia visual e sonora pelas areias de um mundo ancestral.', 'Uma odisseia visual e sonora pelas areias de um mundo ancestral. Descubra a beleza do silencio e a conexao humana em uma das experiencias mais artisticas dos games.', 'thumbnail.png', 'backdrop.png', 'Aventura', 'R$ 19,99', 0, '1.0', '2gb', 'Portugues', 'Livre', '["windows"]', NULL, NULL, '[]', 'publico', NOW(), NOW()),
(8, 1, 'Stardew Valley', 'stardew-valley', 'Cultive muito mais que uma fazenda.', 'Cultive muito mais que uma fazenda. Construa lacos, explore segredos e encontre o seu refugio ideal neste simulador de vida que transborda charme e profundidade.', 'thumbnail.png', 'backdrop.png', 'Simulacao', 'R$ 24,99', 0, '1.6', '500mb', 'Portugues', 'Livre', '["windows"]', NULL, NULL, '[]', 'publico', NOW(), NOW());

-- Tags dos novos jogos
INSERT INTO `jogo_tags` (`jogo_id`, `tag`) VALUES
(5, 'RPG'),
(5, 'Indie'),
(6, 'Plataforma'),
(6, 'Desafio'),
(7, 'Aventura'),
(7, 'Arte'),
(8, 'Simulacao'),
(8, 'Vida no Campo');
