<?php


namespace App\Repository;


use App\Models\User;
use App\Models\RateChart;

use App\Repository\Interfaces\RateChartRepositoryInterface;

class RateChartRepository implements RateChartRepositoryInterface
{

    public function getRateForWeight(float $weight, int $userId)
    {
        $userRates = RateChart::whereIn('user_id', [$userId, 0])
            ->where('weight', $weight)
            ->get();
        return $userRates;
    }


    public function getByUserId(int $userId)
    {
        return RateChart::where('user_id', $userId)->get();
    }

    public function create(array $data): RateChart
    {
        return RateChart::create($data);
    }

    public function getRates(array $filters)
    {
        $query = RateChart::query()->where('user_id', '!=', 0);

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['weight'])) {
            $query->where('weight', $filters['weight']);
        }

        if (!empty($filters['created_date'])) {
            $dates = explode(' ', $filters['created_date']);

            if (count($dates) === 2) {
                $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
                $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));

                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        return $query->get();
    }

    public function findByRateId(int $rateId)
    {
        return RateChart::find($rateId);
    }

    public function updateById(int $rateId, array $updateData): bool
    {
        return RateChart::where('id', $rateId)->update([
            'weight' => $updateData['weight'],
            'rate_amount' => $updateData['rate_amount'],
            'updated_date' => now(),
        ]);
    }

    public function deleteById(int $rateId): bool
    {
        return RateChart::where('id', $rateId)->delete();
    }
}
