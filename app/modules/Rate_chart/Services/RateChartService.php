<?php

namespace App\modules\Rate_chart\Services;

use App\modules\Rate_chart\BO\RateChartBO;
use App\modules\Rate_chart\Helpers\RateChartHelper;
use App\modules\Rate_chart\Loggers\RateChartLogger;
use App\modules\Rate_chart\Validators\RateChartValidator;
use App\Repository\Interfaces\RateChartRepositoryInterface;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;

class RateChartService
{

    protected RateChartRepositoryInterface $rateChartRepositoryInterface;
    protected RateChartLogger $rateChartLogger;
    protected RateChartHelper $rateChartHelper;
    protected RateChartValidator $rateChartValidator;


    public function __construct(RateChartRepositoryInterface $rateChartRepositoryInterface, RateChartLogger $rateChartLogger, RateChartHelper $rateChartHelper, RateChartValidator $rateChartValidator)
    {
        $this->rateChartRepositoryInterface = $rateChartRepositoryInterface;
        $this->rateChartLogger = $rateChartLogger;
        $this->rateChartHelper = $rateChartHelper;
        $this->rateChartValidator = $rateChartValidator;
    }

    public function createRates(RateChartBO $rateChartBO): array
    {
        $userId = $rateChartBO->getUserId();
        $rateDataArray = $rateChartBO->getRateDataArray();
    
        $existingRates = $this->rateChartRepositoryInterface->getByUserId($userId);
        $existingWeights = [];
    
        foreach ($existingRates as $rate) {
            $existingWeights[] = (float) $rate['weight'];
        }
    
        // ✅ Validation moved to Validator
        $this->rateChartValidator->validateCreateRateData($rateDataArray, $existingWeights);
    
        foreach ($rateDataArray as $rateData) {
            $originalWeight = (float) $rateData['weight'];
            $decimalPart = $originalWeight - floor($originalWeight);
    
            if ($decimalPart > 0 && $decimalPart <= 0.5) {
                $rateData['weight'] = floor($originalWeight) + 0.5;
            } elseif ($decimalPart > 0.5) {
                $rateData['weight'] = ceil($originalWeight);
            }
    
            $this->rateChartLogger->createRate([
                'user_id' => $userId,
                'weight' => $rateData['weight'],
                'rate_amount' => $rateData['rate_amount'],
                'created_date' => now(),
                'updated_date' => now(),
            ]);
        }
    
        return [
            'status' => "success",
            'message' => 'Rates created successfully.',
            'status_code' => 201
        ];
    }
    

    public function getRates(RateChartBO $rateChartBO): array
    {
        $filters = $rateChartBO->toArray();

        $rates = $this->rateChartRepositoryInterface->getRates($filters);
        $rateData = $rates->isEmpty();

        $this->rateChartValidator->validatorRateChartNotFound($rateData);

        $formattedRates = $this->rateChartHelper->formatRates($rates);

        return [
            'status' => 'success',
            'data' => $formattedRates,
            'status_code' => 200
        ];
    }

    public function exportRates(RateChartBO $rateChartBO): array
    {
        $filters = $rateChartBO->toArray();

        $data = $this->rateChartRepositoryInterface->getRates($filters);
        $isEmptyRateChart = $data->isEmpty();

        $this->rateChartValidator->validateRateExportNotFound($isEmptyRateChart);

        $formattedData = $this->rateChartHelper->formatExportData($data);

        $filePath = storage_path('app/public/filtered_Customers_Rates.csv');

        (new FastExcel(collect($formattedData)))->export($filePath);

        return [
            'message' => 'Filtered customer rate data exported successfully!',
            'file_path' => asset('storage/filtered_Customers_Rates.csv'),
        ];
    }

    public function updateRate(RateChartBO $rateBO): array
    {
        $rateId = $rateBO->getRateId();
        $userId = $rateBO->getUserId();
        $originalWeight = (float) $rateBO->getWeight();
        $rateAmount = $rateBO->getRateAmount();

        $existingRate = $this->rateChartRepositoryInterface->findByRateId($rateId);
        $this->rateChartValidator->validateRateExists($existingRate);

        $userRates = $this->rateChartRepositoryInterface->getByUserId($userId);
        $this->rateChartValidator->validateUserRateExists($userRates, $userId);

        // Rounding logic
        $decimalPart = $originalWeight - floor($originalWeight);
        if ($decimalPart > 0 && $decimalPart <= 0.5) {
            $roundedWeight = floor($originalWeight) + 0.5;
        } elseif ($decimalPart > 0.5) {
            $roundedWeight = ceil($originalWeight);
        } else {
            $roundedWeight = $originalWeight;
        }

        $this->rateChartValidator->validateDuplicateWeightExists($userRates, $roundedWeight, $rateId, $userId);

        $updateData = [
            'weight' => $roundedWeight,
            'rate_amount' => $rateAmount
        ];

        $updated = $this->rateChartLogger->updateRateChart($rateId, $updateData);
        $this->rateChartValidator->validateUpdateFailed($updated);

        return [
            'status' => 'success',
            'message' => 'Rate updated successfully.',
            'status_code' => 200
        ];
    }

    public function deleteRate(RateChartBO $rateBO): array
    {
        $rateId = $rateBO->getRateId();

        $rate = $this->rateChartRepositoryInterface->findByRateId($rateId);
        $this->rateChartValidator->validateRateExists($rate);

        $deleted = $this->rateChartRepositoryInterface->deleteById($rateId);
        $this->rateChartValidator->validateRateDeleted($deleted);

        return [
            'status' => 'success',
            'message' => 'Rate deleted successfully.',
            'status_code' => 200
        ];
    }
}
