<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Exception\InvalidScenarioException;
use App\Service\GetScenarioService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

final class ScenarioServiceTest extends TestCase
{
    public function testGetsScenario(): void
    {
        $directoryName = 'dummyDirectory';
        $scenarioName = 'dummyFile.yml';
        $dummyResult = ['key' => 'value'];
        $parserMock = $this->createMock(Parser::class);
        $parserMock
            ->expects($this->once())
            ->method('parseFile')
            ->with($this->equalTo("$directoryName/$scenarioName"))
            ->willReturn($dummyResult);
        $service = new GetScenarioService($directoryName, $parserMock);

        $result = $service->byName($scenarioName);

        $this->assertSame($dummyResult, $result);
    }

    public function testGetScenarioInvalidScenarioExceptionIsThrownOnParseException(): void
    {
        $directoryName = 'dummyDirectory';
        $scenarioName = 'dummyFile.yml';

        $parserMock = $this->createMock(Parser::class);
        $parserMock
            ->expects($this->once())
            ->method('parseFile')
            ->willThrowException(new ParseException(''));
        $service = new GetScenarioService($directoryName, $parserMock);

        $this->expectException(InvalidScenarioException::class);

        $service->byName($scenarioName);
    }
}
