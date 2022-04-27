<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

class ConnectionDoesNotExistException extends RuntimeException
{
    public function __construct(string $connectionName)
    {
        parent::__construct("Connection $connectionName does not exist");
    }
}
