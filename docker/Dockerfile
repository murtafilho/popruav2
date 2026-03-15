FROM serversideup/php:8.4-fpm-nginx

USER root

# Extensoes PHP
RUN docker-php-ext-install exif

# GitHub CLI
RUN curl -fsSL https://cli.github.com/packages/githubcli-archive-keyring.gpg \
        -o /usr/share/keyrings/githubcli-archive-keyring.gpg \
    && chmod go+r /usr/share/keyrings/githubcli-archive-keyring.gpg \
    && echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/githubcli-archive-keyring.gpg] https://cli.github.com/packages stable main" \
        > /etc/apt/sources.list.d/github-cli.list \
    && apt-get update -qq \
    && apt-get install -y --no-install-recommends gh \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Git safe directory (bind mount pertence a outro usuario)
RUN git config --global --add safe.directory /var/www/html/joomla_sufis/ginfi/poprua-geo

# uv (Python package runner) - necessario para MCP openstreetmap
RUN curl -LsSf https://astral.sh/uv/install.sh | sh

# Claude Code (AI assistant CLI) - instalacao nativa recomendada
# 1. Remove instalacao npm se existir (evita conflito)
# 2. Instala via script nativo em ~/.local/bin/claude
# 3. Adiciona ao PATH permanentemente
RUN npm uninstall -g @anthropic-ai/claude-code 2>/dev/null || true \
    && curl -fsSL https://claude.ai/install.sh | bash \
    && echo 'export PATH="$HOME/.local/bin:$PATH"' >> /root/.bashrc

ENV PATH="/root/.local/bin:${PATH}"

USER www-data
