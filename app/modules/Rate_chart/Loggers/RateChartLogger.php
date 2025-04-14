<?php

namespace App\modules\Rate_chart\Loggers;

use App\Repository\Interfaces\RateChartRepositoryInterface;

class RateChartLogger
{
    protected RateChartRepositoryInterface $rateChartRepositoryInterface;

    public function __construct(RateChartRepositoryInterface $rateChartRepositoryInterface)
    {
        $this->rateChartRepositoryInterface = $rateChartRepositoryInterface;
    }

    public function createRate(array $rateData)
    {
        $rateData = $this->rateChartRepositoryInterface->create($rateData);
        return $rateData;
    }

    public function updateRateChart(int $rateId, array $updateData)
    {
        $resultData = $this->rateChartRepositoryInterface->updateById($rateId, $updateData);
        return $resultData;
    }
}
