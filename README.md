# 📘 BeReady - Plataforma de Aprendizado de Inglês

Aplicação web para aprendizado de inglês por contextualização visual, desenvolvida com:

- **Back-end:** CakePHP
- **Front-end:** Vite + Node.js
- **Banco de Dados:** configurável

---

## 🚀 Requisitos

Antes de começar, você precisa ter instalado na sua máquina:

- **PHP** (versão mais recente recomendada)
- **Composer**
- **Node.js**
- **NPM**
- **Vite**

---

## 📥 Instalação do Projeto

### 1. Clonar o repositório

```bash
git clone https://github.com/BraianGaspar/Beready_Projeto.git
cd Beready_Projeto
```

---

## ⚙️ Configuração do Back-end (CakePHP)

**1. Acesse a pasta do backend:**

```bash
cd backend
```

**2. Instale as dependências:**

```bash
composer install
```

**3. Inicie o servidor:**

```bash
bin/cake server
```

O backend estará rodando em:

```
http://localhost:8765
```

---

## 🎨 Configuração do Front-end

> ⚠️ Abra outro terminal (Git Bash) para rodar o front simultaneamente

**1. Acesse a pasta do frontend:**

```bash
cd frontend
```

**2. Instale as dependências:**

```bash
npm install
```

**3. Instale o Vite (caso necessário):**

```bash
npm install vite
```

**4. Inicie o servidor de desenvolvimento:**

```bash
npm run dev
```

---

## 🔧 Configuração Adicional

- Configure o banco de dados em:

```
backend/config/app_local.php
```

- Ajuste outras configurações em:

```
backend/config/app.php
```

---

## 🧠 Observações

- O projeto utiliza **CakePHP 5.x**
- Back-end e front-end rodam **separadamente**
- É necessário manter os **dois servidores ativos** simultaneamente

---

## 📌 Resumo Rápido

✔ Instalar: PHP + Composer + Node.js + NPM + Vite

✔ Rodar backend:

```bash
cd backend
composer install
bin/cake server
```

✔ Rodar frontend (outro terminal):

```bash
cd frontend
npm install
npm install vite
npm run dev
```
