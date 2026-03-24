<?php

namespace App\Providers;

use App\Database\ProxyPgConnection;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

/**
 * Registra o driver pgsql_proxy para uso com DB Proxy HTTP.
 *
 * Permite usar DB_CONNECTION=pgsql_proxy no .env para
 * desenvolver fora da RMI com o banco acessado via proxy.
 */
class ProxyDatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Connection::resolverFor('pgsql_proxy', function ($connection, $database, $prefix, $config) {
            return new ProxyPgConnection($connection, $database, $prefix, $config);
        });
    }
}
