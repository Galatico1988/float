@RTK.md

# Projeto TCC

- **Grupo**: 7 membros, mas apenas **eu e meu amigo** utilizam Claude Code via PowerShell
- **Objetivo**: ambos terem sempre o mesmo contexto — o CLAUDE.md é sincronizado via **GitHub** entre os dois
- **Workflow de arquivos**:
  - 📁 **Área de Trabalho\float** → versão mais atualizada do projeto (fonte da verdade)
  - 📁 **Downloads** → cópia de trabalho com modificações do dia
  - Quando terminar o dia, atualizar a pasta float e subir pro GitHub
- **Pasta raiz do projeto**: `C:\Users\Ivan\Desktop\float` (quando existir)
- **Quando trabalhar no TCC**, focar nestes caminhos ao invés do Windows inteiro

## O que é o projeto

**Float** — Plataforma community-driven de games (tipo Reddit / Steam / itch.io)

- Cada usuário tem uma **página personalizada** (estilo banner do YouTube)
- O usuário pode **arrastar imagens** pro fundo da página
- Pode **editar texto em campos específicos** com informações dos seus projetos
- **Outros usuários não podem mexer** na página alheia — cada um tem a sua
- A ideia é uma **comunidade de games** onde desenvolvedores mostram seus projetos

## Stack do projeto

- **Frontend**: PHP + HTML + CSS vanilla + JS vanilla
- **Backend**: PHP com MySQL (XAMPP, PHP 8.2, MySQL 8.3)
- **Banco**: banco `float` com 12 tabelas (ver abaixo)

## Estrutura de pastas (C:\Users\Ivan\Desktop\float)

```
float/
├── api/                    # Backend PHP
│   ├── auth/               # docadastro, dologin, protect
│   ├── comunidade/         # feed, post, reportar_post
│   ├── config/             # conexao.php
│   ├── jogos/              # publicar, editar, deletar, buscar, upload_screenshot, upload_video
│   └── user/               # atualizacao, follow, like, upload, etc.
├── database/
│   └── float.sql           # Schema completo do banco (8 tabelas)
├── public/                 # PASTA PRINCIPAL (servida pelo Apache)
│   ├── .htaccess           # Rotas: /jogos/{slug}/ → jogo.php
│   ├── index.php           # Homepage
│   ├── jogo.php            # Página dinâmica do jogo (busca do banco)
│   ├── pages/              # projetos, jogotemplate, comunidade, perfil, login, cadastro, noticias
│   ├── includes/           # header.php, footer.php
│   └── assets/             # css/, js/, img/
└── tools/                  # scripts auxiliares
```

## Páginas existentes

- **index.php** — Homepage (hero slider, cards de jogos, promo spotlight, notícias)
- **jogo.php** — Página dinâmica do jogo (URL: /jogos/{slug}/)
- **jogotemplate.php** — Template renderizado por jogo.php (não é mais hardcoded)
- **projetos.php** — Formulário multi-etapa (criar + editar jogo, 5 etapas com inline editing)
- **comunidade.php** — Feed da comunidade
- **perfil.php** — Perfil do usuário (jogos linkam pra /jogos/{slug}/)
- **login.php / cadastro.php** — Auth
- **noticias.php** — Notícias

## Banco de dados (8 tabelas)

- `usuario` — Usuários (com avatar, banner, bio)
- `jogo` — Jogos (com slug, tagline, backdrop, preco, versao, idiomas, plataformas JSON, custom_layout JSON)
- `jogo_tags` — Tags dos jogos (muitos-para-muitos)
- `jogo_screenshots` — Screenshots dos jogos (com ordem)
- `jogo_videos` — Vídeos (youtube/link/arquivo, com ordem)
- `jogo_requisitos` — Requisitos de sistema (mínimo + recomendado)
- `avaliacao` — Avaliações (1-5 estrelas)
- `comunidade_posts` — Posts da comunidade
- `curtida_post` — Curtidas nos posts
- `denuncia` — Denúncias
- `seguidor` — Seguidores

## ✅ O que foi feito (sessão 11/09/2026)

1. **Banco expandido** — tabela `jogo` com 15+ colunas novas + 4 tabelas auxiliares
2. **Rotas** — `.htaccess` com `/jogos/{slug}/` → `jogo.php`
3. **Página dinâmica** — `jogo.php` busca do banco e renderiza o template
4. **Template atualizado** — `jogotemplate.php` recebe dados do banco, suporta vídeos (YouTube/Vimeo/arquivo)
5. **APIs criadas** — `publicar.php`, `upload_screenshot.php`, `upload_video.php`
6. **Formulário de publicação** — `projetos.php` com 5 etapas (publicação multi-step)
7. **Etapa 5 — Customização visual** — reescrita de drag-and-drop (Scratch-like) pra **inline editing** com contenteditable + toggles + live preview
8. **Integração** — links no perfil, navbar e homepage apontam pra `/jogos/{slug}/`
9. **Botão deletar jogo** — exclusão com confirmação (perfil + API deletar.php)
10. **Botão editar jogo** — modo edição no formulário (perfil → projetos.php?id=X)
11. **APIs CRUD completas** — publicar.php, editar.php, deletar.php, buscar.php, upload_screenshot.php, upload_video.php
12. **Caminhos absolutos** — todos os paths corrigidos pra funcionar via `/jogos/{slug}/`

## ⚠️ Bugs conhecidos (precisam ser investigados)

1. **Validação no submit** — Ao publicar um jogo, sempre dá erro "título deve ter pelo menos 2 caracteres" / "descrição é obrigatória", mesmo com campos preenchidos. Adicionei `console.log` no submit pra debugar — precisa checar o F12 → Console pra ver se os valores estão vazios ou não.
2. **Vídeos não aparecem** — Caminhos de vídeos já publicados no banco podem estar relativos (`../assets/...`). Rodar SQL de fix:
```sql
UPDATE jogo_videos SET url = CONCAT('/float/public/', url) WHERE url LIKE '../%';
UPDATE jogo SET thumbnail = CONCAT('/float/public/', thumbnail) WHERE thumbnail LIKE '../%';
UPDATE jogo SET backdrop = CONCAT('/float/public/', backdrop) WHERE backdrop LIKE '../%';
UPDATE jogo_screenshots SET caminho = CONCAT('/float/public/', caminho) WHERE caminho LIKE '../%';
```

## ⚠️ Próximos passos

- Resolver bug de validação (verificar console no F12)
- Rodar SQL de fix de caminhos no banco
- Testar fluxo: publicar → editar → acessar `/jogos/{slug}/`
- Popular banco com dados de teste (Hades II)
- Subir pro GitHub pra sincronizar com o amigo

# Sessões & Persistência

- **CLAUDE.md** funciona como "memória de longo prazo" — eu leio esse arquivo no início de cada sessão nova.
- **Histórico de conversa NÃO persiste** — ao fechar a sessão, o contexto Some.
- **Solução**: salvar contexto importante aqui ou em arquivos auxiliares (`~/.claude/memory/`) pra eu retomar na próxima sessão.
- **Quando trabalhar em projetos**, salvar decisões importantes, progresso e pendências neste arquivo ou em arquivos dedicados.

## Regra Git

- **Antes de fechar a sessão**, sempre commitar e fazer push pro GitHub:
```bash
cd C:\Users\Ivan\Desktop\float
git add -A && git commit -m "descrição" && git push
```
- Isso garante que o amigo tenha a versão mais atualizada quando clonar/pull.
