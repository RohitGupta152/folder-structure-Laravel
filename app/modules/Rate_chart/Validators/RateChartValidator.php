<?php

namespace App\modules\Rate_chart\Validators;

use Exception;
use Illuminate\Support\Collection;

class RateChartValidator
{
    public function validateCreateRateData(array $rateDataArray, array $existingWeights): void
    {
        $roundedWeights = [];

        foreach ($rateDataArray as $rateData) {
            $originalWeight = (float) $rateData['weight'];
            $decimalPart = $originalWeight - floor($originalWeight);

            if ($decimalPart > 0 && $decimalPart <= 0.5) {
                $roundedWeight = floor($originalWeight) + 0.5;
            } elseif ($decimalPart > 0.5) {
                $roundedWeight = ceil($originalWeight);
            } else {
                $roundedWeight = $originalWeight;
            }

            // Check duplicate within request
            if (in_array($roundedWeight, $roundedWeights, true)) {
                throw new \Exception("Duplicate rounded weight {$roundedWeight} found in the request.");
            }

            $roundedWeights[] = $roundedWeight;
        }

        // Check for duplicate weights in DB
        $duplicateWeights = array_intersect($existingWeights, $roundedWeights);
        if (!empty($duplicateWeights)) {
            throw new \Exception("Rates with weights " . implode(', ', $duplicateWeights) . " already exist for this User ID.");
        }
    }


    public function validatorRateChartNotFound(bool $rateData)
    {
        if ($rateData) {
            throw new Exception('Rate Chart not found.');
        }
    }

    public function validateRateExportNotFound(bool $isEmpty): void
    {
        if ($isEmpty) {
            throw new Exception("No Rate Chart data found for export.");
        }
    }

    public function validateRateExists($rate): void
    {
        if (empty($rate)) {
            throw new Exception("Rate not found.");
        }
    }

    public function validateUserRateExists($userRates, int $userId): void
    {
        if ($userRates->isEmpty()) {
            throw new Exception("User ID {$userId} does not exist in the database.");
        }
    }

    public function validateDuplicateWeightExists(Collection $userRates, float $roundedWeight, int $rateId, int $userId): void
    {
        foreach ($userRates as $rate) {
            if (
                $rate->id !== $rateId &&
                (float) $rate->weight === $roundedWeight
            ) {
                throw new Exception("Rate with weight {$roundedWeight} already exists for user ID {$userId}.");
            }
        }
    }

    public function validateUpdateFailed(bool $updated): void
    {
        if (!$updated) {
            throw new Exception("Failed to update rate.");
        }
    }

    public function validateRateDeleted(bool $deleted): void
    {
        if (!$deleted) {
            throw new Exception("Failed to delete rate.");
        }
    }

    public function validateCreateRateChartImport(array $rateData, array $existingWeights)
    {
        $newWeight = (float) $rateData['weight'];

        if (in_array($newWeight, $existingWeights)) {
            throw new Exception("Weight {$newWeight} already exists for this user");
        }

        if ($newWeight <= 0) {
            throw new Exception("Weight must be a positive number eg 0.5 kg and More");
        }

        if ($newWeight < 0.5 || $newWeight > 100) {
            throw new Exception("Weight must be between 0 and 100 kg");
        }
    }
}
