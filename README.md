# BeReady - Plataforma de Aprendizado de Inglês

Aplicação web para aprendizado de inglês por contextualização visual, utilizando flashcards, quizzes, traduções e recursos interativos.

---

# Tecnologias Utilizadas

## Back-end

- CakePHP 5
- PHP 8+
- PostgreSQL / Supabase

## Front-end

- Vue.js 3
- TypeScript
- Vite
- Pinia

## Banco de Dados

- PostgreSQL
- Supabase

---

# Estrutura do Projeto

```txt
backend/   → API CakePHP
frontend/  → Interface Vue.js
```

---

# Requisitos

Antes de começar, instale:

- PHP 8.2+
- Composer
- Node.js 20+
- NPM
- PostgreSQL ou Supabase
- Git

---

# Clonar o Projeto

```bash
git clone https://github.com/BraianGaspar/Beready_Projeto.git
cd Beready_Projeto
```

---

# Configuração do Banco de Dados

O projeto utiliza PostgreSQL via Supabase.

## Configurar conexão

Toda a configuração vem do `.env` (não há valores padrão no código). Copie os exemplos e preencha:

```bash
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env
```

A conexão do banco fica em `DATABASE_URL` no `backend/.env`:

```txt
DATABASE_URL="postgres://usuario:senha@host:6543/postgres?encoding=utf8&timezone=UTC&cacheMetadata=true"
```

## Criar o banco do zero

A migration `Initial` cria todas as tabelas, e os seeds inserem roles, permissões e planos:

```bash
cd backend
php bin/cake.php migrations migrate
php bin/cake.php migrations seed
```

---

# Segurança do Banco

Recomendações importantes:

- Ativar Row Level Security (RLS) no Supabase
- Nunca expor `service_role_key`
- Utilizar variáveis `.env`
- Não subir credenciais para o GitHub
- Configurar políticas de acesso nas tabelas

---

# ⚙️ Configuração do Backend (CakePHP)

## Acessar pasta backend

```bash
cd backend
```

---

## Instalar dependências

```bash
composer install
```

---

## Rodar migrations

```bash
bin/cake migrations migrate
```

No Windows:

```bash
php bin/cake.php migrations migrate
```

---

## Rodar os testes

Os testes usam um Postgres separado, definido em `TEST_DATABASE_URL` no `backend/.env`. O banco de teste é apagado e recriado a cada execução, então nunca aponte para o banco real. Para subir um Postgres local de teste:

```bash
docker run -d --name beready_migtest -e POSTGRES_PASSWORD=test -e POSTGRES_DB=beready_test -p 55432:5432 postgres:15-alpine
```

```bash
cd backend
vendor/bin/phpunit
```

---

## Iniciar servidor backend

```bash
bin/cake server
```

Ou no Windows:

```bash
php bin/cake.php server
```

Backend disponível em:

```txt
http://localhost:8765
```

---

# Configuração do Frontend (Vue.js)

> Abra outro terminal para rodar o frontend simultaneamente.

## Acessar pasta frontend

```bash
cd frontend
```

---

## Instalar dependências

```bash
npm install
```

---

## Rodar ambiente de desenvolvimento

```bash
npm run dev
```

Frontend disponível em:

```txt
http://localhost:5173
```

---

# 🔗 Comunicação Frontend ↔ Backend

Verifique a URL da API no frontend.

Exemplo:

```env
VITE_API_URL=http://localhost:8765
```

---

# 🧠 Observações

- O backend e frontend rodam separadamente
- É necessário manter os dois servidores ativos
- O projeto utiliza arquitetura API REST
- O Supabase é utilizado como banco PostgreSQL
- O frontend consome os endpoints do CakePHP

---

# Comandos Rápidos

## Backend

```bash
cd backend
composer install
php bin/cake.php migrations migrate
php bin/cake.php server
```

---

## Frontend

```bash
cd frontend
npm install
npm run dev
```

---

# Variáveis Sensíveis

Adicione ao `.gitignore`:

```gitignore
.env
.env.local
backend/config/app_local.php
```

---

# Autor

Projeto desenvolvido para fins educacionais e aprendizado de inglês com suporte visual e contextual.
