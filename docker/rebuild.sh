#!/bin/bash
# ============================================
# Script de rebuild do container PopRua Geo
# Executar no host: sudo bash rebuild.sh
# ============================================

set -e

COMPOSE_DIR="/opt/docker/poprua-geo"
APP_DIR="/var/www/html/joomla_sufis/ginfi/poprua-geo"
CONTAINER="php84-poprua-geo"

echo "=== PopRua Geo - Rebuild ==="
echo ""

cd "$COMPOSE_DIR"

# 1. Criar diretorios de volume
echo "[1/6] Criando diretorios de volume..."
mkdir -p claude-data ssh-data

# 2. Salvar dados do container atual
echo "[2/6] Salvando dados do container atual..."
if docker ps --format '{{.Names}}' | grep -q "^${CONTAINER}$"; then
    docker cp ${CONTAINER}:/root/.claude/. claude-data/ 2>/dev/null || echo "  (sem dados .claude para copiar)"
    docker cp ${CONTAINER}:/root/.ssh/. ssh-data/ 2>/dev/null || echo "  (sem dados .ssh para copiar)"
    chmod 700 ssh-data
    chmod 600 ssh-data/id_rsa 2>/dev/null || true
else
    echo "  Container nao esta rodando, pulando copia"
fi

# 3. Copiar Dockerfile atualizado do repo
echo "[3/6] Copiando Dockerfile do repositorio..."
cp "${APP_DIR}/docker/Dockerfile" "${COMPOSE_DIR}/Dockerfile"

# 4. Gerar docker-compose.yml
echo "[4/6] Gerando docker-compose.yml..."
cat > "${COMPOSE_DIR}/docker-compose.yml" << 'YAML'
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    image: php84-poprua-geo:local
    container_name: php84-poprua-geo
    restart: unless-stopped
    ports:
      - "127.0.0.1:9084:9000"
    volumes:
      - /var/www/html/joomla_sufis/ginfi/poprua-geo:/var/www/html/joomla_sufis/ginfi/poprua-geo
      - /opt/docker/poprua-geo/claude-data:/root/.claude
      - /opt/docker/poprua-geo/ssh-data:/root/.ssh
    environment:
      PHP_MEMORY_LIMIT: 256M
      PHP_MAX_EXECUTION_TIME: 300
      PHP_UPLOAD_MAX_FILE_SIZE: 64M
      PHP_POST_MAX_SIZE: 64M
    depends_on:
      db:
        condition: service_healthy
      redis:
        condition: service_healthy
    deploy:
      resources:
        limits:
          memory: 512M
          cpus: '1.0'
    networks: [poprua-geo]

  db:
    image: postgis/postgis:17-3.5
    container_name: pg17-poprua-geo
    restart: unless-stopped
    ports:
      - "127.0.0.1:5433:5432"
    volumes:
      - /opt/docker/poprua-geo/postgres-data:/var/lib/postgresql/data
    environment:
      POSTGRES_DB: poprua_geo
      POSTGRES_USER: poprua
      POSTGRES_PASSWORD: ${POSTGRES_PASSWORD}
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U poprua -d poprua_geo"]
      interval: 10s
      timeout: 5s
      retries: 5
    deploy:
      resources:
        limits:
          memory: 512M
    networks: [poprua-geo]

  redis:
    image: redis:7-alpine
    container_name: redis-poprua-geo
    restart: unless-stopped
    ports:
      - "127.0.0.1:6379:6379"
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 10s
      timeout: 5s
      retries: 5
    deploy:
      resources:
        limits:
          memory: 128M
    networks: [poprua-geo]

networks:
  poprua-geo:
    driver: bridge
YAML

# 5. Rebuild
echo "[5/6] Rebuild do container (pode demorar)..."
docker compose up -d --build

# 6. Reinstalar dependencias
echo "[6/6] Reinstalando dependencias PHP..."
docker exec ${CONTAINER} composer install --no-interaction --quiet 2>/dev/null || echo "  (composer install falhou ou nao necessario)"

echo ""
echo "=== Rebuild concluido! ==="
echo ""
docker ps --filter "name=poprua-geo" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
echo ""
echo "Acesso: ssh sufis -> sudo docker exec -it -u root php84-poprua-geo bash"
