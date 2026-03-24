<?php

namespace App\Database;

use Illuminate\Database\PostgresConnection;
use Illuminate\Database\QueryException;

/**
 * Conexão PostgreSQL via HTTP Proxy.
 *
 * Substitui a conexão PDO direta por chamadas HTTP ao db-proxy.php
 * no servidor sufis. Permite desenvolver fora da RMI com Eloquent
 * funcionando normalmente.
 *
 * Configurar no .env:
 *   DB_CONNECTION=pgsql_proxy
 *   DB_PROXY_URL=https://sufis.pbh.gov.br/ginfi/poprua-geo/public/api/db-proxy.php
 *   DB_PROXY_TOKEN=35369933238fbb65105f2560fbf411a54080853f1f078fe1a2247626c96e6a7a
 */
class ProxyPgConnection extends PostgresConnection
{
    protected string $proxyUrl;
    protected string $proxyToken;
    protected int $proxyTimeout;

    public function __construct($pdo, $database = '', $tablePrefix = '', array $config = [])
    {
        $this->proxyUrl = $config['proxy_url'] ?? '';
        $this->proxyToken = $config['proxy_token'] ?? '';
        $this->proxyTimeout = $config['proxy_timeout'] ?? 30;

        // Passa null como PDO - não precisamos de conexão real
        parent::__construct(function () { return null; }, $database, $tablePrefix, $config);
    }

    /**
     * SELECT queries.
     */
    public function select($query, $bindings = [], $useReadPdo = true)
    {
        return $this->proxyRun($query, $bindings, function ($query, $bindings) {
            $result = $this->proxyRequest($query, $bindings);
            return $result['data'] ?? [];
        });
    }

    /**
     * SELECT retornando cursor (usa select normal via proxy).
     */
    public function cursor($query, $bindings = [], $useReadPdo = true)
    {
        $results = $this->select($query, $bindings, $useReadPdo);
        foreach ($results as $result) {
            yield (object) $result;
        }
    }

    /**
     * INSERT query.
     */
    public function insert($query, $bindings = [])
    {
        return $this->proxyStatement($query, $bindings);
    }

    /**
     * UPDATE query.
     */
    public function update($query, $bindings = [])
    {
        return $this->proxyAffecting($query, $bindings);
    }

    /**
     * DELETE query.
     */
    public function delete($query, $bindings = [])
    {
        return $this->proxyAffecting($query, $bindings);
    }

    /**
     * Execute a statement (CREATE, ALTER, etc.)
     */
    public function statement($query, $bindings = [])
    {
        return $this->proxyStatement($query, $bindings);
    }

    /**
     * Execute an affecting statement (returns affected rows).
     */
    public function affectingStatement($query, $bindings = [])
    {
        return $this->proxyAffecting($query, $bindings);
    }

    /**
     * Run a statement and return boolean.
     */
    protected function proxyStatement($query, $bindings)
    {
        return $this->proxyRun($query, $bindings, function ($query, $bindings) {
            $this->proxyRequest($query, $bindings);
            return true;
        });
    }

    /**
     * Run a statement and return affected rows.
     */
    protected function proxyAffecting($query, $bindings)
    {
        return $this->proxyRun($query, $bindings, function ($query, $bindings) {
            $result = $this->proxyRequest($query, $bindings);
            return $result['affected_rows'] ?? 0;
        });
    }

    /**
     * Wraps callback with query logging and error handling.
     */
    protected function proxyRun($query, $bindings, \Closure $callback)
    {
        $start = microtime(true);

        try {
            $result = $callback($query, $bindings);
        } catch (\Exception $e) {
            throw new QueryException(
                $this->getName(),
                $query,
                $this->prepareBindings($bindings),
                $e
            );
        }

        $this->logQuery($query, $bindings, $this->getElapsedTime($start));

        return $result;
    }

    /**
     * HTTP request to the proxy.
     */
    protected function proxyRequest(string $query, array $bindings): array
    {
        // Converter bindings do Laravel (? placeholders) para posicional ($1, $2)
        $prepared = $this->convertBindings($query, $bindings);

        $payload = [
            'token'  => $this->proxyToken,
            'query'  => $prepared['query'],
            'params' => $prepared['params'],
            'limit'  => 0, // Sem limite automático — o Laravel controla
        ];

        $ch = curl_init($this->proxyUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',
                'Accept: application/json, text/plain, */*',
                'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                'Origin: https://sufis.pbh.gov.br',
                'Referer: https://sufis.pbh.gov.br/ginfi/poprua-geo/public/',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->proxyTimeout,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \RuntimeException("DB Proxy curl error: $error");
        }

        $data = json_decode($response, true);

        if (!$data) {
            throw new \RuntimeException("DB Proxy invalid response (HTTP $httpCode): " . substr($response, 0, 500));
        }

        if (!empty($data['error'])) {
            throw new \RuntimeException("DB Proxy: " . $data['error']);
        }

        return $data;
    }

    /**
     * Converte query com ? placeholders para query com valores inline
     * (o proxy já usa prepared statements internamente).
     */
    protected function convertBindings(string $query, array $bindings): array
    {
        if (empty($bindings)) {
            return ['query' => $query, 'params' => []];
        }

        // Substituir ? por $1, $2, $3... (PostgreSQL positional params)
        $index = 0;
        $converted = preg_replace_callback('/\?/', function () use (&$index) {
            $index++;
            return '$' . $index;
        }, $query);

        // Converter tipos para valores adequados
        $params = array_values(array_map(function ($value) {
            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }
            return $value;
        }, $bindings));

        return ['query' => $converted, 'params' => $params];
    }

    /**
     * Não precisamos de PDO real para reconnect.
     */
    public function reconnect()
    {
        // noop — proxy é stateless
    }

    public function disconnect()
    {
        // noop
    }

    /**
     * Retorna o nome da conexão.
     */
    public function getName()
    {
        return $this->getConfig('name') ?? 'pgsql_proxy';
    }
}
