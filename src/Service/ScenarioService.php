<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\InvalidScenarioException;
use Symfony\Component\Yaml\Yaml;
use Throwable;

final class ScenarioService
{
    private string $scenariosDir;

    public function __construct(string $scenariosDir)
    {
        $this->scenariosDir = $scenariosDir;
    }

    /**
     * @throws InvalidScenarioException
     */
    public function getScenario(string $name): array
    {
        try {
            return (array) Yaml::parseFile("$this->scenariosDir/$name");
        } catch (Throwable $e) {
            throw new InvalidScenarioException($name, $e);
        }
    }
}
