# POPRUA v2

Sistema de gestão de população em situação de rua desenvolvido com Laravel 12.

## 🚀 Funcionalidades

- **Sistema de Autenticação Completo**: Login, registro, recuperação de senha e verificação de email
- **Mapa Interativo**: Visualização de pontos e vistorias em mapa interativo usando Leaflet
- **Gestão de Vistorias**: Registro e acompanhamento de vistorias realizadas
- **Gestão de Pontos**: Cadastro e visualização de pontos de abordagem
- **Views Tabulares**: Listagens paginadas e filtradas de pontos e vistorias
- **Sistema de Roles e Permissões**: Controle de acesso usando Spatie Permission
- **Geolocalização**: Integração com API de geolocalização do navegador
- **Layout Mobile-First**: Interface otimizada para dispositivos móveis
- **Tradução pt-BR**: Sistema completamente traduzido para português brasileiro

## 📋 Requisitos

- PHP >= 8.2
- Composer
- Node.js e npm
- Banco de dados (MySQL/PostgreSQL/SQLite)

## 🔧 Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/popruav2.git
cd popruav2
```

2. Instale as dependências do PHP:
```bash
composer install
```

3. Instale as dependências do Node:
```bash
npm install
```

4. Configure o arquivo `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure o banco de dados no `.env` e execute as migrações:
```bash
php artisan migrate
```

6. Compile os assets:
```bash
npm run build
```

7. Inicie o servidor de desenvolvimento:
```bash
php artisan serve
```

## 🗄️ Estrutura do Banco de Dados

- **users**: Usuários do sistema
- **pontos**: Pontos de abordagem
- **vistorias**: Vistorias realizadas
- **roles**: Roles do sistema (Spatie Permission)
- **permissions**: Permissões do sistema (Spatie Permission)

## 🛠️ Tecnologias Utilizadas

- **Laravel 12**: Framework PHP
- **Laravel Breeze**: Autenticação
- **Tailwind CSS**: Framework CSS
- **Leaflet.js**: Mapas interativos
- **Alpine.js**: JavaScript reativo
- **Spatie Permission**: Gerenciamento de roles e permissões

## 📝 Licença

Este projeto é de código aberto e está disponível sob a licença MIT.
