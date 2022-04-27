<?php

namespace App\Service\Connection;

interface ConnectionInterface
{
    public function findWhereNotSatisfiesExpectations(string $table, string $where, string $expectation): array;
}
