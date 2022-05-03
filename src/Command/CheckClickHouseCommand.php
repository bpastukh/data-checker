<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ScenarioService;
use ClickHouseDB\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;

final class CheckClickHouseCommand extends Command
{
    protected static $defaultName = 'app:check';

    private const COMMAND_PREFIX = '[App][Check][ClickHouse]';

    private NotifierInterface $notifier;

    private ScenarioService $scenarioService;

    private Client $client;

    public function __construct(
        NotifierInterface $notifier,
        ScenarioService $scenarioService,
        Client $client
    ) {
        parent::__construct();
        $this->notifier = $notifier;
        $this->scenarioService = $scenarioService;
        $this->client = $client;
    }

    protected function configure(): void
    {
        $this->addArgument('scenario', InputArgument::REQUIRED, 'Scenario to execute (with .yml)');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(self::COMMAND_PREFIX.' Command started');

        $scenarioName = (string) $input->getArgument('scenario');
        $output->writeln(self::COMMAND_PREFIX." Scenario name `$scenarioName`");
        $scenario = $this->scenarioService->getScenario($scenarioName);

        /** @var string $query */
        foreach ($scenario as $check => $query) {
            $output->writeln(self::COMMAND_PREFIX." Check `$check`. Query `$query`");

            $result = $this->client->select($query)->rows();
            $resultCount = count($result);
            $output->writeln(self::COMMAND_PREFIX." Check `$check`. Result count $resultCount");

            if ($resultCount > 0) {
                $output->writeln(self::COMMAND_PREFIX." Check `$check` failed. Sending notification");
                $this->notifier->send(new Notification("Check `$check` failed. Result: $resultCount"));
            }
        }

        $output->writeln(self::COMMAND_PREFIX.' Command finished');

        return self::SUCCESS;
    }
}
