<?php

namespace App\modules\Rate_chart\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RateChart\CreateRateRequest;
use App\Http\Requests\RateChart\DeleteRatesRequest;
use App\Http\Requests\RateChart\GetRatesRequest;
use App\Http\Requests\RateChart\RateChartImportRequest;
use App\Http\Requests\RateChart\UpdateRatesRequest;
use App\modules\Rate_chart\BO\RateChartBO;
use App\modules\Rate_chart\Services\RateChartService;
use Exception;
use Illuminate\Http\JsonResponse;

use Illuminate\Http\Request;

class RateChartController extends Controller
{

    public function createRate(CreateRateRequest $request): JsonResponse
    {
        try {
            $rateChartBO = app(RateChartBO::class);
            $rateChartService = app(RateChartService::class);

            $rateChartBO->setUserId($request['user_id']);
            $rateChartBO->setRateDataArray($request->input('data'));

            if (empty($rateChartBO->getRateDataArray())) {
                return response()->json([
                    'status' => "error",
                    'message' => 'No rate data provided.',
                ], 400);
            }

            $response = $rateChartService->createRates($rateChartBO);

            return response()->json([
                'status' => $response['status'],
                'message' => $response['message'],
            ], $response['status_code']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => "error",
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getRates(GetRatesRequest $request): JsonResponse
    {
        try {
            $rateChartBO = app(RateChartBO::class);
            $rateChartService = app(RateChartService::class);

            $rateChartBO->setUserId($request->input('user_id')); // Required user_id filter
            $rateChartBO->setWeight($request->input('weight'));
            $rateChartBO->setCreatedDate($request->input('created_date'));

            $response = $rateChartService->getRates($rateChartBO);

            return response()->json([
                'status' => $response['status'],
                'data' => $response['data']
            ], $response['status_code']);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function exportRates(GetRatesRequest $request): JsonResponse
    {
        try {
            $rateChartService = app(RateChartService::class);
            $rateChartBO = app(RateChartBO::class);

            $rateChartBO->setUserId($request->input('user_id'));
            $rateChartBO->setWeight($request->input('weight'));
            $rateChartBO->setCreatedDate($request->input('created_date'));

            $response = $rateChartService->exportRates($rateChartBO);

            return response()->json([
                'message'   => $response['message'],
                'file_path' => $response['file_path']
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function updateRate(UpdateRatesRequest $request): JsonResponse
    {
        try {
            $rateBO = app(RateChartBO::class);
            $rateBO->setRateId($request->input('id'));
            $rateBO->setUserId($request->input('user_id'));
            $rateBO->setWeight($request->input('weight'));
            $rateBO->setRateAmount($request->input('rate_amount'));

            $rateUpdateService = app(RateChartService::class);
            $response = $rateUpdateService->updateRate($rateBO);

            return response()->json([
                'status' => $response['status'],
                'message' => $response['message']
            ], $response['status_code']);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteRate(DeleteRatesRequest $request): JsonResponse
    {
        try {
            $rateBO = app(RateChartBO::class);
            $rateBO->setRateId($request->input('id'));

            $rateDeleteService = app(RateChartService::class);
            $response = $rateDeleteService->deleteRate($rateBO);

            return response()->json([
                'status' => $response['status'],
                'message' => $response['message']
            ], $response['status_code']);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function importRates(RateChartImportRequest $request): JsonResponse
    {
        $rateChartService = app(RateChartService::class);
        $result = $rateChartService->handleImport($request);

        return response()->json($result);
    }
}
