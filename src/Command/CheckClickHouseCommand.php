<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ScenarioService;
use ClickHouseDB\Client;
use Psr\Log\LoggerInterface;
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

    private LoggerInterface $logger;

    public function __construct(
        NotifierInterface $notifier,
        ScenarioService   $scenarioService,
        Client            $client,
        LoggerInterface   $logger
    ) {
        parent::__construct();
        $this->notifier = $notifier;
        $this->scenarioService = $scenarioService;
        $this->client = $client;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this->addArgument('scenario', InputArgument::REQUIRED, 'Scenario to execute (with .yml)');
        $this->addOption(
            'secondsBetweenQueries',
            's',
            InputArgument::OPTIONAL,
            'Seconds between queries to sleep.',
            1
        );
        $this->addOption(
            'executeTimes',
            't',
            InputArgument::OPTIONAL,
            'Run scenario times.',
            100
        );
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->logger->debug(self::COMMAND_PREFIX.' Command started');

        $scenarioName = (string) $input->getArgument('scenario');
        $this->logger->debug(self::COMMAND_PREFIX." Scenario name `$scenarioName`");
        $scenario = $this->scenarioService->getScenario($scenarioName);
        $executedTimes = 0;

        while (true) {
            /** @var string $query */
            foreach ($scenario as $check => $query) {
                $this->logger->debug(self::COMMAND_PREFIX." Check `$check`. Query `$query`");

                $result = $this->client->select($query)->rows();
                $resultCount = count($result);
                $this->logger->debug(self::COMMAND_PREFIX." Check `$check`. Result count $resultCount");

                if ($resultCount > 0) {
                    $this->logger->debug(self::COMMAND_PREFIX." Check `$check` failed. Sending notification");
                    $this->notifier->send(
                        new Notification(date('Y-m-d H:i:s')." Check `$check` failed. Result: $resultCount")
                    );
                }

                sleep((int) $input->getOption('secondsBetweenQueries'));
            }

            $executedTimes++;
            if ($executedTimes === (int) $input->getOption('executeTimes')) {
                break;
            }
        }

        $this->logger->debug(self::COMMAND_PREFIX.' Command finished');

        return self::SUCCESS;
    }
}
