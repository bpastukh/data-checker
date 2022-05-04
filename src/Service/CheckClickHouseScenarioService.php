<?php

declare(strict_types=1);

namespace App\Service;

use ClickHouseDB\Client;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;

final class CheckClickHouseScenarioService implements CheckScenarioServiceInterface
{
    public function __construct(
        private GetScenarioService $getScenarioService,
        private Client $client,
        private NotifierInterface $notifier
    ) {
    }

    public function check(string $scenarioName): void
    {
        $scenario = $this->getScenarioService->byName($scenarioName);

        /** @var string $query */
        foreach ($scenario as $check => $query) {
            $result = $this->client->select($query)->rows();
            $resultCount = count($result);

            if ($resultCount > 0) {
                $this->notifier->send(
                    new Notification(date('Y-m-d H:i:s')." Check `$check` failed. Result: $resultCount")
                );
            }
        }
    }
}
