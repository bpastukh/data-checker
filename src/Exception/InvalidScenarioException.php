<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

class InvalidScenarioException extends RuntimeException
{
    public function __construct(string $name)
    {
        parent::__construct("Scenario $name is invalid");
    }
}
