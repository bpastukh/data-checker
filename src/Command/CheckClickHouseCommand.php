<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Connection\ClickHouseConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;

final class CheckClickHouseCommand extends Command
{
    protected static $defaultName = 'app:check';

    private ClickHouseConnection $connection;

    private NotifierInterface $notifier;

    public function __construct(ClickHouseConnection $connectionPool, NotifierInterface $notifier)
    {
        parent::__construct();
        $this->connection = $connectionPool;
        $this->notifier = $notifier;
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

        $table = (string)$input->getArgument('table');
        $where = (string)$input->getArgument('where');
        $result = $this->connection->findWhereNotSatisfiesExpectations($table, $where);

        if ($result > 0) {
            $this->notifier->send(
                new Notification("SELECT FROM $table WHERE $where. Result: $result")
            );
        }

        return self::SUCCESS;
    }
}
