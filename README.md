# POPRUA v2

Sistema de Gestão de População em Situação de Rua - Belo Horizonte

## Sobre o Projeto

O POPRUA v2 é um sistema web para gestão e monitoramento de vistorias relacionadas à população em situação de rua em Belo Horizonte. O sistema permite:

- Cadastro e gestão de pontos de ocupação georreferenciados
- Registro de vistorias com informações detalhadas
- Georreferenciamento completo com suporte a consultas espaciais (PostGIS)
- Gestão de moradores e histórico de movimentação
- Mapa interativo com visualização por bairro/regional
- Relatórios e análises espaciais

## Stack Tecnológica

| Componente | Versão | Descrição |
|------------|--------|-----------|
| PHP | 8.4 | Runtime |
| Laravel | 12 | Framework |
| PostgreSQL | 16 | Banco de dados |
| PostGIS | 3.4 | Extensão geoespacial |
| Redis | - | Cache e filas |
| Tailwind CSS | 3 | Estilização |
| Leaflet.js | - | Mapas interativos |
| Alpine.js | - | JavaScript reativo |

## Funcionalidades

- **Sistema de Autenticação Completo**: Login, registro, recuperação de senha
- **Mapa Interativo**: Visualização de pontos e vistorias com Leaflet
- **Consultas Espaciais**: Pontos por bairro, proximidade, área de cobertura
- **Gestão de Vistorias**: Registro e acompanhamento de vistorias
- **Gestão de Pontos**: Cadastro com georreferenciamento automático
- **Sistema de Roles e Permissões**: Controle de acesso com Spatie Permission
- **Layout Mobile-First**: Interface otimizada para dispositivos móveis
- **Tradução pt-BR**: Sistema completamente em português

## Requisitos

- PHP 8.4+
- PostgreSQL 16+ com PostGIS 3.4+
- Redis
- Composer
- Node.js 18+
- npm

## Instalação

### 1. Clone o repositório

```bash
git clone <url-do-repositorio>
cd popruav2
```

### 2. Instale as dependências

```bash
composer install
npm install
```

### 3. Configure o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure o banco de dados

Edite o arquivo `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=popruav2
DB_USERNAME=postgres
DB_PASSWORD=sua_senha
```

### 5. Crie o banco de dados com PostGIS

```bash
sudo -u postgres psql -c "CREATE DATABASE popruav2;"
sudo -u postgres psql -d popruav2 -c "CREATE EXTENSION postgis;"
```

### 6. Execute as migrações

```bash
php artisan migrate
```

### 7. Compile os assets

```bash
npm run build
# ou para desenvolvimento
npm run dev
```

### 8. Inicie o servidor

```bash
php artisan serve
```

## Estrutura do Banco de Dados

### Tabelas Principais

| Tabela | Descrição |
|--------|-----------|
| `pontos` | Locais de ocupação georreferenciados |
| `vistorias` | Registros de vistorias realizadas |
| `moradores` | Cadastro de pessoas em situação de rua |
| `morador_historicos` | Histórico de movimentação |
| `endereco_atualizados` | Base de endereços de BH (755k registros) |

### Tabelas Geográficas (PostGIS)

| Tabela | Tipo Geometria | Registros | Índice GIST |
|--------|---------------|-----------|-------------|
| `pontos` | POINT | 2.642 | idx_pontos_geom |
| `endereco_atualizados` | POINT | 755.903 | idx_endereco_atualizados_geom |
| `geo_bairros` | MULTIPOLYGON | 493 | idx_geo_bairros_geom |
| `geo_regionais` | MULTIPOLYGON | 10 | idx_geo_regionais_geom |
| `geo_limite_municipio` | GEOMETRY | 1 | idx_geo_limite_municipio_geom |

## Consultas Espaciais

O sistema utiliza PostGIS para consultas geoespaciais otimizadas:

### Pontos por bairro

```sql
SELECT gb.nome as bairro, COUNT(p.id) as total
FROM geo_bairros gb
JOIN pontos p ON ST_Contains(gb.geom, p.geom)
GROUP BY gb.nome
ORDER BY total DESC;
```

### Pontos em raio de 500m

```sql
SELECT id, ST_Distance(geom::geography, ponto::geography) as metros
FROM pontos
WHERE ST_DWithin(geom::geography, ponto::geography, 500);
```

### Identificar bairro de um ponto

```sql
SELECT gb.nome
FROM geo_bairros gb
WHERE ST_Contains(gb.geom, ST_SetSRID(ST_MakePoint(-43.9378, -19.9191), 4326));
```

## Comandos Artisan

```bash
# Migrar dados do MySQL para PostgreSQL
php artisan migrate:mysql-to-postgres

# Executar testes
php artisan test

# Formatar código
vendor/bin/pint --dirty

# Limpar cache
php artisan cache:clear
php artisan config:clear
```

## Estrutura de Diretórios

```
app/
├── Console/Commands/     # Comandos Artisan customizados
├── Http/
│   ├── Controllers/      # Controllers da aplicação
│   └── Requests/         # Form Requests para validação
├── Models/               # Models Eloquent
└── Services/             # Serviços da aplicação

database/
├── factories/            # Factories para testes
├── migrations/           # Migrações do banco
└── seeders/              # Seeders

resources/
├── css/                  # Estilos CSS/Tailwind
├── js/                   # JavaScript
└── views/                # Blade templates

tests/
├── Feature/              # Testes de integração
└── Unit/                 # Testes unitários
```

## Testes

```bash
# Executar todos os testes
php artisan test

# Executar teste específico
php artisan test --filter=NomeDoTeste

# Executar com coverage
php artisan test --coverage
```

## Licença

Projeto proprietário - Prefeitura de Belo Horizonte
