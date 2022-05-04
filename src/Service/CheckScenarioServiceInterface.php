<?php

namespace App\Service;

interface CheckScenarioServiceInterface
{
    public function check(string $scenarioName): void;
}
