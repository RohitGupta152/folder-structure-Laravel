<?php


namespace App\Repository;


use App\Models\User;
use App\Models\RateChart;

use App\Repository\Interfaces\RateChartRepositoryInterface;

class RateChartRepository implements RateChartRepositoryInterface
{

    public function getRateForWeight(float $weight, int $userId)
    {

        // ROHIT ka Code hai 
        // return RateChart::whereIn('user_id', [$userId, 0]) // Check user-specific and default rates
        // ->where('weight', '>=', $weight)
        // // ->orderBy('weight', 'asc')
        // ->get();


        // ROHIT ka Code hai 

        /*$userRates = RateChart::where('user_id', $userId)
            ->where('weight', $weight)
            ->get();
        // dd($userRates->toArray());

        if ($userRates->isNotEmpty()) {
            return $userRates;
        }

        $defaultRates = RateChart::where('user_id', 0)
            ->where('weight', $weight)
            ->get();
        // dd($defaultRates->toArray());

        return $defaultRates; */


        //code is improved 

        $userRates = RateChart::whereIn('user_id', [$userId, 0])
            ->where('weight', $weight)
            ->get();
        return $userRates;





        // ->orderByRaw("FIELD(user_id, $userId, 0)") // Prioritize user-specific rates
        // ->orderBy('weight', 'asc')

        // If no applicable rate is found, return the highest weight rate (fallback)
        // return $rate ?? RateChart::whereIn('user_id', [$userId, 0])
        //     ->orderBy('weight', 'desc')
        //     ->first();

    }


}