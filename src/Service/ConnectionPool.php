<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Connection\ConnectionInterface;

final class ConnectionPool
{
    public function getConnection(string $connectionName): ConnectionInterface
    {
    }
}
