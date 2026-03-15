# POPRUA v2 - Sistema de Gestão de População em Situação de Rua

**Data atual: Março de 2026**

Sistema para gestão de vistorias e monitoramento de população em situação de rua em Belo Horizonte.

## Stack Tecnológica

| Componente | Versão |
|------------|--------|
| PHP | 8.4 |
| Laravel | 12 |
| PostgreSQL | 17 |
| PostGIS | 3.5 |
| Redis | - |
| Node.js | - |

## Banco de Dados

O sistema utiliza **PostgreSQL 17 com PostGIS 3.5** para suporte completo a dados geoespaciais.

### Infraestrutura (Docker em `vlcp-sufis01`)

| Container | Imagem | Porta Host |
|-----------|--------|------------|
| `pg17-poprua-geo` | `postgis/postgis:17-3.5` | 5433 |
| `redis-poprua-geo` | `redis:7-alpine` | 6379 |
| `php84-poprua-geo` | `php84-poprua-geo:local` | 9084 |

### Conexão Principal (PostgreSQL)

**Produção (dentro do container):**
```
DB_CONNECTION=pgsql
DB_HOST=pg17-poprua-geo
DB_PORT=5432
DB_DATABASE=poprua_geo
DB_USERNAME=poprua
DB_PASSWORD=poprua_secret
```

**Desenvolvimento local (acesso via host):**
```
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5433
DB_DATABASE=poprua_geo
DB_USERNAME=poprua
DB_PASSWORD=poprua_secret
```

### Tabelas com Dados Espaciais

| Tabela | Tipo Geometria | Índice GIST |
|--------|---------------|-------------|
| pontos | POINT | idx_pontos_geom |
| endereco_atualizados | POINT | idx_endereco_atualizados_geom |
| geo_bairros | MULTIPOLYGON | idx_geo_bairros_geom |
| geo_regionais | MULTIPOLYGON | idx_geo_regionais_geom |
| geo_limite_municipio | GEOMETRY | idx_geo_limite_municipio_geom |

### Consultas Espaciais Comuns

```sql
-- Pontos por bairro
SELECT gb.nome, COUNT(p.id)
FROM geo_bairros gb
JOIN pontos p ON ST_Contains(gb.geom, p.geom)
GROUP BY gb.nome;

-- Pontos em raio de 500m
SELECT id, ST_Distance(geom::geography, ponto::geography) as metros
FROM pontos
WHERE ST_DWithin(geom::geography, ponto::geography, 500);

-- Bairro de um ponto
SELECT gb.nome FROM geo_bairros gb
WHERE ST_Contains(gb.geom, ST_SetSRID(ST_MakePoint(lng, lat), 4326));
```

## Credenciais de Teste

- **Email:** murtafilho@gmail.com
- **Senha:** xman74102

## Comandos Úteis

```bash
# Migrar dados do MySQL para PostgreSQL
php artisan migrate:mysql-to-postgres

# Executar migrações
php artisan migrate

# Executar testes
php artisan test

# Formatar código
vendor/bin/pint --dirty
```

---

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application.

## Foundational Context
- php - 8.4
- laravel/framework (LARAVEL) - v12
- PostgreSQL - 17 com PostGIS 3.5
- phpunit/phpunit (PHPUNIT) - v11

## Conventions
- Follow all existing code conventions. Check sibling files for correct structure, approach, naming.
- Use descriptive names for variables and methods.
- Check for existing components to reuse before writing a new one.

## Application Structure & Architecture
- Stick to existing directory structure.
- Do not change dependencies without approval.

## Database (PostgreSQL + PostGIS)
- Use Eloquent models and relationships.
- Para consultas espaciais, use funções PostGIS: ST_Contains, ST_Within, ST_Distance, ST_DWithin.
- Todas as geometrias usam SRID 4326 (WGS84).
- Coluna `geom` contém geometria nativa, `lat/lng` são campos auxiliares.

=== boost rules ===

## Laravel Boost
- Use Laravel Boost MCP tools for database queries and debugging.

## Artisan
- Use `list-artisan-commands` tool to check available parameters.

## URLs
- Use `get-absolute-url` tool for correct scheme/domain/port.

## Tinker / Debugging
- Use `tinker` tool to execute PHP and query Eloquent models.
- Use `database-query` tool for read-only database queries.

=== php rules ===

## PHP
- Always use curly braces for control structures.
- Use PHP 8 constructor property promotion.
- Always use explicit return type declarations.

=== laravel/core rules ===

## Do Things the Laravel Way
- Use `php artisan make:` commands.
- Pass `--no-interaction` to all Artisan commands.

### Database
- Use proper Eloquent relationship methods with return type hints.
- Avoid `DB::`; prefer `Model::query()`.
- Use eager loading to prevent N+1 problems.

### Controllers & Validation
- Always create Form Request classes for validation.

### Testing
- Use factories for models in tests.
- Use `php artisan make:test --phpunit {name}` for new tests.

=== pint/core rules ===

## Laravel Pint Code Formatter
- Run `vendor/bin/pint --dirty` before finalizing changes.

=== phpunit/core rules ===

## PHPUnit Core
- All tests must be PHPUnit classes.
- Run `php artisan test --filter=testName` after changes.
</laravel-boost-guidelines>

## Documentacao

| Documento | Descricao |
|-----------|-----------|
| [docs/ARQUITETURA_DOCKER.md](docs/ARQUITETURA_DOCKER.md) | Arquitetura Docker completa: fluxo de requisicoes, containers, rede isolada, operacoes do dia a dia |
| [docs/Identificacao de Pessoas em Situacao de Rua.md](docs/Identificação%20de%20Pessoas%20em%20Situação%20de%20Rua.md) | Documento de referencia do projeto PopRua |
