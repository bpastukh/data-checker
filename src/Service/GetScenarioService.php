<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\InvalidScenarioException;
use Symfony\Component\Yaml\Parser;
use Throwable;

final class GetScenarioService
{
    public function __construct(private string $scenariosDir, private Parser $parser)
    {
    }

    /**
     * @throws InvalidScenarioException
     */
    public function byName(string $name): array
    {
        try {
            return (array) $this->parser->parseFile("$this->scenariosDir/$name");
        } catch (Throwable $e) {
            throw new InvalidScenarioException($name, $e);
        }
    }
}
