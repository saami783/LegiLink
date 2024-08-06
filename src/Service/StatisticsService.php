<?php

// src/Service/StatisticsService.php

namespace App\Service;

use App\Entity\User;
use App\Repository\ApiExecutionRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class StatisticsService
{

    public function __construct(private ApiExecutionRepository $executionRepository)
    { }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getStatistics(User $user, int $dailyRequestLimit): array
    {
        $now = new \DateTimeImmutable('now');
        $oneDayAgo = $now->sub(new \DateInterval('P1D'));
        $sevenDaysAgo = $now->sub(new \DateInterval('P7D'));
        $thirtyDaysAgo = $now->sub(new \DateInterval('P30D'));

        $stats = [
            'last_24_hours' => [
                'executions' => $this->executionRepository->countExecutionsForPeriod($oneDayAgo, $now, $user),
                'requests' => $this->executionRepository->countRequestsForPeriod($oneDayAgo, $now, $user),
                'remaining_requests' => $dailyRequestLimit - $this->executionRepository->countRequestsForPeriod($oneDayAgo, $now, $user)
            ],
            'last_7_days' => [
                'executions' => $this->executionRepository->countExecutionsForPeriod($sevenDaysAgo, $now, $user),
                'requests' => $this->executionRepository->countRequestsForPeriod($sevenDaysAgo, $now, $user),
            ],
            'last_30_days' => [
                'executions' => $this->executionRepository->countExecutionsForPeriod($thirtyDaysAgo, $now, $user),
                'requests' => $this->executionRepository->countRequestsForPeriod($thirtyDaysAgo, $now, $user),
            ],
        ];

        $stats['last_24_hours']['remaining_requests'] = max($stats['last_24_hours']['remaining_requests'], 0);

        return $stats;
    }

}
