<?php

declare(strict_types=1);

namespace App\Service\Connection;

use ClickHouseDB\Client;

final class ClickHouseConnection
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function findWhereNotSatisfiesExpectations(string $table, string $where): int
    {
        $query = <<<QUERY
            SELECT count() count from $table
            WHERE $where
        QUERY;

        return (int)$this->client->select($query)->fetchOne('count');
    }
}
