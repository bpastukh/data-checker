<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Connection\ClickHouseConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckClickHouseCommand extends Command
{
    protected static $defaultName = 'app:check';

    private ClickHouseConnection $connection;

    public function __construct(ClickHouseConnection $connectionPool)
    {
        $this->connection = $connectionPool;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('table', InputArgument::REQUIRED, 'Table select where');
        $this->addArgument('where', InputArgument::REQUIRED, 'Where criteria');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // table = eva_bet
        // where trackerId = 123
        // expectation usdProfitWithCommission = usdAmountWithCommission * 0.35
        // select * from eva_bet where trackerId = 123 and usdProfitWithCommission = usdAmountWithCommission * 0.35


        // select filtered = 1 where player id = 111
        $table = (string)$input->getArgument('table');
        $where = (string)$input->getArgument('where');
        $result = $this->connection->findWhereNotSatisfiesExpectations($table, $where);

        if (count($result) > 0) {
            // notify
            dd($result);
        }

        return self::SUCCESS;
    }
}
