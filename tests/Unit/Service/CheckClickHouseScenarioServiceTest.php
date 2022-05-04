<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\CheckClickHouseScenarioService;
use App\Service\GetScenarioService;
use ClickHouseDB\Client;
use ClickHouseDB\Statement;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Yaml\Parser;

final class CheckClickHouseScenarioServiceTest extends TestCase
{
    public function testCheckOnRowsEmptyNotifierSendIsNotCalled(): void
    {
        $dummyScenario = ['key' => 'value'];
        $parser = $this->createStub(Parser::class);
        $parser->method('parseFile')->willReturn($dummyScenario);
        $getScenarioService = new GetScenarioService('', $parser);

        $statement = $this->createStub(Statement::class);
        $statement->method('rows')->willReturn([]);
        $client = $this->createMock(Client::class);
        $client->expects($this->once())->method('select')
            ->with($this->equalTo($dummyScenario['key']))
            ->willReturn($statement);

        $notifier = $this->createMock(NotifierInterface::class);
        $notifier->expects($this->never())->method('send');

        $service = new CheckClickHouseScenarioService($getScenarioService, $client, $notifier);

        $service->check('dummy.yml');
    }

    public function testCheckOnRowsNotEmptyNotifierSendIsCalled(): void
    {
        $dummyScenario = ['key' => 'value'];
        $parser = $this->createStub(Parser::class);
        $parser->method('parseFile')->willReturn($dummyScenario);
        $getScenarioService = new GetScenarioService('', $parser);

        $statement = $this->createStub(Statement::class);
        $statement->method('rows')->willReturn([1]);
        $client = $this->createMock(Client::class);
        $client->expects($this->once())->method('select')
            ->with($this->equalTo($dummyScenario['key']))
            ->willReturn($statement);

        $notifier = $this->createMock(NotifierInterface::class);
        $notifier->expects($this->once())
            ->method('send')->with($this->isInstanceOf(Notification::class));

        $service = new CheckClickHouseScenarioService($getScenarioService, $client, $notifier);

        $service->check('dummy.yml');
    }
}
