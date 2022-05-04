<?php

declare(strict_types=1);

namespace App\Tests\Unit\Command;

use App\Command\CheckClickHouseCommand;
use App\Service\CheckScenarioServiceInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;

final class CheckClickHouseCommandTest extends TestCase
{
    /**
     * @dataProvider callCheckDataProvider
     */
    public function testCallsCheck(string $executionTimes, int $shouldBeExecutedTimes): void
    {
        $scenarioName = 'test.yml';
        $checkScenarioService = $this->createMock(CheckScenarioServiceInterface::class);
        $checkScenarioService->expects($this->exactly($shouldBeExecutedTimes))->method('check')->with($this->equalTo($scenarioName));

        $command = new CheckClickHouseCommand(
            $checkScenarioService,
            $this->createStub(LoggerInterface::class)
        );

        $result = $command->run(
            new StringInput("$scenarioName -t $executionTimes -s 1"),
            new ConsoleOutput(),
        );

        $this->assertEquals(Command::SUCCESS, $result);
    }

    public function callCheckDataProvider(): array
    {
        return [
            ['1', 1],
            ['2', 2],
            // call once if times to execute parameter is less than 1
            ['0', 1],
        ];
    }
}
