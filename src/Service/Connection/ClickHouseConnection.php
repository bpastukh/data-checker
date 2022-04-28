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

    public function findWhereNotSatisfiesExpectations(string $table, string $where): array
    {
        $query = <<<QUERY
            SELECT * from $table
            $where
        QUERY;

        return $this->client->select($query)->rows();
    }
}
