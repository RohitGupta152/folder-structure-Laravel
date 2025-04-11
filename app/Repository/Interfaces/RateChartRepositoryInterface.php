<?php

namespace App\Repository\Interfaces;


interface RateChartRepositoryInterface
{

    public function getRateForWeight(float $weight, int $userId);

}
