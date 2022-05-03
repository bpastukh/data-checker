<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\InvalidScenarioException;
use Symfony\Component\Yaml\Yaml;

final class ScenarioService
{
    private string $scenariosDir;

    public function __construct(string $scenariosDir)
    {
        $this->scenariosDir = $scenariosDir;
    }

    public function getScenario(string $name): array
    {
        $result = Yaml::parseFile("$this->scenariosDir/$name");

        if (is_array($result)) {
            return $result;
        }

        throw new InvalidScenarioException($name);
    }
}
