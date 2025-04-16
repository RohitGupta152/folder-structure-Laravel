<?php

namespace App\modules\Rate_chart\Helpers;

use Carbon\Carbon;

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

    public function parseDate($dateString)
    {
        if (empty(trim($dateString))) {
            return now()->setTimezone('Asia/Kolkata')->format('Y-m-d h:i:s');
        }

        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'd-m-Y H:i:s',
            'd-m-Y H:i',
            'Y-m-d',
            'd-m-Y',
            'm-d-Y',
            'Y/m/d',
            'd/m/Y',
            'm/d/Y',
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateString)->setTimezone('Asia/Kolkata');

                if (strlen($dateString) <= 10) {
                    $date->setTime(0, 0, 0);
                } elseif (strlen($dateString) <= 16) {
                    $date->setSeconds(0);
                }

                return $date->format('Y-m-d h:i:s'); // 12-hour format
            } catch (\Exception $e) {
                continue;
            }
        }

        return now()->setTimezone('Asia/Kolkata')->format('Y-m-d h:i:s');
    }
}
