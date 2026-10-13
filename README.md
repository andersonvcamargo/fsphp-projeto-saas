# ☕ CaféApp – Plataforma SaaS de Controle Financeiro

Aplicação web multiusuário para controle de receitas e despesas, com site institucional, blog e área logada. O projeto é baseado no projeto da formação **Full Stack PHP Developer** ([UpInside](https://www.upinside.com.br), Robson V. Leite) e foi migrado por mim para **PHP 8.2** e **Docker**.

![Painel do CaféApp](docs/screenshot-dashboard.png)

## Funcionalidades

- Cadastro, login e recuperação de senha por e-mail (PHPMailer)
- Gestão de carteiras, categorias e lançamentos de receitas e despesas
- Contas fixas e recorrentes (mensais e anuais) geradas automaticamente
- Painel com saldo, gráficos (Highcharts) e filtros por status, categoria e período
- Relatório de acessos e usuários online
- Site institucional, blog com busca e categorias, FAQ e SEO básico
- Upload e redimensionamento de imagens

## Tecnologias

| Camada | Tecnologias |
| --- | --- |
| Back-end | PHP 8.2, PDO, arquitetura MVC, Composer (PSR-4) |
| Banco de dados | MariaDB 10.11 |
| Front-end | HTML5, CSS3, JavaScript, jQuery, Highcharts, templates com Plates |
| Bibliotecas | PHPMailer, CoffeeCode (Router, Paginator, Cropper, Uploader, Optimizer), Minify |
| Infraestrutura | Docker, Docker Compose, Apache (mod_rewrite), Adminer |
| Versionamento | Git e GitHub |

## O que fiz neste projeto

- Montei o ambiente de desenvolvimento com Docker (Apache + PHP 8.2, MariaDB e Adminer)
- Atualizei o projeto e suas dependências para versões atuais (PHP 8.2, PHPMailer 7)
- [Adicione aqui outras alterações suas, como correções, novas telas ou melhorias]

## Como rodar localmente

### Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/) e Docker Compose
- [Composer](https://getcomposer.org/) (ou use o container do Composer, mostrado abaixo)

### Passo a passo

1. Clone o repositório:

   ```bash
   git clone https://github.com/andersonvcamargo/[repositório].git
   cd [repositório]
   ```

2. Instale as dependências:

   ```bash
   composer install
   ```

   Sem Composer instalado, use o container:

   ```bash
   docker run --rm -v "$PWD":/app composer install --ignore-platform-reqs
   ```

3. Crie o arquivo de configuração a partir do modelo:

   ```bash
   cp source/Boot/Config.doc.php source/Boot/Config.php
   ```

   Os dados de banco já vêm prontos para o Docker (host `db`, usuário `root`, senha `root`, banco `fsphp_saas`). Configure também os dados de SMTP se quiser testar o envio de e-mails.

4. Suba os containers:

   ```bash
   docker compose up -d --build
   ```

5. Importe o banco de dados:
   - Acesse o Adminer em http://localhost:8081
   - Sistema: MySQL, Servidor: `db`, Usuário: `root`, Senha: `root`, Banco: `fsphp_saas`
   - Importe o arquivo `DataModel.sql`

6. Acesse a aplicação em http://localhost:8080

### Endereços úteis

| Endereço | O que é |
| --- | --- |
| http://localhost:8080 | Site institucional |
| http://localhost:8080/blog | Blog |
| http://localhost:8080/cadastrar | Criar conta |
| http://localhost:8080/entrar | Login |
| http://localhost:8080/app | Área logada |
| http://localhost:8081 | Adminer (banco de dados) |

## Estrutura do projeto

```
├── source/
│   ├── App/        # Controllers (Web e App)
│   ├── Core/       # Model base, Connect, Session, View
│   ├── Models/     # User, Auth, AppInvoice, AppWallet, Post...
│   ├── Support/    # Email, Message, Upload, Seo, Pager
│   └── Boot/       # Configurações e helpers
├── themes/         # Views (cafeweb = site, cafeapp = área logada)
├── shared/         # Scripts, estilos e e-mails compartilhados
├── DataModel.sql   # Estrutura e dados iniciais do banco
├── Dockerfile
└── docker-compose.yml
```

## Segurança

As credenciais do `docker-compose.yml` e do `Config.doc.php` (`root` / `root`) servem **apenas para desenvolvimento local**. Não use em produção. O arquivo `source/Boot/Config.php` está no `.gitignore` e não deve ser versionado.

## Créditos

Projeto desenvolvido durante a formação Full Stack PHP Developer da [UpInside](https://www.upinside.com.br), com instrução de Robson V. Leite.

## Autor

**Anderson Vieira de Camargo**
[LinkedIn](https://linkedin.com/in/andersonvcamargo) · [GitHub](https://github.com/andersonvcamargo) · andersoncamargosi@gmail.com
