---
name: setup-ambiente
description: Configurar e diagnosticar o ambiente de desenvolvimento e produção do POPRUA v2. Use quando o usuário pedir para verificar, configurar ou corrigir o ambiente.
user-invocable: true
allowed-tools: Read, Grep, Bash, Write, Edit
argument-hint: [diagnosticar|corrigir|backup|tuning]
---

# Skill: Setup do Ambiente POPRUA v2

Use esta skill para configurar, diagnosticar e corrigir o ambiente do projeto.

## Infraestrutura

### Servidor de Produção (sufis)

```
SSH: ssh sufis (10.0.25.8, user: cassio.martins)
Path: /var/www/html/joomla_sufis/ginfi/poprua-geo
```

| Container | Imagem | Porta | Função |
|-----------|--------|-------|--------|
| `php84-poprua-geo` | `php84-poprua-geo:local` (base: `serversideup/php:8.4-fpm-nginx`) | `127.0.0.1:9084→9000` | App Laravel 12 |
| `pg17-poprua-geo` | `postgis/postgis:17-3.5` | `127.0.0.1:5433→5432` | PostgreSQL 17 + PostGIS 3.5 |
| `redis-poprua-geo` | `redis:7-alpine` | `127.0.0.1:6379→6379` | Cache, Session, Queue |

### Banco de Dados (Produção)

```
Host: db (interno Docker, porta 5433 no host)
Database: poprua_geo
User: poprua
Password: poprua_secret
Tamanho: ~344 MB
Dados espaciais: SRID 4326 (WGS84)
```

### Ambiente Local (Container de Dev)

```
Host DB: db:5432
Host Redis: redis:6379
Container: dentro da rede poprua-geo_poprua-geo
```

## Comandos de Diagnóstico

### 1. Verificar saúde dos containers

```bash
ssh sufis "sudo docker ps --format 'table {{.Names}}\t{{.Status}}\t{{.Ports}}' | grep poprua"
```

### 2. Verificar espaço em disco

```bash
ssh sufis "df -h /opt/docker/poprua-geo/postgres-data"
```

### 3. Verificar tamanho do banco

```bash
ssh sufis "sudo docker exec pg17-poprua-geo psql -U poprua -d poprua_geo -c \"SELECT pg_size_pretty(pg_database_size('poprua_geo'));\""
```

### 4. Verificar conexões ativas

```bash
ssh sufis "sudo docker exec pg17-poprua-geo psql -U poprua -d poprua_geo -c \"SELECT count(*) FROM pg_stat_activity WHERE datname='poprua_geo';\""
```

### 5. Verificar Redis

```bash
ssh sufis "sudo docker exec redis-poprua-geo redis-cli info memory | grep used_memory_human"
```

### 6. Verificar logs da aplicação

```bash
ssh sufis "sudo docker exec php84-poprua-geo tail -50 /var/www/html/joomla_sufis/ginfi/poprua-geo/storage/logs/laravel.log"
```

## Ações de Correção

### Corrigir .env de Produção

O `.env` de produção deve ter:

```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error
```

**NUNCA** usar `APP_DEBUG=true` em produção — expõe stack traces, queries e credenciais.

### Configurar Backup Automático

Criar cron no servidor sufis:

```bash
# Criar diretório de backup
sudo mkdir -p /opt/docker/poprua-geo/backups

# Adicionar ao crontab (diário às 3h, mantém 7 dias)
echo "0 3 * * * docker exec pg17-poprua-geo pg_dump -U poprua -Fc poprua_geo > /opt/docker/poprua-geo/backups/poprua_geo_\$(date +\%Y\%m\%d).dump && find /opt/docker/poprua-geo/backups -mtime +7 -delete" | sudo tee /etc/cron.d/backup-poprua-geo
```

### Backup Manual

```bash
ssh sufis "sudo docker exec pg17-poprua-geo pg_dump -U poprua -Fc poprua_geo > /opt/docker/poprua-geo/backups/poprua_geo_manual_$(date +%Y%m%d_%H%M).dump"
```

### Restaurar Backup

```bash
ssh sufis "sudo docker exec -i pg17-poprua-geo pg_restore -U poprua -d poprua_geo --clean --if-exists < /opt/docker/poprua-geo/backups/ARQUIVO.dump"
```

## Tuning do PostgreSQL

### Configuração Recomendada

Servidor: 8 CPUs, 8 GB RAM, banco com queries espaciais PostGIS.

```sql
-- Verificar config atual
SHOW shared_buffers;
SHOW work_mem;
SHOW effective_cache_size;
SHOW maintenance_work_mem;
```

| Parâmetro | Atual | Recomendado | Motivo |
|-----------|-------|-------------|--------|
| `shared_buffers` | 128MB | **2GB** | 25% da RAM |
| `work_mem` | 4MB | **32MB** | Queries espaciais pesadas |
| `maintenance_work_mem` | 64MB | **512MB** | VACUUM e índices GIST |
| `effective_cache_size` | 4GB | **6GB** | 75% da RAM |
| `random_page_cost` | 4.0 | **1.1** | Disco SSD |
| `wal_buffers` | -1 | **64MB** | Escrita otimizada |

### Aplicar Tuning

```bash
ssh sufis "sudo docker exec pg17-poprua-geo psql -U poprua -d poprua_geo -c \"
ALTER SYSTEM SET shared_buffers = '2GB';
ALTER SYSTEM SET work_mem = '32MB';
ALTER SYSTEM SET maintenance_work_mem = '512MB';
ALTER SYSTEM SET effective_cache_size = '6GB';
ALTER SYSTEM SET random_page_cost = 1.1;
ALTER SYSTEM SET wal_buffers = '64MB';
ALTER SYSTEM SET checkpoint_completion_target = 0.9;
\""
# Requer restart do container
ssh sufis "sudo docker restart pg17-poprua-geo"
```

## Limpeza de Disco

### Imagens Docker não usadas

```bash
ssh sufis "sudo docker image prune -f"
```

### Build cache

```bash
ssh sufis "sudo docker builder prune -f"
```

### Verificar espaço total do Docker

```bash
ssh sufis "sudo docker system df -v"
```

## Checklist de Configuração

### Produção
- [ ] `APP_ENV=production` e `APP_DEBUG=false`
- [ ] Backup automático do banco configurado
- [ ] PostgreSQL com tuning adequado
- [ ] Disco com pelo menos 20% livre
- [ ] Redis com prefixo para evitar colisão com outros apps
- [ ] Supervisor com caminho correto para queue worker
- [ ] Logs em nível `error` (não `debug`)

### Desenvolvimento
- [ ] Containers rodando (php84, pg17, redis)
- [ ] `.env` com credenciais corretas
- [ ] Migrações executadas (`php artisan migrate`)
- [ ] Cache limpo (`php artisan config:clear`)
- [ ] Node modules instalados (`npm install`)
- [ ] Assets compilados (`npm run build`)

## Instruções para o Agente

Ao receber `$ARGUMENTS`:

- **diagnosticar**: Executar todos os comandos de diagnóstico e reportar status
- **corrigir**: Identificar e corrigir problemas encontrados (pedir confirmação antes)
- **backup**: Executar backup manual ou configurar backup automático
- **tuning**: Verificar e aplicar tuning do PostgreSQL (pedir confirmação antes de reiniciar)
- **sem argumento**: Executar diagnóstico completo e sugerir correções
