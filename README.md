# CMS API - Content Management System

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue.svg)](https://www.php.org)
[![Laravel Version](https://img.shields.io/badge/laravel-12.0-red.svg)](https://laravel.com)
[![Build Status](https://img.shields.io/travis/guilhermehub12/teste-backend.svg?style=flat-square)](https://travis-ci.org/guilhermehub12/teste-backend)  
[![Latest Release](https://img.shields.io/github/release/guilhermehub12/teste-backend.svg?style=flat-square)](https://github.com/guilhermehub12/teste-backend/releases)  
[![License](https://img.shields.io/github/license/guilhermehub12/teste-backend.svg?style=flat-square)](LICENSE)

## Índice

- [Introdução](#introdução)
- [Requisitos](#requisitos)
- [Instalação e Configuração](#instalação-e-configuração)
  - [Clone e Instalação das Dependências](#clone-e-instalação-das-dependências)
  - [Configuração do Ambiente](#configuração-do-ambiente)
  - [Migrations e Seeders](#migrations-e-seeders)
  - [Dockerização](#dockerização)
- [Documentação da API](#documentação-da-api)
  - [Autenticação](#autenticação)
    - [Login](#login)
    - [Registro](#registro)
    - [Logout](#logout)
  - [Usuários](#usuários)
    - [Listar Usuários](#listar-usuários)
    - [Cadastrar Novo Usuário](#cadastrar-novo-usuário)
    - [Editar Usuário](#editar-usuário)
    - [Remover Usuário](#remover-usuário)
  - [Postagens](#postagens)
    - [Listar Postagens](#listar-postagens)
    - [Filtrar por Tag](#filtrar-por-tag)
    - [Pesquisar por Título ou Conteúdo](#pesquisar-por-título-ou-conteúdo)
    - [Cadastrar Nova Postagem](#cadastrar-nova-postagem)
    - [Editar Postagem](#editar-postagem)
    - [Remover Postagem](#remover-postagem)
- [Exemplos de Requisições](#exemplos-de-requisições)
- [Testes e CI/CD](#testes-e-cicd)
- [Contribuição](#contribuição)
- [Licença](#licença)
- [API Blueprint / Swagger](#api-blueprint--swagger)

## Introdução

Esta API foi desenvolvida para um CMS, onde é possível gerenciar postagens (com títulos, autores, conteúdos e tags) e usuários. O projeto foi construído com **Laravel 12** (utilizando o padrão RESTful), implementa autenticação via **Passport** e segue boas práticas como uso de Repository Pattern, Traits para Request/Response, injeção de dependências, paginação e rate limiting.

## Funcionalidades Principais

- Autenticação com Laravel Passport
- Gerenciamento de usuários com acesso baseado em funções
- Gerenciamento de postagens com tags
- Documentação de API com L5-Swagger
- Registro de auditoria com Laravel Auditing
- Otimização de desempenho com Laravel Octane
- Visualização de log em tempo real com Log Viewer e Laradumps
- Conjunto de testes abrangente

## Tech Stack

- PHP 8.2
- Laravel 12
- Laravel Passport
- L5-Swagger
- Laravel Auditing
- Laravel Octane
- Log Viewer
- PHPUnit

## Monitoramento
- Visualização de logs em tempo real com Log Viewer em /log-viewer
- Trilha de auditoria para todas as alterações no banco de dados usando Laravel Auditing

## Segurança
- Autenticação via Laravel Passport
- Limitação de taxa de requisições
- Validação de entrada
- Proteção CORS
- Respostas forçadas em JSON


## Requisitos

- **PHP**: 8.2 ou superior  
- **Node**: Versão 20+ 
- **Composer**  
- **Banco de Dados**: MySQL ou PostgreSQL  
- **Docker** (para dockerização da aplicação)  
- **Git** para versionamento

## Instalação e Configuração

### Clone e Instalação das Dependências

```bash
git clone https://github.com/guilhermehub12/teste-backend.git
cd teste-backend
composer install
php artisan passport:client --personal
npm install
php artisan serve
```

### Configuração do Ambiente

1. Copie o arquivo `.env.example` para `.env`:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

2. Configure as variáveis de ambiente, especialmente:
   - **Conexão com o Banco de Dados** (`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
   - **Configurações de Passport** conforme sua escolha
   - Outras variáveis (cache, mail, etc.)

### Migrations e Seeders

Execute as migrations e os seeders para criar e popular o banco de dados:

```bash
php || sail artisan migrate --seed
```

### Dockerização

A aplicação já conta com um Dockerfile e um docker-compose.yml. Para levantar o ambiente via Docker, basta executar:

```bash
sail up -d
```

Isso iniciará os containers da aplicação, do banco de dados e demais serviços necessários.

## Documentação da API

A documentação completa da API está disponível no formato **Swagger (OpenAPI)**. Você pode visualizar o arquivo `api-docs.yaml` na pasta do projeto `storage/api-docs/api-docs.yaml` ou acessar o `localhost/api/doc` após a inicialização do projeto.

### Autenticação

#### Login

**Endpoint:** `POST /auth/login`  
**Content-Type:** `application/json`

**Corpo da requisição:**

```json
{
  "email": "joao.silva@example.com",
  "password": "senha123"
}
```

**Resposta de sucesso (HTTP 200):**

```json
{
  "message": "Login realizado com sucesso.",
  "user": {
    "id": 1,
    "nome": "João Silva",
    "email": "joao.silva@example.com",
    "telefone": "(11) 98765-4321",
    "is_valid": true
  },
  "token": "seu-token-jwt-aqui"
}
```

**Resposta de erro (HTTP 401):**

```json
{
  "message": "Credenciais inválidas. Verifique seu email e senha."
}
```

#### Registro

**Endpoint:** `POST /auth/register`  
**Content-Type:** `application/json`

**Corpo da requisição:**

```json
{
  "nome": "Ana Costa",
  "email": "ana.costa@example.com",
  "password": "senha123",
  "telefone": "(41) 99877-6655"
}
```

**Resposta de sucesso (HTTP 201):**

```json
{
  "message": "Usuário registrado com sucesso.",
  "user": {
    "id": 4,
    "nome": "Ana Costa",
    "email": "ana.costa@example.com",
    "telefone": "(41) 99877-6655",
    "is_valid": true
  },
  "token": "seu-token-jwt-aqui"
}
```

**Resposta de erro:**

```json
{
  "message": "Erro ao cadastrar o usuário. Verifique os dados fornecidos."
}
```

#### Logout

**Endpoint:** `POST /auth/logout`  
**Cabeçalho:** `Authorization: Bearer {seu_token}`

**Resposta de sucesso (HTTP 200):**

```json
{
  "message": "Logout realizado com sucesso."
}
```

### Usuários

#### Listar Usuários

**Endpoint:** `GET /users`  
**Cabeçalho:** `Authorization: Bearer {seu_token}`

**Resposta:**

```json
[
  {
    "id": 1,
    "nome": "João Silva",
    "email": "joao.silva@example.com",
    "telefone": "(11) 98765-4321",
    "is_valid": true
  },
  {
    "id": 2,
    "nome": "Maria Oliveira",
    "email": "maria.oliveira@example.com",
    "telefone": "(21) 91234-5678",
    "is_valid": false
  },
  {
    "id": 3,
    "nome": "Pedro Souza",
    "email": "pedro.souza@example.com",
    "telefone": "(31) 99876-5432",
    "is_valid": true
  }
]
```

#### Cadastrar Novo Usuário

**Endpoint:** `POST /users`  
**Content-Type:** `application/json`  
**Cabeçalho:** `Authorization: Bearer {seu_token}`

**Corpo da requisição:**

```json
{
  "nome": "Ana Costa",
  "email": "ana.costa@example.com",
  "password": "senha123",
  "telefone": "(41) 99877-6655",
  "is_valid": true
}
```

**Resposta de sucesso (HTTP 201):**

```json
{
  "id": 4,
  "nome": "Ana Costa",
  "email": "ana.costa@example.com",
  "telefone": "(41) 99877-6655",
  "is_valid": true
}
```

#### Editar Usuário

**Endpoint:** `PUT /users/{id}`  
**Content-Type:** `application/json`  
**Cabeçalho:** `Authorization: Bearer {seu_token}`

**Corpo da requisição (exemplo):**

```json
{
  "nome": "Ana Costa",
  "email": "ana.costa@example.com",
  "password": "novaSenha456",
  "telefone": "(41) 99877-6655",
  "is_valid": false
}
```

**Resposta de sucesso:**

```json
{
  "id": 4,
  "nome": "Ana Costa",
  "email": "ana.costa@example.com",
  "telefone": "(41) 99877-6655",
  "is_valid": false
}
```

#### Remover Usuário

**Endpoint:** `DELETE /users/{id}`  
**Cabeçalho:** `Authorization: Bearer {seu_token}`

**Resposta de sucesso (HTTP 200):**

```json
{
  "message": "Usuário removido com sucesso."
}
```

### Postagens

#### Listar Postagens

**Endpoint:** `GET /posts`  
**Cabeçalho:** `Authorization: Bearer {seu_token}`

**Resposta:**

```json
[
  {
    "id": 1,
    "title": "Notion",
    "author": {
      "id": 1,
      "nome": "Nome do Usuario",
      "telefone": "0000000000",
      "email": "email@email.com"
    },
    "content": "Sed soluta nemo et consectetur reprehenderit ea...",
    "tags": ["organization", "planning", "collaboration", "writing", "calendar"]
  },
  {
    "id": 2,
    "title": "json-server",
    "author": { ... },
    "content": "Laudantium illum modi tenetur possimus natus...",
    "tags": ["api", "json", "schema", "node", "github", "rest"]
  }
]
```

#### Filtrar por Tag

**Endpoint:** `GET /posts?tag=node`  
**Cabeçalho:** `Authorization: Bearer {seu_token}`

**Resposta:**  
Retorna apenas as postagens que contenham a tag "node".

#### Pesquisar por Título ou Conteúdo

**Endpoint:** `GET /posts?query=palavra-chave`  
**Cabeçalho:** `Authorization: Bearer {seu_token}`

**Resposta:**  
Retorna as postagens cujos títulos ou conteúdos contenham a palavra-chave pesquisada.

#### Cadastrar Nova Postagem

**Endpoint:** `POST /posts`  
**Content-Type:** `application/json`  
**Cabeçalho:** `Authorization: Bearer {seu_token}`

**Corpo da requisição:**

```json
{
  "title": "hotel",
  "author": 1,
  "content": "Local app manager. Start apps within your browser...",
  "tags": ["node", "organizing", "webapps", "domain", "developer", "https", "proxy"]
}
```

**Resposta de sucesso (HTTP 201):**

```json
{
  "id": 5,
  "title": "hotel",
  "author": {
    "id": 1,
    "nome": "Nome do Usuario",
    "telefone": "0000000000",
    "email": "email@email.com"
  },
  "content": "Local app manager. Start apps within your browser...",
  "tags": ["node", "organizing", "webapps", "domain", "developer", "https", "proxy"]
}
```

#### Editar Postagem

**Endpoint:** `PUT /posts/{id}`  
**Content-Type:** `application/json`  
**Cabeçalho:** `Authorization: Bearer {seu_token}`

**Corpo da requisição:**

```json
{
  "title": "hotel",
  "author": 2,
  "content": "Local app manager. Start apps within your browser...",
  "tags": ["organizing", "webapps", "domain", "developer", "proxy"]
}
```

**Resposta de sucesso:**

```json
{
  "id": 5,
  "title": "hotel",
  "author": {
    "id": 2,
    "nome": "Nome do Usuario",
    "telefone": "0000000000",
    "email": "email@email.com"
  },
  "content": "Local app manager. Start apps within your browser...",
  "tags": ["organizing", "webapps", "domain", "developer", "proxy"]
}
```

#### Remover Postagem

**Endpoint:** `DELETE /posts/{id}`  
**Cabeçalho:** `Authorization: Bearer {seu_token}`

**Resposta de sucesso (HTTP 200):**

```json
{
  "message": "Postagem removida com sucesso."
}
```

## Exemplos de Requisições

Utilize ferramentas como [Postman](https://www.postman.com/) ou [Insomnia](https://insomnia.rest/) para testar os endpoints. Uma coleção de requisições (JSON exportado) está disponível na pasta `/docs` para facilitar os testes.

<!-- ## Testes e CI/CD

- **Testes Automatizados:**  
  Execute os testes com:
  ```bash
  php artisan test
  ```
  ou
  ```bash
  vendor/bin/phpunit
  ``` -->


## Contribuição

1. Faça um fork do repositório.
2. Crie uma branch para sua feature:
   ```bash
   git checkout -b minha-nova-feature
   ```
3. Faça as alterações e commit:
   ```bash
   git commit -am 'Adiciona nova feature'
   ```
4. Envie para a branch:
   ```bash
   git push origin minha-nova-feature
   ```
5. Abra um Pull Request.

## Licença

Distribuído sob a [MIT License](LICENSE).

---

### Considerações Finais

- **Padrões e Boas Práticas:**  
  A aplicação utiliza Repository Pattern, Traits para Request e Response, injeção de dependências, paginação e rate limiting para otimização das requisições.

- **Autenticação e Autorização:**  
  Implementado com Passport para proteger as rotas da API.
