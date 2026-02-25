# POPRUA v2 - Sistema de Gestão de População em Situação de Rua

**Data atual: Fevereiro de 2026**

Sistema para gestão de vistorias e monitoramento de população em situação de rua em Belo Horizonte.

## Stack Tecnológica

| Componente | Versão |
|------------|--------|
| PHP | 8.4 |
| Laravel | 12 |
| PostgreSQL | 16 |
| PostGIS | 3.4 |
| Redis | - |
| Node.js | - |

## Banco de Dados

O sistema utiliza **PostgreSQL 16 com PostGIS 3.4** para suporte completo a dados geoespaciais.

### Conexão Principal (PostgreSQL)
```
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=popruav2
DB_USERNAME=postgres
DB_PASSWORD=xman74102
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
- PostgreSQL - 16 com PostGIS 3.4
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
