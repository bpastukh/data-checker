<?php

declare(strict_types=1);

namespace App\Service\Connection;

final class ClickHouseConnection implements ConnectionInterface
{
    public function findWhereNotSatisfiesExpectations(string $table, string $where, string $expectation): array
    {
        // TODO: Implement findWhereNotSatisfiesExpectations() method.
    }
}
