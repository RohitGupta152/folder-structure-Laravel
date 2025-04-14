<?php

namespace App\Repository\Interfaces;

use App\Models\RateChart;

interface RateChartRepositoryInterface
{

    public function getRateForWeight(float $weight, int $userId);

    public function getByUserId(int $userId);
    public function create(array $data): RateChart;
    public function getRates(array $filters);
    public function findByRateId(int $rateId);
    public function updateById(int $rateId, array $updateData): bool;
    public function deleteById(int $rateId): bool;
}
