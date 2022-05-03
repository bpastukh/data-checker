<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;
use Throwable;

class InvalidScenarioException extends RuntimeException
{
    public function __construct(string $name, null|Throwable $previous = null)
    {
        parent::__construct("Scenario $name is invalid", 0, $previous);
    }
}
