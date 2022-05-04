<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\CheckClickHouseScenarioService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckClickHouseCommand extends Command
{
    protected static $defaultName = 'app:check:click-house';

    public function __construct(
        private CheckClickHouseScenarioService $checkClickHouseScenarioService,
        private LoggerInterface                $logger
    ) {
        parent::__construct();
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
        $scenarioName = (string) $input->getArgument('scenario');
        $this->logger->debug("Scenario name `$scenarioName`");
        $sleepTime = (int) $input->getOption('secondsBetweenQueries');
        if ($sleepTime <= 0) {
            $sleepTime = 1;
        }
        $timesToExecute = (int) $input->getOption('executeTimes');
        $executedTimes = 0;

        while (true) {
            $this->logger->debug("Checking scenario `$scenarioName`");
            $this->checkClickHouseScenarioService->check($scenarioName);
            $this->logger->debug("Scenario `$scenarioName` checked");

            $this->logger->debug("Sleeping for $sleepTime seconds");
            sleep($sleepTime);

            ++$executedTimes;
            $this->logger->debug("Executed $executedTimes of $timesToExecute times");
            if ($executedTimes === $timesToExecute) {
                break;
            }
        }

        return self::SUCCESS;
    }
}
