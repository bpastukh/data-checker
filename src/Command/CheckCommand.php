<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ConnectionPool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckCommand extends Command
{
    protected static $defaultName = 'app:check';

    private ConnectionPool $connectionPool;

    public function __construct(ConnectionPool $connectionPool)
    {
        $this->connectionPool = $connectionPool;
        parent::__construct();
    }


    protected function configure(): void
    {
        $this->addArgument('connection', InputArgument::REQUIRED, 'Connection to check');
        $this->addArgument('table', InputArgument::REQUIRED, 'Table select where');
        $this->addArgument('where', InputArgument::REQUIRED, 'Where criteria');
        $this->addArgument('expectation', InputArgument::REQUIRED, 'Expectation to meet');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connectionName = (string)$input->getArgument('connection');
        $connection = $this->connectionPool->getConnection($connectionName);

        // table = eva_bet
        // where trackerId = 123
        // expectation usdProfitWithCommission = usdAmountWithCommission * 0.35
        $table = (string)$input->getArgument('table');
        $where = (string)$input->getArgument('where');
        $expectation = (string)$input->getArgument('expectation');
        $result = $connection->findWhereNotSatisfiesExpectations($table, $where, $expectation);

        if (count($result) > 0) {
            // notify
            dd($result);
        }

        return self::SUCCESS;
    }
}
