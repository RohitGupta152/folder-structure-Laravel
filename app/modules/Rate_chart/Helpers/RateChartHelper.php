<?php

namespace App\modules\Rate_chart\Helpers;

class RateChartHelper
{

    public static function formatRates($rates): array
    {
        $formatted = [];

        foreach ($rates as $rate) {
            $formatted[] = [
                'weight'        => $rate['weight'] . ' Kg',
                'rate_amount'   => $rate['rate_amount'] . ' Rs',
                'created_date'  => date('d-M-y  h:i A', strtotime($rate['created_date'])),
                'updated_date'  => date('d-M-y  h:i A', strtotime($rate['updated_date']))
            ];
        }

        return $formatted;
    }

    public static function formatExportData($data): array
    {
        $formatted = [];

        foreach ($data as $rate) {
            // dd($rate);
            $formatted[] = [
                'Customer Name' => $rate->user['name'] ?? 'Default Rate',
                'Customer Email' => $rate->user['email'] ?? '',
                'Weight (KG)' => $rate->weight . ' Kg',
                'Rate Amount' => $rate->rate_amount . ' Rs',
                'Created On' => date('d-M-y h:i A', strtotime($rate->created_date)),
                'Updated On' => date('d-M-y h:i A', strtotime($rate->updated_date)),
            ];
        }

        return $formatted;
    }
}
